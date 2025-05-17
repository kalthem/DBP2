document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("filterSelect");
    const userTableBody = document.getElementById("userTableBody");
    const statsRow = document.getElementById("userStatsRow");

    // Helper to update stats
    function reloadStats() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "Controllers/ManageUsersController.php?action=stats", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const stats = JSON.parse(xhr.responseText);
                    statsRow.innerHTML = `
                    <div class="col-6 col-md-3">
                        <button class="btn btn-primary w-100">
                            <div style="font-size:1.3rem">${stats.total}</div>
                            <div style="font-size:.9rem">Total Users</div>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="btn btn-success w-100">
                            <div style="font-size:1.3rem">${stats.approved}</div>
                            <div style="font-size:.9rem">Approved</div>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="btn btn-warning w-100">
                            <div style="font-size:1.3rem">${stats.suspended}</div>
                            <div style="font-size:.9rem">Suspended</div>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="btn btn-secondary w-100">
                            <div style="font-size:1.3rem">${stats.pending}</div>
                            <div style="font-size:.9rem">Pending</div>
                        </button>
                    </div>
                    `;
                } catch (e) { /* ignore */ }
            }
        };
        xhr.send();
    }

    // SEARCH
    searchInput.addEventListener("input", function () {
        const query = this.value;
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "Controllers/ManageUsersController.php?action=search&query=" + encodeURIComponent(query), true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                userTableBody.innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });

    // FILTER
    filterSelect.addEventListener("change", function () {
        const filter = this.value;
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "Controllers/ManageUsersController.php?action=filter&filter=" + encodeURIComponent(filter), true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                userTableBody.innerHTML = xhr.responseText;
            }
        };
        xhr.send();
        // Always update stats on filter change
        reloadStats();
    });

    // ACTION BUTTONS
    userTableBody.addEventListener("click", function (e) {
        if (e.target.classList.contains("action-btn")) {
            const userId = e.target.getAttribute("data-id");
            const userAction = e.target.getAttribute("data-action");
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "Controllers/ManageUsersController.php?action=perform", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === "success") {
                    // Refresh the table and stats on success
                    filterSelect.dispatchEvent(new Event('change'));
                    reloadStats();
                } else {
                    alert("Failed to perform action.");
                }
            };
            xhr.send("userId=" + encodeURIComponent(userId) + "&userAction=" + encodeURIComponent(userAction));
        }
    });

    // PAGINATION
    document.querySelectorAll(".pagination-link").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const page = this.getAttribute("data-page");
            const filter = this.getAttribute("data-filter");
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "Controllers/ManageUsersController.php?action=paginate&filter=" + encodeURIComponent(filter) + "&page=" + encodeURIComponent(page), true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    userTableBody.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    });
});