<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        if (empty($facultyId) && $faculties->count() > 0) {
            $facultyId = $faculties->first()->id;
        }

        $schedules = collect();
        if (Schema::hasTable('faculty_schedules') && $facultyId) {
            $schedules = DB::table('faculty_schedules')
                ->where('faculty_id', $facultyId)
                ->where('slot_type', 'Consultation')
                ->pluck('time_slot')
                ->toArray();
        }

        $bookedSlots = [];
        if ($facultyId) {
            $bookedSlots = Appointment::where('faculty_id', $facultyId)
                ->whereDate('appointment_date', $selectedDate)
                ->whereIn('status', ['pending', 'approved', 'confirmed'])
                ->pluck('time_slot')
                ->toArray();
        }

        return view('student.book-appointment', compact(
            'faculties', 
            'schedules', 
            'bookedSlots', 
            'search', 
            'department', 
            'facultyId', 
            'selectedDate'
        ));
    }

    // 2. I-store/I-save ang bag-ong gihimo nga Appointment (Gikuha gikan sa Route 7)
    public function store(Request $request)
    {
        $request->validate([
            'reason'            => 'required|string',
            'consultation_type' => 'required|string',
        ]);

        $facultyId = $request->input('faculty_id');
        if (empty($facultyId)) {
            $firstFaculty = User::where('role', 'faculty')->first();
            $facultyId = $firstFaculty ? $firstFaculty->id : 1;
        }

        $timeSlot = $request->input('time_slot');
        if (empty($timeSlot)) {
            $timeSlot = '09:00 AM - 10:00 AM';
        }

        Appointment::create([
            'student_id'        => Auth::id(),
            'faculty_id'        => $facultyId,
            'reason'            => $request->input('reason'),
            'description'       => $request->input('description'),
            'consultation_type' => $request->input('consultation_type'),
            'appointment_date'  => $request->input('appointment_date', now()->format('Y-m-d')),
            'time_slot'         => $timeSlot,
            'status'            => 'pending',
        ]);

        return redirect()->route('student.my-appointments')->with('success', 'Appointment requested successfully!');
    }

    // 3. Ipakita ang "My Appointments" sa Student (Gikuha gikan sa Route 7)
    public function myAppointments()
    {
        $studentId = Auth::id();

        $upcomingAppointments = Appointment::with('faculty')
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'approved', 'confirmed'])
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
        $status = $request->input('status');

        $appointment = Appointment::find($id);
        if ($appointment) {
            if ($status === 'delete') {
                $appointment->delete();
                return back()->with('success', "Appointment record #{$id} has been permanently deleted.");
            }

            $appointment->status = $status;
            $appointment->save();

            return back()->with('success', "Appointment status updated to " . strtoupper($status) . "!");
        }

        return back()->withErrors(['error' => 'Appointment record not found.']);
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

    // 8. Admin Update Appointment Status (Gikuha gikan sa Route 6)
    public function updateStatusByAdmin(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $status = $request->input('status');

        if (in_array($status, ['approved', 'rejected', 'completed', 'pending', 'cancelled'])) {
            $appointment->status = $status;
            $appointment->save();
            return back()->with('success', "Appointment status updated to " . strtoupper($status) . "!");
        }

        return back()->withErrors(['status' => 'Invalid status option provided.']);
    }
}