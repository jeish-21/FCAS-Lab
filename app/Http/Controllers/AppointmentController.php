<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Notifications\StudentStatusNotification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    /**
     * ==========================================
     * STUDENT APPOINTMENT METHODS
     * ==========================================
     */

    // 1. Ipakita ang "Book an Appointment" page (Gikuha gikan sa Route 7)
    public function create(Request $request)
    {
        $request->validate([
            'appointment_date' => 'sometimes|date_format:Y-m-d',
            'faculty_id' => 'sometimes|integer',
        ]);
        $search = $request->input('search');
        $department = $request->input('department');
        $facultyId = $request->input('faculty_id');
        $selectedDate = $request->input('appointment_date', now()->format('Y-m-d'));

        $query = User::where('role', 'faculty');

        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if (!empty($department)) {
            $query->where('department', $department);
        }

        $faculties = $query->orderBy('name', 'asc')->get();

        if (!$faculties->contains('id', (int) $facultyId)) {
            $facultyId = $faculties->first()->id ?? null;
        }

        $scheduleData = $facultyId
            ? $this->scheduleDataFor((int) $facultyId, $selectedDate)
            : ['consultationSlots' => [], 'unavailableSlots' => []];

        $bookedSlots = [];
        if ($facultyId) {
            $bookedSlots = Appointment::where('faculty_id', $facultyId)
                ->whereDate('appointment_date', $selectedDate)
                ->whereIn('status', ['pending', 'approved', 'confirmed', 'accepted'])
                ->pluck('time_slot')
                ->toArray();
        }

        $consultationSlots = $scheduleData['consultationSlots'];
        $unavailableSlots = $scheduleData['unavailableSlots'];
        $canBook = Auth::user()->status === 'approved';

        return view('student.book-appointment', compact(
            'faculties',
            'consultationSlots',
            'unavailableSlots',
            'bookedSlots',
            'search',
            'department',
            'facultyId',
            'selectedDate',
            'canBook'
        ));
    }

    // 2. I-store/I-save ang bag-ong gihimo nga Appointment (Gikuha gikan sa Route 7)
    public function store(Request $request)
    {
        if (Auth::user()->status !== 'approved') {
            return redirect()->route('student.dashboard')
                ->withErrors(['booking' => 'Your account must be approved before you can book an appointment.']);
        }

        $request->validate([
            'faculty_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'faculty')],
            'appointment_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time_slot' => ['required', Rule::in($this->timeSlots())],
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'consultation_type' => 'required|in:In-Person Office,Online Video Call',
        ]);

        $facultyId = (int) $request->input('faculty_id');
        $appointmentDate = $request->input('appointment_date');
        $timeSlot = $request->input('time_slot');
        DB::transaction(function () use ($facultyId, $appointmentDate, $timeSlot, $request) {
            User::where('role', 'faculty')->lockForUpdate()->findOrFail($facultyId);
            $scheduleData = $this->scheduleDataFor($facultyId, $appointmentDate);

            if (in_array($timeSlot, $scheduleData['unavailableSlots'], true)
                || (count($scheduleData['consultationSlots']) > 0
                    && !in_array($timeSlot, $scheduleData['consultationSlots'], true))) {
                throw ValidationException::withMessages([
                    'time_slot' => 'The selected faculty member is not available at that time.',
                ]);
            }

            $isBooked = Appointment::where('faculty_id', $facultyId)
                ->whereDate('appointment_date', $appointmentDate)
                ->where('time_slot', $timeSlot)
                ->whereIn('status', ['pending', 'approved', 'confirmed', 'accepted'])
                ->exists();
            if ($isBooked) {
                throw ValidationException::withMessages([
                    'time_slot' => 'That time slot has already been booked. Please choose another time.',
                ]);
            }

            Appointment::create([
                'student_id'        => Auth::id(),
                'faculty_id'        => $facultyId,
                'reason'            => $request->input('reason'),
                'description'       => $request->input('description'),
                'consultation_type' => $request->input('consultation_type'),
                'appointment_date'  => $appointmentDate,
                'time_slot'         => $timeSlot,
                'status'            => 'pending',
            ]);
        });

        return redirect()->route('student.my-appointments')->with('success', 'Appointment requested successfully!');
    }

    public function availabilitySlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'faculty')],
            'appointment_date' => 'required|date_format:Y-m-d',
        ]);

        $facultyId = (int) $validated['faculty_id'];
        $date = $validated['appointment_date'];
        $scheduleData = $this->scheduleDataFor($facultyId, $date);
        $bookedSlots = Appointment::where('faculty_id', $facultyId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved', 'confirmed', 'accepted'])
            ->pluck('time_slot')
            ->all();

        $slots = [];
        foreach ($this->timeSlots() as $time) {
            $isBooked = in_array($time, $bookedSlots, true);
            $isMarkedUnavailable = in_array($time, $scheduleData['unavailableSlots'], true);
            $isOutsideConsultationSchedule = count($scheduleData['consultationSlots']) > 0
                && !in_array($time, $scheduleData['consultationSlots'], true);
            $unavailable = $isBooked || $isMarkedUnavailable || $isOutsideConsultationSchedule;

            $slots[] = [
                'time' => $time,
                'available' => !$unavailable,
                'reason' => $isBooked ? 'Booked' : ($unavailable ? 'Unavailable' : 'Available'),
            ];
        }

        return response()->json(['slots' => $slots]);
    }

    // 3. Ipakita ang "My Appointments" sa Student (Gikuha gikan sa Route 7)
    public function myAppointments()
    {
        $studentId = Auth::id();

        $upcomingAppointments = Appointment::with('faculty')
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'approved', 'confirmed', 'accepted'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        $pastAppointments = Appointment::with('faculty')
            ->where('student_id', $studentId)
            ->whereIn('status', ['completed', 'rescheduled', 'rejected', 'cancelled'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        $stats = [
            'total'     => Appointment::where('student_id', $studentId)->count(),
            'completed' => Appointment::where('student_id', $studentId)->where('status', 'completed')->count(),
            'upcoming'  => $upcomingAppointments->count(),
        ];

        return view('student.my-appointments', compact('upcomingAppointments', 'pastAppointments', 'stats'));
    }

    // 4. Consultation Logs sa Student (Gikuha gikan sa Route 7)
    public function consultationLogs()
    {
        $studentId = Auth::id();

        $logs = Appointment::with('faculty')
            ->where('student_id', $studentId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('student.consultation-logs', compact('logs'));
    }


    /**
     * ==========================================
     * FACULTY APPOINTMENT METHODS
     * ==========================================
     */

    // 5. Faculty Consultation Requests View (Gikuha gikan sa Route 5)
    public function facultyConsultationRequests()
    {
        $facultyId = Auth::id();

        $requests = Appointment::with('student')
            ->where('faculty_id', $facultyId)
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('faculty.consultation-requests', compact('requests'));
    }

    // 6. Faculty Update Status / Approve / Reject / Delete Appointment (Gikuha gikan sa Route 5)
    public function updateStatusByFaculty(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed,pending,cancelled,delete',
        ]);
        $status = $validated['status'];

        $appointment = Appointment::where('faculty_id', Auth::id())->findOrFail($id);
        if ($status === 'delete') {
            $appointment->delete();
            return back()->with('success', "Appointment record #{$id} has been permanently deleted.");
        }

        $statusChanged = $appointment->status !== $status;
        if ($statusChanged) {
            $appointment->status = $status;
            $appointment->save();
            $appointment->student->notify(new StudentStatusNotification(
                "Your appointment with {$appointment->faculty->name} is now " . strtoupper($status) . '.',
                $status,
                'appointment'
            ));
        }

        return back()->with('success', "Appointment status updated to " . strtoupper($status) . "!");
    }


    /**
     * ==========================================
     * ADMIN APPOINTMENT METHODS
     * ==========================================
     */

    // 7. Admin View All Appointments (Gikuha gikan sa Route 6)
    public function adminAllAppointments(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Appointment::with(['student', 'faculty']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('faculty', function ($fq) use ($search) {
                      $fq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        return view('admin.all-appointments', compact('appointments', 'search', 'status'));
    }

    public function editAdminAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $faculties = User::where('role', 'faculty')->orderBy('name')->get();

        return view('admin.edit-appointment', compact('appointment', 'faculties'));
    }

    public function updateAdminAppointment(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $validated = $request->validate([
            'faculty_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'faculty')],
            'appointment_date' => 'required|date_format:Y-m-d',
            'time_slot' => 'required|string|max:100',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'consultation_type' => 'required|string|max:100',
            'status' => 'required|in:pending,approved,confirmed,completed,rejected,cancelled',
        ]);

        $statusChanged = $appointment->status !== $validated['status'];
        $appointment->update($validated);

        if ($statusChanged) {
            $appointment->student->notify(new StudentStatusNotification(
                "An administrator updated your appointment status to " . strtoupper($appointment->status) . '.',
                $appointment->status,
                'appointment'
            ));
        }

        return redirect()->route('admin.all-appointments')->with('success', 'Appointment record updated successfully.');
    }

    private function scheduleDataFor(int $facultyId, string $date): array
    {
        if (!Schema::hasTable('faculty_schedules')) {
            return ['consultationSlots' => [], 'unavailableSlots' => []];
        }

        $day = Carbon::parse($date)->format('D');
        $query = DB::table('faculty_schedules')->where('faculty_id', $facultyId);
        if (Schema::hasColumn('faculty_schedules', 'schedule_date')) {
            $query->where(function ($scheduleQuery) use ($date, $day) {
                $scheduleQuery->whereDate('schedule_date', $date)
                    ->orWhere(function ($weeklyQuery) use ($day) {
                        $weeklyQuery->whereNull('schedule_date')->where('day_of_week', $day);
                    });
            });
        } else {
            $query->where('day_of_week', $day);
        }

        $schedules = $query->get();

        return [
            'consultationSlots' => $schedules->where('slot_type', 'Consultation')->pluck('time_slot')->unique()->values()->all(),
            'unavailableSlots' => $schedules->where('slot_type', '!=', 'Consultation')->pluck('time_slot')->unique()->values()->all(),
        ];
    }

    private function timeSlots(): array
    {
        return [
            '07:00 AM', '07:30 AM', '08:00 AM', '08:30 AM',
            '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM',
            '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM',
            '01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM',
            '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM',
            '05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM',
            '07:00 PM', '07:30 PM', '08:00 PM', '08:30 PM',
        ];
    }
}