@extends('layouts.app')

@section('title', 'My Schedule - SNSU Faculty System')

@section('content')
<div class="main-wrapper">
    <div class="shape shape-top-right"></div>
    <div class="shape shape-bottom-left"></div>

    <div class="app-container">
        <!-- Top Navigation Header -->
        <header class="top-navbar">
            <div class="brand-section">
                <a href="{{ route('faculty.dashboard') }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                    <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="brand-logo">
                    <h1 class="brand-title">SNSU Faculty Consultation<br>& Appointment System</h1>
                </a>
            </div>

            <!-- Dynamic Profile Badge -->
            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">
                        {{ Auth::user()->name ?? 'Faculty Member' }}
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
            <!-- Faculty Sidebar Navigation -->
            <aside class="sidebar">
                <a href="{{ route('faculty.dashboard') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.dashboard') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-border-all"></i> FACULTY HUB
                </a>
                <a href="{{ route('faculty.profile-settings') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.profile-settings*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-gear"></i> PROFILE & SETTINGS
                </a>
                <a href="{{ route('faculty.my-schedule') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-regular fa-calendar-days"></i> MY SCHEDULE
                </a>
                <a href="{{ route('faculty.consultation-requests') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.consultation-requests*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-plus"></i> CONSULTATION REQUESTS
                </a>
                <a href="{{ route('faculty.student-management') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.student-management*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-users"></i> STUDENT MANAGEMENT
                </a>
                <a href="{{ route('faculty.announcements') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.announcements*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS
                </a>
                <a href="{{ route('faculty.support') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.support*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT
                </a>
            </aside>

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">MY SCHEDULE</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="dash-card support-card-glass" style="padding: 20px;">
                    <div style="margin-bottom: 20px;">
                        <h3 class="card-title-support" style="margin: 0;">Weekly Consultation & Teaching Timetable</h3>
                        <p style="color: #ccc; font-size: 0.85rem; margin-top: 4px;">Click any box once to toggle Consultation slot or Undo back to Empty directly in Database. Accepted student consultations appear in blue.</p>
                    </div>

                    @php
                        $schedulesList = isset($schedules) ? $schedules : collect();
                        $approvedList = isset($approvedAppointments) ? $approvedAppointments : collect();
                    @endphp

                    <!-- Timetable Grid -->
                    <div style="display: grid; grid-template-columns: 90px repeat(5, 1fr); gap: 8px; text-align: center;">
                        <!-- Header Row -->
                        <div style="font-weight: bold; color: #2ae269;">Time</div>
                        <div style="font-weight: bold; color: white;">Mon</div>
                        <div style="font-weight: bold; color: white;">Tue</div>
                        <div style="font-weight: bold; color: white;">Wed</div>
                        <div style="font-weight: bold; color: white;">Thu</div>
                        <div style="font-weight: bold; color: white;">Fri</div>

                        @php
                            $timeSlots = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '01:00 PM', '02:00 PM'];
                            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                        @endphp

                        @foreach($timeSlots as $time)
                            <div style="color: #ddd; font-size: 0.82rem; font-weight: 600; padding: 10px 0; display: flex; align-items: center; justify-content: center;">
                                {{ $time }}
                            </div>

                            @foreach($days as $day)
                                @php
                                    // Robust Matching for Student Appointments
                                    $bookedAppointment = $approvedList->firstWhere(function($app) use ($day, $time) {
                                        if (empty($app->appointment_date)) return false;

                                        // 1. Day of week matching (e.g., Wed -> Wed)
                                        $appDay = \Carbon\Carbon::parse($app->appointment_date)->format('D');
                                        $dayMatches = (strtolower($appDay) === strtolower($day));

                                        // 2. Time slot matching (Handles "01:00 PM", "1:00 PM", "01:00 PM - 02:00 PM", etc.)
                                        $appTime = $app->time_slot ?? '';
                                        
                                        // Extract digits and AM/PM
                                        $cleanGrid = preg_replace('/[^0-9AMPM]/i', '', $time);
                                        $cleanApp  = preg_replace('/[^0-9AMPM]/i', '', $appTime);

                                        $timeMatches = (
                                            $appTime === $time ||
                                            str_contains($appTime, $time) ||
                                            (!empty($cleanGrid) && str_contains($cleanApp, $cleanGrid)) ||
                                            (str_contains($time, '01:00') && str_contains($appTime, '01:00')) ||
                                            (str_contains($time, '01:00') && str_contains($appTime, '1:00'))
                                        );

                                        return $dayMatches && $timeMatches;
                                    });

                                    $slot = $schedulesList->firstWhere(function($item) use ($day, $time) {
                                        return isset($item->day_of_week) && $item->day_of_week === $day && isset($item->time_slot) && $item->time_slot === $time;
                                    });

                                    $slotType = $slot->slot_type ?? 'Empty';
                                    $nextType = ($slotType === 'Consultation') ? 'Empty' : 'Consultation';
                                @endphp

                                @if($bookedAppointment)
                                    <!-- Blue Highlight Box for Student Appointment -->
                                    <div title="Student: {{ $bookedAppointment->student->name ?? 'Student' }} | Status: {{ strtoupper($bookedAppointment->status) }}"
                                         style="width: 100%; border: 1px solid #3b82f6; border-radius: 10px; padding: 6px; min-height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: rgba(59, 130, 246, 0.55); color: #ffffff; box-shadow: 0 0 12px rgba(59, 130, 246, 0.7); cursor: pointer;">
                                        <span style="font-size: 0.8rem; color: #ffffff; font-weight: 800;">
                                            <i class="fa-solid fa-user-check"></i> {{ Str::limit($bookedAppointment->student->name ?? 'Student', 11) }}
                                        </span>
                                        <span style="font-size: 0.65rem; color: #dbeafe; font-weight: 600; text-transform: uppercase;">
                                            {{ $bookedAppointment->status }}
                                        </span>
                                    </div>
                                @else
                                    <!-- Direct Form Submit Button for Master Schedule Availability Toggle -->
                                    <form action="{{ route('faculty.my-schedule.save') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="day_of_week" value="{{ $day }}">
                                        <input type="hidden" name="time_slot" value="{{ $time }}">
                                        <input type="hidden" name="slot_type" value="{{ $nextType }}">

                                        <button type="submit" 
                                                title="Click to toggle {{ $nextType }}"
                                                style="width: 100%; border: none; border-radius: 10px; padding: 10px; cursor: pointer; min-height: 45px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; transition: all 0.2s ease;
                                                @if($slotType === 'Consultation')
                                                    background: #2ae269; color: #1a1a1a; box-shadow: 0 0 10px rgba(42, 226, 105, 0.4);
                                                @elseif($slotType === 'Lecture')
                                                    background: rgba(255,255,255,0.25); color: white;
                                                @else
                                                    background: rgba(255,255,255,0.08); border: 1px dashed rgba(255,255,255,0.2); color: #aaa;
                                                @endif">
                                            {{ $slotType }}
                                        </button>
                                    </form>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>
@endsection