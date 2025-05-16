document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const chargePointsTableContainer = document.getElementById('chargePointsTableContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const baseUrl = document.querySelector('meta[name="base-url"]').content;

    // Initialize filter form
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadFilteredChargePoints();
        });
    }

    // Initialize clear filters button
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (filterForm) filterForm.reset();
            loadFilteredChargePoints();
        });
    }

    // Load filtered charge points
    function loadFilteredChargePoints() {
        loadingSpinner.style.display = 'block';
        chargePointsTableContainer.innerHTML = '';
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        const xhr = new XMLHttpRequest();
        const url = `${baseUrl}/Controllers/load_charge_points.php?${params.toString()}`;
        
        xhr.open('GET', url);
        
        xhr.onload = function() {
            loadingSpinner.style.display = 'none';
            
            if (xhr.status === 200) {
                chargePointsTableContainer.innerHTML = xhr.responseText;
                initializeBookingForms();
            } else {
                showAlert(`Error loading data (Status: ${xhr.status}). Please try again.`, 'danger');
            }
        };
        
        xhr.onerror = function() {
            loadingSpinner.style.display = 'none';
            showAlert('Network error occurred. Please check your connection.', 'danger');
        };
        
        xhr.send();
    }

    // Initialize booking forms with validation
    function initializeBookingForms() {
        document.querySelectorAll('.booking-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateBookingForm(form)) {
                    submitBookingForm(form);
                }
            });

            // Add real-time validation
            form.querySelectorAll('input[type="datetime-local"]').forEach(input => {
                input.addEventListener('change', function() {
                    validateBookingForm(form);
                });
            });
        });
    }

    // Validate booking form
    function validateBookingForm(form) {
        const startInput = form.querySelector('input[name="start_time"]');
        const endInput = form.querySelector('input[name="end_time"]');
        const errorElement = form.querySelector('.booking-error');
        
        if (!startInput.value || !endInput.value) {
            return false;
        }

        const startTime = new Date(startInput.value);
        const endTime = new Date(endInput.value);
        const now = new Date();

        if (startTime < now) {
            if (errorElement) errorElement.textContent = "Start time cannot be in the past";
            return false;
        }

        if (startTime >= endTime) {
            if (errorElement) errorElement.textContent = "End time must be after start time";
            return false;
        }

        // Minimum booking duration (1 hour)
        const minHours = 1;
        const diffHours = (endTime - startTime) / (1000 * 60 * 60);
        if (diffHours < minHours) {
            if (errorElement) errorElement.textContent = `Minimum booking duration is ${minHours} hour(s)`;
            return false;
        }

        if (errorElement) errorElement.textContent = "";
        return true;
    }

    // Submit booking form
    function submitBookingForm(form) {
    const submitBtn = form.querySelector('.book-now-btn');
    const originalBtnText = submitBtn.innerHTML;
    const errorElement = form.querySelector('.booking-error');

    // Set loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Processing...
    `;

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.error) {
            throw new Error(data.error);
        }
    })
    .catch(error => {
        console.error('Booking error:', error);
        const errorMsg = error.message || 'An unexpected error occurred';
        if (errorElement) {
            errorElement.textContent = errorMsg;
        } else {
            showAlert(errorMsg, 'danger');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}
    // Show alert message
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container') || document.body;
        container.prepend(alertDiv);
        
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
            bsAlert.close();
        }, 5000);
    }

    // Initialize booking forms on first load
    initializeBookingForms();
});