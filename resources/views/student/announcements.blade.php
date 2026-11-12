@extends('layouts.app')

@section('title', 'Announcements - SNSU Appointment System')

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
                        {{ Auth::user()->name ?? 'Christine Joy Tuyor' }}
                        @if(Auth::user() && Auth::user()->student_id)
                            ({{ Auth::user()->student_id }})
                        @else
                            (2022-04356)
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
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.my-appointments*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-check"></i> MY APPOINTMENTS
                </a>
                <a href="{{ route('student.consultation-logs') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.consultation-logs*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-file-signature"></i> CONSULTATION LOGS
                </a>
                <a href="{{ route('student.announcements') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS
                </a>
                <a href="{{ route('student.support') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.support*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT
                </a>
            </aside>

            <!-- Main Content Workspace -->
            <main class="dashboard-content">
                <h2 class="dash-title">ANNOUNCEMENTS</h2>

                <div class="dash-card support-card-glass" style="padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 class="card-title-support" style="margin: 0; font-size: 1.4rem;">Faculty Announcements</h3>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div style="display: grid; grid-template-columns: 2fr 1.2fr 1.2fr auto; gap: 12px; margin-bottom: 25px; align-items: center;">
                        <div style="display: flex; align-items: center; background: rgba(255,255,255,0.15); padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fa-solid fa-magnifying-glass" style="color: #bbb; margin-right: 10px;"></i>
                            <input type="text" id="announcementSearch" onkeyup="filterAnnouncements()" placeholder="Search Announcements..." style="background: transparent; border: none; outline: none; color: white; width: 100%; font-size: 0.88rem;">
                        </div>

                        <select id="categoryFilter" onchange="filterAnnouncements()" class="support-dark-select" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: white; border-radius: 12px; padding: 10px; font-size: 0.88rem;">
                            <option value="" style="background: #1e301e; color: #fff;">All Categories</option>
                            <option value="General" style="background: #1e301e; color: #fff;">General</option>
                            <option value="Advising" style="background: #1e301e; color: #fff;">Advising</option>
                            <option value="Exams" style="background: #1e301e; color: #fff;">Exams</option>
                        </select>

                        <input type="date" id="dateFilter" onchange="filterAnnouncements()" class="support-dark-select" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: white; border-radius: 12px; padding: 10px; font-size: 0.88rem;">

                        <button type="button" onclick="clearDateFilter()" title="Clear Date Filter" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); border-radius: 12px; color: #fff; padding: 10px 14px; cursor: pointer; font-size: 0.82rem; font-weight: 600; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-calendar-xmark"></i> Clear Date
                        </button>
                    </div>

                    <!-- Dynamic Announcements Feed Container -->
                    <div id="announcementList" style="display: grid; grid-template-columns: 1fr; gap: 18px;">
                        @forelse($announcements as $ann)
                            @php
                                $authorName = 'Faculty Member';
                                if (isset($ann->author) && !empty($ann->author)) {
                                    $authorName = $ann->author;
                                } elseif (isset($ann->faculty) && isset($ann->faculty->name)) {
                                    $authorName = $ann->faculty->name;
                                }
                            @endphp
                            <div class="announcement-item-card" data-category="{{ $ann->category ?? 'General' }}" data-date="{{ \Carbon\Carbon::parse($ann->created_at ?? now())->format('Y-m-d') }}" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 20px; transition: all 0.3s ease;">
                                <div style="display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="background: #2ae269; color: #1a1a1a; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                            <h4 style="color: white; font-size: 1.15rem; font-weight: 700; margin: 0;">{{ $ann->title ?? 'Faculty Announcement' }}</h4>
                                            <span style="color: #2ae269; font-size: 0.82rem; font-weight: 700; background: rgba(42, 226, 105, 0.15); padding: 4px 10px; border-radius: 20px;">
                                                <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($ann->created_at ?? now())->format('M d, Y • h:i A') }}
                                            </span>
                                        </div>
                                        <p style="color: #e0e0e0; font-size: 0.95rem; line-height: 1.5; margin-bottom: 10px;">
                                            {{ $ann->content ?? '' }}
                                        </p>
                                        <small style="color: #a0a0a0; font-weight: 600;">
                                            Posted by: <strong style="color: #fff;">{{ $authorName }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="announcement-item-card" data-category="General" data-date="2026-10-20" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 20px;">
                                <div style="display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="background: #2ae269; color: #1a1a1a; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                            <h4 style="color: white; font-size: 1.15rem; font-weight: 700; margin: 0;">Faculty Announcement</h4>
                                            <span style="color: #2ae269; font-size: 0.82rem; font-weight: 700; background: rgba(42, 226, 105, 0.15); padding: 4px 10px; border-radius: 20px;">
                                                <i class="fa-regular fa-clock"></i> Oct 20, 2026
                                            </span>
                                        </div>
                                        <p style="color: #e0e0e0; font-size: 0.95rem; line-height: 1.5; margin-bottom: 10px;">
                                            Dili ko mu entertain ron kay busy sa accreditation.
                                        </p>
                                        <small style="color: #a0a0a0; font-weight: 600;">
                                            Posted by: <strong style="color: #fff;">Bucky Barnes</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Persistence & Real-time Filter Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('announcementSearch');
    const categorySelect = document.getElementById('categoryFilter');
    const dateInput = document.getElementById('dateFilter');

    // Restore saved selections from localStorage
    if (localStorage.getItem('snsu_ann_search') !== null) {
        searchInput.value = localStorage.getItem('snsu_ann_search');
    }
    if (localStorage.getItem('snsu_ann_category') !== null) {
        categorySelect.value = localStorage.getItem('snsu_ann_category');
    }
    if (localStorage.getItem('snsu_ann_date') !== null) {
        dateInput.value = localStorage.getItem('snsu_ann_date');
    }

    filterAnnouncements();
});

function filterAnnouncements() {
    let searchInput = document.getElementById('announcementSearch');
    let categorySelect = document.getElementById('categoryFilter');
    let dateInput = document.getElementById('dateFilter');

    let searchVal = searchInput.value.toLowerCase().trim();
    let categoryVal = categorySelect.value.toLowerCase().trim();
    let dateVal = dateInput.value;

    // Save to localStorage for persistence across reloads
    localStorage.setItem('snsu_ann_search', searchInput.value);
    localStorage.setItem('snsu_ann_category', categorySelect.value);
    localStorage.setItem('snsu_ann_date', dateInput.value);

    let cards = document.querySelectorAll('.announcement-item-card');

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        let cardCategory = (card.getAttribute('data-category') || '').toLowerCase();
        let cardDate = card.getAttribute('data-date') || '';

        let matchesSearch = (searchVal === '') || text.includes(searchVal);
        let matchesCategory = (categoryVal === '') || cardCategory.includes(categoryVal) || text.includes(categoryVal);
        let matchesDate = (dateVal === '') || (cardDate === dateVal);

        if (matchesSearch && matchesCategory && matchesDate) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function clearDateFilter() {
    let dateInput = document.getElementById('dateFilter');
    dateInput.value = '';
    localStorage.removeItem('snsu_ann_date');
    filterAnnouncements();
}
</script>
@endsection