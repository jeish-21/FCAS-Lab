<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class FacultyController extends Controller
{
    /**
     * ==========================================
     * FACULTY DASHBOARD & PROFILE METHODS
     * ==========================================
     */

    // 1. Ipakita ang Faculty Dashboard
    public function dashboard()
    {
        return view('faculty.dashboard');
    }

    // 2. Ipakita ang Faculty Profile Settings Page
    public function profileSettings()
    {
        return view('faculty.profile-settings');
    }


    /**
     * ==========================================
     * FACULTY SCHEDULE MANAGEMENT METHODS
     * ==========================================
     */

    // 3. Ipakita ang Master Schedules ug Active Student Appointments
    public function mySchedule()
    {
        $facultyId = Auth::id();
        $schedules = collect();
        $approvedAppointments = collect();

        if (Schema::hasTable('faculty_schedules')) {
            $schedules = DB::table('faculty_schedules')
                ->where('faculty_id', $facultyId)
                ->get();
        }

        if (Schema::hasTable('appointments')) {
            // Fetch ALL non-cancelled/non-rejected student appointments
            $approvedAppointments = Appointment::with('student')
                ->where('faculty_id', $facultyId)
                ->whereIn('status', ['pending', 'approved', 'confirmed', 'accepted'])
                ->get();
        }

        return view('faculty.my-schedule', compact('schedules', 'approvedAppointments'));
    }

    // 4. I-save, I-update, o I-clear ang Schedule Slot
    public function saveSchedule(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required_without:schedule_date|nullable|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'schedule_date' => 'nullable|date_format:Y-m-d',
            'time_slot'   => 'required|string',
            'slot_type'   => 'required|in:Consultation,Lecture,Break,Unavailable,Empty,Clear',
        ]);

        $facultyId = Auth::id();
        $slotType  = $request->input('slot_type');
        $scheduleDate = $request->input('schedule_date');
        $dayOfWeek = $scheduleDate
            ? Carbon::parse($scheduleDate)->format('D')
            : $request->input('day_of_week');

        if (Schema::hasTable('faculty_schedules')) {
            if ($slotType === 'Empty' || $slotType === 'Clear') {
                $query = DB::table('faculty_schedules')
                    ->where('faculty_id', $facultyId)
                    ->where('time_slot', $request->input('time_slot'));
                if ($scheduleDate) {
                    $query->whereDate('schedule_date', $scheduleDate);
                } else {
                    $query->whereNull('schedule_date')->where('day_of_week', $dayOfWeek);
                }
                $query->delete();

                return back()->with('success', 'Schedule slot cleared and set to Empty!');
            }

            DB::table('faculty_schedules')->updateOrInsert(
                $scheduleDate ? [
                    'faculty_id' => $facultyId,
                    'schedule_date' => $scheduleDate,
                    'time_slot' => $request->input('time_slot'),
                ] : [
                    'faculty_id'  => $facultyId,
                    'schedule_date' => null,
                    'day_of_week' => $dayOfWeek,
                    'time_slot'   => $request->input('time_slot'),
                ],
                [
                    'day_of_week'  => $dayOfWeek,
                    'slot_type'    => $slotType,
                    'title'        => $request->input('title', $slotType),
                    'is_available' => $slotType === 'Consultation' ? 1 : 0,
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );
        }

        return back()->with('success', 'Schedule slot updated successfully!');
    }


    /**
     * ==========================================
     * FACULTY STUDENT MANAGEMENT METHODS
     * ==========================================
     */

    // 5. Ipakita ang Student Management list uban ang search ug total consultations
    public function studentManagement(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('role', 'student');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('student_id', 'LIKE', "%{$search}%")
                  ->orWhere('course', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->orderBy('name', 'asc')->get();

        $facultyId = Auth::id();
        foreach ($students as $student) {
            $student->total_consultations = Appointment::where('student_id', $student->id)
                ->where('faculty_id', $facultyId)
                ->count();
        }

        return view('faculty.student-management', compact('students', 'search'));
    }
}