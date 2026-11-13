<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Notifications\StudentStatusNotification;

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

        $verifiedUsers = User::whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.user-management', compact('pendingUsers', 'verifiedUsers'));
    }

    // 3. I-approve ang Account sa User
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'student'
            && (!$user->cor_path || !Storage::disk('local')->exists($user->cor_path))) {
            return back()->withErrors(['cor' => 'A COR document must be available for review before approving this student.']);
        }

        $user->status = 'approved';
        $user->save();
        if ($user->role === 'student') {
            $user->notify(new StudentStatusNotification(
                'Your student registration has been approved. You can now book appointments.',
                'approved',
                'account'
            ));
        }

        return back()->with('success', "Account for {$user->name} has been APPROVED successfully!");
    }

    // 4. I-reject ang Account sa User
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();
        if ($user->role === 'student') {
            $user->notify(new StudentStatusNotification(
                'Your student registration was rejected. Please contact the System Admin for assistance.',
                'rejected',
                'account'
            ));
        }

        return back()->with('success', "Account for {$user->name} has been REJECTED.");
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $previousStatus = $user->status;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'student_id' => 'nullable|string|max:255|unique:users,student_id,' . $user->id,
            'course' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $user->update($validated);
        if ($user->role === 'student' && $previousStatus !== $user->status && in_array($user->status, ['approved', 'rejected'], true)) {
            $message = $user->status === 'approved'
                ? 'Your student registration has been approved. You can now book appointments.'
                : 'Your student registration was rejected. Please contact the System Admin for assistance.';
            $user->notify(new StudentStatusNotification($message, $user->status, 'account'));
        }

        return redirect()->route('admin.user-management')
            ->with('success', "User profile for {$user->name} has been updated.");
    }

    public function downloadCor($id)
    {
        $user = User::where('role', 'student')->findOrFail($id);
        abort_unless($user->cor_path && Storage::disk('local')->exists($user->cor_path), 404);

        return Storage::disk('local')->download($user->cor_path, 'student-' . $user->student_id . '-cor');
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