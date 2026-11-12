@extends('layouts.app')

@section('title', 'Consultation Requests - SNSU Faculty System')

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
                <a href="{{ route('faculty.my-schedule') }}" class="sidebar-link" data-active="{{ Request::routeIs('faculty.my-schedule*') ? 'true' : 'false' }}">
                    <i class="fa-regular fa-calendar-days"></i> MY SCHEDULE
                </a>
                <a href="{{ route('faculty.consultation-requests') }}" class="sidebar-link active" data-active="true">
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
                <h2 class="dash-title">CONSULTATION REQUESTS</h2>

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="dash-card support-card-glass" style="padding: 24px;">
                    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h3 class="card-title-support" style="margin: 0;">Manage Student Appointments</h3>
                            <p style="color: #ccc; font-size: 0.88rem; margin-top: 4px;">Approve, reject, complete, or undo appointment statuses in real-time.</p>
                        </div>

                        <!-- Status Filter Buttons -->
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button onclick="filterRequests('all')" class="filter-btn active" id="btn-all" style="background: #2ae269; color: #1a1a1a; font-weight: 700; border: none; padding: 6px 14px; border-radius: 8px; cursor: pointer;">All</button>
                            <button onclick="filterRequests('pending')" class="filter-btn" id="btn-pending" style="background: rgba(255,255,255,0.15); color: white; font-weight: 600; border: none; padding: 6px 14px; border-radius: 8px; cursor: pointer;">Pending</button>
                            <button onclick="filterRequests('approved')" class="filter-btn" id="btn-approved" style="background: rgba(255,255,255,0.15); color: white; font-weight: 600; border: none; padding: 6px 14px; border-radius: 8px; cursor: pointer;">Approved</button>
                            <button onclick="filterRequests('completed')" class="filter-btn" id="btn-completed" style="background: rgba(255,255,255,0.15); color: white; font-weight: 600; border: none; padding: 6px 14px; border-radius: 8px; cursor: pointer;">Completed</button>
                            <button onclick="filterRequests('cancelled')" class="filter-btn" id="btn-cancelled" style="background: rgba(255,255,255,0.15); color: white; font-weight: 600; border: none; padding: 6px 14px; border-radius: 8px; cursor: pointer;">Cancelled / Rejected</button>
                        </div>
                    </div>

                    <!-- Dynamic List of Requests -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @forelse($requests as $req)
                            @php
                                $statusColor = '#2ae269';
                                $statusLabel = strtoupper($req->status ?? 'PENDING');
                                if(in_array($req->status, ['rejected', 'cancelled'])) {
                                    $statusColor = '#ef4444';
                                } elseif($req->status === 'completed') {
                                    $statusColor = '#3b82f6';
                                } elseif($req->status === 'approved' || $req->status === 'confirmed') {
                                    $statusColor = '#2ae269';
                                } else {
                                    $statusColor = '#f59e0b';
                                }
                            @endphp

                            <div class="request-item-card" data-status="{{ strtolower($req->status ?? 'pending') }}" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                        <h4 style="color: white; font-size: 1.15rem; font-weight: 700; margin: 0;">
                                            {{ $req->student->name ?? 'Student Requestor' }}
                                        </h4>
                                        <span style="background: {{ $statusColor }}; color: #1a1a1a; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 6px;">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <p style="color: #2ae269; font-size: 0.85rem; font-weight: 600; margin: 0 0 8px 0;">
                                        {{ $req->student->course ?? 'BSIT' }} | Requested Date: {{ \Carbon\Carbon::parse($req->appointment_date)->format('M d, Y') }} ({{ $req->time_slot ?? '09:00 AM' }})
                                    </p>
                                    <p style="color: #e0e0e0; font-size: 0.92rem; margin: 0;">
                                        <strong style="color: #fff;">Reason:</strong> {{ $req->reason }}
                                        @if($req->description)
                                            <br><small style="color: #aaa;">{{ $req->description }}</small>
                                        @endif
                                    </p>
                                </div>

                                <!-- Action Workflow Buttons -->
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if($req->status === 'pending' || empty($req->status))
                                        <!-- Accept -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" style="background: #2ae269; color: #1a1a1a; font-weight: 800; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-check"></i> Accept
                                            </button>
                                        </form>

                                        <!-- Decline -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" style="background: #ef4444; color: white; font-weight: 800; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-xmark"></i> Decline
                                            </button>
                                        </form>

                                    @elseif($req->status === 'approved' || $req->status === 'confirmed')
                                        <!-- Mark Completed -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" style="background: #3b82f6; color: white; font-weight: 800; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-circle-check"></i> Complete
                                            </button>
                                        </form>

                                        <!-- Cancel -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" style="background: #f59e0b; color: white; font-weight: 800; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-ban"></i> Cancel
                                            </button>
                                        </form>

                                        <!-- Undo / Revert Back to Pending -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" title="Revert to Pending" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 12px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-rotate-left"></i> Undo
                                            </button>
                                        </form>

                                    @else
                                        <!-- Undo / Revert Back to Pending -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); font-weight: 700; padding: 8px 14px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-rotate-left"></i> Revert to Pending
                                            </button>
                                        </form>

                                        <!-- Permanent Delete -->
                                        <form action="{{ route('faculty.appointments.update-status', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this record?')">
                                            @csrf
                                            <input type="hidden" name="status" value="delete">
                                            <button type="submit" style="background: #ef4444; color: white; font-weight: 700; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer;">
                                                <i class="fa-solid fa-trash-can"></i> Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="background: rgba(255,255,255,0.08); border: 1px dashed rgba(255,255,255,0.25); border-radius: 16px; padding: 30px; text-align: center; color: #aaa;">
                                <i class="fa-regular fa-calendar-xmark" style="font-size: 2rem; color: #2ae269; margin-bottom: 10px;"></i>
                                <p style="margin: 0; font-size: 1rem; font-weight: 600;">No consultation requests found in database.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                
            </main>
        </div>
    </div>
</div>

<script>
function filterRequests(status) {
    const items = document.querySelectorAll('.request-item-card');
    const buttons = document.querySelectorAll('.filter-btn');

    // Update active button styling
    buttons.forEach(btn => {
        btn.style.background = 'rgba(255,255,255,0.15)';
        btn.style.color = 'white';
        btn.style.fontWeight = '600';
    });

    const activeBtn = document.getElementById('btn-' + status);
    if (activeBtn) {
        activeBtn.style.background = '#2ae269';
        activeBtn.style.color = '#1a1a1a';
        activeBtn.style.fontWeight = '800';
    }

    // Filter items in DOM
    items.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        if (status === 'all') {
            item.style.display = 'flex';
        } else if (status === 'cancelled') {
            if (itemStatus === 'cancelled' || itemStatus === 'rejected') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else {
            if (itemStatus === status) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        }
    });
}
</script>
@endsection