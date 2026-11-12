@extends('layouts.app') 

@section('title', 'Welcome - SNSU Faculty Consultation & Appointment System') 

@section('content') 
<div class="main-wrapper"> 
    <!-- Abstract Graphic Shapes --> 
    <div class="shape shape-top-right"></div> 
    <div class="shape shape-bottom-left"></div> 

    <!-- Main Glass Card Container --> 
    <div class="glass-card"> 
        <!-- Logo & Header --> 
        <div class="header-section"> 
            <img src="{{ asset('images/snsulogo.png') }}" alt="SNSU Logo" class="logo"> 
            <h1 class="system-title">SNSU Faculty Consultation<br>& Appointment System</h1> 
        </div> 

        <!-- Inner Glass Card --> 
        <div class="inner-glass-card"> 
            <h2 class="sub-title">SELECT YOUR ROLE TO LOG IN</h2> 

            <!-- Role Options Grid --> 
            <div class="roles-grid"> 
                <!-- Student Role --> 
                <a href="{{ route('login', ['role' => 'student']) }}" class="role-card shiny-role-card"> 
                    <div class="icon-wrapper"> 
                        <i class="fa-solid fa-user-graduate"></i> 
                    </div> 
                    <div class="role-info"> 
                        <h3>STUDENT LOG IN</h3> 
                        <p>For SNSU Students</p> 
                    </div> 
                </a> 

                <!-- Faculty Role --> 
                <a href="{{ route('login', ['role' => 'faculty']) }}" class="role-card shiny-role-card"> 
                    <div class="icon-wrapper"> 
                        <i class="fa-solid fa-chalkboard-user"></i> 
                    </div> 
                    <div class="role-info"> 
                        <h3>FACULTY LOG IN</h3> 
                        <p>For SNSU Faculty Members</p> 
                    </div> 
                </a> 

                <!-- Admin/Staff Role --> 
                <a href="{{ route('login', ['role' => 'admin']) }}" class="role-card shiny-role-card"> 
                    <div class="icon-wrapper"> 
                        <i class="fa-solid fa-user-gear"></i> 
                    </div> 
                    <div class="role-info"> 
                        <h3>ADMIN/STAFF LOG IN</h3> 
                        <p>For Department Admins & System Administrators</p> 
                    </div> 
                </a> 
            </div> 

            <!-- Dual Sign Up Links (Student & Faculty) --> 
            <div class="signup-link"> 
                <p>
                    New User? 
                    <a href="{{ route('register', ['role' => 'student']) }}"><u>Sign Up as Student</u></a> | 
                    <a href="{{ route('register', ['role' => 'faculty']) }}"><u>Sign Up as Faculty</u></a>
                </p>
            </div> 
        </div> 
    </div> 
</div> 
@endsection