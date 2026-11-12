@extends('layouts.app')

@section('title', 'Profile & Settings - SNSU Faculty System')

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

            <!-- Dynamic Profile Badge & Logout -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="{{ route('faculty.profile-settings') }}" class="user-profile-badge" style="text-decoration: none;">
                    <div class="avatar-img">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-greeting">Logged in as:</span>
                        <span class="user-name">
                            {{ Auth::user()->name ?? 'Bucky Barnes' }}
                            <small class="pill-role-badge">Faculty</small>
                        </span>
                    </div>
                    <i class="fa-solid fa-chevron-down profile-settings-btn"></i>
                </a>

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
            <!-- Faculty Sidebar Navigation -->
            <aside class="sidebar">
                <a href="{{ route('faculty.dashboard') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.dashboard') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-border-all"></i> FACULTY HUB
                </a>
                
                <a href="{{ route('faculty.profile-settings') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.profile-settings') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-gear"></i> PROFILE & SETTINGS
                </a>

                <a href="{{ route('faculty.my-schedule') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.my-schedule') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-days"></i> MY SCHEDULE
                </a>

                <a href="{{ route('faculty.consultation-requests') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.consultation-requests') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-user-plus"></i> CONSULTATION REQUESTS
                </a>
                
                <a href="{{ route('faculty.student-management') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.student-management') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-users"></i> STUDENT MANAGEMENT
                </a>
                
                <a href="{{ route('faculty.announcements') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.announcements') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS
                </a>
                
                <a href="{{ route('faculty.support') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.support') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT<br><small style="font-size: 0.7rem; font-weight: 500;">Submit Course Issues</small>
                </a>
                
            
            </aside>   

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">PROFILE & SETTINGS</h2>

                <div class="dash-card support-card-glass" style="max-width: 650px; margin: 0 auto; width: 100%;">
                    <h3 class="card-title-support" style="margin-bottom: 20px;">Faculty Account Information</h3>

                    @if(session('success'))
                        <div style="background: rgba(42, 226, 105, 0.25); border: 1px solid #2ae269; color: #ffffff; padding: 10px 15px; border-radius: 10px; margin-bottom: 15px; font-weight: 600;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('faculty.profile.update') }}" method="POST" class="support-form-custom">
                        @csrf
                        
                        <div class="support-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="support-dark-select" value="{{ Auth::user()->name ?? 'Bucky Barnes' }}" required style="padding: 10px 14px;">
                        </div>

                        <div class="support-group">
                            <label>Department</label>
                            <input type="text" name="department" class="support-dark-select" value="{{ Auth::user()->department ?? 'College of Computer and Information Sciences' }}" style="padding: 10px 14px;">
                        </div>

                        <div class="support-group">
                            <label>Designation / Academic Position</label>
                            <input type="text" name="designation" class="support-dark-select" value="{{ Auth::user()->designation ?? 'Assistant Professor I' }}" style="padding: 10px 14px;">
                        </div>

                        <div class="support-group">
                            <label>Email Address (Institutional)</label>
                            <input type="email" class="support-dark-select" value="{{ Auth::user()->email ?? 'bucky@ssct.edu.ph' }}" disabled style="opacity: 0.7; cursor: not-allowed; padding: 10px 14px;">
                        </div>

                        <div class="support-group">
                            <label>New Password (Leave blank to keep current password)</label>
                            <input type="password" name="password" class="support-dark-select" placeholder="••••••••" style="padding: 10px 14px;">
                        </div>

                        <div style="margin-top: 10px;">
                            <button type="submit" class="btn-green-submit">Save Profile Changes</button>
                        </div>
                    </form>
                </div>

                <div class="footer-nav-link" style="margin-top: 15px;">
                    <a href="{{ route('welcome') }}"><u>Back to Main Site</u></a>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection