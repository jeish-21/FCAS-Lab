@extends('layouts.app') 

@section('title', 'Support - SNSU Appointment System') 

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
                <a href="{{ route('student.faculty-availability') }}" class="sidebar-link" data-active="{{ Request::routeIs('student.faculty-availability*') ? 'true' : 'false' }}"> 
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
                <a href="{{ route('student.support') }}" class="sidebar-link active" data-active="true"> 
                    <i class="fa-regular fa-circle-question"></i> SUPPORT 
                </a> 
            </aside> 

            <!-- Main Content Workspace --> 
            <main class="dashboard-content"> 
                <h2 class="dash-title">SUPPORT</h2> 

                @if(session('success'))
                    <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #4ade80; padding: 12px 16px; border-radius: 12px; font-weight: 700; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="support-main-layout">
                    <!-- Left Side Column Container -->
                    <div class="support-left-col">
                        <!-- Contact Support Card -->
                        <div class="dash-card support-card-glass"> 
                            <!-- Floating Handshake Badge -->
                            <div class="support-handshake-badge">
                                <i class="fa-solid fa-handshake-angle"></i>
                            </div>

                            <h3 class="card-title-support">Contact Support</h3> 
                            <h4 class="card-subtitle-support">Helpdesk Request</h4>

                            <!-- Functional Form Connected to DB Route -->
                            <form action="{{ route('student.support.submit') }}" method="POST" class="support-form-custom" enctype="multipart/form-data">
                                @csrf
                                <div class="support-group">
                                    <label>Subject</label>
                                    <div class="support-select-wrapper">
                                        <select class="support-dark-select" name="subject" required>
                                            <option value="" disabled selected>Select a subject...</option>
                                            <option value="Appointment Booking Issue">Appointment Booking Issue</option>
                                            <option value="Account & Profile Update">Account & Profile Update</option>
                                            <option value="Technical Glitch / Bug">Technical Glitch / Bug</option>
                                            <option value="Other Inquiry">Other Inquiry</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>

                                <div class="support-group">
                                    <label>Description</label>
                                    <textarea class="support-dark-textarea" name="description" placeholder="Describe your concern in detail..." required></textarea>
                                </div>

                                <div class="support-upload-row">
                                    <label>Attachment</label>
                                    <label for="support-file" class="btn-glass-upload">
                                        <i class="fa-solid fa-upload"></i> <span id="fileNameLabel">Upload File</span>
                                    </label>
                                    <input type="file" id="support-file" name="attachment" style="display: none;" onchange="updateFileName(this)">
                                </div>

                                <button type="submit" class="btn-green-submit">
                                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                                </button>
                            </form>
                        </div> 

                        <!-- Bottom 3-Column Channels Cards -->
                        <div class="support-channels-grid">
                            <a href="javascript:void(0)" onclick="alert('Live Chat Support feature is active! Connecting to an agent...')" class="channel-card" style="text-decoration: none;">
                                <div class="channel-icon icon-blue">
                                    <i class="fa-solid fa-comments"></i>
                                </div>
                                <div class="channel-info">
                                    <strong>Live Chat</strong>
                                    <span>Online Now</span>
                                </div>
                            </a>

                            <a href="mailto:Support@snsu.edu.ph" class="channel-card" style="text-decoration: none;">
                                <div class="channel-icon icon-orange">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="channel-info">
                                    <strong>Direct Email</strong>
                                    <span>Support@snsu.edu.ph</span>
                                </div>
                            </a>

                            <a href="tel:9005547289" class="channel-card" style="text-decoration: none;">
                                <div class="channel-icon icon-green">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="channel-info">
                                    <strong>Call Us</strong>
                                    <small>Support Hotline:</small>
                                    <span>(900) 554-7289</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Right Side FAQ Column with Dynamic Interactive Search & Accordions -->
                    <div class="support-right-col">
                        <div class="dash-card support-card-glass faq-card-container">
                            <h3 class="card-title-support">Frequently Asked Questions (FAQ)</h3>

                            <!-- Dynamic FAQ Search Box -->
                            <div class="faq-dark-search" style="display: flex; align-items: center; background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 10px; margin-bottom: 15px;">
                                <i class="fa-solid fa-magnifying-glass" style="color: #ccc; margin-right: 10px;"></i>
                                <input type="text" id="faqSearchInput" onkeyup="filterFaqs()" placeholder="Search FAQs..." style="background: transparent; border: none; outline: none; color: white; width: 100%;">
                            </div>

                            <!-- Interactive Accordion List -->
                            <div class="faq-accordion-list" id="faqListContainer">
                                <div class="faq-accordion-item" style="margin-bottom: 10px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px;">
                                    <button type="button" class="faq-accordion-header" onclick="toggleFaq(this)" style="background: none; border: none; color: white; width: 100%; display: flex; justify-content: space-between; align-items: center; font-weight: bold; cursor: pointer;">
                                        <span>How to Book an Appointment?</span>
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div class="faq-accordion-body" style="display: none; padding-top: 10px; color: #e0e0e0; font-size: 0.85rem;">
                                        Click on "Book an Appointment" in the sidebar, select your desired faculty member, choose an available date and time slot, select consultation type, and click submit.
                                    </div>
                                </div>

                                <div class="faq-accordion-item" style="margin-bottom: 10px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px;">
                                    <button type="button" class="faq-accordion-header" onclick="toggleFaq(this)" style="background: none; border: none; color: white; width: 100%; display: flex; justify-content: space-between; align-items: center; font-weight: bold; cursor: pointer;">
                                        <span>What if I need to cancel?</span>
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div class="faq-accordion-body" style="display: none; padding-top: 10px; color: #e0e0e0; font-size: 0.85rem;">
                                        Navigate to "My Appointments", find your scheduled consultation, and click the cancel button at least 24 hours prior to the schedule.
                                    </div>
                                </div>

                                <div class="faq-accordion-item" style="margin-bottom: 10px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px;">
                                    <button type="button" class="faq-accordion-header" onclick="toggleFaq(this)" style="background: none; border: none; color: white; width: 100%; display: flex; justify-content: space-between; align-items: center; font-weight: bold; cursor: pointer;">
                                        <span>How is feedback used?</span>
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div class="faq-accordion-body" style="display: none; padding-top: 10px; color: #e0e0e0; font-size: 0.85rem;">
                                        Feedback is reviewed by department heads and faculty members to improve academic consultation quality and schedule accuracy.
                                    </div>
                                </div>

                                <div class="faq-accordion-item" style="margin-bottom: 10px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px;">
                                    <button type="button" class="faq-accordion-header" onclick="toggleFaq(this)" style="background: none; border: none; color: white; width: 100%; display: flex; justify-content: space-between; align-items: center; font-weight: bold; cursor: pointer;">
                                        <span>How is consultation evaluated?</span>
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div class="faq-accordion-body" style="display: none; padding-top: 10px; color: #e0e0e0; font-size: 0.85rem;">
                                        Evaluations are anonymously answered after every completed session to maintain objective quality metrics.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </main> 
        </div> 
    </div> 
</div> 

<script>
// Dynamic Accordion Toggle
function toggleFaq(btn) {
    const body = btn.nextElementSibling;
    const icon = btn.querySelector('i');

    if (body.style.display === 'none' || body.style.display === '') {
        body.style.display = 'block';
        icon.className = 'fa-solid fa-chevron-up';
    } else {
        body.style.display = 'none';
        icon.className = 'fa-solid fa-chevron-down';
    }
}

// Dynamic FAQ Filter Function
function filterFaqs() {
    let input = document.getElementById('faqSearchInput').value.toLowerCase();
    let items = document.querySelectorAll('#faqListContainer .faq-accordion-item');

    items.forEach(function(item) {
        let text = item.innerText.toLowerCase();
        if (text.includes(input)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Dynamic File Name Label Display
function updateFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameLabel').innerText = input.files[0].name;
    }
}
</script>

@include('partials.profile-modal')
@endsection