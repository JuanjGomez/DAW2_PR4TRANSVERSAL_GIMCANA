<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/userIndex.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #map {
            height: 100vh;
            width: 100%;
            position: relative;
        }
        .filters-container {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 90%;
            max-width: 500px;
        }
        .filters-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .filters-tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .filters-tab {
            flex-grow: 1;
            text-align: center;
            padding: 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        .filters-tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            font-weight: 600;
        }
        .map-buttons-container {
            position: absolute;
            top: 0px; /* Moved lower */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .map-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .map-button {
            background-color: #1f2937;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .map-button:hover {
            background-color: #374151;
        }
        #filtersSection {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 10px;
        }
        .important {
            background-color: darkred;
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .important:hover {
            background-color: #a00;
        }
        .tags-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        .tag-chip {
            background-color: #f3f4f6;
            color: #4b5563;
            padding: 8px 12px;
            border-radius: 9999px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tag-chip:hover {
            background-color: #e5e7eb;
        }
        .tag-chip.selected {
            background-color: #3b82f6;
            color: white;
        }
        .apply-filters-btn {
            width: 100%;
            padding: 12px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }
        .apply-filters-btn:hover {
            background-color: #2563eb;
        }
        .hidden {
            display: none !important;
        }
        #placeDetailsModal {
            z-index: 10000; /* Asegúrate de que sea mayor que otros elementos */
        }
    </style>
</head>
<body class="m-0 p-0">
    <!-- Contenedor de botones y filtros -->
    <div class="map-buttons-container">
        <!-- Botones superiores -->
        <div class="map-buttons">
            <button id="lobbiesBtn" class="map-button">Gimcanas</button>
            <button id="filtrosBtn" class="map-button">Filtros</button>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="important">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>

        <!-- Sección de filtros (ahora debajo de los botones) -->
        <div id="filtersSection" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Filtrar por Tags</h2>
            </div>
            <div id="tagsList" class="flex flex-wrap gap-2">
                <!-- Los tags se cargarán dinámicamente aquí como "chips" -->
            </div>
            <div class="mt-4">
                <button id="applyFilters" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Aplicar Filtros</button>
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar Gimcana -->
    <div id="gimcanaModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
        <div id="modal-content" class="bg-white p-6 rounded-lg shadow-lg relative">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Selecciona una Gimcana</h2>
                <button id="closeModal" class="text-red-500 hover:text-red-700 text-2xl font-bold">&times;</button>
            </div>
            <div id="gimcanaList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Lista de gimcanas se llenará dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal para detalles de Gimcana -->
    <div id="gimcanaDetailsModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
        <div id="gimcanaDetailsContent" class="bg-white p-6 rounded-lg shadow-lg">
            <!-- Contenido se llenará dinámicamente -->
        </div>
    </div>

    <!-- Modal para detalles del lugar -->
    <div id="placeDetailsModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center" inert>
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 id="placeName" class="text-xl font-bold mb-2"></h2>
            <p id="placeAddress" class="text-gray-600 mb-4"></p>
            <p id="placeDescription" class="text-gray-600 mb-4"></p>
            <div class="flex justify-end gap-2">
                <button id="closePlaceModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cerrar</button>
                <button id="addToFavorites" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Añadir a favoritos</button>
            </div>
        </div>
    </div>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa
        const map = L.map('map').setView([41.3851, 2.1734], 13);

        // Añadir capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marcador del usuario
        let userMarker;

        // Obtener y mostrar la ubicación del usuario
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;

                    // Si ya existe un marcador, actualizar su posición
                    if (userMarker) {
                        userMarker.setLatLng([latitude, longitude]);
                    } else {
                        // Crear nuevo marcador circular
                        userMarker = L.circleMarker([latitude, longitude], {
                            radius: 10,
                            fillColor: '#4285F4', // Color azul de Google Maps
                            color: '#ffffff',     // Borde blanco
                            weight: 2,            // Grosor del borde
                            opacity: 1,
                            fillOpacity: 1
                        }).addTo(map);
                        map.setView([latitude, longitude], 15);
                    }
                },
                (error) => {
                    console.error('Error al obtener la ubicación:', error);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 27000
                }
            );
        }

        // Eventos para los botones
        document.getElementById('lobbiesBtn').addEventListener('click', () => {
            // Aquí irá la lógica para abrir el modal de Lobbies
            console.log('Abrir modal de Lobbies');
        });

        // Variables globales
        let places = []; // Almacenará todos los places
        let markers = []; // Almacenará los marcadores del mapa
        let selectedTags = new Set(); // Almacenará los tags seleccionados

        // Función para cargar los tags
        async function loadTags() {
            console.log('Cargando tags...'); // Mensaje de depuración
            try {
                const response = await fetch('/api/tags');
                console.log('Respuesta recibida:', response); // Mensaje de depuración
                if (!response.ok) {
                    throw new Error('Error al cargar los tags');
                }
                const tags = await response.json();
                console.log('Tags cargados:', tags); // Mensaje de depuración
                const tagsList = document.getElementById('tagsList');
                tagsList.innerHTML = tags.map(tag => `
                    <div class="tag-chip ${selectedTags.has(tag.id) ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'} 
                        px-4 py-2 rounded-full cursor-pointer transition-colors duration-200"
                        data-id="${tag.id}" onclick="toggleTag(${tag.id})">
                        ${tag.name}
                    </div>
                `).join('');
                console.log('Tags generados en el DOM:', tagsList.innerHTML); // Mensaje de depuración
            } catch (error) {
                console.error('Error cargando tags:', error);
                alert('Error al cargar los tags. Por favor, inténtalo de nuevo.');
            }
        }

        // Función para alternar la selección de un tag
        function toggleTag(tagId) {
            if (selectedTags.has(tagId)) {
                selectedTags.delete(tagId);
            } else {
                selectedTags.add(tagId);
            }
            // Actualizar la apariencia del chip
            const tagChip = document.querySelector(`.tag-chip[data-id="${tagId}"]`);
            if (tagChip) {
                tagChip.classList.toggle('bg-blue-500');
                tagChip.classList.toggle('text-white');
                tagChip.classList.toggle('bg-gray-200');
                tagChip.classList.toggle('text-gray-700');
            }
        }

        // Función para cargar los places
        async function loadPlaces() {
            try {
                const response = await fetch('/api/places');
                places = await response.json();
                // Asegurarse de que cada place tenga la propiedad tags
                places = places.map(place => ({
                    ...place,
                    tags: place.tags || [] // Si tags es undefined, se asigna un array vacío
                }));
                updateMapMarkers();
            } catch (error) {
                console.error('Error cargando places:', error);
            }
        }

        // Función para actualizar los marcadores en el mapa
        function updateMapMarkers() {
            // Limpiar marcadores existentes
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Filtrar places según los tags seleccionados
            const filteredPlaces = places.filter(place => {
                if (selectedTags.size === 0) return true;
                return place.tags && place.tags.some(tag => selectedTags.has(tag.id));
            });

            // Añadir marcadores al mapa
            filteredPlaces.forEach(place => {
                const marker = L.marker([place.latitude, place.longitude]).addTo(map);
                marker.bindPopup(`<b>${place.name}</b><br>${place.address}`);
                
                // Agregar evento click al marcador
                marker.on('click', () => {
                    showPlaceDetails(place);
                });
                
                markers.push(marker);
            });
        }

        // Función para mostrar los detalles del lugar
        function showPlaceDetails(place) {
            console.log('Mostrando detalles del lugar:', place);
            
            // Actualizar el contenido del modal
            document.getElementById('placeName').textContent = place.name;
            document.getElementById('placeAddress').textContent = place.address;
            document.getElementById('placeDescription').textContent = place.description || 'Sin descripción';
            
            // Configurar el botón de favoritos
            const addToFavoritesBtn = document.getElementById('addToFavorites');
            addToFavoritesBtn.onclick = () => addPlaceToFavorites(place.id);
            
            // Mostrar el modal
            const modal = document.getElementById('placeDetailsModal');
            modal.classList.remove('hidden');
            
            // Verificar si el modal se está mostrando
            console.log('Modal visibility:', modal.classList.contains('hidden') ? 'hidden' : 'visible');
        }

        // Función para añadir un lugar a favoritos
        async function addPlaceToFavorites(placeId) {
            try {
                const response = await fetch('/api/favorite-places', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ place_id: placeId })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al añadir a favoritos');
                }

                const data = await response.json();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Añadido a favoritos!',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Deshabilitar el botón después de añadir a favoritos
                const addToFavoritesBtn = document.getElementById('addToFavorites');
                addToFavoritesBtn.disabled = true;
                addToFavoritesBtn.textContent = 'En favoritos';
                addToFavoritesBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                addToFavoritesBtn.classList.add('bg-green-500', 'cursor-not-allowed');
                
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        }

        // Cerrar el modal de detalles
        document.getElementById('closePlaceModal').addEventListener('click', (e) => {
            e.preventDefault();
            const modal = document.getElementById('placeDetailsModal');
            modal.classList.add('hidden');
            modal.removeAttribute('inert'); // Asegurarse de que el modal sea interactivo
        });

        // Evento para abrir/cerrar la sección de filtros
        document.getElementById('filtrosBtn').addEventListener('click', () => {
            const filtersSection = document.getElementById('filtersSection');
            filtersSection.classList.toggle('hidden');
            if (!filtersSection.classList.contains('hidden')) {
                loadTags();
            }
        });

        // Evento para aplicar los filtros
        document.getElementById('applyFilters').addEventListener('click', () => {
            updateMapMarkers();
        });

        // Cargar los places al iniciar
        loadPlaces();
    </script>
    <script src="{{ asset('js/toolsUser.js') }}"></script>
</body>
</html>