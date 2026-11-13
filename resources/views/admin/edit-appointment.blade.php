@extends('layouts.app')

@section('title', 'Edit Appointment - SNSU Admin')

@section('content')
<div class="main-wrapper">
    <div class="app-container" style="max-width: 900px; margin: 40px auto;">
        <main class="dashboard-content">
            <h2 class="dash-title">EDIT APPOINTMENT #{{ $appointment->id }}</h2>
            <div class="dash-card support-card-glass faculty-card-box" style="padding: 24px;">
                @if($errors->any())
                    <div role="alert" style="color: #fecaca; margin-bottom: 16px;">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif
                <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <p style="color: #ddd;">Student: {{ $appointment->student->name ?? 'Unknown student' }}</p>
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
                        <label style="color: white;">Assigned faculty
                            <select class="support-dark-select" name="faculty_id" required>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ (string) old('faculty_id', $appointment->faculty_id) === (string) $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label style="color: white;">Appointment date
                            <input class="support-dark-select" type="date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date) }}" required>
                        </label>
                        <label style="color: white;">Time slot
                            <input class="support-dark-select" name="time_slot" value="{{ old('time_slot', $appointment->time_slot) }}" required>
                        </label>
                        <label style="color: white;">Consultation type
                            <input class="support-dark-select" name="consultation_type" value="{{ old('consultation_type', $appointment->consultation_type) }}" required>
                        </label>
                        <label style="color: white;">Purpose / reason
                            <input class="support-dark-select" name="reason" value="{{ old('reason', $appointment->reason) }}" required>
                        </label>
                        <label style="color: white;">Status
                            <select class="support-dark-select" name="status" required>
                                @foreach(['pending', 'approved', 'completed', 'cancelled', 'rejected', 'confirmed'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $appointment->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label style="color: white; grid-column: 1 / -1;">Description
                            <textarea class="support-dark-select" name="description" rows="4">{{ old('description', $appointment->description) }}</textarea>
                        </label>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button class="btn-green-post" type="submit">Save Appointment</button>
                        <a href="{{ route('admin.all-appointments') }}" style="color: white; padding: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
