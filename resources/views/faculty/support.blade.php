@extends('layouts.app')

@section('title', 'Faculty Support - SNSU System')

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
                <a href="{{ route('faculty.dashboard') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.dashboard') ? 'true' : 'false' }}">
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
                <a href="{{ route('faculty.support') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT
                </a>
            </aside>

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">FACULTY SUPPORT & HELP DESK</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <!-- Submit Support Form -->
                    <div class="dash-card support-card-glass" style="padding: 24px;">
                        <h3 style="color: white; margin-top: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-headset" style="color: #2ae269;"></i> Submit Technical Issue / Inquiry
                        </h3>
                        <p style="color: #aaa; font-size: 0.8rem; margin-top: -5px; margin-bottom: 18px;">
                            Report system bugs, schedule conflicts, or course alignment concerns to System Admin
                        </p>

                        <form action="{{ route('faculty.support.submit') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                            @csrf
                            <div>
                                <label style="display: block; color: white; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Issue Category</label>
                                <select name="category" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; padding: 10px; font-size: 0.85rem; box-sizing: border-box;" required>
                                    <option value="Schedule Matrix / Timetable Bug" style="background: #1a1a1a;">Schedule Matrix / Timetable Bug</option>
                                    <option value="Student Roster Issue" style="background: #1a1a1a;">Student Roster Issue</option>
                                    <option value="Account / Profile Concern" style="background: #1a1a1a;">Account / Profile Concern</option>
                                    <option value="General Inquiry" style="background: #1a1a1a;">General Inquiry</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; color: white; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Subject / Brief Summary</label>
                                <input type="text" name="subject" placeholder="e.g. Schedule slot not updating on Thursday" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; padding: 10px; font-size: 0.85rem; box-sizing: border-box;" required>
                            </div>

                            <div>
                                <label style="display: block; color: white; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Detailed Explanation</label>
                                <textarea name="message" rows="4" placeholder="Describe the error or technical issue encountered..." style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; padding: 10px; font-size: 0.85rem; box-sizing: border-box; resize: vertical;" required></textarea>
                            </div>

                            <div style="text-align: right; margin-top: 5px;">
                                <button type="submit" style="background: #2ae269; color: #1a1a1a; border: none; font-weight: 800; padding: 10px 22px; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                                    Submit Ticket
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Submitted Tickets List (DB Sync) -->
                    <div class="dash-card support-card-glass" style="padding: 24px;">
                        <h3 style="color: white; margin-top: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-list-check" style="color: #2ae269;"></i> Submitted Support Tickets
                        </h3>
                        <p style="color: #aaa; font-size: 0.8rem; margin-top: -5px; margin-bottom: 18px;">
                            Track status of your submitted tickets
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 12px; max-height: 380px; overflow-y: auto;">
                            @if(isset($tickets) && count($tickets) > 0)
                                @foreach($tickets as $ticket)
                                    <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 14px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                            <div>
                                                <h4 style="color: white; margin: 0; font-size: 0.9rem;">
                                                    #TKT-{{ $ticket->id }}: {{ $ticket->subject }}
                                                </h4>
                                                <p style="color: #888; margin: 3px 0 6px 0; font-size: 0.75rem;">
                                                    Submitted: {{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                                                    <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">Resolved</span>
                                                @elseif($ticket->status === 'in_progress')
                                                    <span style="background: rgba(234, 179, 8, 0.2); color: #fde047; border: 1px solid #eab308; padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">In Progress</span>
                                                @else
                                                    <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">Open</span>
                                                @endif
                                            </div>
                                        </div>

                                        <p style="color: #ccc; margin: 4px 0 0 0; font-size: 0.8rem; line-height: 1.3;">
                                            {{ $ticket->message }}
                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align: center; color: #aaa; padding: 30px; background: rgba(255,255,255,0.03); border-radius: 12px;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 1.8rem; margin-bottom: 8px; display: block;"></i>
                                    No support tickets submitted yet.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

              
            </main>
        </div>
    </div>
</div>
@endsection