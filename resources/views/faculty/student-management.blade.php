@extends('layouts.app')

@section('title', 'Student Management - SNSU Faculty System')

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
                <a href="{{ route('faculty.student-management') }}" class="sidebar-link active" data-active="true">
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
                <h2 class="dash-title">STUDENT MANAGEMENT</h2>

                <div class="dash-card support-card-glass" style="padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h3 style="color: white; margin: 0; font-size: 1.2rem;">Student Directory & Advisees</h3>
                            <p style="color: #aaa; margin: 4px 0 0 0; font-size: 0.85rem;">View and manage enrolled students under your consultations and courses</p>
                        </div>

                        <!-- Real Search Form -->
                        <form action="{{ route('faculty.student-management') }}" method="GET" style="display: flex; gap: 8px;">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search student name or ID..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; color: white; padding: 8px 16px; font-size: 0.85rem; width: 240px;">
                            <button type="submit" style="background: #2ae269; color: #1a1a1a; border: none; border-radius: 20px; padding: 8px 16px; font-weight: 700; cursor: pointer; font-size: 0.85rem;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Live Student Table -->
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                            <thead>
                                <tr style="background: rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.15);">
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">STUDENT ID</th>
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">FULL NAME</th>
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">COURSE & DEPARTMENT</th>
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">TOTAL CONSULTATIONS</th>
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">STATUS</th>
                                    <th style="padding: 12px 16px; font-size: 0.85rem; color: #2ae269;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($students) && count($students) > 0)
                                    @foreach($students as $student)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 14px 16px; font-weight: bold; color: #2ae269; font-size: 0.9rem;">
                                                {{ $student->student_id ?? 'N/A' }}
                                            </td>
                                            <td style="padding: 14px 16px; font-size: 0.9rem;">
                                                {{ $student->name }}
                                            </td>
                                            <td style="padding: 14px 16px; color: #ccc; font-size: 0.85rem;">
                                                {{ $student->course ?? 'BSIT' }} @if($student->department) - {{ $student->department }} @endif
                                            </td>
                                            <td style="padding: 14px 16px; font-size: 0.85rem; color: #aaa;">
                                                {{ $student->total_consultations ?? 0 }} {{ Str::plural('Session', $student->total_consultations ?? 0) }}
                                            </td>
                                            <td style="padding: 14px 16px;">
                                                @if(($student->total_consultations ?? 0) > 0)
                                                    <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Active Advisee</span>
                                                @else
                                                    <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">Enrolled Student</span>
                                                @endif
                                            </td>
                                            <td style="padding: 14px 16px;">
                                                <a href="{{ route('faculty.consultation-requests') }}" style="background: #2ae269; color: #1a1a1a; font-weight: 700; text-decoration: none; padding: 6px 14px; border-radius: 12px; font-size: 0.8rem; display: inline-block;">
                                                    View History
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 30px; color: #aaa;">
                                            <i class="fa-solid fa-users-slash" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                            No student records found in database.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                
            </main>
        </div>
    </div>
</div>
@endsection