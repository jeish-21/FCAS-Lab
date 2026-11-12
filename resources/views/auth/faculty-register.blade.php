@extends('layouts.app')

@section('title', 'Faculty Sign Up - SNSU Appointment System')

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
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>

            <h2 class="sub-title login-heading">
                FACULTY REGISTRATION <span class="email-note">(requires @ssct.edu.ph email)</span>
            </h2>

            {{-- Error & Validation Alerts --}}
            @if ($errors->any())
                <div style="background: rgba(220, 53, 69, 0.25); border: 1px solid #dc3545; color: #ffffff; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post', ['role' => 'faculty']) }}" method="POST" class="register-form">
                @csrf
                <input type="hidden" name="role" value="faculty">

                <div class="form-row">
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                        </div>
                    </div>
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-building-user input-icon"></i>
                            <select name="department" required>
                                <option value="" disabled selected>Select Department / College</option>
                                <option value="CEIT" {{ old('department') == 'CEIT' ? 'selected' : '' }}>College of Engineering & Information Technology (CEIT)</option>
                                <option value="COT" {{ old('department') == 'COT' ? 'selected' : '' }}>College of Technology (COT)</option>
                                <option value="CTE" {{ old('department') == 'CTE' ? 'selected' : '' }}>College of Teacher Education (CTE)</option>
                                <option value="CAS" {{ old('department') == 'CAS' ? 'selected' : '' }}>College of Arts & Sciences (CAS)</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-briefcase input-icon"></i>
                            <input type="text" name="designation" placeholder="Designation (e.g. Instructor, Professor)" value="{{ old('designation') }}" required>
                        </div>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="text" name="email" id="reg_email" placeholder="Enter Faculty Email Username" value="{{ old('email') }}" required>
                        <span class="domain-badge">@ssct.edu.ph</span>
                    </div>
                    <span class="input-subtext">Type only your email username prefix (e.g. 'llatoza').</span>
                </div>

                <div class="form-row">
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-shield-halved input-icon"></i>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">CREATE FACULTY ACCOUNT</button>
            </form>

            <div class="auth-navigation-links">
                <p>Already have an account? <a href="{{ route('login', ['role' => 'faculty']) }}"><u>Log In Here</u></a></p>
                <p><a href="{{ route('welcome') }}"><u>Go Back to Role Selection</u></a></p>
            </div>
        </div>
    </div>
</div>
@endsection