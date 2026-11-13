@extends('layouts.app') 

@section('title', 'Student Dashboard - SNSU Appointment System') 

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
                        {{ Auth::user()->name ?? 'Christine Joy Tuyor' }} 
                        <small style="color: #2ae269; font-weight: 800;">
                            ({{ Auth::user()->student_id ?? '2022-04356' }})
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
                <a href="{{ route('student.dashboard') }}" class="sidebar-link active" data-active="true"> 
                    <i class="fa-solid fa-border-all"></i> DASHBOARD 
                </a> 
                <a href="{{ route('student.book-appointment') }}" class="sidebar-link"> 
                    <i class="fa-regular fa-calendar-plus"></i> BOOK AN APPOINTMENT 
                </a> 
                <a href="{{ route('student.faculty-availability') }}" class="sidebar-link"> 
                    <i class="fa-solid fa-user-clock"></i> FACULTY AVAILABILITY 
                </a>
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link"> 
                    <i class="fa-regular fa-calendar-check"></i> MY APPOINTMENTS 
                </a> 
                <a href="{{ route('student.consultation-logs') }}" class="sidebar-link"> 
                    <i class="fa-solid fa-file-signature"></i> CONSULTATION LOGS 
                </a> 
                <a href="{{ route('student.announcements') }}" class="sidebar-link"> 
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS 
                </a> 
                <a href="{{ route('student.support') }}" class="sidebar-link"> 
                    <i class="fa-regular fa-circle-question"></i> SUPPORT 
                </a> 
            </aside> 

            <!-- Main Content Workspace --> 
            <main class="dashboard-content"> 
                <h2 class="dash-title">STUDENT DASHBOARD</h2> 
                @if(Auth::user()->status === 'rejected')
                    <div role="alert" class="dash-card support-card-glass" style="padding: 14px 18px; margin-bottom: 16px; border: 1px solid #ef4444; color: #fecaca;">
                        Your registration was rejected. Appointment booking is disabled; review your in-app notification or contact the System Admin.
                    </div>
                @elseif(Auth::user()->status !== 'approved')
                    <div role="status" class="dash-card support-card-glass" style="padding: 14px 18px; margin-bottom: 16px; border: 1px solid #eab308; color: #fde68a;">
                        Your registration is pending COR verification. Appointment booking will be enabled after approval.
                    </div>
                @endif

                <section class="dash-card support-card-glass" aria-label="In-app notifications" style="padding: 16px 20px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                        <h3 class="card-title-support" style="margin: 0;">
                            <i class="fa-solid fa-bell"></i> Notifications
                            @if(($unreadNotificationCount ?? 0) > 0)
                                <span style="background: #ef4444; color: white; border-radius: 12px; padding: 2px 8px; font-size: 0.75rem;">{{ $unreadNotificationCount }} new</span>
                            @endif
                        </h3>
                    </div>
                    <div style="margin-top: 10px;">
                        @forelse($notifications as $notification)
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; border-top: 1px solid rgba(255,255,255,0.12); padding: 10px 0; color: {{ $notification->read_at ? '#d0d0d0' : 'white' }};">
                                <div>
                                    <strong>{{ $notification->data['status'] ?? 'Update' }}</strong>
                                    <span>{{ $notification->data['message'] ?? 'Your account has an update.' }}</span>
                                    <small style="display: block; color: #aaa;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if(!$notification->read_at)
                                    <form action="{{ route('student.notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: transparent; border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 8px; padding: 6px 9px; cursor: pointer;">Mark read</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p style="color: #bbb; margin: 10px 0 0;">No notifications yet.</p>
                        @endforelse
                    </div>
                </section>

                <!-- Top Row Grid -->
                <div class="dash-main-top-grid">
                    
                    <!-- Upcoming Appointment Card -->
                    <div class="dash-card support-card-glass dash-upcoming-card">
                        <h3 class="card-title-support">Upcoming Appointments</h3>

                        @if(isset($upcomingAppointment) && $upcomingAppointment)
                            <div class="upcoming-info-box-custom">
                                <span class="badge-snsu-tag">SNSU</span>
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($upcomingAppointment->appointment_date)->format('d F Y') }}</p>
                                <p><strong>Time:</strong> {{ $upcomingAppointment->time_slot ?? '08:30 AM' }}</p>
                                <p><strong>Faculty Member:</strong> {{ $upcomingAppointment->faculty->name ?? 'Faculty Member' }}</p>
                            </div>

                            <a href="https://meet.google.com" target="_blank" class="btn-join-virtual-custom">
                                <i class="fa-solid fa-video"></i> Join Virtual Room
                            </a>

                            <a href="{{ route('student.my-appointments') }}" class="btn-view-details-glow">
                                View Details <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
                            </a>
                        @else
                            <div style="text-align: center; color: #d0d0d0; padding: 20px 10px;">
                                <i class="fa-regular fa-calendar-xmark" style="font-size: 2rem; margin-bottom: 8px; display: block; color: #a4f238;"></i>
                                <p style="margin: 0; font-size: 0.85rem;">No upcoming appointments found.</p>
                            </div>
                            <a href="{{ route('student.book-appointment') }}" class="btn-join-virtual-custom">
                                <i class="fa-regular fa-calendar-plus"></i> Book Consultation
                            </a>
                        @endif
                    </div>

                    <!-- Faculty Availability Card with Dynamic Slots & Fully Booked States -->
                    <div class="dash-card support-card-glass dash-availability-card">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="card-title-support" style="margin: 0;">Faculty Availability</h3>
                            <a href="{{ route('student.faculty-availability') }}" style="color: #a4f238; font-size: 0.82rem; font-weight: 700; text-decoration: underline;">View All</a>
                        </div>

                        <div class="dash-search-bar-white">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="facultySearchInput" oninput="filterFacultyList()" placeholder="Search faculty name or department...">
                        </div>

                        <div class="faculty-avail-rows" id="facultyListContainer">
                            @forelse($faculties as $faculty)
                                @php
                                    $todayCount = $faculty->today_count ?? 0;
                                    $maxSlots = $faculty->max_slots ?? 5;
                                    $remaining = max(0, $maxSlots - $todayCount);
                                    $isFull = $todayCount >= $maxSlots;
                                @endphp

                                @if($isFull)
                                    <!-- FULLY BOOKED ROW -->
                                    <div class="faculty-avail-item-row" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); cursor: not-allowed; opacity: 0.8;">
                                        <div class="faculty-info-box">
                                            <strong>{{ $faculty->name }}</strong>
                                            <small>{{ $faculty->department ?? 'College of Computer Studies' }}</small>
                                        </div>
                                        <div class="status-avail-box" style="background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.5); color: #ff8888;">
                                            <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; display: inline-block;"></span> Fully Booked
                                        </div>
                                        <div class="summary-avail-box">
                                            <strong style="color: #ff6b6b !important;">0 Slots Left</strong>
                                            <small style="color: #ffa8a8 !important;">({{ $todayCount }}/{{ $maxSlots }} Full)</small>
                                        </div>
                                    </div>
                                @else
                                    <!-- AVAILABLE ROW -->
                                    <a href="{{ route('student.book-appointment') }}?faculty_id={{ $faculty->id }}" class="faculty-avail-item-row">
                                        <div class="faculty-info-box">
                                            <strong>{{ $faculty->name }}</strong>
                                            <small>{{ $faculty->department ?? 'College of Computer Studies' }}</small>
                                        </div>
                                        <div class="status-avail-box">
                                            <span class="dot-green-pulse"></span> Available
                                        </div>
                                        <div class="summary-avail-box">
                                            <strong>{{ $remaining }} Slots Left</strong>
                                            <small>Available Today</small>
                                        </div>
                                    </a>
                                @endif
                            @empty
                                <div style="text-align: center; color: #aaa; padding: 20px;">No faculty members registered.</div>
                            @endforelse

                            <div id="noFacultyMatch" style="display: none; text-align: center; color: #e0e0e0; padding: 20px; font-size: 0.85rem;">
                                <i class="fa-solid fa-user-slash" style="font-size: 1.5rem; margin-bottom: 6px; display: block; color: #2ae269;"></i>
                                No matching faculty member found.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row Grid -->
                <div class="dash-main-bottom-grid">
                    
                    <!-- Consultation Request Status Card -->
                    <a href="{{ route('student.my-appointments') }}" class="dash-card support-card-glass dash-status-card">
                        <h3 class="card-title-support">Consultation Request Status</h3>

                        <div class="status-progress-meta">
                            <span>Pending Requests Active</span>
                            <strong>{{ $pendingCount ?? 0 }}</strong>
                        </div>

                        <div class="dash-progress-track">
                            <div class="dash-progress-fill" style="width: {{ min(($pendingCount ?? 0) * 10, 100) }}%;"></div>
                        </div>

                        <div class="status-progress-meta">
                            <span style="color: #d0d0d0; font-size: 0.78rem;">Limit Capacity</span>
                            <strong style="color: #a4f238;">{{ $pendingCount ?? 0 }} / 10 Slots Used</strong>
                        </div>
                    </a>

                    <!-- Quick Announcements Card -->
                    <div class="dash-card support-card-glass dash-announcements-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <h3 class="card-title-support" style="margin: 0;">Quick Announcements</h3>
                            <a href="{{ route('student.announcements') }}" style="color: #a4f238; font-size: 0.82rem; font-weight: 700; text-decoration: underline;">View All</a>
                        </div>
                        
                        <div class="quick-announcements-cols">
                            @forelse($announcements as $announcement)
                                <a href="{{ route('student.announcements') }}" class="quick-announce-item">
                                    <p><strong>{{ $announcement->title ?? 'Notice' }}:</strong> {{ Str::limit($announcement->content ?? '', 75) }}</p>
                                </a>
                            @empty
                                <a href="{{ route('student.announcements') }}" class="quick-announce-item">
                                    <p><strong>Faculty Announcement:</strong> Dili ko mu entertain ron kay busy sa accreditation.</p>
                                </a>
                            @endforelse
                        </div>
                    </div>

                </div>
            </main> 
        </div> 
    </div> 
</div> 

<script>
function filterFacultyList() {
    let input = document.getElementById('facultySearchInput').value.toLowerCase().trim();
    let items = document.querySelectorAll('#facultyListContainer .faculty-avail-item-row');
    let noMatchDiv = document.getElementById('noFacultyMatch');
    let hasMatch = false;

    items.forEach(function(item) {
        let text = item.innerText.toLowerCase();
        if (text.includes(input)) {
            item.style.setProperty('display', 'grid', 'important');
            hasMatch = true;
        } else {
            item.style.setProperty('display', 'none', 'important');
        }
    });

    if (!hasMatch && input !== "") {
        noMatchDiv.style.display = "block";
    } else {
        noMatchDiv.style.display = "none";
    }
}
</script>
@endsection