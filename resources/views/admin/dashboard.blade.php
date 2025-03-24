<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .answer-container {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }
        .answer-container.correct {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .chip:hover {
            background-color: #2563eb;
        }
        .chip-remove {
            margin-left: 0.5rem;
            cursor: pointer;
        }
        #tags-dropdown {
            max-height: 200px;
            overflow-y: auto;
        }
        .tag-option {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }
        .tag-option:hover {
            background-color: #f3f4f6;
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
                    <button onclick="showTab('gimcanas')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="gimcanas">
                        Gimcanas
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
                            <label for="address" class="block text-gray-700">Dirección</label>
                            <input type="text" id="address" name="address" class="w-full px-4 py-2 border rounded-lg" required>
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
                        <div class="mb-4">
                            <label for="icon" class="block text-gray-700">Icono</label>
                            <input type="text" id="icon" name="icon" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="tags" class="block text-gray-700">Tags</label>
                            <div id="tags-container" class="flex flex-wrap gap-2 mb-2">
                                <!-- Los tags seleccionados aparecerán aquí como "chips" -->
                            </div>
                            <div class="relative">
                                <input type="text" id="tags-input" class="w-full px-4 py-2 border rounded-lg" placeholder="Añadir tags...">
                                <div id="tags-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 hidden">
                                    <!-- Las opciones de tags aparecerán aquí -->
                                </div>
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

        <!-- Contenido de Gimcanas -->
        <div id="gimcanas-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Formulario de gimcanas -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Añadir Nueva Gimcana</h2>
                    <form id="gimcanaForm">
                        <div class="mb-4">
                            <label for="gimcana-name" class="block text-gray-700">Nombre</label>
                            <input type="text" id="gimcana-name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="gimcana-description" class="block text-gray-700">Descripción</label>
                            <textarea id="gimcana-description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                            Guardar Gimcana
                        </button>
                    </form>
                </div>

                <!-- Lista de gimcanas -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Gimcanas Guardadas</h2>
                    <div id="gimcanasList" class="space-y-4"></div>
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
                            <label for="cp-place" class="block text-gray-700">Lugar</label>
                            <select id="cp-place" name="place_id" class="w-full px-4 py-2 border rounded-lg" required>
                                <option value="">Selecciona un lugar</option>
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="cp-gimcana" class="block text-gray-700">Gimcana</label>
                            <select id="cp-gimcana" name="gimcana_id" class="w-full px-4 py-2 border rounded-lg" required>
                                <option value="">Selecciona una gimcana</option>
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="cp-challenge" class="block text-gray-700">Reto</label>
                            <textarea id="cp-challenge" name="challenge" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="cp-clue" class="block text-gray-700">Pista</label>
                            <textarea id="cp-clue" name="clue" rows="2" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="cp-order" class="block text-gray-700">Orden</label>
                            <input type="number" id="cp-order" name="order" min="1" class="w-full px-4 py-2 border rounded-lg" required>
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

        <!-- Modal de edición -->
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto mt-20">
                <h2 class="text-xl font-bold mb-4">Editar Lugar</h2>
                <form id="editPlaceForm">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-4">
                        <label for="edit-name" class="block text-gray-700">Nombre</label>
                        <input type="text" id="edit-name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit-address" class="block text-gray-700">Dirección</label>
                        <input type="text" id="edit-address" name="address" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit-latitude" class="block text-gray-700">Latitud</label>
                            <input type="number" step="any" id="edit-latitude" name="latitude" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label for="edit-longitude" class="block text-gray-700">Longitud</label>
                            <input type="number" step="any" id="edit-longitude" name="longitude" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="edit-icon" class="block text-gray-700">Icono</label>
                        <input type="text" id="edit-icon" name="icon" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tags</label>
                        <div id="edit-tags-container" class="flex flex-wrap gap-2 mb-2">
                            <!-- Los tags seleccionados aparecerán aquí como "chips" -->
                        </div>
                        <div class="relative">
                            <input type="text" id="edit-tags-input" class="w-full px-4 py-2 border rounded-lg" placeholder="Buscar tags...">
                            <div id="edit-tags-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 hidden">
                                <!-- Las opciones de tags disponibles aparecerán aquí -->
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let currentMarker = null;
        let markers = [];
        let activeTab = 'places';
        let places = [];
        let gimcanas = [];

        // Inicializar mapa
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadPlaces();
            loadGimcanas();
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
                
                // Convertir los valores de latitud y longitud a decimal
                formData.set('latitude', parseFloat(formData.get('latitude')).toFixed(8));
                formData.set('longitude', parseFloat(formData.get('longitude')).toFixed(8));

                // Obtener los tags seleccionados
                const tags = Array.from(document.querySelectorAll('#tags-container .chip')).map(chip => parseInt(chip.dataset.id));

                // Crear objeto con los datos del formulario
                const data = {
                    name: formData.get('name'),
                    address: formData.get('address'),
                    latitude: formData.get('latitude'),
                    longitude: formData.get('longitude'),
                    icon: formData.get('icon'),
                    tags: tags
                };

                fetch('/api/places', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Error ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire(
                        '¡Éxito!',
                        'El lugar ha sido añadido correctamente.',
                        'success'
                    );
                    loadPlaces();
                    document.getElementById('placeForm').reset();
                    document.getElementById('tags-container').innerHTML = '';
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al añadir el lugar',
                        text: error.message
                    });
                });
            });

            // Formulario de gimcanas
            document.getElementById('gimcanaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/api/gimcanas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(gimcana => {
                    loadGimcanas();
                    this.reset();
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
                });
            });

            document.getElementById('tags-input').addEventListener('focus', () => {
                document.getElementById('tags-dropdown').classList.remove('hidden');
                loadTags();
            });

            document.getElementById('tags-input').addEventListener('blur', () => {
                setTimeout(() => {
                    document.getElementById('tags-dropdown').classList.add('hidden');
                }, 200);
            });
        }

        function loadPlaces() {
            fetch('/api/places')
                .then(response => response.json())
                .then(data => {
                    places = data;
                    const placesList = document.getElementById('placesList');
                    placesList.innerHTML = '';
                    clearMarkers();

                    // Actualizar el selector de lugares en el formulario de checkpoints
                    const placeSelect = document.getElementById('cp-place');
                    placeSelect.innerHTML = '<option value="">Selecciona un lugar</option>';

                    places.forEach(place => {
                        // Añadir al listado
                        const placeElement = document.createElement('div');
                        placeElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                        placeElement.innerHTML = `
                            <h3 class="font-bold">${place.name}</h3>
                            <p class="text-gray-600">${place.address}</p>
                            <div class="mt-2">
                                <button onclick="deletePlace(${place.id})" class="text-red-500 hover:text-red-700">
                                    Eliminar
                                </button>
                                <button onclick="openEditModal(${place.id})" class="text-blue-500 hover:text-blue-700 ml-2">
                                    Editar
                                </button>
                            </div>
                        `;
                        placesList.appendChild(placeElement);

                        // Añadir al mapa
                        const marker = L.marker([place.latitude, place.longitude])
                            .bindPopup(`<b>${place.name}</b><br>${place.address}`)
                            .addTo(map);
                        markers.push(marker);

                        // Añadir al selector
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    });
                });
        }

        function addTag(id, name, formType) {
            const container = formType === 'edit' ? document.getElementById('edit-tags-container') : document.getElementById('tags-container');
            const chip = document.createElement('div');
            chip.className = 'chip';
            chip.innerHTML = `
                ${name}
                <span class="chip-remove" onclick="removeTag(${id}, '${formType}')">×</span>
            `;
            chip.dataset.id = id;
            container.appendChild(chip);
        }

        function removeTag(id, formType) {
            const container = formType === 'edit' ? document.getElementById('edit-tags-container') : document.getElementById('tags-container');
            const chip = container.querySelector(`.chip[data-id="${id}"]`);
            if (chip) {
                chip.remove();
            }
        }

        function loadTags() {
            fetch('/api/tags')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar los tags');
                    }
                    return response.json();
                })
                .then(tags => {
                    const tagsDropdown = document.getElementById('tags-dropdown');
                    tagsDropdown.innerHTML = '';

                    tags.forEach(tag => {
                        const option = document.createElement('div');
                        option.className = 'tag-option';
                        option.textContent = tag.name;
                        option.dataset.id = tag.id;
                        option.addEventListener('click', () => addTag(tag.id, tag.name, 'add'));
                        tagsDropdown.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los tags');
                });
        }

        function loadGimcanas() {
            fetch('/api/gimcanas')
                .then(response => response.json())
                .then(data => {
                    gimcanas = data;
                    const gimcanasList = document.getElementById('gimcanasList');
                    gimcanasList.innerHTML = '';

                    // Actualizar el selector de gimcanas en el formulario de checkpoints
                    const gimcanaSelect = document.getElementById('cp-gimcana');
                    gimcanaSelect.innerHTML = '<option value="">Selecciona una gimcana</option>';

                    gimcanas.forEach(gimcana => {
                        // Añadir al listado
                        const gimcanaElement = document.createElement('div');
                        gimcanaElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                        gimcanaElement.innerHTML = `
                            <h3 class="font-bold">${gimcana.name}</h3>
                            <p class="text-gray-600">${gimcana.description}</p>
                            <div class="mt-2">
                                <button onclick="deleteGimcana(${gimcana.id})" class="text-red-500 hover:text-red-700">
                                    Eliminar
                                </button>
                            </div>
                        `;
                        gimcanasList.appendChild(gimcanaElement);

                        // Añadir al selector
                        const option = document.createElement('option');
                        option.value = gimcana.id;
                        option.textContent = gimcana.name;
                        gimcanaSelect.appendChild(option);
                    });
                });
        }

        function loadCheckpoints() {
            fetch('/api/checkpoints')
                .then(response => response.json())
                .then(checkpoints => {
                    const checkpointsList = document.getElementById('checkpointsList');
                    checkpointsList.innerHTML = '';

                    checkpoints.forEach(checkpoint => {
                        // Encontrar el lugar asociado para obtener las coordenadas
                        const place = places.find(p => p.id === checkpoint.place_id);
                        const gimcana = gimcanas.find(g => g.id === checkpoint.gimcana_id);

                        if (!place || !gimcana) return;

                        // Añadir al listado
                        const checkpointElement = document.createElement('div');
                        checkpointElement.className = 'checkpoint-card p-4 border rounded-lg hover:bg-gray-50';
                        checkpointElement.innerHTML = `
                            <h3 class="font-bold">${place.name} (Orden: ${checkpoint.order})</h3>
                            <p class="text-gray-600"><strong>Gimcana:</strong> ${gimcana.name}</p>
                            <p class="text-gray-600"><strong>Reto:</strong> ${checkpoint.challenge}</p>
                            <p class="text-gray-500"><strong>Pista:</strong> ${checkpoint.clue}</p>
                            <div class="mt-2">
                                <button onclick="deleteCheckpoint(${checkpoint.id})" class="text-red-500 hover:text-red-700">
                                    Eliminar
                                </button>
                            </div>
                        `;
                        checkpointsList.appendChild(checkpointElement);

                        // Añadir al mapa si estamos en la pestaña de checkpoints
                        if (activeTab === 'checkpoints') {
                            const marker = L.marker([place.latitude, place.longitude])
                                .bindPopup(`<b>${place.name}</b><br>Orden: ${checkpoint.order}<br>${checkpoint.clue}`)
                                .addTo(map);
                            markers.push(marker);
                        }
                    });
                });
        }

        function clearMarkers() {
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
        }

        function deletePlace(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/places/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.error || 'Error al eliminar el lugar');
                            });
                        }
                        // Si la respuesta es 204 (No Content), no intentes parsear JSON
                        if (response.status === 204) {
                            return null;
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire(
                            '¡Eliminado!',
                            'El lugar ha sido eliminado.',
                            'success'
                        );
                        loadPlaces();
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al eliminar el lugar',
                            text: error.message
                        });
                    });
                }
            });
        }

        function deleteGimcana(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta gimcana?')) {
                fetch(`/api/gimcanas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => loadGimcanas());
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

        function openEditModal(id) {
            fetch(`/places/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar el lugar');
                    }
                    return response.json();
                })
                .then(place => {
                    document.getElementById('edit-id').value = place.id;
                    document.getElementById('edit-name').value = place.name;
                    document.getElementById('edit-address').value = place.address;
                    document.getElementById('edit-latitude').value = place.latitude;
                    document.getElementById('edit-longitude').value = place.longitude;
                    document.getElementById('edit-icon').value = place.icon;

                    // Limpiar los tags seleccionados
                    const tagsContainer = document.getElementById('edit-tags-container');
                    tagsContainer.innerHTML = '';

                    // Añadir los tags asociados al lugar
                    place.tags.forEach(tag => {
                        const chip = document.createElement('div');
                        chip.className = 'chip';
                        chip.innerHTML = `
                            ${tag.name}
                            <span class="chip-remove" onclick="removeTag(${tag.id}, 'edit')">×</span>
                        `;
                        chip.dataset.id = tag.id;
                        tagsContainer.appendChild(chip);
                    });

                    // Cargar todos los tags disponibles
                    fetch('/api/tags')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al cargar los tags');
                            }
                            return response.json();
                        })
                        .then(tags => {
                            const tagsDropdown = document.getElementById('edit-tags-dropdown');
                            tagsDropdown.innerHTML = '';

                            tags.forEach(tag => {
                                const option = document.createElement('div');
                                option.className = 'tag-option';
                                option.textContent = tag.name;
                                option.dataset.id = tag.id;
                                option.addEventListener('click', () => addTag(tag.id, tag.name, 'edit'));
                                tagsDropdown.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cargar los tags');
                        });

                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar el lugar');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('editPlaceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const tags = Array.from(document.querySelectorAll('#edit-tags-container .chip')).map(chip => parseInt(chip.dataset.id));

            const data = {
                id: formData.get('id'),
                name: formData.get('name'),
                address: formData.get('address'),
                latitude: formData.get('latitude'),
                longitude: formData.get('longitude'),
                icon: formData.get('icon'),
                tags: tags
            };

            fetch(`/places/${data.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Error al actualizar el lugar');
                    });
                }
                return response.json();
            })
            .then(place => {
                Swal.fire({
                    icon: 'success',
                    title: 'Lugar actualizado con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                closeEditModal();
                loadPlaces();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar el lugar',
                    text: error.message
                });
            });
        });

        document.getElementById('edit-tags-input').addEventListener('focus', function() {
            document.getElementById('edit-tags-dropdown').classList.remove('hidden');
        });

        document.getElementById('edit-tags-input').addEventListener('blur', function() {
            setTimeout(() => {
                document.getElementById('edit-tags-dropdown').classList.add('hidden');
            }, 200);
        });
    </script>
</body>
</html>