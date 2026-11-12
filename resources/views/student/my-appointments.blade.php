@extends('layouts.app')

@section('title', 'My Appointments - SNSU Student System')

@section('content')
<div class="main-wrapper">
    <div class="shape shape-top-right"></div>
    <div class="shape shape-bottom-left"></div>

    <div class="app-container">
        <!-- Top Navigation Header -->
        <header class="top-navbar">
            <div class="brand-section">
                <a href="{{ route('student.dashboard') }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                    <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="brand-logo">
                    <h1 class="brand-title">SNSU Faculty Consultation<br>& Appointment System</h1>
                </a>
            </div>

            <!-- Profile Badge -->
            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">
                        {{ Auth::user()->name ?? 'Student User' }} 
                        @if(Auth::user() && Auth::user()->student_id)
                            ({{ Auth::user()->student_id }})
                        @endif
                    </span>
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
                <a href="{{ route('student.dashboard') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.dashboard') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-border-all"></i> DASHBOARD
                </a>
                <a href="{{ route('student.book-appointment') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.book-appointment*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-plus"></i> BOOK AN APPOINTMENT
                </a>
                <a href="{{ route('student.faculty-availability') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.faculty-availability*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-clock"></i> FACULTY AVAILABILITY
                </a>
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-regular fa-calendar-check"></i> MY APPOINTMENTS
                </a>
                <a href="{{ route('student.consultation-logs') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.consultation-logs*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-file-signature"></i> CONSULTATION LOGS
                </a>
                <a href="{{ route('student.announcements') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.announcements*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS
                </a>
                <a href="{{ route('student.support') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.support*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT
                </a>
            </aside>

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">MY APPOINTMENTS</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                    
                    <!-- Upcoming Consultations -->
                    <div class="dash-card support-card-glass" style="padding: 20px;">
                        <h3 class="card-title-support" style="font-size: 1.1rem; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-regular fa-clock" style="color: #2ae269;"></i> Upcoming Consultations
                        </h3>

                        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                            @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
                                @foreach($upcomingAppointments as $app)
                                    @php
                                        $facultyName = ($app->faculty && $app->faculty->id !== Auth::id()) 
                                            ? $app->faculty->name 
                                            : 'Faculty Member';
                                    @endphp
                                    <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="background: #2ae269; color: #1a1a1a; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem;">
                                                {{ strtoupper(substr($facultyName, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 style="color: white; margin: 0; font-size: 0.95rem;">{{ $facultyName }}</h4>
                                                <p style="color: #aaa; margin: 2px 0 0 0; font-size: 0.8rem;">
                                                    <i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($app->appointment_date)->format('F d, Y') }} | {{ $app->time_slot }}
                                                </p>
                                                <p style="color: #888; margin: 2px 0 0 0; font-size: 0.75rem;">
                                                    <strong>Reason:</strong> {{ $app->reason }} ({{ $app->consultation_type }})
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            @if($app->status === 'approved' || $app->status === 'confirmed')
                                                <span style="background: #22c55e; color: #1a1a1a; font-weight: 800; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem;">Confirmed</span>
                                            @else
                                                <span style="background: #eab308; color: #1a1a1a; font-weight: 800; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem;">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align: center; padding: 25px; color: #aaa; background: rgba(255,255,255,0.05); border-radius: 12px;">
                                    <i class="fa-regular fa-calendar-xmark" style="font-size: 1.8rem; margin-bottom: 8px; display: block;"></i>
                                    No upcoming consultations scheduled.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="dash-card support-card-glass" style="padding: 20px;">
                        <h3 class="card-title-support" style="font-size: 1.1rem; margin-top: 0;">Quick Stats</h3>

                        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                            <div style="background: rgba(255,255,255,0.08); padding: 12px 16px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #ccc; font-size: 0.9rem;">Total Appointments:</span>
                                <strong style="color: white; font-size: 1.2rem;">{{ $stats['total'] ?? 0 }}</strong>
                            </div>
                            <div style="background: rgba(42, 226, 105, 0.15); border: 1px solid rgba(42, 226, 105, 0.3); padding: 12px 16px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #2ae269; font-size: 0.9rem;">Completed:</span>
                                <strong style="color: #2ae269; font-size: 1.2rem;">{{ $stats['completed'] ?? 0 }}</strong>
                            </div>
                            <div style="background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.3); padding: 12px 16px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #eab308; font-size: 0.9rem;">Upcoming:</span>
                                <strong style="color: #eab308; font-size: 1.2rem;">{{ $stats['upcoming'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Past Consultations -->
                <div class="dash-card support-card-glass" style="padding: 20px;">
                    <h3 class="card-title-support" style="font-size: 1.1rem; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-history" style="color: #2ae269;"></i> Past Consultations
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                        @if(isset($pastAppointments) && count($pastAppointments) > 0)
                            @foreach($pastAppointments as $app)
                                @php
                                    $facultyName = ($app->faculty && $app->faculty->id !== Auth::id()) 
                                        ? $app->faculty->name 
                                        : 'Faculty Member';
                                @endphp
                                <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="background: rgba(255,255,255,0.2); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem;">
                                            {{ strtoupper(substr($facultyName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 style="color: white; margin: 0; font-size: 0.95rem;">{{ $facultyName }}</h4>
                                            <p style="color: #aaa; margin: 2px 0 0 0; font-size: 0.8rem;">
                                                <i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($app->appointment_date)->format('F d, Y') }} | {{ $app->time_slot }}
                                            </p>
                                            <p style="color: #888; margin: 2px 0 0 0; font-size: 0.75rem;">
                                                <strong>Reason:</strong> {{ $app->reason }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        @if($app->status === 'completed')
                                            <span style="background: #22c55e; color: #1a1a1a; font-weight: 800; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem;">
                                                <i class="fa-solid fa-circle-check"></i> Completed
                                            </span>
                                        @elseif($app->status === 'rejected')
                                            <span style="background: #ef4444; color: white; font-weight: 800; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem;">Rejected</span>
                                        @else
                                            <span style="background: #6b7280; color: white; font-weight: 800; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem;">{{ ucfirst($app->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; padding: 25px; color: #aaa; background: rgba(255,255,255,0.03); border-radius: 12px;">
                                <i class="fa-solid fa-folder-open" style="font-size: 1.8rem; margin-bottom: 8px; display: block;"></i>
                                No past consultation records found in database.
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection