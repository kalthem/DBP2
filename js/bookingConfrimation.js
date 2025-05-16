document.addEventListener('DOMContentLoaded', function() {
    // Set minimum datetime for booking (current time)
    const now = new Date();
    const nowString = now.toISOString().slice(0, 16);
    document.getElementById('start_time').min = nowString;
    
    // Update end time min when start time changes
    document.getElementById('start_time').addEventListener('change', function() {
        document.getElementById('end_time').min = this.value;
    });
    
    // Handle form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const baseUrl = document.querySelector('meta[name="base-url"]').content;
        
        showLoading(true);
        
        fetch(`${baseUrl}/Controllers/BookingController.php`, {
            method: 'POST',
            body: formData
        })
        .then(handleResponse)
        .then(handleSuccess)
        .catch(handleError)
        .finally(() => showLoading(false));
    });
    
    function handleResponse(response) {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Network response was not ok');
            });
        }
        return response.json();
    }
    
    function handleSuccess(data) {
        if (data.success) {
            window.location.href = `${document.querySelector('meta[name="base-url"]').content}/Views/bookingHistory.phtml?success=${encodeURIComponent(data.message)}`;
        } else {
            showAlert(data.message, 'danger');
        }
    }
    
    function handleError(error) {
        console.error('Error:', error);
        showAlert(error.message || 'An error occurred. Please try again.', 'danger');
    }
    
    function showLoading(show) {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.style.display = show ? 'block' : 'none';
        }
    }
    
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Show any initial messages from URL params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        showAlert(decodeURIComponent(urlParams.get('error')), 'danger');
    }
    if (urlParams.has('success')) {
        showAlert(decodeURIComponent(urlParams.get('success')), 'success');
    }
});