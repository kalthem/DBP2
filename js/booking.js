window.addEventListener("DOMContentLoaded", function () {
  // Handle booking form validation
  const bookingForm = document.getElementById("bookingForm");
  const errorBox = document.getElementById("formError");

  if (bookingForm) {
    bookingForm.addEventListener("submit", function (e) {
      const start = new Date(document.getElementById("start_time").value);
      const end = new Date(document.getElementById("end_time").value);
      const now = new Date();

      // Validate start and end times
      if (start < now) {
        e.preventDefault();
        errorBox.textContent = "Start time cannot be in the past.";
      } else if (start >= end) {
        e.preventDefault();
        errorBox.textContent = "End time must be after start time.";
      } else {
        errorBox.textContent = "";
      }
    });
  }

  // If on "My Bookings" page, initialize table actions
  const cancelButtons = document.querySelectorAll('.btn-cancel-booking');
  
  cancelButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      if (!confirm('Are you sure you want to cancel this booking?')) {
        e.preventDefault();
      }
    });
  });
});
