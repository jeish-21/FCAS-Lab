@extends('layouts.app')

@section('title', 'Student Sign Up - SNSU Appointment System')

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
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <h2 class="sub-title login-heading">
                STUDENT REGISTRATION <span class="email-note">(requires @ssct.edu.ph email)</span>
            </h2>

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div style="background: rgba(220, 53, 69, 0.25); border: 1px solid #dc3545; color: #ffffff; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation" style="margin-right: 4px;"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post', ['role' => 'student']) }}" method="POST" enctype="multipart/form-data" class="register-form">
                @csrf
                <input type="hidden" name="role" value="student">

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

                <div class="input-field-wrapper">
                    <label for="cor" style="display: block; color: #e0e0e0; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                        Certificate of Registration (COR) <span aria-hidden="true">*</span>
                    </label>
                    <div class="input-box">
                        <i class="fa-solid fa-file-arrow-up input-icon"></i>
                        <input type="file" id="cor" name="cor" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,application/pdf,image/*" required>
                    </div>
                    <span class="input-subtext">Required for account verification. Accepted: PDF or image, up to 10 MB.</span>
                </div>

                <div class="form-row">
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-id-card input-icon"></i>
                            <input type="text" name="student_id" placeholder="Student ID Number (e.g. 2024-0012)" value="{{ old('student_id') }}" required>
                        </div>
                    </div>
                    <div class="input-field-wrapper">
                        <div class="input-box">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            <select name="course" required>
                                <option value="" disabled selected>Select Course / Program</option>
                                <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BS in Information Technology (BSIT)</option>
                                <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>BS in Computer Science (BSCS)</option>
                                <option value="BSED" {{ old('course') == 'BSED' ? 'selected' : '' }}>BS in Secondary Education (BSED)</option>
                                <option value="BSCE" {{ old('course') == 'BSCE' ? 'selected' : '' }}>BS in Civil Engineering (BSCE)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <div class="input-box">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="text" name="email" id="reg_email" placeholder="Enter your Student Email Username" value="{{ old('email') }}" required>
                        <span class="domain-badge">@ssct.edu.ph</span>
                    </div>
                    <span class="input-subtext">Type only your email username prefix (e.g. 'juan.delacruz').</span>
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

                <button type="submit" class="btn-submit">CREATE ACCOUNT</button>
            </form>

            <div class="auth-navigation-links">
                <p>Already have an account? <a href="{{ route('login', ['role' => 'student']) }}"><u>Log In Here</u></a></p>
                <p><a href="{{ route('welcome') }}"><u>Go Back to Role Selection</u></a></p>
            </div>
        </div>
    </div>
</div>
@endsection