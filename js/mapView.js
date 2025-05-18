// Initialize map
var map = L.map('map').setView([26.2285, 50.5861], 12);  // Default center (Bahrain)

// Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors",
}).addTo(map);

// Attempt to track the user's location
if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(function (position) {
        const userLat = position.coords.latitude;
        const userLon = position.coords.longitude;
        L.marker([userLat, userLon])
            .addTo(map)
            .bindPopup("You are here")
            .openPopup();
        map.setView([userLat, userLon], 13);
    });
}

// Store charge point markers
let chargePointMarkers = [];

// Function to display all charge point markers on the map
function displayAllMarkers() {
    chargePoints.forEach(cp => {
        if (cp.latitude && cp.longitude) {
            const marker = L.marker([parseFloat(cp.latitude), parseFloat(cp.longitude)]).addTo(map);
            marker.bindPopup(`
                <strong>${cp.city}</strong><br>
                Charger Type: ${cp.charger_type}<br>
                <a href="index.php?page=book_charge_point&id=${cp.id}" class="btn btn-sm btn-primary mt-1">Book</a>
            `);
            chargePointMarkers.push(marker);
        }
    });
}

// Function to update the map markers after filtering
function updateMap(data) {
    // Remove any existing markers
    chargePointMarkers.forEach(marker => map.removeLayer(marker));
    chargePointMarkers = [];

    data.forEach(cp => {
        if (cp.latitude && cp.longitude) {
            const marker = L.marker([parseFloat(cp.latitude), parseFloat(cp.longitude)]).addTo(map);
            marker.bindPopup(`
                <strong>${cp.city}</strong><br>
                Charger Type: ${cp.charger_type}<br>
                <a href="index.php?page=book_charge_point&id=${cp.id}" class="btn btn-sm btn-primary mt-1">Book</a>
            `);
            chargePointMarkers.push(marker);
        }
    });
}

// Function to update data based on filter form
function updateData() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    // Update the map markers with filtered data
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'Controller/getFilteredChargePoints.php?' + params, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                updateMap(data);  // Update the map with filtered charge points
            } catch (e) {
                console.error("Invalid JSON from server:", xhr.responseText);
            }
        }
    };
    xhr.send();
}

// Debouncing input event listener for the filter form
let debounceTimer;
document.getElementById('filter-form').addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(updateData, 300); // Wait 300ms after the user stops typing
});

// Display all markers on page load
displayAllMarkers();
