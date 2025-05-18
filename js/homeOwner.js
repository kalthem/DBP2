document.addEventListener("DOMContentLoaded", function () {
    const contentDiv = document.getElementById("dashboardContent");

    function loadSection(section) {
        fetch(`Controllers/loadHomeownerDashboard.php?section=${section}`)
            .then(res => res.text())
            .then(html => {
                contentDiv.innerHTML = html;
            })
            .catch(err => {
                contentDiv.innerHTML = "<div class='alert alert-danger'>Failed to load content.</div>";
                console.error(err);
            });
    }

    document.getElementById("btnManageChargePoint").addEventListener("click", () => {
        loadSection("chargepoint");
    });

    document.getElementById("btnViewBookingRequests").addEventListener("click", () => {
        loadSection("bookings");
    });

    // Default section
    loadSection("chargepoint");

    // Delegate click for approve/decline
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("approve-btn") || e.target.classList.contains("decline-btn")) {
            const bookingId = e.target.dataset.id;
            const status = e.target.classList.contains("approve-btn") ? "confirmed" : "declined";

            fetch(`Controllers/updateBookingStatus.php?id=${bookingId}&status=${status}`)
                .then(res => res.text())
                .then(() => loadSection("bookings"));
        }
    });
});
