document.addEventListener("DOMContentLoaded", function () {
    loadChargePoint();
    loadBookings();

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("approve-btn") || e.target.classList.contains("decline-btn")) {
            const bookingId = e.target.dataset.id;
            const status = e.target.classList.contains("approve-btn") ? "confirmed" : "declined";

            fetch(`Controllers/updateBookingStatus.php?id=${bookingId}&status=${status}`)
                .then(res => res.text())
                .then(() => loadBookings());
        }
    });
});

function loadChargePoint() {
    fetch("Controllers/loadHomeownerDashboard.php?section=chargepoint")
        .then(res => res.text())
        .then(html => document.getElementById("chargePointSection").innerHTML = html);
}

function loadBookings() {
    fetch("Controllers/loadHomeownerDashboard.php?section=bookings")
        .then(res => res.text())
        .then(html => document.getElementById("bookingSection").innerHTML = html);
}
