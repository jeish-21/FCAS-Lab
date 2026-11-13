@extends('layouts.app')

@section('title', 'All Appointments - SNSU Admin')

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
                <h2 class="dash-title">SYSTEM CONSULTATIONS & APPOINTMENTS</h2>
                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Search and Filter Bar Card -->
                <div class="dash-card support-card-glass faculty-card-box" style="margin-bottom: 20px;">
                    <form action="{{ route('admin.all-appointments') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        <div style="flex: 1; min-width: 200px;">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by student, faculty, or reason..." class="support-dark-select" style="padding: 10px 14px; width: 100%;">
                        </div>

                        <div style="width: 200px;">
                            <select name="status" class="support-dark-select" style="width: 100%;">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Approved / Confirmed</option>
                                <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected / Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-green-post" style="padding: 10px 20px;">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>

                        @if(!empty($search) || !empty($status))
                            <a href="{{ route('admin.all-appointments') }}" style="color: rgba(255,255,255,0.7); text-decoration: underline; font-size: 0.9rem;">Clear Filters</a>
                        @endif
                    </form>
                </div>

                <!-- Appointments Table Card -->
                <div class="dash-card support-card-glass faculty-card-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 class="card-title-support" style="color: #facc15; margin: 0;">
                            <i class="fa-solid fa-calendar-check"></i> Master Appointments List
                        </h3>
                        <span style="background: rgba(250, 204, 21, 0.2); color: #facc15; border: 1px solid #eab308; padding: 4px 12px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;">
                            Total Records: {{ $appointments->count() }}
                        </span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; text-transform: uppercase;">
                                    <th style="padding: 12px 15px;">Student</th>
                                    <th style="padding: 12px 15px;">Faculty Assigned</th>
                                    <th style="padding: 12px 15px;">Consultation Type & Reason</th>
                                    <th style="padding: 12px 15px;">Date & Time Slot</th>
                                    <th style="padding: 12px 15px; text-align: center;">Status</th>
                                    <th style="padding: 12px 15px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem;">
                                @forelse($appointments as $appointment)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <td style="padding: 14px 15px; font-weight: 600;">
                                        {{ $appointment->student->name ?? 'Student #' . $appointment->student_id }}
                                    </td>
                                    <td style="padding: 14px 15px; color: #60a5fa; font-weight: 600;">
                                        {{ $appointment->faculty->name ?? 'Faculty #' . $appointment->faculty_id }}
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <strong>{{ ucfirst($appointment->consultation_type ?? 'Online') }}</strong><br>
                                        <small style="color: rgba(255,255,255,0.7);">{{ $appointment->reason }}</small>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <i class="fa-regular fa-calendar" style="margin-right: 4px; color: #a4f238;"></i> {{ $appointment->appointment_date }}<br>
                                        <small style="color: rgba(255,255,255,0.7);"><i class="fa-regular fa-clock" style="margin-right: 4px;"></i> {{ $appointment->time_slot }}</small>
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        @if(in_array(strtolower($appointment->status), ['approved', 'confirmed']))
                                            <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Approved</span>
                                        @elseif(strtolower($appointment->status) === 'completed')
                                            <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Completed</span>
                                        @elseif(in_array(strtolower($appointment->status), ['rejected', 'cancelled']))
                                            <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Rejected</span>
                                        @else
                                            <span style="background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid #eab308; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Pending</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        <a href="{{ route('admin.appointments.edit', $appointment->id) }}" style="background: #2563eb; color: white; text-decoration: none; padding: 7px 12px; border-radius: 8px; font-weight: 700;">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="padding: 25px; text-align: center; color: rgba(255,255,255,0.6);">
                                        No appointment records found matching the selected criteria.
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