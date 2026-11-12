// Global Modal Control Functions (Accessible anywhere for inline handlers)
window.hideProfileModal = function () {
    const profileModal = document.getElementById('profileEditModal');
    if (profileModal) {
        profileModal.classList.add('modal-hidden');
        profileModal.style.setProperty('display', 'none', 'important');
    }
};

window.showProfileModal = function () {
    const profileModal = document.getElementById('profileEditModal');
    if (profileModal) {
        profileModal.classList.remove('modal-hidden');
        profileModal.style.setProperty('display', 'flex', 'important');
    }
};

window.closeStudentProfileModal = function (e) {
    if (e.target.id === 'profileEditModal') {
        window.hideProfileModal();
    }
};

document.addEventListener("DOMContentLoaded", function () {
    console.log("SNSU System Scripts Initialized.");

    // 1. List of all email input IDs across login and registration forms
    const emailInputIds = [
        "student_email", 
        "faculty_email", 
        "admin_email", 
        "superadmin_email",
        "reg_email"
    ];

    // Auto-strip domain suffix (@) and spaces to avoid duplicating domain suffixes
    emailInputIds.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", function () {
                let cleanedValue = this.value.replace(/\s+/g, '');
                if (cleanedValue.includes("@")) {
                    cleanedValue = cleanedValue.split("@")[0];
                }
                this.value = cleanedValue;
            });
        }
    });

    // 2. Click feedback animation effect for Role Cards
    const roleCards = document.querySelectorAll('.role-card');
    roleCards.forEach(card => {
        card.addEventListener('click', function () {
            this.style.transform = 'scale(0.98)';
        });
    });

    // 3. Client-side Registration Password Matching & Real-time Indicator
    const registerForm = document.querySelector('.register-form');
    if (registerForm) {
        const pass = registerForm.querySelector('input[name="password"]');
        const passConfirm = registerForm.querySelector('input[name="password_confirmation"]');

        if (pass && passConfirm) {
            const checkPasswordMatch = () => {
                if (passConfirm.value.length === 0) {
                    passConfirm.parentElement.style.borderColor = "#ccc";
                    return;
                }
                if (pass.value === passConfirm.value) {
                    passConfirm.parentElement.style.borderColor = "#2ae269"; // Green indicator
                } else {
                    passConfirm.parentElement.style.borderColor = "#ff4d4d"; // Red indicator
                }
            };

            pass.addEventListener('input', checkPasswordMatch);
            passConfirm.addEventListener('input', checkPasswordMatch);
        }

        registerForm.addEventListener('submit', function (e) {
            if (pass && passConfirm && pass.value !== passConfirm.value) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                passConfirm.focus();
            }
        });
    }

    // 4. Student Dashboard - Real-time Faculty Search Filter
    const searchInput = document.querySelector('.search-input-wrapper input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const filterValue = this.value.toLowerCase();
            const facultyRows = document.querySelectorAll('.faculty-row');

            facultyRows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                if (textContent.includes(filterValue)) {
                    row.style.display = 'grid';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 5. Book an Appointment - Faculty Selection Toggle
    const facultyCards = document.querySelectorAll('.faculty-card-select, .faculty-select-card');
    facultyCards.forEach(card => {
        card.addEventListener('click', function () {
            facultyCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 6. Book an Appointment - Calendar Day Selection
    const dayCells = document.querySelectorAll('.calendar-days-grid .day-cell, .mini-cal-grid-custom .m-day');
    dayCells.forEach(cell => {
        cell.addEventListener('click', function () {
            dayCells.forEach(c => c.classList.remove('selected', 'active-green-day'));
            this.classList.add('selected');
        });
    });

    // 7. Book an Appointment - Time Slot Selection Buttons
    const slotBtns = document.querySelectorAll('.btn-slot-select, .btn-select-slot');
    slotBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (this.classList.contains('disabled')) return;
            e.preventDefault();
            slotBtns.forEach(b => {
                b.classList.add('secondary');
            });
            this.classList.remove('secondary');
        });
    });

    // 8. My Appointments - Mini Calendar Day Cell Toggle
    const miniDays = document.querySelectorAll('.mini-cal-grid .m-day');
    miniDays.forEach(day => {
        day.addEventListener('click', function () {
            miniDays.forEach(d => d.classList.remove('active-day'));
            this.classList.add('active-day');
        });
    });

    // 9. Consultation Logs - Real-time Search & Status Filter
    const logsSearchInput = document.getElementById('logsSearchInput');
    const statusFilterSelect = document.getElementById('statusFilter');

    function filterConsultationLogs() {
        const textFilter = logsSearchInput ? logsSearchInput.value.toLowerCase() : '';
        const statusFilter = statusFilterSelect ? statusFilterSelect.value.toLowerCase() : '';
        const logRows = document.querySelectorAll('.logs-table-row, .logs-row-item');

        logRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const matchesText = rowText.includes(textFilter);
            const matchesStatus = statusFilter === '' || rowText.includes(statusFilter);

            if (matchesText && matchesStatus) {
                row.style.display = 'grid';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (logsSearchInput) {
        logsSearchInput.addEventListener('input', filterConsultationLogs);
    }
    if (statusFilterSelect) {
        statusFilterSelect.addEventListener('change', filterConsultationLogs);
    }

    // 10. Consultation Logs - Download Summary PDF Handler
    const downloadPdfBtns = document.querySelectorAll('.btn-download-pdf, .btn-pdf-glass');
    downloadPdfBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            alert('Downloading consultation summary PDF...');
        });
    });

    // 11. Announcements - Real-time Search & Category Filter
    const announcementSearchInput = document.getElementById('announcementSearchInput');
    const categoryFilterSelect = document.getElementById('categoryFilter');

    function filterAnnouncements() {
        const textFilter = announcementSearchInput ? announcementSearchInput.value.toLowerCase() : '';
        const categoryFilter = categoryFilterSelect ? categoryFilterSelect.value.toLowerCase() : '';

        // Filter featured card
        const featuredCard = document.querySelector('.featured-announcement-card');
        if (featuredCard) {
            const featuredText = featuredCard.textContent.toLowerCase();
            const matchesText = featuredText.includes(textFilter);
            const matchesCategory = categoryFilter === '' || categoryFilter === 'all' || featuredText.includes(categoryFilter);

            featuredCard.style.display = (matchesText && matchesCategory) ? 'flex' : 'none';
        }

        // Filter sub cards grid
        const subCards = document.querySelectorAll('.sub-announcement-card, .announcement-card');
        subCards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const matchesText = cardText.includes(textFilter);
            const matchesCategory = categoryFilter === '' || categoryFilter === 'all' || cardText.includes(categoryFilter);

            card.style.display = (matchesText && matchesCategory) ? 'flex' : 'none';
        });
    }

    if (announcementSearchInput) {
        announcementSearchInput.addEventListener('input', filterAnnouncements);
    }
    if (categoryFilterSelect) {
        categoryFilterSelect.addEventListener('change', filterAnnouncements);
    }

    // 12. Create New Announcement Button Handler
    const btnCreateAnnouncement = document.querySelector('.btn-create-announcement');
    if (btnCreateAnnouncement) {
        btnCreateAnnouncement.addEventListener('click', function () {
            alert('Opening announcement creation form...');
        });
    }

    // 13. Support Page - FAQ Accordion Toggle
    const faqHeaders = document.querySelectorAll('.faq-header');
    faqHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const faqItem = this.parentElement;
            faqItem.classList.toggle('active');
        });
    });

    // 14. Support Page - FAQ Real-time Search
    const faqSearchInput = document.getElementById('faqSearchInput');
    if (faqSearchInput) {
        faqSearchInput.addEventListener('input', function () {
            const filterValue = this.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // 15. Support Page - Form Handler
    const supportForm = document.getElementById('supportForm');
    if (supportForm) {
        supportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Your helpdesk request has been submitted successfully!');
            this.reset();
        });
    }

    // 16. Profile Edit Modal - Fixed Trigger & Submission Handling
    const profileBadge = document.querySelector('.user-profile-badge');
    const closeProfileBtn = document.getElementById('closeProfileModal');
    const profileUpdateForm = document.getElementById('profileUpdateForm');

    // Ensure hidden on initial load
    window.hideProfileModal();

    if (profileBadge) {
        profileBadge.addEventListener('click', function (e) {
            if (e.target.closest('form') || e.target.closest('button') || e.target.closest('.notification-icon-badge')) {
                return;
            }
            e.stopPropagation();
            window.showProfileModal();
        });
    }

    if (closeProfileBtn) {
        closeProfileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            window.hideProfileModal();
        });
    }

    if (profileUpdateForm) {
        profileUpdateForm.addEventListener('submit', function () {
            window.hideProfileModal();
        });
    }

    // 17. Faculty Hub - Quick Post Announcement Form Handler
    const quickPostForm = document.querySelector('.quick-post-form');
    if (quickPostForm) {
        quickPostForm.addEventListener('submit', function (e) {
            const textarea = this.querySelector('.faculty-post-textarea');
            if (textarea && textarea.value.trim() === '') {
                e.preventDefault();
                alert('Please enter an announcement message before posting.');
                textarea.focus();
            } else {
                alert('Announcement posted successfully!');
            }
        });
    }

    // 18. Faculty My Schedule - Interactive Schedule Modal Handler
    const scheduleModal = document.getElementById('scheduleModal');
    const addSlotBtn = document.getElementById('addSlotBtn');
    const closeScheduleModalBtn = document.getElementById('closeScheduleModal');
    const scheduleForm = document.getElementById('scheduleModalForm');
    const deleteSlotBtn = document.getElementById('deleteSlotBtn');

    let activeSlotElement = null;

    // FORCE HIDE MODAL ON INITIAL PAGE LOAD
    function closeModal() {
        if (scheduleModal) {
            scheduleModal.classList.add('modal-hidden');
            scheduleModal.style.setProperty('display', 'none', 'important');
        }
    }

    // Ensure modal is strictly hidden when page renders
    closeModal();

    function openModalWithSlotData(slot) {
        if (!scheduleModal) return;

        const day = slot.getAttribute('data-day') || 'Mon';
        const time = slot.getAttribute('data-time') || '08:00 AM';
        const type = slot.getAttribute('data-type') || (slot.classList.contains('filled-green') ? 'consultation' : (slot.classList.contains('glass-muted') ? 'lecture' : 'empty'));
        const label = slot.getAttribute('data-label') || slot.innerText.trim();

        const modalDaySelect = document.getElementById('modalDaySelect');
        const modalTimeSelect = document.getElementById('modalTimeSelect');
        const modalTypeSelect = document.getElementById('modalTypeSelect');
        const modalLabelInput = document.getElementById('modalLabelInput');

        if (modalDaySelect) modalDaySelect.value = day;
        if (modalTimeSelect) modalTimeSelect.value = time;
        if (modalTypeSelect) modalTypeSelect.value = type;
        if (modalLabelInput) modalLabelInput.value = label;

        if (deleteSlotBtn) {
            deleteSlotBtn.style.display = type !== 'empty' ? 'block' : 'none';
        }

        // Show modal ONLY when explicitly clicked
        scheduleModal.classList.remove('modal-hidden');
        scheduleModal.style.setProperty('display', 'flex', 'important');
    }

    // Attach click listeners ONLY to slot boxes on My Schedule page
    const schedSlotBoxes = document.querySelectorAll('.weekly-schedule-table .slot-box');
    schedSlotBoxes.forEach(box => {
        box.style.cursor = 'pointer';
        box.addEventListener('click', function (e) {
            e.stopPropagation();
            if (scheduleModal) {
                activeSlotElement = this;
                openModalWithSlotData(this);
            }
        });
    });

    if (addSlotBtn) {
        addSlotBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            activeSlotElement = null;
            const modalLabelInput = document.getElementById('modalLabelInput');
            const modalTypeSelect = document.getElementById('modalTypeSelect');
            if (modalLabelInput) modalLabelInput.value = '';
            if (modalTypeSelect) modalTypeSelect.value = 'consultation';
            if (deleteSlotBtn) deleteSlotBtn.style.display = 'none';

            if (scheduleModal) {
                scheduleModal.classList.remove('modal-hidden');
                scheduleModal.style.setProperty('display', 'flex', 'important');
            }
        });
    }

    if (closeScheduleModalBtn) {
        closeScheduleModalBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeModal();
        });
    }

    if (scheduleModal) {
        scheduleModal.addEventListener('click', function (e) {
            if (e.target === scheduleModal) {
                closeModal();
            }
        });
    }

    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const selectedDay = document.getElementById('modalDaySelect') ? document.getElementById('modalDaySelect').value : 'Mon';
            const selectedTime = document.getElementById('modalTimeSelect') ? document.getElementById('modalTimeSelect').value : '08:00 AM';
            const selectedType = document.getElementById('modalTypeSelect') ? document.getElementById('modalTypeSelect').value : 'consultation';
            const labelText = document.getElementById('modalLabelInput') ? document.getElementById('modalLabelInput').value.trim() : '';

            let targetSlot = activeSlotElement;
            if (!targetSlot) {
                targetSlot = document.querySelector(`.slot-box[data-day="${selectedDay}"][data-time="${selectedTime}"]`);
            }

            if (targetSlot) {
                targetSlot.className = 'slot-box';
                targetSlot.setAttribute('data-day', selectedDay);
                targetSlot.setAttribute('data-time', selectedTime);
                targetSlot.setAttribute('data-type', selectedType);
                targetSlot.setAttribute('data-label', labelText);

                if (selectedType === 'consultation') {
                    targetSlot.classList.add('filled-green');
                    targetSlot.innerHTML = labelText || 'Consultation';
                } else if (selectedType === 'lecture') {
                    targetSlot.classList.add('glass-muted');
                    targetSlot.innerHTML = labelText || 'Lecture / Class';
                } else {
                    targetSlot.classList.add('empty');
                    targetSlot.innerHTML = '';
                }
            }

            closeModal();
        });
    }

    if (deleteSlotBtn) {
        deleteSlotBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (activeSlotElement) {
                activeSlotElement.className = 'slot-box empty';
                activeSlotElement.setAttribute('data-type', 'empty');
                activeSlotElement.setAttribute('data-label', '');
                activeSlotElement.innerHTML = '';
            }
            closeModal();
        });
    }

    // 19. Faculty Hub - Pending Student Request Action Handlers
    const acceptDeclineBtns = document.querySelectorAll('.btn-accept-decline');
    acceptDeclineBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const reqRow = this.closest('.request-item-row');
            const studentName = reqRow ? reqRow.querySelector('strong').textContent : 'this student';
            const action = confirm(`Accept consultation request for ${studentName}? Click 'OK' to Accept, or 'Cancel' to Decline.`);
            
            if (action) {
                alert(`Request for ${studentName} accepted.`);
                if (reqRow) reqRow.style.opacity = '0.5';
            } else {
                alert(`Request for ${studentName} declined.`);
                if (reqRow) reqRow.remove();
            }
        });
    });

    // 20. Active Link Highlighter for Sidebar Navigation
    const currentPath = window.location.pathname.replace(/\/$/, '');
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        const linkPath = link.getAttribute('href') ? link.getAttribute('href').replace(/\/$/, '') : '';
        if (
            linkPath === currentPath || 
            (currentPath.includes('my_appointments') && linkPath.includes('my-appointments')) ||
            (currentPath.includes('consultation_logs') && linkPath.includes('consultation-logs')) ||
            (currentPath.includes('announcements') && linkPath.includes('announcements')) ||
            (currentPath.includes('support') && linkPath.includes('support')) ||
            (currentPath.includes('my-schedule') && linkPath.includes('my-schedule')) ||
            (currentPath.includes('faculty/dashboard') && linkPath.includes('faculty/dashboard'))
        ) {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });
});