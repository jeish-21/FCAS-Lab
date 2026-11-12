@extends('layouts.app')

@section('title', 'Support Tickets - SNSU Admin')

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
                <h2 class="dash-title">SYSTEM SUPPORT TICKETS</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Search and Filter Bar Card -->
                <div class="dash-card support-card-glass faculty-card-box" style="margin-bottom: 20px;">
                    <form action="{{ route('admin.support-tickets') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        <div style="flex: 1; min-width: 200px;">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by ticket subject, sender, or message..." class="support-dark-select" style="padding: 10px 14px; width: 100%;">
                        </div>

                        <div style="width: 200px;">
                            <select name="status" class="support-dark-select" style="width: 100%;">
                                <option value="">All Ticket Statuses</option>
                                <option value="open" {{ ($status ?? '') == 'open' ? 'selected' : '' }}>Open / Pending</option>
                                <option value="in_progress" {{ ($status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ ($status ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-green-post" style="padding: 10px 20px;">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>

                        @if(!empty($search) || !empty($status))
                            <a href="{{ route('admin.support-tickets') }}" style="color: rgba(255,255,255,0.7); text-decoration: underline; font-size: 0.9rem;">Clear Filters</a>
                        @endif
                    </form>
                </div>

                <!-- Support Tickets Master Table Card -->
                <div class="dash-card support-card-glass faculty-card-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 class="card-title-support" style="color: #a78bfa; margin: 0;">
                            <i class="fa-solid fa-headset"></i> User Helpdesk & Support Issues
                        </h3>
                        <span style="background: rgba(167, 139, 250, 0.2); color: #c084fc; border: 1px solid #a855f7; padding: 4px 12px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;">
                            Total: {{ $tickets->count() }}
                        </span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; text-transform: uppercase;">
                                    <th style="padding: 12px 15px;">Ticket ID</th>
                                    <th style="padding: 12px 15px;">User / Sender</th>
                                    <th style="padding: 12px 15px;">Subject & Issue</th>
                                    <th style="padding: 12px 15px;">Category</th>
                                    <th style="padding: 12px 15px; text-align: center;">Status</th>
                                    <th style="padding: 12px 15px; text-align: center;">Admin Resolution</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem;">
                                @forelse($tickets as $ticket)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <td style="padding: 14px 15px; font-weight: 700; color: #a855f7;">
                                        #TCK-{{ $ticket->id ?? '001' }}
                                    </td>
                                    <td style="padding: 14px 15px; font-weight: 600;">
                                        {{ $ticket->user_name ?? 'SNSU User' }}<br>
                                        <small style="color: rgba(255,255,255,0.6);">{{ $ticket->email ?? 'user@ssct.edu.ph' }}</small>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <strong>{{ $ticket->subject ?? 'Account Login Assistance' }}</strong>
                                        <br><small style="color: rgba(255,255,255,0.6);">{{ Str::limit($ticket->message ?? 'Unable to access faculty consultation calendar', 40) }}</small>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <span style="background: rgba(255,255,255,0.1); padding: 3px 8px; border-radius: 6px; font-size: 0.8rem;">
                                            {{ $ticket->category ?? 'Technical' }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        @if(($ticket->status ?? 'open') === 'resolved')
                                            <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Resolved</span>
                                        @elseif(($ticket->status ?? 'open') === 'in_progress')
                                            <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">In Progress</span>
                                        @else
                                            <span style="background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid #eab308; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Open</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        <form action="{{ route('admin.support-tickets.update-status', $ticket->id ?? 1) }}" method="POST" style="display: inline-flex; gap: 5px;">
                                            @csrf
                                            <button type="submit" name="status" value="in_progress" title="Mark In Progress" style="background: #3b82f6; border: none; color: white; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 700;">
                                                <i class="fa-solid fa-spinner"></i> Process
                                            </button>
                                            <button type="submit" name="status" value="resolved" title="Mark Resolved" style="background: #22c55e; border: none; color: white; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 700;">
                                                <i class="fa-solid fa-circle-check"></i> Resolve
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="padding: 25px; text-align: center; color: rgba(255,255,255,0.6);">
                                        No support tickets submitted yet or matching the filter criteria.
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