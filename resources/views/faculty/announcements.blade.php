@extends('layouts.app')

@section('title', 'Faculty Announcements - SNSU Appointment System')

@section('content')
<div class="app-container">
    {{-- Top Navbar --}}
    <div class="top-navbar">
        <div class="brand-section">
            <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="brand-logo">
            <h2 class="brand-title">SNSU Faculty Consultation & Appointment System</h2>
        </div>

        <div class="user-profile-badge">
            <div class="avatar-img">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div class="user-details">
                <span class="user-greeting">Logged in as:</span>
                <span class="user-name">{{ Auth::user()->name }} (Faculty)</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 10px;">
                @csrf
                <button type="submit" class="btn-create-announcement" style="background: rgba(220, 53, 69, 0.4); border-color: rgba(220, 53, 69, 0.6);">Logout</button>
            </form>
        </div>
    </div>

    {{-- Main Body Layout --}}
    <div class="dashboard-body">
        {{-- Sidebar --}}
        <div class="sidebar">
            <a href="{{ route('faculty.dashboard') }}" class="sidebar-link"><i class="fa-solid fa-gauge"></i> FACULTY HUB</a>
            <a href="{{ route('faculty.profile-settings') }}" class="sidebar-link"><i class="fa-solid fa-user-gear"></i> PROFILE & SETTINGS</a>
            <a href="{{ route('faculty.my-schedule') }}" class="sidebar-link"><i class="fa-solid fa-calendar-days"></i> MY SCHEDULE</a>
            <a href="{{ route('faculty.consultation-requests') }}" class="sidebar-link"><i class="fa-solid fa-comments"></i> CONSULTATION REQUESTS</a>
            <a href="{{ route('faculty.student-management') }}" class="sidebar-link"><i class="fa-solid fa-users"></i> STUDENT MANAGEMENT</a>
            <a href="{{ route('faculty.announcements') }}" class="sidebar-link active"><i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS</a>
            <a href="{{ route('faculty.support') }}" class="sidebar-link"><i class="fa-solid fa-circle-question"></i> SUPPORT</a>
        </div>

        {{-- Main Dashboard Content --}}
        <div class="dashboard-content">
            <h1 class="dash-title">ANNOUNCEMENTS</h1>

            @if(session('success'))
                <div class="dash-card" style="border-color: #2ae269; background: rgba(42, 226, 105, 0.2); color: #ffffff; margin-bottom: 15px;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 6px; color: #a4f238;"></i> {{ session('success') }}
                </div>
            @endif

            <div class="quick-announcements-cols">
                
                {{-- Left Side: Post New Advisory --}}
                <div class="dash-card support-card-glass">
                    <h3 class="card-title-support">Post New Advisory</h3>
                    <p class="card-subtitle-support" style="font-size: 0.8rem; font-weight: 500;">Broadcast announcements to all enrolled students and advisees</p>

                    <form action="{{ route('faculty.announcements.store') }}" method="POST" class="support-form-custom">
                        @csrf
                        <div class="support-group">
                            <label>Announcement Title</label>
                            <div class="input-box">
                                <input type="text" name="title" placeholder="e.g. Rescheduled Consultation Hours" required>
                            </div>
                        </div>

                        <div class="support-group">
                            <label>Target Audience</label>
                            <div class="support-select-wrapper">
                                <select name="target_audience" class="support-dark-select" required>
                                    <option value="All Students & Advisees">All Students & Advisees</option>
                                    <option value="BSIT - 3A">BSIT - 3A</option>
                                    <option value="BSIT - 3B">BSIT - 3B</option>
                                    <option value="Advisees Only">Advisees Only</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="support-group">
                            <label>Announcement Content</label>
                            <textarea name="content" class="support-dark-textarea" placeholder="Write full details here..." required></textarea>
                        </div>

                        <button type="submit" class="btn-green-submit">
                            Publish Announcement
                        </button>
                    </form>
                </div>

                {{-- Right Side: Recent Posted Advisories --}}
                <div class="dash-card support-card-glass">
                    <h3 class="card-title-support">Recent Posted Advisories</h3>
                    <p class="card-subtitle-support" style="font-size: 0.8rem; font-weight: 500;">Manage and view previously broadcasted announcements</p>

                    <div class="appt-cards-list" style="margin-top: 10px;">
                        @forelse($announcements as $announcement)
                            <div class="quick-announce-item" style="position: relative;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                                    <strong style="color: #ffffff; font-size: 0.95rem;">{{ $announcement->title }}</strong>
                                    <small style="color: #a4f238; font-size: 0.75rem; font-weight: 700;">{{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') }}</small>
                                </div>
                                
                                <span class="badge-status badge-pending" style="font-size: 0.68rem; padding: 2px 8px; margin-bottom: 8px;">
                                    Audience: {{ $announcement->target_audience }}
                                </span>

                                <p style="color: #e0e0e0; font-size: 0.82rem; line-height: 1.4; margin-top: 6px;">{{ $announcement->content }}</p>

                                <div style="text-align: right; margin-top: 10px;">
                                    <form action="{{ route('faculty.announcements.delete', $announcement->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this announcement?')" class="btn-create-announcement" style="background: rgba(220, 53, 69, 0.6); border-color: rgba(220, 53, 69, 0.8); font-size: 0.72rem; padding: 4px 10px;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 30px 10px; color: #d0d0d0;">
                                <i class="fa-solid fa-bullhorn" style="font-size: 2rem; margin-bottom: 8px; display: block; color: #a4f238;"></i>
                                <p style="font-size: 0.85rem; margin: 0;">No announcements posted yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection