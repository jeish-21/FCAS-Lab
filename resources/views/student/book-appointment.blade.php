@extends('layouts.app')

@section('title', 'Book an Appointment - SNSU System')

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

            <div class="user-profile-badge">
                <div class="avatar-img">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="user-details">
                    <span class="user-greeting">Logged in as:</span>
                    <span class="user-name">{{ Auth::user()->name ?? 'Student User' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 8px;">
                    @csrf
                    <button type="submit" title="Logout" style="background: none; border: none; cursor: pointer; color: #1a1a1a; font-size: 1rem;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="dashboard-body">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <a href="{{ route('student.dashboard') }}" class="sidebar-link">
                    <i class="fa-solid fa-border-all"></i> DASHBOARD
                </a>
                <a href="{{ route('student.book-appointment') }}" class="sidebar-link active" data-active="true">
                    <i class="fa-regular fa-calendar-plus"></i> BOOK AN APPOINTMENT
                </a>
                <a href="{{ route('student.faculty-availability') }}" class="sidebar-link">
                    <i class="fa-solid fa-user-clock"></i> FACULTY AVAILABILITY
                </a>
                <a href="{{ route('student.my-appointments') }}" class="sidebar-link">
                    <i class="fa-regular fa-calendar-check"></i> MY APPOINTMENTS
                </a>
                <a href="{{ route('student.consultation-logs') }}" class="sidebar-link">
                    <i class="fa-solid fa-folder-open"></i> CONSULTATION LOGS
                </a>
                <a href="{{ route('student.announcements') }}" class="sidebar-link">
                    <i class="fa-solid fa-bullhorn"></i> ANNOUNCEMENTS
                </a>
                <a href="{{ route('student.support') }}" class="sidebar-link">
                    <i class="fa-regular fa-circle-question"></i> SUPPORT
                </a>
            </aside>

            <!-- Booking Form -->
            <main class="dashboard-content">
                <h2 class="dash-title">BOOK AN APPOINTMENT</h2>

                @if($errors->any())
                    <div role="alert" style="background: rgba(220, 53, 69, 0.2); border: 1px solid #ef4444; color: #fecaca; padding: 12px; border-radius: 10px; margin-bottom: 16px;">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                @if(!$canBook)
                    <div role="status" class="dash-card support-card-glass" style="padding: 20px; color: #facc15;">
                        Your student account is pending approval. Appointment booking will be available after an administrator approves your registration.
                    </div>
                @else
                <form action="{{ route('student.book-appointment.post') }}" method="POST">
                    @csrf
                    
                    @php
                        $selectedParamId = $facultyId ?? ($faculties->first()->id ?? '');
                    @endphp

                    <!-- Hidden Input for Selected Faculty ID -->
                    <input type="hidden" name="faculty_id" id="selected_faculty_id" value="{{ $selectedParamId }}">

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        
                        <!-- Step 1: Select Faculty -->
                        <div class="dash-card support-card-glass" style="padding: 20px;">
                            <h3 style="color: white; margin-top: 0; font-size: 1.05rem;">Step 1: Select Faculty Member</h3>
                            
                            <div style="margin-bottom: 12px;">
                                <input type="text" id="faculty_search" onkeyup="filterFacultySelection()" placeholder="Search Faculty..." class="glass-input">
                            </div>

                            <div id="facultyListWrapper" style="display: flex; flex-direction: column; gap: 10px; max-height: 300px; overflow-y: auto;">
                                @if(isset($faculties) && count($faculties) > 0)
                                    @foreach($faculties as $faculty)
                                        @php
                                            $isSelected = ($faculty->id == $selectedParamId);
                                        @endphp
                                        <div class="faculty-select-card" id="faculty-card-{{ $faculty->id }}" onclick="selectFaculty({{ $faculty->id }}, this)" style="background: {{ $isSelected ? 'rgba(42, 226, 105, 0.2)' : 'rgba(255,255,255,0.08)' }}; border: 1px solid {{ $isSelected ? '#2ae269' : 'rgba(255,255,255,0.15)' }}; border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.2s;">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #2ae269; color: #1a1a1a; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                                                {{ strtoupper(substr($faculty->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 style="color: white; margin: 0; font-size: 0.88rem;">{{ $faculty->name }}</h4>
                                                <p style="color: #aaa; margin: 2px 0 0 0; font-size: 0.75rem;">{{ $faculty->department ?? 'College of Computer Studies' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #aaa; padding: 15px; text-align: center;">No faculty members found.</div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 2: Choose Date & Time -->
                        <div class="dash-card support-card-glass" style="padding: 20px;">
                            <h3 style="color: white; margin-top: 0; font-size: 1.05rem;">Step 2: Choose Date & Time</h3>
                            
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">Select Date</label>
                                <input type="date" id="appointment_date" name="appointment_date" value="{{ $selectedDate }}" class="glass-input" min="{{ date('Y-m-d') }}" onchange="refreshAvailability()" required>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">Available Time Slots (7:00 AM - 8:30 PM)</label>
                                <div class="glass-select-wrapper">
                                    <select name="time_slot" id="time_slot" class="glass-select" required>
                                        <option value="" disabled selected class="glass-option">-- Select Preferred Time --</option>
                                        
                                        @php
                                            $allTimeSlots = [
                                                '07:00 AM', '07:30 AM', '08:00 AM', '08:30 AM', 
                                                '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', 
                                                '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', 
                                                '01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM', 
                                                '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM',
                                                '05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM',
                                                '07:00 PM', '07:30 PM', '08:00 PM', '08:30 PM'
                                            ];

                                            $availableFacultySchedules = $consultationSlots ?? [];
                                            $facultyUnavailableSlots = $unavailableSlots ?? [];
                                            $takenBookedSlots = isset($bookedSlots) ? $bookedSlots : [];
                                        @endphp

                                        @foreach($allTimeSlots as $time)
                                            @php
                                                $isBooked = in_array($time, $takenBookedSlots);
                                                $isMarkedUnavailable = in_array($time, $facultyUnavailableSlots);
                                                $isOutsideConsultationSchedule = !empty($availableFacultySchedules) && !in_array($time, $availableFacultySchedules);
                                            @endphp

                                            @if($isBooked || $isMarkedUnavailable || $isOutsideConsultationSchedule)
                                                <option value="{{ $time }}" disabled style="background-color: #ef4444; color: #ffffff; font-weight: bold;">
                                                    {{ $time }} - {{ $isBooked ? 'Booked' : 'Unavailable' }}
                                                </option>
                                            @else
                                                <!-- Available (BERDE) -->
                                                <option value="{{ $time }}" class="glass-option" style="color: #2ae269; font-weight: bold;">
                                                    ✅ {{ $time }} - Available
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div id="availabilityRefreshError" role="alert" style="display: none; color: #fecaca; font-size: 0.8rem; margin-top: 6px;">
                                    Availability could not be refreshed. Reload the page to try again.
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Appointment Details -->
                        <div class="dash-card support-card-glass" style="padding: 20px;">
                            <h3 style="color: white; margin-top: 0; font-size: 1.05rem;">Step 3: Appointment Details</h3>
                            
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Reason for Consultation</label>
                                <input type="text" name="reason" placeholder="e.g. Thesis Advising" class="glass-input" required>
                            </div>

                            <div style="margin-bottom: 12px;">
                                <label style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Description</label>
                                <textarea name="description" rows="3" placeholder="Additional details..." class="glass-input" style="resize: vertical;"></textarea>
                            </div>

                            <div style="margin-bottom: 18px;">
                                <label style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Consultation Type</label>
                                <div class="glass-select-wrapper">
                                    <select name="consultation_type" class="glass-select" required>
                                        <option value="In-Person Office" class="glass-option">In-Person Office</option>
                                        <option value="Online Video Call" class="glass-option">Online Video Call</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" style="width: 100%; background: linear-gradient(180deg, #2ae269 0%, #1c9d46 100%); color: #0f2306; font-weight: 800; border: 1px solid rgba(255,255,255,0.6); padding: 12px; border-radius: 12px; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(42, 226, 105, 0.35); transition: transform 0.2s ease;">
                                <i class="fa-regular fa-calendar-check"></i> Review and Book Appointment
                            </button>
                        </div>

                    </div>
                </form>
                @endif
            </main>
        </div>
    </div>
</div>

<!-- Extra Glassmorphism Styles for Dropdowns & Form Inputs -->
<style>
.glass-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 10px !important;
    color: #ffffff !important;
    padding: 10px 14px !important;
    font-size: 0.85rem !important;
    outline: none !important;
    box-sizing: border-box !important;
    transition: all 0.25s ease !important;
}

.glass-input:focus {
    background: rgba(255, 255, 255, 0.22) !important;
    border-color: #2ae269 !important;
    box-shadow: 0 0 10px rgba(42, 226, 105, 0.4) !important;
}

.glass-input::placeholder {
    color: #cccccc !important;
}

.glass-select-wrapper {
    position: relative;
    width: 100%;
}

.glass-select-wrapper::after {
    content: '\f0d7';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #2ae269;
    pointer-events: none;
    font-size: 0.9rem;
}

.glass-select {
    width: 100%;
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 10px !important;
    color: #ffffff !important;
    padding: 10px 35px 10px 14px !important;
    font-size: 0.85rem !important;
    outline: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
    transition: all 0.25s ease !important;
}

.glass-select:focus {
    background: rgba(255, 255, 255, 0.22) !important;
    border-color: #2ae269 !important;
    box-shadow: 0 0 10px rgba(42, 226, 105, 0.4) !important;
}

.glass-option {
    background: #142816 !important;
    color: #ffffff !important;
    padding: 10px !important;
    font-size: 0.88rem !important;
}
</style>

<script>
function selectFaculty(id, element) {
    document.getElementById('selected_faculty_id').value = id;
    document.querySelectorAll('.faculty-select-card').forEach(card => {
        card.style.background = 'rgba(255,255,255,0.08)';
        card.style.border = '1px solid rgba(255,255,255,0.15)';
    });
    element.style.background = 'rgba(42, 226, 105, 0.2)';
    element.style.border = '1px solid #2ae269';
    refreshAvailability();
}

function refreshAvailability() {
    const url = new URL("{{ route('student.book-appointment') }}", window.location.origin);
    url.searchParams.set('faculty_id', document.getElementById('selected_faculty_id').value);
    url.searchParams.set('appointment_date', document.getElementById('appointment_date').value);
    window.location.href = url.toString();
}

async function refreshTimeSlots() {
    const select = document.getElementById('time_slot');
    const facultyId = document.getElementById('selected_faculty_id');
    const date = document.getElementById('appointment_date');
    const error = document.getElementById('availabilityRefreshError');

    if (!select || !facultyId || !facultyId.value || !date || !date.value) return;

    try {
        const url = new URL(@json(route('student.availability-slots')), window.location.origin);
        url.searchParams.set('faculty_id', facultyId.value);
        url.searchParams.set('appointment_date', date.value);
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Availability request failed.');

        const result = await response.json();
        const selectedTime = select.value;
        select.replaceChildren(new Option('-- Select Preferred Time --', '', true, true));

        result.slots.forEach(slot => {
            const option = new Option(`${slot.time} - ${slot.reason}`, slot.time);
            option.disabled = !slot.available;
            if (!slot.available) {
                option.style.backgroundColor = '#ef4444';
                option.style.color = '#ffffff';
            }
            select.add(option);
        });

        if (result.slots.some(slot => slot.time === selectedTime && slot.available)) {
            select.value = selectedTime;
        }
        error.style.display = 'none';
    } catch (error) {
        document.getElementById('availabilityRefreshError').style.display = 'block';
    }
}

if (document.getElementById('time_slot')) {
    window.setInterval(() => {
        if (document.visibilityState === 'visible') refreshTimeSlots();
    }, 15000);
}

function filterFacultySelection() {
    let val = document.getElementById('faculty_search').value.toLowerCase();
    let cards = document.querySelectorAll('.faculty-select-card');

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        if (text.includes(val)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection