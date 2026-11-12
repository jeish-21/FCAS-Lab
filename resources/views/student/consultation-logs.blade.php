@extends('layouts.app')

@section('title', 'Consultation Logs - SNSU Student')

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

            <!-- Dynamic Profile Badge -->
            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">
                        {{ Auth::user()->name ?? 'Christian Luib' }}
                        <small style="color: #2ae269; font-weight: 800;">
                            ({{ Auth::user()->student_id ?? '22-0123' }})
                        </small>
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
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.my-appointments*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-check"></i> MY APPOINTMENTS
                </a>
                <a href="{{ route('student.consultation-logs') }}" class="sidebar-link active" data-active="true">
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
                <h2 class="dash-title">CONSULTATION LOGS</h2>

                <div class="logs-container">
                    <!-- Filter and Search Bar -->
                    <div class="dash-card support-card-glass logs-filter-bar-custom">
                        <div class="logs-search-wrapper">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="logSearchInput" onkeyup="filterLogsTable()" placeholder="Search by faculty, reason, or status...">
                        </div>

                        <select id="logStatusSelect" class="support-dark-select logs-status-select" onchange="filterLogsTable()">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="approved">Approved / Confirmed</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Canceled / Rejected</option>
                        </select>

                        <!-- Dynamic Date Picker Input & Clear Date Button -->
                        <div class="logs-date-wrapper" style="display: flex; align-items: center; gap: 8px;">
                            <input type="date" id="logDateFilter" class="support-dark-select" onchange="filterLogsTable()">
                            <button type="button" onclick="clearDateFilter()" title="Show All Dates" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); border-radius: 10px; color: #fff; padding: 10px 12px; cursor: pointer; font-size: 0.8rem; white-space: nowrap;">
                                <i class="fa-solid fa-calendar-xmark"></i> Clear Date
                            </button>
                        </div>
                    </div>

                    <!-- Consultation Logs Table -->
                    <div class="dash-card support-card-glass">
                        <div class="logs-table-custom">
                            <div class="logs-table-header-custom">
                                <div>Date</div>
                                <div>Time</div>
                                <div>Faculty Member</div>
                                <div>Status</div>
                                <div>Action / Summary</div>
                            </div>

                            <div class="logs-table-body-custom" id="logsTableBody">
                                @forelse($logs as $log)
                                    <div class="logs-row-item" data-status="{{ strtolower($log->status) }}" data-date="{{ \Carbon\Carbon::parse($log->appointment_date)->format('Y-m-d') }}">
                                        <div class="col-date">
                                            {{ \Carbon\Carbon::parse($log->appointment_date)->format('d M Y') }}
                                        </div>
                                        <div class="col-time">
                                            {{ $log->time_slot ?? '08:30 AM' }}
                                        </div>
                                        <div class="col-faculty">
                                            <div class="faculty-avatar-circle" style="display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1a1a1a;">
                                                {{ strtoupper(substr($log->faculty->name ?? 'F', 0, 1)) }}
                                            </div>
                                            <div class="faculty-meta">
                                                <strong>{{ $log->faculty->name ?? 'Dr. Mark Davis' }}</strong>
                                                <span>{{ $log->reason ?? 'Academic Advising' }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            @if($log->status === 'completed')
                                                <span class="pill-badge pill-completed"><i class="fa-solid fa-circle-check"></i> Completed</span>
                                            @elseif($log->status === 'approved' || $log->status === 'confirmed')
                                                <span class="pill-badge" style="background: #2563eb;"><i class="fa-solid fa-calendar-check"></i> Confirmed</span>
                                            @elseif($log->status === 'pending')
                                                <span class="pill-badge" style="background: #eab308; color: #1a1a1a;"><i class="fa-solid fa-clock"></i> Pending</span>
                                            @else
                                                <span class="pill-badge pill-canceled-student"><i class="fa-solid fa-circle-xmark"></i> {{ ucfirst($log->status) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" class="btn-pdf-glass" onclick="downloadSummary('{{ $log->id }}')">
                                                <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>
                                                <div class="pdf-text">
                                                    <span>Download Log</span>
                                                    <small>PDF Summary</small>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <!-- Fallback Sample Dynamic Rows when database is empty -->
                                    <div class="logs-row-item" data-status="pending" data-date="2026-10-20">
                                        <div class="col-date">20 Oct 2026</div>
                                        <div class="col-time">08:30 AM</div>
                                        <div class="col-faculty">
                                            <div class="faculty-avatar-circle" style="display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1a1a1a;">L</div>
                                            <div class="faculty-meta">
                                                <strong>Leah Latoza</strong>
                                                <span>Grades Consultation</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="pill-badge" style="background: #eab308; color: #1a1a1a;"><i class="fa-solid fa-clock"></i> Pending</span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn-pdf-glass" onclick="downloadSummary('SAMPLE-01')">
                                                <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>
                                                <div class="pdf-text">
                                                    <span>Download Log</span>
                                                    <small>PDF Summary</small>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

             
            </main>
        </div>
    </div>
</div>

<!-- Dynamic Search, Date Persistence & Filtering Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('logSearchInput');
        const statusSelect = document.getElementById('logStatusSelect');
        const dateInput = document.getElementById('logDateFilter');

        // Restore saved filter selections from localStorage upon page load/reload
        if (localStorage.getItem('snsu_logs_search') !== null) {
            searchInput.value = localStorage.getItem('snsu_logs_search');
        }
        if (localStorage.getItem('snsu_logs_status') !== null) {
            statusSelect.value = localStorage.getItem('snsu_logs_status');
        }
        if (localStorage.getItem('snsu_logs_date') !== null) {
            dateInput.value = localStorage.getItem('snsu_logs_date');
        }

        // Apply filters initial state
        filterLogsTable();
    });

    function filterLogsTable() {
        let searchInput = document.getElementById('logSearchInput');
        let statusSelect = document.getElementById('logStatusSelect');
        let dateInput = document.getElementById('logDateFilter');

        let searchVal = searchInput.value.toLowerCase().trim();
        let statusVal = statusSelect.value.toLowerCase().trim();
        let dateVal = dateInput.value;

        // Save current filter values to localStorage
        localStorage.setItem('snsu_logs_search', searchInput.value);
        localStorage.setItem('snsu_logs_status', statusSelect.value);
        localStorage.setItem('snsu_logs_date', dateInput.value);

        let rows = document.querySelectorAll('#logsTableBody .logs-row-item');

        rows.forEach(function(row) {
            let rowText = row.innerText.toLowerCase();
            let rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
            let rowDate = row.getAttribute('data-date') || '';

            let matchesSearch = (searchVal === '') || rowText.includes(searchVal);
            let matchesStatus = (statusVal === '') || (rowStatus === statusVal);
            let matchesDate = (dateVal === '') || (rowDate === dateVal);

            if (matchesSearch && matchesStatus && matchesDate) {
                row.style.setProperty('display', 'grid', 'important');
            } else {
                row.style.setProperty('display', 'none', 'important');
            }
        });
    }

    function clearDateFilter() {
        let dateInput = document.getElementById('logDateFilter');
        dateInput.value = '';
        localStorage.removeItem('snsu_logs_date');
        filterLogsTable();
    }

    function downloadSummary(logId) {
        alert('Generating PDF summary download for Consultation Log ID: #' + logId);
    }
</script>
@endsection