@extends('layouts.app')

@section('title', 'Admin Dashboard - SNSU System Administration')

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

            <!-- Profile Badge -->
            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">{{ Auth::user()->name ?? 'System Admin' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 8px;">
                    @csrf
                    <button type="submit" title="Logout" style="background: none; border: none; cursor: pointer; color: #1a1a1a; font-size: 1rem;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body Layout -->
        <div class="dashboard-body">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-solid fa-chart-pie"></i> ADMIN DASHBOARD
                </a>
                <a href="{{ route('admin.user-management') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.user-management*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-users-gear"></i> USER MANAGEMENT
                </a>
                <a href="{{ route('admin.faculty-directory') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.faculty-directory*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-tie"></i> FACULTY DIRECTORY
                </a>
                <a href="{{ route('admin.student-roster') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.student-roster*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-graduate"></i> STUDENT ROSTER
                </a>
                <a href="{{ route('admin.all-appointments') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.all-appointments*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-calendar-check"></i> ALL APPOINTMENTS
                </a>
                <a href="{{ route('admin.support-tickets') }}" class="sidebar-link" data-active="{{ Request::routeIs('admin.support-tickets*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-headset"></i> SUPPORT TICKETS
                </a>
            </aside>

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">SYSTEM OVERVIEW</h2>

                <!-- Stat Cards Row (Live Database Sync) -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
                    
                    <div class="dash-card support-card-glass" style="padding: 18px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="color: #aaa; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL USERS</span>
                            <h3 style="color: #38bdf8; font-size: 1.8rem; margin: 4px 0 0 0; font-weight: 800;">{{ $stats['total_users'] ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-users" style="font-size: 2rem; color: rgba(255,255,255,0.15);"></i>
                    </div>

                    <div class="dash-card support-card-glass" style="padding: 18px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="color: #aaa; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">FACULTY MEMBERS</span>
                            <h3 style="color: #60a5fa; font-size: 1.8rem; margin: 4px 0 0 0; font-weight: 800;">{{ $stats['faculty_members'] ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-user-tie" style="font-size: 2rem; color: rgba(255,255,255,0.15);"></i>
                    </div>

                    <div class="dash-card support-card-glass" style="padding: 18px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="color: #aaa; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">ACTIVE CONSULTATIONS</span>
                            <h3 style="color: #facc15; font-size: 1.8rem; margin: 4px 0 0 0; font-weight: 800;">{{ $stats['active_consultations'] ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-calendar-check" style="font-size: 2rem; color: rgba(255,255,255,0.15);"></i>
                    </div>

                    <div class="dash-card support-card-glass" style="padding: 18px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="color: #aaa; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">OPEN SUPPORT TICKETS</span>
                            <h3 style="color: #ef4444; font-size: 1.8rem; margin: 4px 0 0 0; font-weight: 800;">{{ $stats['open_tickets'] ?? 0 }}</h3>
                        </div>
                        <i class="fa-solid fa-headset" style="font-size: 2rem; color: rgba(255,255,255,0.15);"></i>
                    </div>

                </div>

                <!-- Recent Activity Table (Live Database Sync) -->
                <div class="dash-card support-card-glass" style="padding: 24px;">
                    <div style="margin-bottom: 18px;">
                        <h3 style="color: white; margin: 0; font-size: 1.1rem;">System Activity & User Registrations</h3>
                        <p style="color: #aaa; margin: 4px 0 0 0; font-size: 0.8rem;">Monitor recent accounts and application interactions</p>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.15);">
                                    <th style="padding: 12px 16px; font-size: 0.8rem; color: #2ae269;">USER NAME</th>
                                    <th style="padding: 12px 16px; font-size: 0.8rem; color: #2ae269;">EMAIL</th>
                                    <th style="padding: 12px 16px; font-size: 0.8rem; color: #2ae269;">ASSIGNED ROLE</th>
                                    <th style="padding: 12px 16px; font-size: 0.8rem; color: #2ae269;">DATE REGISTERED</th>
                                    <th style="padding: 12px 16px; font-size: 0.8rem; color: #2ae269;">ACCOUNT STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentUsers) && count($recentUsers) > 0)
                                    @foreach($recentUsers as $user)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 12px 16px; font-weight: bold; font-size: 0.88rem;">
                                                {{ $user->name }}
                                            </td>
                                            <td style="padding: 12px 16px; color: #ccc; font-size: 0.85rem;">
                                                {{ $user->email }}
                                            </td>
                                            <td style="padding: 12px 16px;">
                                                @if($user->role === 'faculty')
                                                    <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 700;">Faculty</span>
                                                @elseif($user->role === 'admin' || $user->role === 'superadmin')
                                                    <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 700;">Admin</span>
                                                @else
                                                    <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 700;">Student</span>
                                                @endif
                                            </td>
                                            <td style="padding: 12px 16px; color: #aaa; font-size: 0.82rem;">
                                                {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                                            </td>
                                            <td style="padding: 12px 16px;">
                                                @if($user->status === 'approved' || $user->status === 'active')
                                                    <span style="color: #4ade80; font-weight: 800; font-size: 0.8rem;">Active</span>
                                                @elseif($user->status === 'rejected')
                                                    <span style="color: #ef4444; font-weight: 800; font-size: 0.8rem;">Rejected</span>
                                                @else
                                                    <span style="color: #fde047; font-weight: 800; font-size: 0.8rem;">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 25px; color: #aaa;">
                                            No user activity records found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="footer-nav-link" style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('welcome') }}" style="color: #2ae269; text-decoration: underline;">Back to Main Site</a>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection