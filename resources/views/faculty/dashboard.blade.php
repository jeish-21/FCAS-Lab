@extends('layouts.app')

@section('title', 'Faculty Dashboard - SNSU System')

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

            <!-- Profile Badge -->
            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">{{ Auth::user()->name ?? 'Faculty Member' }}</span>
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
                <a href="{{ route('faculty.dashboard') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-solid fa-border-all"></i> FACULTY HUB
                </a>
                <a href="{{ route('faculty.profile-settings') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.profile*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-gear"></i> PROFILE & SETTINGS
                </a>
                <a href="{{ route('faculty.my-schedule') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.my-schedule*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-days"></i> MY SCHEDULE
                </a>
                <a href="{{ route('faculty.consultation-requests') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.consultation-requests*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-calendar-check"></i> CONSULTATION REQUESTS
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
                <h2 class="dash-title">FACULTY HUB</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <!-- Quick Post Announcement Card -->
                    <div class="dash-card support-card-glass" style="padding: 20px;">
                        <h3 class="card-title-support" style="font-size: 1.1rem; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-bullhorn" style="color: #2ae269;"></i> Quick Post Announcement
                        </h3>

                        <form action="{{ route('faculty.announcements.quick-post') }}" method="POST" style="margin-top: 15px;">
                            @csrf
                            <textarea name="message" rows="4" placeholder="Type announcement here..." style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; color: white; padding: 12px; font-size: 0.9rem; resize: vertical; box-sizing: border-box;" required></textarea>
                            
                            <div style="text-align: right; margin-top: 10px;">
                                <button type="submit" style="background: #2ae269; color: #1a1a1a; font-weight: 700; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-paper-plane"></i> Post
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Pending Student Requests Card -->
                    <div class="dash-card support-card-glass" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 class="card-title-support" style="font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-clock-rotate-left" style="color: #eab308;"></i> Pending Student Requests
                                </h3>
                                <a href="{{ route('faculty.consultation-requests') }}" style="color: #2ae269; font-size: 0.85rem; text-decoration: none; font-weight: 600;">View All &rarr;</a>
                            </div>

                            <p style="color: #aaa; font-size: 0.8rem; margin: 5px 0 15px 0;">Upcoming, Unresolved Consultation Requests</p>

                            <!-- Live Database Requests Sync -->
                            @php
                                $facultyId = Auth::id();
                                $pendingRequests = \App\Models\Appointment::with('student')
                                    ->where('faculty_id', $facultyId)
                                    ->where('status', 'pending')
                                    ->take(2)
                                    ->get();
                            @endphp

                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @if($pendingRequests->count() > 0)
                                    @foreach($pendingRequests as $req)
                                        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <h4 style="color: white; margin: 0; font-size: 0.9rem;">{{ $req->student->name ?? 'Student User' }}</h4>
                                                <p style="color: #aaa; margin: 2px 0 0 0; font-size: 0.75rem;">
                                                    Course: {{ $req->student->course ?? 'BSIT' }}
                                                </p>
                                                <p style="color: #888; margin: 2px 0 0 0; font-size: 0.75rem;">
                                                    Reason: {{ $req->reason }}
                                                </p>
                                            </div>
                                            <span style="background: #eab308; color: #1a1a1a; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px;">Pending</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="text-align: center; color: #aaa; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 10px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-circle-check" style="color: #2ae269; font-size: 1.2rem; display: block; margin-bottom: 5px;"></i>
                                        No pending consultation requests.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <a href="{{ route('faculty.consultation-requests') }}" style="display: block; width: 100%; text-align: center; background: #22c55e; color: #1a1a1a; font-weight: 800; padding: 12px; border-radius: 10px; text-decoration: none; font-size: 0.9rem;">
                                Manage Requests in Consultation Requests Module
                            </a>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>
</div>
@endsection