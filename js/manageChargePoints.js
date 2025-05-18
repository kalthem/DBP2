document.addEventListener('DOMContentLoaded', () => {
    let map, marker;
    const modals = {
        add: document.querySelector('#addModal'),
        edit: document.querySelector('#editModal')
    };

    // Initialize Leaflet map
    function initMap(lat = 26.2235, lng = 50.5876) {
        if (map) map.remove();
        
        map = L.map('leafletMap').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        marker = L.marker([lat, lng], {draggable: true}).addTo(map);
        marker.on('dragend', updateCoordinates);
        map.on('click', e => marker.setLatLng(e.latlng));
    }

    // Update coordinate inputs
    function updateCoordinates(e) {
        const coords = e.target.getLatLng();
        const activeForm = document.querySelector('.modal.show form');
        if (activeForm) {
            activeForm.querySelector('[name="latitude"]').value = coords.lat.toFixed(6);
            activeForm.querySelector('[name="longitude"]').value = coords.lng.toFixed(6);
        }
    }

    // Handle edit button clicks
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            const form = modals.edit.querySelector('form');
            
            // Populate form data
            form.querySelector('#editId').value = row.dataset.id;
            form.querySelector('[name="location_id"]').value = row.dataset.location;
            form.querySelector('[name="charger_type"]').value = row.dataset.type;
            form.querySelector('[name="status"]').value = row.dataset.status;
            
            // Set coordinates
            form.querySelector('[name="latitude"]').value = row.dataset.lat;
            form.querySelector('[name="longitude"]').value = row.dataset.lng;
            
            // Set availability days
            const days = row.dataset.days.split(',');
            form.querySelectorAll('[name="availability_days[]"]').forEach(checkbox => {
                checkbox.checked = days.includes(checkbox.value);
            });
            
            // Initialize map with existing coordinates
            initMap(parseFloat(row.dataset.lat), parseFloat(row.dataset.lng));
        });
    });

    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', e => {
            let valid = true;
            
            // Required field validation
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Coordinate validation
            const lat = parseFloat(form.querySelector('[name="latitude"]').value);
            const lng = parseFloat(form.querySelector('[name="longitude"]').value);
            if (isNaN(lat) || isNaN(lng)) {
                alert('Please select a valid location on the map');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                alert('Please fill all required fields correctly');
            }
        });
    });

    // Map modal handling
    document.querySelectorAll('[data-bs-target="#mapModal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = btn.closest('form');
            const latInput = form.querySelector('[name="latitude"]');
            const lngInput = form.querySelector('[name="longitude"]');
            
            // Initialize map with existing values or default
            const lat = parseFloat(latInput.value) || 26.2235;
            const lng = parseFloat(lngInput.value) || 50.5876;
            initMap(lat, lng);
        });
    });

    // Pagination handling
    document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.set('page', e.target.dataset.page);
            window.location.href = url.toString();
        });
    });

    // Image preview
    document.querySelectorAll('[type="file"]').forEach(input => {
        input.addEventListener('change', () => {
            const preview = input.closest('.modal').querySelector('.image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
});
