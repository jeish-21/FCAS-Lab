<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * ==========================================
     * FACULTY PROFILE METHODS
     * ==========================================
     */

    // 1. I-update ang Profile sa Faculty
    public function facultyUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'department'  => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'password'    => 'nullable|string|min:6',
        ]);

        $user->name = $request->input('name');
        if ($request->filled('department')) {
            $user->department = $request->input('department');
        }
        if ($request->filled('designation')) {
            $user->designation = $request->input('designation');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }


    /**
     * ==========================================
     * STUDENT PROFILE METHODS
     * ==========================================
     */

    // 2. I-update ang Profile sa Student
    public function studentUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'course'     => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'password'   => 'nullable|string|min:6',
        ]);

        $user->name = $request->input('name');
        if ($request->filled('student_id')) {
            $user->student_id = $request->input('student_id');
        }
        if ($request->filled('course')) {
            $user->course = $request->input('course');
        }
        if ($request->filled('department')) {
            $user->department = $request->input('department');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}