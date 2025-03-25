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
<<<<<<< HEAD
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        
        .tag {
            padding: 0.25rem 0.75rem;
            background-color: #e5e7eb;
            border-radius: 9999px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .tag.selected {
            background-color: #3b82f6;
            color: white;
        }
        
        .tag:hover {
            background-color: #d1d5db;
        }
        
        .tag.selected:hover {
            background-color: #2563eb;
        }
        
        .new-tag-form {
            display: flex;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        
        .new-tag-form input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
=======
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
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Panel de Administrador</h1>

        <!-- Pestañas -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
<<<<<<< HEAD
                <div class="flex justify-between">
                    <div class="flex">
                        <button onclick="showTab('places')" class="tab-btn py-4 px-6 border-b-2 font-medium border-blue-500 text-blue-600" data-tab="places">
                            Lugares
                        </button>
                        <button onclick="showTab('tags')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="tags">
                            Etiquetas
                        </button>
                    </div>
                    <div class="flex">
                        <button onclick="showTab('gimcanas')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="gimcanas">
                            Gimcanas
                        </button>
                        <button onclick="showTab('checkpoints')" class="tab-btn py-4 px-6 border-b-2 font-medium" data-tab="checkpoints">
                            Puntos de Control
                        </button>
                    </div>
                </div>
=======
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
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
            </div>
        </div>

        <!-- Contenido de Lugares -->
        <div id="places-tab" class="tab-content">
<<<<<<< HEAD
            <h2 class="text-2xl font-bold mb-4">Gestión de Lugares</h2>
            
            <!-- Selector de etiquetas existentes -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Filtrar por etiquetas</h3>
                <div id="tag-container" class="tag-container"></div>
            </div>
            
            <!-- Formulario de lugares -->
            <form id="place-form" class="mb-8 bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Añadir Nuevo Lugar</h3>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nombre</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
=======
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
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
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
                    <label for="icon" class="block text-gray-700">Icono (opcional)</label>
                    <input type="text" id="icon" name="icon" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Etiquetas del lugar</label>
                    <div id="place-tags-container" class="tag-container"></div>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                    Guardar Lugar
                </button>
            </form>
            
            <!-- Lista de lugares -->
            <div id="places-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Los lugares se cargarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Contenido de Etiquetas -->
        <div id="tags-tab" class="tab-content hidden">
            <h2 class="text-2xl font-bold mb-4">Gestión de Etiquetas</h2>
            
            <!-- Formulario para crear etiquetas -->
            <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                <h3 class="text-xl font-bold mb-4">Crear Nueva Etiqueta</h3>
                <div class="flex gap-4">
                    <input type="text" id="new-tag-input" placeholder="Nombre de la etiqueta..." 
                           class="flex-1 px-4 py-2 border rounded-lg">
                    <button onclick="createNewTag()" 
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Crear Etiqueta
                    </button>
                </div>
            </div>
            
            <!-- Lista de etiquetas -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Etiquetas Existentes</h3>
                <div id="tags-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Las etiquetas se cargarán dinámicamente aquí -->
                </div>
            </div>
        </div>

        <!-- Contenido de Gimcanas -->
        <div id="gimcanas-tab" class="tab-content">
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
                            <textarea id="gimcana-description" name="description" class="w-full px-4 py-2 border rounded-lg" rows="3" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="gimcana-max-groups" class="block text-gray-700">Número máximo de grupos</label>
                            <input type="number" id="gimcana-max-groups" name="max_groups" class="w-full px-4 py-2 border rounded-lg" min="1" required>
                        </div>
                        <div class="mb-4">
                            <label for="gimcana-max-users-per-group" class="block text-gray-700">Número máximo de usuarios por grupo</label>
                            <input type="number" id="gimcana-max-users-per-group" name="max_users_per_group" class="w-full px-4 py-2 border rounded-lg" min="1" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="gimcana-max-groups" class="block text-gray-700">Máximo de grupos</label>
                                <input type="number" id="gimcana-max-groups" name="max_groups" min="1" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label for="gimcana-max-users" class="block text-gray-700">Máximo de usuarios por grupo</label>
                                <input type="number" id="gimcana-max-users" name="max_users_per_group" min="1" class="w-full px-4 py-2 border rounded-lg" required>
                            </div>
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
        <div id="checkpoints-tab" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Formulario de puntos de control -->
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

                <!-- Lista de puntos de control -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Puntos de Control</h2>
                    <div id="checkpointsList" class="space-y-4">
                        <!-- Los puntos de control se cargarán dinámicamente aquí -->
                    </div>
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

        <!-- Modal de edición de gimcana -->
        <div id="editGimcanaModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Editar Gimcana</h2>
                <form id="editGimcanaForm">
                    <input type="hidden" id="edit-gimcana-id" name="id">
                    <div class="mb-4">
                        <label for="edit-gimcana-name" class="block text-gray-700">Nombre</label>
                        <input type="text" id="edit-gimcana-name" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit-gimcana-description" class="block text-gray-700">Descripción</label>
                        <textarea id="edit-gimcana-description" name="description" class="w-full px-4 py-2 border rounded-lg" rows="3" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit-gimcana-max-groups" class="block text-gray-700">Número máximo de grupos</label>
                        <input type="number" id="edit-gimcana-max-groups" name="max_groups" class="w-full px-4 py-2 border rounded-lg" min="1" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit-gimcana-max-users-per-group" class="block text-gray-700">Número máximo de usuarios por grupo</label>
                        <input type="number" id="edit-gimcana-max-users-per-group" name="max_users_per_group" class="w-full px-4 py-2 border rounded-lg" min="1" required>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<<<<<<< HEAD
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Función para crear nueva etiqueta
        function createNewTag() {
            const input = document.getElementById('new-tag-input');
            const name = input.value.trim();
            
            if (name) {
                createTag(name).then(newTag => {
                    if (newTag) {
                        input.value = '';
                        showSuccess('Etiqueta creada exitosamente');
                    }
                });
            } else {
                showError('El nombre de la etiqueta no puede estar vacío');
            }
        }
=======
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

            // Cargar lugares y gimcanas en los selectores
            fetch('/api/places')
                .then(response => response.json())
                .then(places => {
                    const placeSelect = document.getElementById('cp-place');
                    places.forEach(place => {
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    });
                });

            fetch('/api/gimcanas')
                .then(response => response.json())
                .then(gimcanas => {
                    const gimcanaSelect = document.getElementById('cp-gimcana');
                    gimcanas.forEach(gimcana => {
                        const option = document.createElement('option');
                        option.value = gimcana.id;
                        option.textContent = gimcana.name;
                        gimcanaSelect.appendChild(option);
                    });
                });

            // Manejar el envío del formulario
            document.getElementById('checkpointForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                const data = {
                    place_id: formData.get('place_id'),
                    gimcana_id: formData.get('gimcana_id'),
                    challenge: formData.get('challenge'),
                    clue: formData.get('clue'),
                    order: parseInt(formData.get('order'))
                };

                fetch('/api/checkpoints', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Error al crear el punto de control');
                        });
                    }
                    return response.json();
                })
                .then(checkpoint => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Punto de control creado con éxito',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadCheckpoints();
                    this.reset();
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al crear el punto de control',
                        text: error.message
                    });
                });
            });
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

                const data = {
                    name: formData.get('name'),
                    description: formData.get('description'),
                    max_groups: parseInt(formData.get('max_groups')),
                    max_users_per_group: parseInt(formData.get('max_users_per_group'))
                };

                fetch('/gimcanas', {
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
                        'La gimcana ha sido añadida correctamente.',
                        'success'
                    );
                    loadGimcanas();
                    document.getElementById('gimcanaForm').reset();
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al añadir la gimcana',
                        text: error.message
                    });
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
            fetch('/gimcanas', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar las gimcanas');
                }
                return response.json();
            })
            .then(gimcanas => {
                const gimcanasList = document.getElementById('gimcanasList');
                gimcanasList.innerHTML = '';
                
                gimcanas.forEach(gimcana => {
                    const gimcanaElement = document.createElement('div');
                    gimcanaElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                    gimcanaElement.innerHTML = `
                        <h3 class="font-bold">${gimcana.name}</h3>
                        <p class="text-gray-600">${gimcana.description}</p>
                        <div class="mt-2">
                            <button onclick="deleteGimcana(${gimcana.id})" class="text-red-500 hover:text-red-700">
                                Eliminar
                            </button>
                            <button onclick="openEditGimcanaModal(${gimcana.id})" class="text-blue-500 hover:text-blue-700 ml-2">
                                Editar
                            </button>
                        </div>
                    `;
                    gimcanasList.appendChild(gimcanaElement);
                });
            })
            .catch(error => {
                console.error('Error:', error);
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
                    fetch(`/gimcanas/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.error || 'Error al eliminar la gimcana');
                            });
                        }
                        return response.json();
                    })
                    .then(() => {
                        Swal.fire(
                            '¡Eliminado!',
                            'La gimcana ha sido eliminada.',
                            'success'
                        );
                        loadGimcanas();
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al eliminar la gimcana',
                            text: error.message
                        });
                    });
                }
            });
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

        function openEditGimcanaModal(id) {
            fetch(`/gimcanas/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar la gimcana');
                    }
                    return response.json();
                })
                .then(gimcana => {
                    document.getElementById('edit-gimcana-id').value = gimcana.id;
                    document.getElementById('edit-gimcana-name').value = gimcana.name;
                    document.getElementById('edit-gimcana-description').value = gimcana.description;
                    document.getElementById('edit-gimcana-max-groups').value = gimcana.max_groups;
                    document.getElementById('edit-gimcana-max-users-per-group').value = gimcana.max_users_per_group;

                    document.getElementById('editGimcanaModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al cargar la gimcana',
                        text: error.message
                    });
                });
        }

        document.getElementById('editGimcanaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            const data = {
                id: formData.get('id'),
                name: formData.get('name'),
                description: formData.get('description'),
                max_groups: parseInt(formData.get('max_groups')),
                max_users_per_group: parseInt(formData.get('max_users_per_group'))
            };

            fetch(`/gimcanas/${data.id}`, {
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
                        throw new Error(err.message || 'Error al actualizar la gimcana');
                    });
                }
                return response.json();
            })
            .then(gimcana => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gimcana actualizada con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                closeEditGimcanaModal();
                loadGimcanas();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar la gimcana',
                    text: error.message
                });
            });
        });

        function closeEditGimcanaModal() {
            document.getElementById('editGimcanaModal').classList.add('hidden');
        }
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
    </script>
</body>
</html>