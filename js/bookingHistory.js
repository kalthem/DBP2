document.addEventListener('DOMContentLoaded', function() {
    // Handle cancel booking buttons
    document.querySelectorAll('.cancel-booking').forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch(`${document.querySelector('meta[name="base-url"]').content}/Controllers/BookingController.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=cancelBooking&booking_id=${bookingId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    });
    
    // Show success/error messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showAlert(urlParams.get('success'), 'success');
    } else if (urlParams.has('error')) {
        showAlert(urlParams.get('error'), 'danger');
    }
    
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
    }
});