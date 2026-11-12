@extends('layouts.app')

@section('title', 'Faculty Availability - SNSU Student')

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
                        {{ Auth::user()->name ?? 'Student' }}
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
                <a href="{{ route('student.faculty-availability') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-solid fa-user-clock"></i> FACULTY AVAILABILITY
                </a>
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.my-appointments*') ? 'true' : 'false' }}">
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
                <h2 class="dash-title">FACULTY AVAILABILITY & SLOTS</h2>

                <div class="dash-card support-card-glass" style="padding: 24px;">
                    <!-- Filter and Search Controls -->
                    <form method="GET" action="{{ route('student.faculty-availability') }}" id="availFilterForm" style="display: grid; grid-template-columns: 2fr 1.2fr 1.2fr; gap: 12px; margin-bottom: 25px; align-items: center;">
                        <div style="display: flex; align-items: center; background: rgba(255,255,255,0.15); padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fa-solid fa-magnifying-glass" style="color: #bbb; margin-right: 10px;"></i>
                            <input type="text" id="facultyAvailSearch" onkeyup="filterAvailGrid()" placeholder="Search faculty name or department..." style="background: transparent; border: none; outline: none; color: white; width: 100%; font-size: 0.88rem;">
                        </div>

                        <select id="departmentFilter" onchange="filterAvailGrid()" class="support-dark-select" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: white; border-radius: 12px; padding: 10px; font-size: 0.88rem;">
                            <option value="" style="background: #1e301e; color: #fff;">All Departments</option>
                            <option value="CEIT" style="background: #1e301e; color: #fff;">CEIT</option>
                            <option value="COT" style="background: #1e301e; color: #fff;">COT</option>
                            <option value="CTE" style="background: #1e301e; color: #fff;">CTE</option>
                        </select>

                        <input type="date" name="date" id="availDateFilter" onchange="document.getElementById('availFilterForm').submit()" class="support-dark-select" value="{{ $selectedDate ?? date('Y-m-d') }}" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: white; border-radius: 12px; padding: 10px; font-size: 0.88rem;">
                    </form>

                    <!-- Faculty Availability Cards Grid -->
                    <div id="facultyGridContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                        @forelse($faculties as $faculty)
                            @php
                                $todayCount = $faculty->today_count ?? 0;
                                $maxSlots = $faculty->max_slots ?? 5;
                                $remaining = max(0, $maxSlots - $todayCount);
                                $isFull = $todayCount >= $maxSlots;
                            @endphp

                            <div class="faculty-card-item" data-dept="{{ $faculty->department ?? 'CEIT' }}" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <h4 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">{{ $faculty->name }}</h4>
                                        @if($isFull)
                                            <span style="background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.5); color: #ff8888; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 12px;">
                                                <i class="fa-solid fa-circle-xmark"></i> Fully Booked
                                            </span>
                                        @else
                                            <span style="background: rgba(42, 226, 105, 0.2); border: 1px solid rgba(42, 226, 105, 0.4); color: #2ae269; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 12px;">
                                                <i class="fa-solid fa-circle-check"></i> Available
                                            </span>
                                        @endif
                                    </div>
                                    <p style="color: #d0d0d0; font-size: 0.82rem; margin: 0 0 10px 0;">{{ $faculty->department ?? 'College of Computer Studies' }}</p>
                                    
                                    <div style="background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px; color: #fff; font-size: 0.82rem;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span>Slots Used Today:</span>
                                            <strong>{{ $todayCount }} / {{ $maxSlots }}</strong>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>Remaining Vacuum:</span>
                                            <strong style="color: {{ $isFull ? '#ff6b6b' : '#a4f238' }};">{{ $remaining }} Slots Left</strong>
                                        </div>
                                    </div>
                                </div>

                                @if($isFull)
                                    <button disabled style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 10px; color: #888; font-weight: 700; cursor: not-allowed; font-size: 0.88rem;">
                                        Unavailable for Today
                                    </button>
                                @else
                                    <a href="{{ route('student.book-appointment') }}?faculty_id={{ $faculty->id }}" style="width: 100%; background: linear-gradient(180deg, #2ae269 0%, #1c9d46 100%); border: 1px solid rgba(255,255,255,0.6); border-radius: 10px; padding: 10px; color: #ffffff; font-weight: 800; text-align: center; text-decoration: none; font-size: 0.88rem; box-shadow: 0 4px 12px rgba(42, 226, 105, 0.3);">
                                        Book Consultation
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div style="color: #ccc; grid-column: 1 / -1; text-align: center; padding: 30px;">
                                No faculty members found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
function filterAvailGrid() {
    let searchVal = document.getElementById('facultyAvailSearch').value.toLowerCase().trim();
    let deptVal = document.getElementById('departmentFilter').value.toLowerCase().trim();
    let cards = document.querySelectorAll('.faculty-card-item');

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        let dept = (card.getAttribute('data-dept') || '').toLowerCase();

        let matchesSearch = (searchVal === '') || text.includes(searchVal);
        let matchesDept = (deptVal === '') || dept.includes(deptVal) || text.includes(deptVal);

        if (matchesSearch && matchesDept) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection