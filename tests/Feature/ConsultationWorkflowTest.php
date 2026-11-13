<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConsultationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_requires_a_cor_and_starts_pending(): void
    {
        Storage::fake('local');
        $registration = [
            'first_name' => 'Taylor',
            'last_name' => 'Student',
            'student_id' => '2026-001',
            'course' => 'BSIT',
            'email' => 'taylor.student',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post(route('register.post', ['role' => 'student']), $registration)
            ->assertSessionHasErrors('cor');
        $this->assertDatabaseMissing('users', ['email' => 'taylor.student@ssct.edu.ph']);

        $this->post(route('register.post', ['role' => 'student']), $registration + [
            'role' => 'admin',
            'cor' => UploadedFile::fake()->create('registration.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('login', ['role' => 'student']));

        $student = User::where('email', 'taylor.student@ssct.edu.ph')->firstOrFail();
        $this->assertSame('student', $student->role);
        $this->assertSame('pending', $student->status);
        Storage::disk('local')->assertExists($student->cor_path);
    }

    public function test_admin_account_decision_creates_an_in_app_notification(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('cor/verified.pdf', 'pdf content');
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $studentWithoutCor = User::factory()->create(['role' => 'student', 'status' => 'pending']);
        $this->actingAs($admin)
            ->post(route('admin.users.approve', $studentWithoutCor->id))
            ->assertSessionHasErrors('cor');
        $this->assertDatabaseHas('users', ['id' => $studentWithoutCor->id, 'status' => 'pending']);

        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'pending',
            'cor_path' => 'cor/verified.pdf',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $student->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $student->id, 'status' => 'approved']);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'type' => \App\Notifications\StudentStatusNotification::class,
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Your student registration has been approved');

        $notificationId = DB::table('notifications')
            ->where('notifiable_id', $student->id)
            ->value('id');
        $this->post(route('student.notifications.read', $notificationId))->assertRedirect();
        $this->assertNotNull(DB::table('notifications')->where('id', $notificationId)->value('read_at'));

        $rejectedStudent = User::factory()->create(['role' => 'student', 'status' => 'pending']);
        $this->actingAs($admin)
            ->post(route('admin.users.reject', $rejectedStudent->id))
            ->assertRedirect();
        $rejectionData = json_decode(DB::table('notifications')
            ->where('notifiable_id', $rejectedStudent->id)
            ->value('data'), true);
        $this->assertSame('rejected', $rejectionData['status']);
    }

    public function test_student_cannot_book_a_faculty_blocked_slot(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $faculty = User::factory()->create(['role' => 'faculty', 'status' => 'approved']);
        $date = now()->addDay()->toDateString();
        $this->actingAs($faculty)
            ->post(route('faculty.my-schedule.save'), [
                'schedule_date' => $date,
                'time_slot' => '09:00 AM',
                'slot_type' => 'Unavailable',
            ])
            ->assertRedirect();

        $this->actingAs($student)
            ->getJson(route('student.availability-slots', [
                'faculty_id' => $faculty->id,
                'appointment_date' => $date,
            ]))
            ->assertOk()
            ->assertJsonPath('slots.4.time', '09:00 AM')
            ->assertJsonPath('slots.4.available', false)
            ->assertJsonPath('slots.4.reason', 'Unavailable');

        $this->actingAs($student)
            ->get(route('student.book-appointment', ['faculty_id' => $faculty->id, 'appointment_date' => $date]))
            ->assertOk()
            ->assertSee('09:00 AM - Unavailable');

        $this->post(route('student.book-appointment.post'), [
            'faculty_id' => $faculty->id,
            'appointment_date' => $date,
            'time_slot' => '09:00 AM',
            'reason' => 'Project consultation',
            'consultation_type' => 'In-Person Office',
        ])
            ->assertSessionHasErrors('time_slot');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_faculty_status_change_notifies_the_student(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $faculty = User::factory()->create(['role' => 'faculty', 'status' => 'approved']);
        $appointment = Appointment::create([
            'student_id' => $student->id,
            'faculty_id' => $faculty->id,
            'reason' => 'Project consultation',
            'consultation_type' => 'In-Person Office',
            'appointment_date' => now()->addDay()->toDateString(),
            'time_slot' => '09:00 AM',
            'status' => 'pending',
        ]);

        $this->actingAs($faculty)
            ->post(route('faculty.appointments.update-status', $appointment->id), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'type' => \App\Notifications\StudentStatusNotification::class,
        ]);
    }

    public function test_pending_student_is_rejected_by_booking_endpoint(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'pending']);
        $faculty = User::factory()->create(['role' => 'faculty', 'status' => 'approved']);

        $this->actingAs($student)
            ->post(route('student.book-appointment.post'), [
                'faculty_id' => $faculty->id,
                'appointment_date' => now()->addDay()->toDateString(),
                'time_slot' => '09:00 AM',
                'reason' => 'Project consultation',
                'consultation_type' => 'In-Person Office',
            ])
            ->assertRedirect(route('student.dashboard'))
            ->assertSessionHasErrors('booking');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_admin_can_edit_appointment_fields_without_a_password_control(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $faculty = User::factory()->create(['role' => 'faculty', 'status' => 'approved']);
        $newFaculty = User::factory()->create(['role' => 'faculty', 'status' => 'approved']);
        $appointment = Appointment::create([
            'student_id' => $student->id,
            'faculty_id' => $faculty->id,
            'reason' => 'Initial purpose',
            'consultation_type' => 'In-Person Office',
            'appointment_date' => now()->addDay()->toDateString(),
            'time_slot' => '09:00 AM',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $student->id))
            ->assertOk()
            ->assertDontSee('name="password"');

        $this->actingAs($admin)
            ->put(route('admin.appointments.update', $appointment->id), [
                'faculty_id' => $newFaculty->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'time_slot' => '10:00 AM',
                'reason' => 'Updated purpose',
                'description' => 'Updated details',
                'consultation_type' => 'Online Video Call',
                'status' => 'approved',
            ])
            ->assertRedirect(route('admin.all-appointments'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'faculty_id' => $newFaculty->id,
            'reason' => 'Updated purpose',
            'time_slot' => '10:00 AM',
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'type' => \App\Notifications\StudentStatusNotification::class,
        ]);
    }

    public function test_admin_can_edit_user_profile_without_changing_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $passwordHash = $student->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $student->id), [
                'name' => 'Updated Student',
                'email' => 'updated.student@example.test',
                'student_id' => '2026-099',
                'course' => 'BSCS',
                'department' => 'CEIT',
                'designation' => '',
                'status' => 'approved',
                'password' => 'admin-supplied-password',
            ])
            ->assertRedirect(route('admin.user-management'));

        $student->refresh();
        $this->assertSame('Updated Student', $student->name);
        $this->assertSame($passwordHash, $student->password);
    }
}
