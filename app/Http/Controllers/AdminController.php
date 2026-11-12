<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /**
     * ==========================================
     * ADMIN DASHBOARD & USER MANAGEMENT METHODS
     * ==========================================
     */

    // 1. Ipakita ang Admin Dashboard nga adunay mga estadistika ug recent users
    public function dashboard()
    {
        $stats = [
            'total_users'          => User::count(),
            'faculty_members'      => User::where('role', 'faculty')->count(),
            'active_consultations' => Schema::hasTable('appointments') 
                                        ? Appointment::whereIn('status', ['pending', 'approved', 'confirmed'])->count() 
                                        : 0,
            'open_tickets'         => Schema::hasTable('support_tickets') 
                                        ? DB::table('support_tickets')->where('status', 'open')->count() 
                                        : 0,
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    // 2. Ipakita ang User Management (Pending ug Verified users)
    public function userManagement()
    {
        $pendingUsers = User::where('status', 'pending')
            ->orWhereNull('status')
            ->orderBy('created_at', 'desc')
            ->get();

        $verifiedUsers = User::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.user-management', compact('pendingUsers', 'verifiedUsers'));
    }

    // 3. I-approve ang Account sa User
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return back()->with('success', "Account for {$user->name} has been APPROVED successfully!");
    }

    // 4. I-reject ang Account sa User
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return back()->with('success', "Account for {$user->name} has been REJECTED.");
    }


    /**
     * ==========================================
     * DIRECTORIES & ROSTERS METHODS
     * ==========================================
     */

    // 5. Faculty Directory (Naa'y search ug department filter)
    public function facultyDirectory(Request $request)
    {
        $search = $request->input('search');
        $department = $request->input('department');

        $query = User::where('role', 'faculty');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('designation', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($department)) {
            $query->where('department', $department);
        }

        $faculties = $query->orderBy('name', 'asc')->get();

        return view('admin.faculty-directory', compact('faculties', 'search', 'department'));
    }

    // 6. Student Roster (Naa'y search ug course filter)
    public function studentRoster(Request $request)
    {
        $search = $request->input('search');
        $course = $request->input('course');

        $query = User::where('role', 'student');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('student_id', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($course)) {
            $query->where('course', $course);
        }

        $students = $query->orderBy('name', 'asc')->get();

        return view('admin.student-roster', compact('students', 'search', 'course'));
    }
}