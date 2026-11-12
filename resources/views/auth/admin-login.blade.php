@extends('layouts.app')

@section('title', 'Admin Login - SNSU Appointment System')

@section('content')
<div class="main-wrapper">
    <div class="shape shape-top-right"></div>
    <div class="shape shape-bottom-left"></div>

    <div class="glass-card">
        <div class="header-section">
            <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="logo">
            <h1 class="system-title">SNSU Faculty Consultation<br>& Appointment System</h1>
        </div>

        <div class="inner-glass-card">
            <div class="role-avatar-badge">
                <i class="fa-solid fa-user-gear"></i>
            </div>

            <h2 class="sub-title login-heading">
                ADMIN/STAFF LOGIN <span class="email-note">(requires @ssct.edu.ph email)</span>
            </h2>

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                <input type="hidden" name="role" value="admin">

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-regular fa-user input-icon"></i>
                        <input type="text" name="email" id="admin_email" placeholder="Enter your Admin Email" required>
                        <span class="domain-badge">@ssct.edu.ph</span>
                    </div>
                    <span class="input-subtext">For Department Admins & System Administrators only.</span>
                </div>

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="forgot-pass-link">
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" class="btn-submit">LOG IN</button>
            </form>

            <div class="auth-navigation-links">
                <p>Not an Admin or Staff? <a href="{{ route('welcome') }}"><u>Go Back to Role Selection</u></a></p>
            </div>
        </div>
    </div>

    <div class="superadmin-link">
        <a href="{{ route('superadmin.login') }}">Go to SuperAdmin Login</a>
    </div>
</div>
@endsection