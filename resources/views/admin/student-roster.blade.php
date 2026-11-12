@extends('layouts.app')

@section('title', 'Student Roster - SNSU Admin')

@section('content')
<div class="main-wrapper">
    <div class="shape shape-top-right"></div>
    <div class="shape shape-bottom-left"></div>

    <div class="app-container">
        <!-- Top Navigation Header -->
        <header class="top-navbar">
            <div class="brand-section">
                <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                    <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="brand-logo">
                    <h1 class="brand-title">SNSU Consultation & System Administration</h1>
                </a>
            </div>

            <!-- Profile Badge & Logout -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="user-profile-badge">
                    <div class="avatar-img">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-greeting">Logged in as:</span>
                        <span class="user-name">
                            {{ Auth::user()->name ?? 'System Administrator' }}
                            <small class="pill-role-badge" style="background: #ef4444; color: white;">Admin</small>
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; padding: 6px 12px; border-radius: 12px; font-weight: 700; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body Layout -->
        <div class="dashboard-body">
            <!-- Admin Sidebar Navigation -->
            <aside class="sidebar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.dashboard') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-gauge-high"></i> ADMIN DASHBOARD
                </a>
                
                <a href="{{ route('admin.user-management') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.user-management') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-users-gear"></i> USER MANAGEMENT
                </a>

                <a href="{{ route('admin.faculty-directory') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.faculty-directory') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-tie"></i> FACULTY DIRECTORY
                </a>
                
                <a href="{{ route('admin.student-roster') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.student-roster') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-graduation-cap"></i> STUDENT ROSTER
                </a>
                
                <a href="{{ route('admin.all-appointments') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.all-appointments') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-calendar-check"></i> ALL APPOINTMENTS
                </a>
                
                <a href="{{ route('admin.support-tickets') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.support-tickets') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-headset"></i> SUPPORT TICKETS
                </a>
            </aside>   

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">STUDENT ROSTER</h2>

                <!-- Search and Filter Bar Card -->
                <div class="dash-card support-card-glass faculty-card-box" style="margin-bottom: 20px;">
                    <form action="{{ route('admin.student-roster') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        <div style="flex: 1; min-width: 200px;">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by student name, ID number, or email..." class="support-dark-select" style="padding: 10px 14px; width: 100%;">
                        </div>

                        <div style="width: 220px;">
                            <select name="course" class="support-dark-select" style="width: 100%;">
                                <option value="">All Courses / Programs</option>
                                <option value="BS Information Technology" {{ ($course ?? '') == 'BS Information Technology' ? 'selected' : '' }}>BS IT</option>
                                <option value="BS Computer Science" {{ ($course ?? '') == 'BS Computer Science' ? 'selected' : '' }}>BS CS</option>
                                <option value="BS Information Systems" {{ ($course ?? '') == 'BS Information Systems' ? 'selected' : '' }}>BS IS</option>
                                <option value="BS Computer Engineering" {{ ($course ?? '') == 'BS Computer Engineering' ? 'selected' : '' }}>BS CpE</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-green-post" style="padding: 10px 20px;">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>

                        @if(!empty($search) || !empty($course))
                            <a href="{{ route('admin.student-roster') }}" style="color: rgba(255,255,255,0.7); text-decoration: underline; font-size: 0.9rem;">Clear Filters</a>
                        @endif
                    </form>
                </div>

                <!-- Student Table Card -->
                <div class="dash-card support-card-glass faculty-card-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 class="card-title-support" style="color: #4ade80; margin: 0;">
                            <i class="fa-solid fa-graduation-cap"></i> Enrolled & Registered Students
                        </h3>
                        <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 4px 12px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;">
                            Total: {{ $students->count() }}
                        </span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; text-transform: uppercase;">
                                    <th style="padding: 12px 15px;">Student Name</th>
                                    <th style="padding: 12px 15px;">Student ID</th>
                                    <th style="padding: 12px 15px;">Email Address</th>
                                    <th style="padding: 12px 15px;">Course / Program</th>
                                    <th style="padding: 12px 15px; text-align: center;">Account Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem;">
                                @forelse($students as $student)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <td style="padding: 14px 15px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(34, 197, 94, 0.3); display: flex; align-items: center; justify-content: center; color: #4ade80; font-weight: bold;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        {{ $student->name }}
                                    </td>
                                    <td style="padding: 14px 15px; font-weight: 700; color: #a4f238;">
                                        {{ $student->student_id ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 15px;">{{ $student->email }}</td>
                                    <td style="padding: 14px 15px;">{{ $student->course ?? 'Unassigned' }}</td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        @if(($student->status ?? 'approved') === 'approved')
                                            <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Active</span>
                                        @elseif($student->status === 'rejected')
                                            <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Rejected</span>
                                        @else
                                            <span style="background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid #eab308; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="padding: 25px; text-align: center; color: rgba(255,255,255,0.6);">
                                        No student records found matching the selected criteria.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="footer-nav-link" style="margin-top: 15px;">
                    <a href="{{ route('welcome') }}"><u>Back to Main Site</u></a>
                </div>
            </main>
        </div>
    </div>
</div>

<script src="{{ asset('js/main.js') }}"></script>
@endsection