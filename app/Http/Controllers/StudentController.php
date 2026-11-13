<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentController extends Controller
{
    /**
     * ==========================================
     * STUDENT DASHBOARD METHOD
     * ==========================================
     */

    // 1. Ipakita ang Student Dashboard
    public function dashboard()
    {
        $studentId = Auth::id();
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        $unreadNotificationCount = Auth::user()->unreadNotifications()->count();

        $upcomingAppointment = Appointment::with('faculty')
            ->where('student_id', $studentId)
            ->whereIn('status', ['approved', 'confirmed', 'pending'])
            ->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->orderBy('appointment_date', 'asc')
            ->first();

        $faculties = User::where('role', 'faculty')
            ->orderBy('name', 'asc')
            ->take(10)
            ->get();

        $pendingCount = Appointment::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();

        $announcements = collect();
        if (Schema::hasTable('announcements')) {
            $announcements = DB::table('announcements')
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get();
        }

        return view('student.dashboard', compact(
            'upcomingAppointment',
            'faculties',
            'pendingCount',
            'announcements',
            'notifications',
            'unreadNotificationCount'
        ));
    }

    public function markNotificationRead($id)
    {
        $notification = Auth::user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return back();
    }


    /**
     * ==========================================
     * FACULTY AVAILABILITY METHOD (STUDENT SIDE)
     * ==========================================
     */

    // 2. Ipakita ang Faculty Availability para sa mga Estudyante
    public function facultyAvailability(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));

        $faculties = User::where('role', 'faculty')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($faculties as $faculty) {
            $faculty->today_count = Appointment::where('faculty_id', $faculty->id)
                ->whereDate('appointment_date', $selectedDate)
                ->whereIn('status', ['pending', 'approved', 'confirmed', 'completed'])
                ->count();

            $faculty->max_slots = $faculty->max_slots ?? 5;
        }

        return view('student.faculty-availability', compact('faculties', 'selectedDate'));
    }
}