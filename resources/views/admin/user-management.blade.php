@extends('layouts.app')

@section('title', 'User Management & Approvals - SNSU Admin')

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
                <h2 class="dash-title">USER ACCOUNT VERIFICATION & MANAGEMENT</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div role="alert" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fecaca; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px;">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                <!-- Section 1: Pending Registrations for Verification -->
                <div class="dash-card support-card-glass faculty-card-box" style="margin-bottom: 25px;">
                    <h3 class="card-title-support" style="color: #facc15;">
                        <i class="fa-solid fa-user-clock"></i> Pending Account Verification Requests
                    </h3>
                    <p class="schedule-subhead" style="margin-bottom: 15px;">Verify if new registrations belong to real Faculty or Students</p>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; text-transform: uppercase;">
                                    <th style="padding: 12px 15px;">Full Name</th>
                                    <th style="padding: 12px 15px;">Email</th>
                                    <th style="padding: 12px 15px;">Requested Role</th>
                                    <th style="padding: 12px 15px;">Department / Course / Designation</th>
                                    <th style="padding: 12px 15px;">Student ID</th>
                                    <th style="padding: 12px 15px; text-align: center;">COR / Actions</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem;">
                                @forelse($pendingUsers as $user)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <td style="padding: 14px 15px; font-weight: 600;">{{ $user->name }}</td>
                                    <td style="padding: 14px 15px;">{{ $user->email }}</td>
                                    <td style="padding: 14px 15px;">
                                        @if($user->role === 'faculty')
                                            <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Faculty</span>
                                        @else
                                            <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Student</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        {{ $user->department ?? $user->course ?? $user->designation ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        {{ $user->student_id ?? $user->id_number ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            @if($user->role === 'student' && $user->cor_path)
                                                <a href="{{ route('admin.users.cor', $user->id) }}" target="_blank" rel="noopener" style="background: #2563eb; color: white; padding: 6px 10px; border-radius: 8px; font-weight: 700; text-decoration: none;">
                                                    <i class="fa-solid fa-file-arrow-down"></i> View COR
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user->id) }}" style="background: rgba(255,255,255,0.2); color: white; padding: 6px 10px; border-radius: 8px; font-weight: 700; text-decoration: none;">Edit</a>
                                            <!-- Accept / Approve Form -->
                                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="margin:0;">
                                                @csrf
                                                <button type="submit" style="background: #22c55e; border: none; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                                    <i class="fa-solid fa-check"></i> Accept
                                                </button>
                                            </form>

                                            <!-- Reject Form -->
                                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="margin:0;">
                                                @csrf
                                                <button type="submit" style="background: #ef4444; border: none; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('Are you sure you want to REJECT this account registration?')">
                                                    <i class="fa-solid fa-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="padding: 20px; text-align: center; color: rgba(255,255,255,0.6);">
                                        No pending account verification requests at the moment.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 2: Verified Active Users -->
                <div class="dash-card support-card-glass faculty-card-box">
                    <h3 class="card-title-support" style="color: #4ade80;">
                        <i class="fa-solid fa-user-check"></i> Existing Accounts
                    </h3>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; text-transform: uppercase;">
                                    <th style="padding: 12px 15px;">Full Name</th>
                                    <th style="padding: 12px 15px;">Email</th>
                                    <th style="padding: 12px 15px;">Role</th>
                                    <th style="padding: 12px 15px;">Department / Course</th>
                                    <th style="padding: 12px 15px;">Student ID</th>
                                    <th style="padding: 12px 15px;">Status</th>
                                    <th style="padding: 12px 15px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem;">
                                @forelse($verifiedUsers as $vUser)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <td style="padding: 14px 15px; font-weight: 600;">{{ $vUser->name }}</td>
                                    <td style="padding: 14px 15px;">{{ $vUser->email }}</td>
                                    <td style="padding: 14px 15px;">
                                        <span style="text-transform: capitalize; font-weight: 700;">{{ $vUser->role }}</span>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        {{ $vUser->department ?? $vUser->course ?? $vUser->designation ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        {{ $vUser->student_id ?? $vUser->id_number ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <span style="color: {{ $vUser->status === 'approved' ? '#4ade80' : '#f87171' }}; font-weight: 700; text-transform: capitalize;">{{ $vUser->status }}</span>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <a href="{{ route('admin.users.edit', $vUser->id) }}" style="color: #93c5fd; font-weight: 700;">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="padding: 20px; text-align: center; color: rgba(255,255,255,0.6);">
                                        No existing accounts found.
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