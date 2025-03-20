<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 500px; 
            width: 100%;
            margin-bottom: 1rem;
        }
        .checkpoint-card {
            border-left: 4px solid #3b82f6;
            margin-bottom: 1rem;
        }
        .checkpoint-card.active {
            border-left-color: #10b981;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Panel de Administrador</h1>

        <!-- Pestañas -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <button onclick="showTab('places')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="places">
                        Lugares
                    </button>
                    <button onclick="showTab('checkpoints')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="checkpoints">
                        Puntos de Control
                    </button>
                </nav>
            </div>
        </div>

        <!-- Contenido de Lugares -->
        <div id="places-tab" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Formulario de lugares -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Añadir Nuevo Lugar</h2>
                    <form id="placeForm">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">Nombre</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Descripción</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="latitude" class="block text-gray-700">Latitud</label>
                                <input type="number" step="any" id="latitude" name="latitude" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label for="longitude" class="block text-gray-700">Longitud</label>
                                <input type="number" step="any" id="longitude" name="longitude" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                            Guardar Lugar
                        </button>
                    </form>
                </div>

                <!-- Lista de lugares -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Lugares Guardados</h2>
                    <div id="placesList" class="space-y-4"></div>
                </div>
            </div>
        </div>

        <!-- Contenido de Checkpoints -->
        <div id="checkpoints-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Formulario de checkpoints -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Añadir Nuevo Punto de Control</h2>
                    <form id="checkpointForm">
                        <div class="mb-4">
                            <label for="cp-name" class="block text-gray-700">Nombre</label>
                            <input type="text" id="cp-name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="cp-description" class="block text-gray-700">Descripción</label>
                            <textarea id="cp-description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="cp-hint" class="block text-gray-700">Pista</label>
                            <textarea id="cp-hint" name="hint" rows="2" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="cp-latitude" class="block text-gray-700">Latitud</label>
                                <input type="number" step="any" id="cp-latitude" name="latitude" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label for="cp-longitude" class="block text-gray-700">Longitud</label>
                                <input type="number" step="any" id="cp-longitude" name="longitude" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="cp-order" class="block text-gray-700">Orden</label>
                                <input type="number" id="cp-order" name="order" min="1" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label for="cp-points" class="block text-gray-700">Puntos</label>
                                <input type="number" id="cp-points" name="points" min="0" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                            Guardar Punto de Control
                        </button>
                    </form>
                </div>

                <!-- Lista de checkpoints -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Puntos de Control</h2>
                    <div id="checkpointsList" class="space-y-4"></div>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow-lg">
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let currentMarker = null;
        let markers = [];
        let activeTab = 'places';

        // Inicializar mapa
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadPlaces();
            loadCheckpoints();
            setupForms();
            showTab('places');
        });

        function initMap() {
            map = L.map('map').setView([41.3851, 2.1734], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: ' OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }
                currentMarker = L.marker([lat, lng]).addTo(map);

                // Actualizar campos según la pestaña activa
                if (activeTab === 'places') {
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                } else {
                    document.getElementById('cp-latitude').value = lat;
                    document.getElementById('cp-longitude').value = lng;
                }
            });
        }

        function showTab(tabName) {
            activeTab = tabName;
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                if (btn.dataset.tab === tabName) {
                    btn.classList.add('border-blue-500', 'text-blue-600');
                }
            });
        }

        function setupForms() {
            // Formulario de lugares
            document.getElementById('placeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/api/places', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(place => {
                    loadPlaces();
                    this.reset();
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                        currentMarker = null;
                    }
                });
            });

            // Formulario de checkpoints
            document.getElementById('checkpointForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/api/checkpoints', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(checkpoint => {
                    loadCheckpoints();
                    this.reset();
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                        currentMarker = null;
                    }
                });
            });
        }

        function loadPlaces() {
            fetch('/api/places')
                .then(response => response.json())
                .then(places => {
                    const placesList = document.getElementById('placesList');
                    placesList.innerHTML = '';
                    clearMarkers();

                    places.forEach(place => {
                        // Añadir al listado
                        const placeElement = document.createElement('div');
                        placeElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                        placeElement.innerHTML = `
                            <h3 class="font-bold">${place.name}</h3>
                            <p class="text-gray-600">${place.description}</p>
                            <div class="mt-2">
                                <button onclick="deletePlace(${place.id})" class="text-red-500 hover:text-red-700">
                                    Eliminar
                                </button>
                            </div>
                        `;
                        placesList.appendChild(placeElement);

                        // Añadir al mapa
                        const marker = L.marker([place.latitude, place.longitude])
                            .bindPopup(`<b>${place.name}</b><br>${place.description}`)
                            .addTo(map);
                        markers.push(marker);
                    });
                });
        }

        function loadCheckpoints() {
            fetch('/api/checkpoints')
                .then(response => response.json())
                .then(checkpoints => {
                    const checkpointsList = document.getElementById('checkpointsList');
                    checkpointsList.innerHTML = '';
                    clearMarkers();

                    checkpoints.forEach(checkpoint => {
                        // Añadir al listado
                        const checkpointElement = document.createElement('div');
                        checkpointElement.className = 'checkpoint-card p-4 border rounded-lg hover:bg-gray-50';
                        checkpointElement.innerHTML = `
                            <h3 class="font-bold">${checkpoint.name} (Orden: ${checkpoint.order})</h3>
                            <p class="text-gray-600">${checkpoint.description}</p>
                            <p class="text-gray-500"><strong>Pista:</strong> ${checkpoint.hint}</p>
                            <p class="text-gray-500"><strong>Puntos:</strong> ${checkpoint.points}</p>
                            <div class="mt-2">
                                <button onclick="deleteCheckpoint(${checkpoint.id})" class="text-red-500 hover:text-red-700">
                                    Eliminar
                                </button>
                            </div>
                        `;
                        checkpointsList.appendChild(checkpointElement);

                        // Añadir al mapa
                        const marker = L.marker([checkpoint.latitude, checkpoint.longitude])
                            .bindPopup(`<b>${checkpoint.name}</b><br>Orden: ${checkpoint.order}<br>${checkpoint.hint}`)
                            .addTo(map);
                        markers.push(marker);
                    });
                });
        }

        function clearMarkers() {
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
        }

        function deletePlace(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este lugar?')) {
                fetch(`/api/places/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => loadPlaces());
            }
        }

        function deleteCheckpoint(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este punto de control?')) {
                fetch(`/api/checkpoints/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => loadCheckpoints());
            }
        }
    </script>
</body>
</html>