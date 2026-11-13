@extends('layouts.app')

@section('title', 'Edit User - SNSU Admin')

@section('content')
<div class="main-wrapper">
    <div class="app-container" style="max-width: 900px; margin: 40px auto;">
        <main class="dashboard-content">
            <h2 class="dash-title">EDIT USER PROFILE</h2>
            <div class="dash-card support-card-glass faculty-card-box" style="padding: 24px;">
                <p style="color: #ddd;">Editing {{ $user->name }} ({{ ucfirst($user->role) }}). Passwords are private and cannot be viewed or changed here.</p>
                @if($errors->any())
                    <div role="alert" style="color: #fecaca; margin-bottom: 16px;">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
                        <label style="color: white;">Full name
                            <input class="support-dark-select" name="name" value="{{ old('name', $user->name) }}" required>
                        </label>
                        <label style="color: white;">Email
                            <input class="support-dark-select" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </label>
                        <label style="color: white;">Student ID
                            <input class="support-dark-select" name="student_id" value="{{ old('student_id', $user->student_id) }}">
                        </label>
                        <label style="color: white;">Course / Program
                            <input class="support-dark-select" name="course" value="{{ old('course', $user->course) }}">
                        </label>
                        <label style="color: white;">Department
                            <input class="support-dark-select" name="department" value="{{ old('department', $user->department) }}">
                        </label>
                        <label style="color: white;">Designation
                            <input class="support-dark-select" name="designation" value="{{ old('designation', $user->designation) }}">
                        </label>
                        <label style="color: white;">Account status
                            <select class="support-dark-select" name="status" required>
                                @foreach(['pending', 'approved', 'rejected'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $user->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button class="btn-green-post" type="submit">Save Profile</button>
                        <a href="{{ route('admin.user-management') }}" style="color: white; padding: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
