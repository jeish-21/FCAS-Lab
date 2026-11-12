@extends('layouts.app')

@section('title', 'Student Login - SNSU Appointment System')

@section('content')
<div class="main-wrapper">
    <div class="glass-card">
        <div class="header-section">
            <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="logo">
            <h1 class="system-title">SNSU Faculty Consultation<br>& Appointment System</h1>
        </div>

        <div class="inner-glass-card">
            <div class="role-avatar-badge">
                <i class="fa-solid fa-user"></i>
            </div>

            <h2 class="sub-title login-heading">
                STUDENT LOGIN <span class="email-note">(requires @ssct.edu.ph email)</span>
            </h2>

            {{-- Pending Success Banner --}}
            @if (session('success'))
                <div style="background: rgba(40, 167, 69, 0.25); border: 1px solid #28a745; color: #ffffff; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; text-align: center; font-weight: 500;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div style="background: rgba(220, 53, 69, 0.25); border: 1px solid #dc3545; color: #ffffff; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation" style="margin-right: 4px;"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                <input type="hidden" name="role" value="student">

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" name="email" id="login_email" placeholder="Enter Student Email Username" value="{{ old('email') }}" required>
                        <span class="domain-badge">@ssct.edu.ph</span>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-submit">LOG IN</button>
            </form>

            <div class="auth-navigation-links">
                <p>Not a Student? <a href="{{ route('welcome') }}"><u>Go Back to Role Selection</u></a></p>
            </div>
        </div>
    </div>
</div>
@endsection