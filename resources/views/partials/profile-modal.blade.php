<!-- Edit Profile Glassmorphism Centered Pop-Up Modal -->
<div id="profileEditModal" class="modal-overlay modal-hidden" onclick="closeStudentProfileModal(event)">
    <div class="dash-card profile-modal-card" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
<div class="profile-modal-header">
    <h3 class="profile-modal-title">
        <i class="fa-solid fa-user-pen"></i> Edit Student Profile
    </h3>
    <!-- Clean Close Icon -->
    <span id="closeProfileModal" class="modal-close-btn" onclick="hideProfileModal()">&times;</span>
</div>

        <!-- Profile Form -->
        <form action="{{ route('student.profile.update') }}" method="POST" class="profile-update-form" id="profileUpdateForm">
            @csrf

            <!-- Full Name -->
            <div class="profile-form-group">
                <label class="profile-form-label">Full Name</label>
                <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" class="profile-form-input" required placeholder="John Student">
            </div>

            <!-- Student ID Number -->
            <div class="profile-form-group">
                <label class="profile-form-label">Student ID Number</label>
                <input type="text" name="student_id" value="{{ Auth::user()->student_id ?? '' }}" class="profile-form-input" placeholder="e.g. 2024-00123">
            </div>

            <!-- Course / Program -->
            <div class="profile-form-group">
                <label class="profile-form-label">Course / Program</label>
                <input type="text" name="course" value="{{ Auth::user()->course ?? '' }}" class="profile-form-input" placeholder="e.g. BS Information Technology">
            </div>

            <!-- New Password -->
            <div class="profile-form-group">
                <label class="profile-form-label">
                    New Password <small class="password-note">(Leave blank to keep current)</small>
                </label>
                <input type="password" name="password" class="profile-form-input" placeholder="Enter new password">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit-booking profile-submit-btn">
                Save Changes
            </button>
        </form>
    </div>
</div>