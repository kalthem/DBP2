// Initialize the map centered on Bahrain
const map = L.map('map').setView([26.2285, 50.5861], 12);

// Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors"
}).addTo(map);

// Add "You are here" marker if location is available
if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            const userLat = position.coords.latitude;
            const userLon = position.coords.longitude;
            const userMarker = L.marker([userLat, userLon]).addTo(map);
            userMarker.bindPopup("You are here").openPopup();
            map.setView([userLat, userLon], 13);
        },
        function () {
            console.warn("Geolocation not allowed or failed.");
        }
    );
}

// Array to store current map markers
let chargePointMarkers = [];

// Function to create a marker for a charge point
function createMarker(cp) {
    if (cp.latitude && cp.longitude) {
        const marker = L.marker([parseFloat(cp.latitude), parseFloat(cp.longitude)]).addTo(map);
        marker.bindPopup(`
            <strong>${cp.city}</strong><br>
            Charger Type: ${cp.charger_type}<br>
            <a href="index.php?action=bookchargepoint&id=${cp.id}" class="btn btn-sm btn-primary mt-1">Book</a>
        `);
        chargePointMarkers.push(marker);
    }
}

// Display all charge points initially
function displayAllMarkers() {
    chargePoints.forEach(createMarker);
}

// Update the map with filtered results
function updateMap(data) {
    chargePointMarkers.forEach(marker => map.removeLayer(marker));
    chargePointMarkers = [];
    data.forEach(createMarker);
}

// AJAX call to get filtered charge points
function updateData() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'Controller/getFilteredChargePoints.php?' + params, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                updateMap(data);
            } catch (e) {
                console.error("Invalid JSON from server:", xhr.responseText);
            }
        }
    };
    xhr.send();
}

// Debounce filter input to avoid spamming requests
let debounceTimer;
document.getElementById('filter-form').addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(updateData, 300);
});

// Load all markers on page load
displayAllMarkers();
