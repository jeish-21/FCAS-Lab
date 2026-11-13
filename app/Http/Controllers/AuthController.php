<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * ==========================================
     * LOGIN METHODS
     * ==========================================
     */

    // 1. Ipakita ang Login Page base sa role (student, faculty, admin)
    public function showLogin($role = 'student')
    {
        if ($role === 'faculty') {
            return view('auth.faculty-login');
        }
        if ($role === 'admin') {
            return view('auth.admin-login');
        }

        return view('auth.student-login');
    }

    // 2. I-handle ang Login POST Request (Auto-appends @ssct.edu.ph & Status Check)
    public function login(Request $request)
    {
        $emailInput = trim($request->input('email'));
        $password = $request->input('password');
        $loginRole = $request->input('role', 'student');

        $username = str_replace('@ssct.edu.ph', '', $emailInput);
        $fullEmail = $username . '@ssct.edu.ph';

        // Verify Credentials First
        if (Auth::attempt(['email' => $fullEmail, 'password' => $password])) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Admin and Superadmin bypass verification check
            if ($user->role === 'admin' || $user->role === 'superadmin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            // Account Status Check for Student and Faculty
            if ($user->status === 'pending' || empty($user->status)) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Your account registration is currently PENDING and needs to be approved by the System Admin before you can log in.',
                ])->withInput();
            }

            if ($user->status === 'rejected') {
                if ($user->role !== 'student') {
                    Auth::logout();
                    $request->session()->invalidate();
                    return back()->withErrors([
                        'email' => 'Your account registration request has been REJECTED by the System Admin.',
                    ])->withInput();
                }
            }

            // Status Approved - Sync Role & Redirect Access
            $request->session()->regenerate();

            if ($loginRole === 'faculty' || $user->role === 'faculty') {
                return redirect()->route('faculty.dashboard');
            }

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or user does not exist in the database.',
        ])->withInput();
    }


    /**
     * ==========================================
     * REGISTRATION METHODS
     * ==========================================
     */

    // 3. Ipakita ang Registration Page base sa role
    public function showRegister($role = 'student')
    {
        if ($role === 'faculty') {
            return view('auth.faculty-register');
        }
        return view('auth.register');
    }

    // 4. I-handle ang Register POST Request (Creation & Pending status)
    public function register(Request $request, $role = 'student')
    {
        $postRole = $role === 'faculty' ? 'faculty' : 'student';
        $emailInput = trim($request->input('email'));

        $username = str_replace('@ssct.edu.ph', '', $emailInput);
        $fullEmail = $username . '@ssct.edu.ph';

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'cor' => $postRole === 'student'
                ? 'required|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:10240'
                : 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:10240',
        ]);

        $studentId = $request->input('student_id') ?? $request->input('id_number') ?? $request->input('student_no');

        if (User::where('email', $fullEmail)->exists()) {
            return back()->withErrors([
                'email' => 'The email ' . $fullEmail . ' is already registered in the system. Please use a different email address.',
            ])->withInput();
        }

        if (!empty($studentId) && User::where('student_id', $studentId)->exists()) {
            return back()->withErrors([
                'student_id' => 'The Student ID ' . $studentId . ' is already registered in the system.',
            ])->withInput();
        }

        $firstName = trim($request->input('first_name'));
        $lastName = trim($request->input('last_name'));
        $name = (!empty($firstName) || !empty($lastName)) 
                ? trim($firstName . ' ' . $lastName) 
                : trim($request->input('name', 'Registered User'));

        $course      = $request->input('course')       ?? $request->input('program');
        $department  = $request->input('department')   ?? $request->input('college')     ?? 'College of Computer Studies';
        $designation = $request->input('designation')  ?? $request->input('position');

        $corPath = null;
        if ($postRole === 'student') {
            $corPath = $request->file('cor')->store('cor', 'local');
            if ($corPath === false) {
                throw new \RuntimeException('The COR document could not be stored.');
            }
        }

        User::create([
            'name'        => $name,
            'student_id'  => $studentId,
            'course'      => $course,
            'department'  => $department,
            'designation' => $designation,
            'cor_path'    => $corPath,
            'email'       => $fullEmail,
            'password'    => Hash::make($request->input('password')),
            'role'        => $postRole,
            'status'      => 'pending',
        ]);

        return redirect()->route('login', ['role' => $postRole])
            ->with('success', 'Registration submitted successfully! Your account is PENDING approval after the System Admin reviews your registration documents.');
    }


    /**
     * ==========================================
     * LOGOUT METHOD
     * ==========================================
     */

    // 5. I-handle ang Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }
}