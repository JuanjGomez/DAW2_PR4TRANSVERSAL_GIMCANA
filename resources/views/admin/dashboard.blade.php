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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Panel de Administrador</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
                    Logout
                </button>
            </form>
        </div>
        
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
                            <input type="hidden" id="icon" name="icon" value="default-marker">
                            <div class="icon-selector mt-2 flex flex-wrap gap-2">
                                <div class="icon-option cursor-pointer p-2 border rounded selected" data-value="default-marker">
                                    <img src="https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png" alt="Predeterminado" class="w-6 h-auto">
                                    <span class="text-xs block text-center mt-1">Predeterminado</span>
                                </div>
                                <div class="icon-option cursor-pointer p-2 border rounded" data-value="museum-marker">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png" alt="Museo" class="w-6 h-auto">
                                    <span class="text-xs block text-center mt-1">Museo</span>
                                </div>
                                <div class="icon-option cursor-pointer p-2 border rounded" data-value="monument-marker">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png" alt="Monumento" class="w-6 h-auto">
                                    <span class="text-xs block text-center mt-1">Monumento</span>
                                </div>
                                <div class="icon-option cursor-pointer p-2 border rounded" data-value="restaurant-marker">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png" alt="Restaurante" class="w-6 h-auto">
                                    <span class="text-xs block text-center mt-1">Restaurante</span>
                                </div>
                                <div class="icon-option cursor-pointer p-2 border rounded" data-value="park-marker">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png" alt="Parque" class="w-6 h-auto">
                                    <span class="text-xs block text-center mt-1">Parque</span>
                                </div>
                            </div>
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
                        
                        <!-- Sección de respuestas -->
                            <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Respuestas</label>
                            <div id="answers-container" class="space-y-2">
                                <!-- Las respuestas se añadirán aquí dinámicamente -->
                            </div>
                            <div class="mt-2 flex justify-between">
                                <button type="button" onclick="addAnswer()" class="bg-green-500 text-white py-1 px-3 rounded-lg hover:bg-green-600">
                                    Añadir Respuesta
                                </button>
                                <span class="text-sm text-gray-500">Selecciona la respuesta correcta</span>
                            </div>
                        </div>
                        
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                                Añadir Punto de Control
                            </button>
                    </form>
                </div>

                <!-- Lista de checkpoints -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Puntos de Control Guardados</h2>
                    <div id="checkpointsList" class="space-y-4">
                        <!-- Los checkpoints se cargarán aquí dinámicamente -->
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
                        <input type="hidden" id="edit-icon" name="icon" value="default-marker">
                        <div class="edit-icon-selector mt-2 flex flex-wrap gap-2">
                            <div class="icon-option cursor-pointer p-2 border rounded selected" data-value="default-marker">
                                <img src="https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png" alt="Predeterminado" class="w-6 h-auto">
                                <span class="text-xs block text-center mt-1">Predeterminado</span>
                            </div>
                            <div class="icon-option cursor-pointer p-2 border rounded" data-value="museum-marker">
                                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png" alt="Museo" class="w-6 h-auto">
                                <span class="text-xs block text-center mt-1">Museo</span>
                            </div>
                            <div class="icon-option cursor-pointer p-2 border rounded" data-value="monument-marker">
                                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png" alt="Monumento" class="w-6 h-auto">
                                <span class="text-xs block text-center mt-1">Monumento</span>
                            </div>
                            <div class="icon-option cursor-pointer p-2 border rounded" data-value="restaurant-marker">
                                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png" alt="Restaurante" class="w-6 h-auto">
                                <span class="text-xs block text-center mt-1">Restaurante</span>
                            </div>
                            <div class="icon-option cursor-pointer p-2 border rounded" data-value="park-marker">
                                <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png" alt="Parque" class="w-6 h-auto">
                                <span class="text-xs block text-center mt-1">Parque</span>
                            </div>
                        </div>
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

        <!-- Modal para editar Checkpoint -->
        <div id="editCheckpointModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">Editar Punto de Control</h2>
                <form id="editCheckpointForm">
                    <input type="hidden" id="edit-checkpoint-id" name="id">
                    
                        <div class="mb-4">
                            <label for="edit-checkpoint-place" class="block text-gray-700">Lugar</label>
                            <select id="edit-checkpoint-place" name="place_id" class="w-full px-4 py-2 border rounded-lg" required>
                                <option value="">Selecciona un lugar</option>
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                    
                        <div class="mb-4">
                            <label for="edit-checkpoint-gimcana" class="block text-gray-700">Gimcana</label>
                            <select id="edit-checkpoint-gimcana" name="gimcana_id" class="w-full px-4 py-2 border rounded-lg" required>
                                <option value="">Selecciona una gimcana</option>
                                <!-- Se llenará dinámicamente -->
                            </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit-checkpoint-challenge" class="block text-gray-700">Reto</label>
                        <textarea id="edit-checkpoint-challenge" name="challenge" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
    </div>

                    <div class="mb-4">
                        <label for="edit-checkpoint-clue" class="block text-gray-700">Pista</label>
                        <textarea id="edit-checkpoint-clue" name="clue" rows="2" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                            </div>
                    
                    <div class="mb-4">
                        <label for="edit-checkpoint-order" class="block text-gray-700">Orden</label>
                        <input type="number" id="edit-checkpoint-order" name="order" min="1" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    
                    <!-- Sección de respuestas para edición -->
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Respuestas</label>
                        <div id="edit-answers-container" class="space-y-2">
                            <!-- Las respuestas se añadirán aquí dinámicamente -->
                        </div>
                        <div class="mt-2 flex justify-between">
                            <button type="button" onclick="addEditAnswer()" class="bg-green-500 text-white py-1 px-3 rounded-lg hover:bg-green-600">
                                Añadir Respuesta
                            </button>
                            <span class="text-sm text-gray-500">Selecciona la respuesta correcta</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditCheckpointModal()" class="bg-gray-300 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                            Guardar Cambios
                        </button>
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

        // Inicializar mapa
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadPlaces();
            // loadGimcanas();
            loadCheckpoints();
            setupForms();
            showTab('places');
            setupIconSelection();
            console.log("Configuración de iconos inicializada");
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
            // Ocultar todas las pestañas
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Mostrar la pestaña seleccionada
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Actualizar pestaña activa
            activeTab = tabName;
            
            // Cargar datos específicos para cada pestaña
            if (tabName === 'gimcanas') {
                console.log("Pestaña de gimcanas seleccionada, cargando datos...");
                loadGimcanas();
            } else if (tabName === 'checkpoints') {
                loadGimcanas();  // Recargar gimcanas para actualizar el selector
                loadPlaces();    // También recargar lugares
                loadCheckpoints();
            } else if (tabName === 'places') {
                loadPlaces();
            }
            
            // Actualizar botones de navegación
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

            // Añadir esta función para el formulario de checkpoints
            const checkpointForm = document.getElementById('checkpointForm');
            if (checkpointForm) {
                checkpointForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const placeId = document.getElementById('cp-place').value;
                    const gimcanaId = document.getElementById('cp-gimcana').value;
                    const challenge = document.getElementById('cp-challenge').value;
                    const clue = document.getElementById('cp-clue').value;
                    const order = document.getElementById('cp-order').value;
                    
                    // Obtener las respuestas
                    const answers = Array.from(document.querySelectorAll('#answers-container input[name="answers[]"]'))
                        .map(input => input.value);
                    
                    // Obtener la respuesta correcta
                    const correctAnswerRadio = document.querySelector('input[name="correct_answer"]:checked');
                    const correctAnswer = correctAnswerRadio ? parseInt(correctAnswerRadio.value) : 0;
                    
                    // Validaciones
                    if (answers.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Respuestas incompletas',
                            text: 'Añade al menos una respuesta al reto'
                        });
                        return;
                    }
                    
                    if (!placeId || !gimcanaId || !challenge || !clue || !order) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Campos incompletos',
                            text: 'Por favor completa todos los campos'
                        });
                        return;
                    }
                    
                    // Datos para enviar
                    const formData = {
                        place_id: placeId,
                        gimcana_id: gimcanaId,
                        challenge: challenge,
                        clue: clue,
                        order: order,
                        answers: answers,
                        correct_answer: correctAnswer
                    };
                    
                    // Enviar datos
                    fetch('/api/checkpoints', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.error || 'Error al guardar el punto de control');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Limpiar el formulario pero mantener los selects
                        document.getElementById('cp-challenge').value = '';
                        document.getElementById('cp-clue').value = '';
                        document.getElementById('cp-order').value = '';
                        
                        // Limpiar las respuestas excepto una
                        const answersContainer = document.getElementById('answers-container');
                        answersContainer.innerHTML = '';
                        addAnswer(); // Añadir una respuesta vacía
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Punto de control añadido correctamente'
                        });
                        
                        loadCheckpoints();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    });
                });
            }
        }

        function loadPlaces() {
            // Definir iconos personalizados para el mapa
            const markerIcons = {
                'default-marker': L.icon({
                    iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                'museum-marker': L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                'monument-marker': L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                'restaurant-marker': L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                'park-marker': L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                })
            };

            // Limpiar marcadores existentes
            if (map && markers) {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
            }
            
            return fetch('/places', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los lugares');
                }
                return response.json();
            })
            .then(data => {
                // Asignar a la variable global
                places = data;
                console.log("Lugares cargados:", places.length);
                
                // Actualizar la lista de lugares
                const placesList = document.getElementById('placesList');
                if (placesList) {
                    placesList.innerHTML = '';
                    
                    places.forEach(place => {
                        // Añadir a la lista
                        const placeElement = document.createElement('div');
                        placeElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                        placeElement.innerHTML = `
                            <h3 class="font-bold">${place.name}</h3>
                            <p class="text-gray-600">${place.address}</p>
                            <p class="text-sm text-gray-500">Lat: ${place.latitude}, Lng: ${place.longitude}</p>
                            <div class="mt-2">
                                <button onclick="openEditModal(${place.id})" class="text-blue-500 hover:text-blue-700 ml-2">Editar</button>
                                <button onclick="deletePlace(${place.id})" class="text-red-500 hover:text-red-700">Eliminar</button>
                            </div>
                        `;
                        placesList.appendChild(placeElement);
                    });
                }
                
                // Actualizar el select de lugares
                const placeSelect = document.getElementById('cp-place');
                if (placeSelect) {
                    placeSelect.innerHTML = '<option value="">Selecciona un lugar</option>';
                    
                    places.forEach(place => {
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    });
                }
                
                // Añadir marcadores al mapa
                if (map) {
                    places.forEach(place => {
                        // Crear icono basado en el valor guardado
                        let iconUrl;
                        switch(place.icon) {
                            case 'default-marker':
                                iconUrl = 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png';
                                break;
                            case 'museum-marker':
                                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png';
                                break;
                            case 'monument-marker':
                                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png';
                                break;
                            case 'restaurant-marker':
                                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png';
                                break;
                            case 'park-marker':
                                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png';
                                break;
                            default:
                                iconUrl = 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png';
                        }
                        
                        const icon = L.icon({
                            iconUrl: iconUrl,
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                            shadowSize: [41, 41]
                        });
                        
                        const marker = L.marker([place.latitude, place.longitude], { icon: icon })
                            .addTo(map)
                            .bindPopup(`<b>${place.name}</b><br>${place.address}`);
                        markers.push(marker);
                    });
                }
                
                return places;
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
            console.log("Cargando gimcanas...");
            fetch('/gimcanas')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar las gimcanas');
                    }
                    return response.json();
                })
                .then(data => {
                    // Guardar en variable global
                    window.gimcanas = data;
                    
                    // Actualizar la lista de gimcanas
                    const gimcanasList = document.getElementById('gimcanasList');
                    if (gimcanasList) {
                        gimcanasList.innerHTML = '';
                        
                        if (data.length === 0) {
                            gimcanasList.innerHTML = '<p class="text-gray-500">No hay gimcanas registradas</p>';
                            return;
                        }
                        
                        data.forEach(gimcana => {
                            const gimcanaElement = document.createElement('div');
                            gimcanaElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
                            gimcanaElement.innerHTML = `
                                <h3 class="font-bold">${gimcana.name}</h3>
                                <p class="text-gray-600">${gimcana.description || 'Sin descripción'}</p>
                                <p class="text-sm text-gray-500">Grupos: ${gimcana.max_groups}, Usuarios por grupo: ${gimcana.max_users_per_group}</p>
                                <div class="mt-2">
                                    <button onclick="deleteGimcana(${gimcana.id})" class="text-red-500 hover:text-red-700 mr-2">
                                        Eliminar
                                    </button>
                                    <button onclick="openEditGimcanaModal(${gimcana.id})" class="text-blue-500 hover:text-blue-700">
                                        Editar
                                    </button>
                                </div>
                            `;
                            gimcanasList.appendChild(gimcanaElement);
                        });
                    } else {
                        console.error("Elemento gimcanasList no encontrado");
                    }
                    
                    // Actualizar selector de gimcanas en el formulario de checkpoints
                    const gimcanaSelect = document.getElementById('cp-gimcana');
                    if (gimcanaSelect) {
                        // Guardar la selección actual si existe
                        const currentSelection = gimcanaSelect.value;
                        
                        gimcanaSelect.innerHTML = '<option value="">Selecciona una gimcana</option>';
                        
                        data.forEach(gimcana => {
                            const option = document.createElement('option');
                            option.value = gimcana.id;
                            option.textContent = gimcana.name;
                            gimcanaSelect.appendChild(option);
                        });
                        
                        // Restaurar la selección anterior si es posible
                        if (currentSelection) {
                            gimcanaSelect.value = currentSelection;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error cargando gimcanas:', error);
                    const gimcanasList = document.getElementById('gimcanasList');
                    if (gimcanasList) {
                        gimcanasList.innerHTML = '<p class="text-red-500">Error al cargar las gimcanas: ' + error.message + '</p>';
                    }
                });
        }

        function loadCheckpoints() {
            console.log("Cargando checkpoints...");
            fetch('/api/checkpoints')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar los checkpoints');
                    }
                    return response.json();
                })
                .then(checkpoints => {
                    console.log("Checkpoints cargados:", checkpoints);
                    
                    const checkpointsList = document.getElementById('checkpointsList');
                    if (!checkpointsList) {
                        console.error("No se encontró el elemento checkpointsList");
                        return;
                    }
                    
                    checkpointsList.innerHTML = '';
                    
                    if (checkpoints.length === 0) {
                        checkpointsList.innerHTML = '<p class="text-gray-500">No hay puntos de control registrados.</p>';
                        return;
                    }
                    
                    checkpoints.forEach(checkpoint => {
                        // Usar directamente los datos relacionados de la respuesta
                        const checkpointElement = document.createElement('div');
                        checkpointElement.className = 'checkpoint-card p-4 border rounded-lg hover:bg-gray-50';
                        
                        const placeName = checkpoint.place ? checkpoint.place.name : 'Lugar no encontrado';
                        const gimcanaName = checkpoint.gimcana ? checkpoint.gimcana.name : 'Gimcana no encontrada';
                        
                        // Crear HTML para las respuestas, resaltando la correcta
                        let answersHTML = '';
                        if (checkpoint.answers && checkpoint.answers.length > 0) {
                            answersHTML = '<div class="mt-2"><strong>Respuestas:</strong><ul class="list-disc pl-5">';
                            checkpoint.answers.forEach((answer, index) => {
                                const isCorrect = index === checkpoint.correct_answer;
                                answersHTML += `<li class="${isCorrect ? 'text-green-600 font-bold' : ''}">${answer}${isCorrect ? ' ✓' : ''}</li>`;
                            });
                            answersHTML += '</ul></div>';
                        }
                        
                        checkpointElement.innerHTML = `
                            <h3 class="font-bold">${placeName} (Orden: ${checkpoint.order})</h3>
                            <p class="text-gray-600"><strong>Gimcana:</strong> ${gimcanaName}</p>
                            <p class="text-gray-600"><strong>Reto:</strong> ${checkpoint.challenge}</p>
                            <p class="text-gray-500"><strong>Pista:</strong> ${checkpoint.clue}</p>
                            ${answersHTML}
                            <div class="mt-2">
                                <button onclick="deleteCheckpoint(${checkpoint.id})" class="text-red-500 hover:text-red-700 mr-2">
                                    Eliminar
                                </button>
                                <button onclick="openEditCheckpointModal(${checkpoint.id})" class="text-blue-500 hover:text-blue-700">
                                    Editar
                                </button>
                            </div>
                        `;
                        
                        checkpointsList.appendChild(checkpointElement);
                        
                        // Si estamos en la pestaña de checkpoints, añadir marcador si tenemos lugar
                        if (checkpoint.place && activeTab === 'checkpoints') {
                            try {
                                const place = checkpoint.place;
                                const marker = L.marker([place.latitude, place.longitude])
                                    .bindPopup(`<b>${place.name}</b><br>Orden: ${checkpoint.order}<br>${checkpoint.clue}`)
                                    .addTo(map);
                                markers.push(marker);
                            } catch (e) {
                                console.error("Error al añadir marcador:", e);
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error cargando checkpoints:', error);
                    const checkpointsList = document.getElementById('checkpointsList');
                    if (checkpointsList) {
                        checkpointsList.innerHTML = '<p class="text-red-500">Error al cargar los puntos de control: ' + error.message + '</p>';
                    }
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
                    
                    // Seleccionar visualmente el icono correcto
                    const editIconSelector = document.querySelector('.edit-icon-selector');
                    editIconSelector.querySelectorAll('.icon-option').forEach(option => {
                        option.classList.remove('selected', 'bg-blue-100', 'border-blue-500');
                        if (option.dataset.value === place.icon) {
                            option.classList.add('selected', 'bg-blue-100', 'border-blue-500');
                        }
                    });

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
                    setupIconSelection();
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

        // Funciones para manejar las respuestas
        function addAnswer() {
            const answersContainer = document.getElementById('answers-container');
            const answerCount = answersContainer.children.length;
            
            const answerDiv = document.createElement('div');
            answerDiv.className = 'flex items-center space-x-2 bg-gray-50 p-2 rounded';
            
            answerDiv.innerHTML = `
                <input type="radio" name="correct_answer" value="${answerCount}" class="mr-2" ${answerCount === 0 ? 'checked' : ''}>
                <input type="text" name="answers[]" class="flex-grow px-3 py-2 border rounded-lg" placeholder="Respuesta" required>
                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAnswer(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            
            answersContainer.appendChild(answerDiv);
        }

        function removeAnswer(button) {
            const answerDiv = button.closest('div');
            answerDiv.remove();
            
            // Actualizar los valores de los inputs radio para mantener la coherencia
            const answersContainer = document.getElementById('answers-container');
            Array.from(answersContainer.querySelectorAll('input[type="radio"]')).forEach((radio, index) => {
                radio.value = index;
            });
        }

        function addEditAnswer() {
            const answersContainer = document.getElementById('edit-answers-container');
            const answerCount = answersContainer.children.length;
            
            const answerDiv = document.createElement('div');
            answerDiv.className = 'flex items-center space-x-2 bg-gray-50 p-2 rounded';
            
            answerDiv.innerHTML = `
                <input type="radio" name="edit_correct_answer" value="${answerCount}" class="mr-2" ${answerCount === 0 ? 'checked' : ''}>
                <input type="text" name="edit_answers[]" class="flex-grow px-3 py-2 border rounded-lg" placeholder="Respuesta" required>
                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeEditAnswer(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            
            answersContainer.appendChild(answerDiv);
        }

        function removeEditAnswer(button) {
            const answerDiv = button.closest('div');
            answerDiv.remove();
            
            // Actualizar los valores de los inputs radio para mantener la coherencia
            const answersContainer = document.getElementById('edit-answers-container');
            Array.from(answersContainer.querySelectorAll('input[type="radio"]')).forEach((radio, index) => {
                radio.value = index;
            });
        }

        // Inicialización después de cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Añadir una respuesta inicial al cargar el formulario
            const existingInit = initMap;
            initMap = function() {
                existingInit();
                addAnswer(); // Añadir respuesta inicial
            };
        });

        function setupIconSelection() {
            // Para el formulario principal
            document.querySelectorAll('.icon-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Obtener el contenedor padre
                    const container = this.closest('.icon-selector, .edit-icon-selector');
                    if (!container) return;
                    
                    // Quitar selección de todos los iconos en este contenedor
                    container.querySelectorAll('.icon-option').forEach(opt => {
                        opt.classList.remove('selected', 'bg-blue-100');
                    });
                    
                    // Añadir selección a este icono
                    this.classList.add('selected', 'bg-blue-100');
                    
                    // Actualizar el input correspondiente
                    const isEdit = container.classList.contains('edit-icon-selector');
                    const inputId = isEdit ? 'edit-icon' : 'icon';
                    document.getElementById(inputId).value = this.dataset.value;
                    
                    console.log(`Icono ${isEdit ? 'de edición ' : ''}seleccionado:`, this.dataset.value);
                });
            });
        }

        // Modificar openEditModal para ejecutar setupIconSelection después de cargar los datos
        const originalOpenEditModal = openEditModal;
        openEditModal = function(id) {
            originalOpenEditModal(id);
            setTimeout(setupIconSelection, 100); // Dar tiempo para que se actualice el DOM
        };

        // Añade esta función para abrir el modal de edición de checkpoint
        function openEditCheckpointModal(id) {
            console.log("Abriendo modal para editar checkpoint:", id);
            
            // Cargar los datos necesarios
            Promise.all([
                fetch('/places').then(res => res.json()),
                fetch('/gimcanas').then(res => res.json()),
                fetch(`/api/checkpoints/${id}`).then(res => res.json())
            ])
            .then(([places, gimcanas, checkpoint]) => {
                console.log("Datos cargados - Lugares:", places.length, "Gimcanas:", gimcanas.length, "Checkpoint:", checkpoint);
                
                // Actualizar selector de lugares
                const placeSelect = document.getElementById('edit-checkpoint-place');
                placeSelect.innerHTML = '<option value="">Selecciona un lugar</option>';
                
                places.forEach(place => {
                    const option = document.createElement('option');
                    option.value = place.id;
                    option.textContent = place.name;
                    placeSelect.appendChild(option);
                });
                
                // Actualizar selector de gimcanas
                const gimcanaSelect = document.getElementById('edit-checkpoint-gimcana');
                gimcanaSelect.innerHTML = '<option value="">Selecciona una gimcana</option>';
                
                gimcanas.forEach(gimcana => {
                    const option = document.createElement('option');
                    option.value = gimcana.id;
                    option.textContent = gimcana.name;
                    gimcanaSelect.appendChild(option);
                });
                
                // Ahora llenar el formulario con los datos del checkpoint
                document.getElementById('edit-checkpoint-id').value = checkpoint.id;
                document.getElementById('edit-checkpoint-challenge').value = checkpoint.challenge;
                document.getElementById('edit-checkpoint-clue').value = checkpoint.clue;
                document.getElementById('edit-checkpoint-order').value = checkpoint.order;
                document.getElementById('edit-checkpoint-place').value = checkpoint.place_id;
                document.getElementById('edit-checkpoint-gimcana').value = checkpoint.gimcana_id;
                
                // Cargar las respuestas existentes
                const answersContainer = document.getElementById('edit-answers-container');
                answersContainer.innerHTML = '';
                
                // Si hay respuestas, cargarlas
                if (checkpoint.answers && checkpoint.answers.length > 0) {
                    checkpoint.answers.forEach((answer, index) => {
                        const answerDiv = document.createElement('div');
                        answerDiv.className = 'flex items-center space-x-2 bg-gray-50 p-2 rounded';
                        
                        const isCorrect = index === checkpoint.correct_answer;
                        
                        answerDiv.innerHTML = `
                            <input type="radio" name="edit_correct_answer" value="${index}" 
                                class="mr-2" ${isCorrect ? 'checked' : ''}>
                            <input type="text" name="edit_answers[]" value="${answer}" 
                                class="flex-grow px-3 py-2 border rounded-lg" placeholder="Respuesta" required>
                            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeEditAnswer(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        `;
                        
                        answersContainer.appendChild(answerDiv);
                    });
                } else {
                    // Si no hay respuestas, crear al menos una por defecto
                    addEditAnswer();
                }
                
                // Mostrar el modal
                document.getElementById('editCheckpointModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error("Error preparando el modal de edición:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar los datos para editar el punto de control'
                });
            });
        }

        // Función para cerrar el modal de edición de checkpoint
        function closeEditCheckpointModal() {
            document.getElementById('editCheckpointModal').classList.add('hidden');
        }

        // Modificar el event listener del formulario de edición de checkpoint
        document.addEventListener('DOMContentLoaded', function() {
            // Código existente...
            
            // Añadir listener para el formulario de edición de checkpoint
            document.getElementById('editCheckpointForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const checkpointId = document.getElementById('edit-checkpoint-id').value;
                const placeId = document.getElementById('edit-checkpoint-place').value;
                const gimcanaId = document.getElementById('edit-checkpoint-gimcana').value;
                const challenge = document.getElementById('edit-checkpoint-challenge').value;
                const clue = document.getElementById('edit-checkpoint-clue').value;
                const order = document.getElementById('edit-checkpoint-order').value;
                
                // Obtener las respuestas editadas
                const answers = Array.from(document.querySelectorAll('#edit-answers-container input[name="edit_answers[]"]'))
                    .map(input => input.value);
                
                // Obtener la respuesta correcta editada
                const correctAnswerRadio = document.querySelector('input[name="edit_correct_answer"]:checked');
                const correctAnswer = correctAnswerRadio ? parseInt(correctAnswerRadio.value) : 0;
                
                // Validaciones
                if (answers.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Respuestas incompletas',
                        text: 'Añade al menos una respuesta al reto'
                    });
                    return;
                }
                
                if (!placeId || !gimcanaId || !challenge || !clue || !order) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor completa todos los campos'
                    });
                    return;
                }
                
                // Preparar los datos para el envío
                const data = {
                    place_id: placeId,
                    gimcana_id: gimcanaId,
                    challenge: challenge,
                    clue: clue,
                    order: parseInt(order),
                    answers: answers,
                    correct_answer: correctAnswer
                };
                
                console.log("Enviando datos al servidor:", data);
                
                // Hacer la petición al servidor usando AJAX
                fetch(`/api/checkpoints/${checkpointId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log("Respuesta recibida:", response.status);
                    
                    // Manejar respuesta no exitosa
                    if (!response.ok) {
                        if (response.headers.get('content-type')?.includes('application/json')) {
                            return response.json().then(errData => {
                                throw new Error(errData.error || 'Error al actualizar el punto de control');
                            });
                        } else {
                            // Si no es JSON, mostrar mensaje genérico
                            throw new Error(`Error del servidor: ${response.status}`);
                        }
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log("Checkpoint actualizado:", data);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Punto de control actualizado correctamente'
                    });
                    
                    closeEditCheckpointModal();
                    loadCheckpoints(); // Recargar la lista de checkpoints
                })
                .catch(error => {
                    console.error("Error actualizando checkpoint:", error);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al actualizar el punto de control',
                        text: error.message
                    });
                });
            });
        });
    </script>
</body>
</html>