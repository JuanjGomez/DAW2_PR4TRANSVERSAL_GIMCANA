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
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1001;
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
            margin-top: 80px;
            z-index: 1000;
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
            z-index: 10000;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            position: relative;
            z-index: 10001;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
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
            <button id="favoritesBtn" class="map-button" onclick="showFavorites()">Favoritos (0)</button>
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
    <div id="placeDetailsModal" class="hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <h2 id="placeName" class="text-xl font-bold mb-2"></h2>
            <p id="placeAddress" class="text-gray-600 mb-4"></p>
            <p id="placeDescription" class="text-gray-600 mb-4"></p>
            <div class="flex justify-end gap-2">
                <button id="closePlaceModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cerrar</button>
                <button id="addToFavorites" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Añadir a favoritos</button>
            </div>
        </div>
    </div>

    <!-- Modal para lugares favoritos -->
    <div id="favoritesModal" class="hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Mis Lugares Favoritos</h2>
                <button id="closeFavoritesModal" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="favoritesList" class="space-y-4">
                <!-- Los lugares favoritos se cargarán dinámicamente aquí -->
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
        let favoritePlaces = new Set(); // Almacenará los IDs de los lugares favoritos

        // Función para cargar los places
        async function loadPlaces() {
            try {
                const response = await fetch('/api/places', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('Error al cargar los lugares');
                }
                
                const data = await response.json();
                places = data;
                // Asegurarse de que cada place tenga la propiedad tags
                places = places.map(place => ({
                    ...place,
                    tags: place.tags || [] // Si tags es undefined, se asigna un array vacío
                }));
                updateMapMarkers();
            } catch (error) {
                console.error('Error cargando places:', error);
                if (error.message.includes('Unexpected token')) {
                    window.location.href = '/login';
                }
            }
        }

        // Función para cargar los tags
        async function loadTags() {
            console.log('Cargando tags...');
            try {
                const response = await fetch('/api/tags', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('Error al cargar los tags');
                }
                
                const tags = await response.json();
                console.log('Tags cargados:', tags);
                const tagsList = document.getElementById('tagsList');
                if (tagsList) {
                    tagsList.innerHTML = tags.map(tag => `
                        <div class="tag-chip ${selectedTags.has(tag.id) ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'} 
                            px-4 py-2 rounded-full cursor-pointer transition-colors duration-200"
                            data-id="${tag.id}" onclick="toggleTag(${tag.id})">
                            ${tag.name}
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error cargando tags:', error);
                if (error.message.includes('Unexpected token')) {
                    window.location.href = '/login';
                }
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

        // Función para mostrar los lugares favoritos
        function showFavorites() {
            // Filtrar los lugares favoritos
            const favoritePlacesList = places.filter(place => favoritePlaces.has(place.id));
            
            // Limpiar marcadores existentes
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Añadir solo los marcadores de lugares favoritos
            favoritePlacesList.forEach(place => {
                const marker = L.marker([place.latitude, place.longitude]).addTo(map);
                marker.bindPopup(`<b>${place.name}</b><br>${place.address}`);
                
                // Agregar evento click al marcador
                marker.on('click', () => {
                    showPlaceDetails(place, true); // true indica que es un lugar favorito
                });
                
                markers.push(marker);
            });

            // Actualizar el estado del botón de favoritos
            const favoritesBtn = document.getElementById('favoritesBtn');
            if (favoritesBtn) {
                favoritesBtn.classList.toggle('bg-blue-500');
                favoritesBtn.classList.toggle('bg-gray-700');
            }
        }

        // Función para mostrar los detalles del lugar
        function showPlaceDetails(place, isFavorite = false) {
            console.log('Mostrando detalles del lugar:', place);
            
            // Actualizar el contenido del modal
            document.getElementById('placeName').textContent = place.name;
            document.getElementById('placeAddress').textContent = place.address;
            document.getElementById('placeDescription').textContent = place.description || 'Sin descripción';
            
            // Obtener los botones del modal
            const closeBtn = document.getElementById('closePlaceModal');
            const actionBtn = document.getElementById('addToFavorites');
            
            if (isFavorite) {
                // Si es un lugar favorito, cambiar el botón a "Eliminar"
                actionBtn.textContent = 'Eliminar de favoritos';
                actionBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                actionBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                actionBtn.onclick = () => removeFromFavorites(place.id);
            } else {
                // Si no es favorito, mostrar el botón de añadir a favoritos
                actionBtn.textContent = favoritePlaces.has(place.id) ? 'En favoritos' : 'Añadir a favoritos';
                actionBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                actionBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
                actionBtn.disabled = favoritePlaces.has(place.id);
                actionBtn.onclick = () => addPlaceToFavorites(place.id);
            }
            
            // Mostrar el modal
            const modal = document.getElementById('placeDetailsModal');
            modal.classList.remove('hidden');
        }

        // Función para cargar los lugares favoritos
        async function loadFavoritePlaces() {
            try {
                const response = await fetch('/api/favorite-places', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('Error al cargar los lugares favoritos');
                }
                
                const data = await response.json();
                favoritePlaces = new Set(data.map(place => place.id));
                updateFavoritesButton();
            } catch (error) {
                console.error('Error cargando lugares favoritos:', error);
            }
        }

        // Función para actualizar el botón de favoritos
        function updateFavoritesButton() {
            const favoritesBtn = document.getElementById('favoritesBtn');
            if (favoritesBtn) {
                favoritesBtn.innerHTML = `Favoritos (${favoritePlaces.size})`;
            }
        }

        // Función para añadir un lugar a favoritos
        async function addPlaceToFavorites(placeId) {
            try {
                const response = await fetch('/api/favorite-places', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ place_id: placeId })
                });

                if (!response.ok) {
                    throw new Error('Error al añadir a favoritos');
                }

                favoritePlaces.add(placeId);
                updateFavoritesButton();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Añadido a favoritos!',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Actualizar el botón en el modal
                const addToFavoritesBtn = document.getElementById('addToFavorites');
                if (addToFavoritesBtn) {
                    addToFavoritesBtn.disabled = true;
                    addToFavoritesBtn.textContent = 'En favoritos';
                    addToFavoritesBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                    addToFavoritesBtn.classList.add('bg-green-500', 'cursor-not-allowed');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo añadir a favoritos'
                });
            }
        }

        // Función para eliminar un lugar de favoritos
        async function removeFromFavorites(placeId) {
            try {
                const response = await fetch(`/api/favorite-places/${placeId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al eliminar de favoritos');
                }

                favoritePlaces.delete(placeId);
                updateFavoritesButton();
                
                // Cerrar el modal
                document.getElementById('placeDetailsModal').classList.add('hidden');
                
                // Actualizar los marcadores si estamos en la vista de favoritos
                const favoritesBtn = document.getElementById('favoritesBtn');
                if (favoritesBtn && favoritesBtn.classList.contains('bg-blue-500')) {
                    showFavorites();
                }
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado de favoritos!',
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar de favoritos'
                });
            }
        }

        // Eventos para los modales
        document.getElementById('closePlaceModal').addEventListener('click', () => {
            document.getElementById('placeDetailsModal').classList.add('hidden');
        });

        document.getElementById('closeFavoritesModal').addEventListener('click', () => {
            document.getElementById('favoritesModal').classList.add('hidden');
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

        // Cargar lugares favoritos al iniciar
        loadFavoritePlaces();

        // Cargar los places al iniciar
        loadPlaces();
    </script>
    <script src="{{ asset('js/toolsUser.js') }}"></script>
</body>
</html>