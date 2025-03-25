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
        .distance-filter {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 250px;
            transform: translateX(calc(100% + 20px));
        }
        .distance-filter h3 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        .distance-controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .clear-filter {
            background-color: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .clear-filter:hover {
            background-color: #dc2626;
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
            padding: 0 15px;
        }
        .map-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .map-button {
            background-color: #1f2937;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
            white-space: nowrap;
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
            max-height: 80vh;
            overflow-y: auto;
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
            padding: 6px 12px;
            border-radius: 9999px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            white-space: nowrap;
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
        /* Media queries para dispositivos móviles */
        @media (max-width: 768px) {
            .map-buttons-container {
                top: 10px;
            }
            .map-button {
                padding: 6px 12px;
                font-size: 12px;
            }
            .distance-filter {
                position: fixed;
                top: auto;
                bottom: 20px;
                right: 20px;
                width: 200px;
                padding: 10px;
                z-index: 1002;
                transform: none;
            }
            #filtersSection {
                margin-top: 60px;
                padding: 10px;
                z-index: 1001;
            }
            .tag-chip {
                padding: 4px 8px;
                font-size: 12px;
            }
            .modal-content {
                width: 95%;
                margin: 10px;
                padding: 15px;
            }
        }
        /* Media queries para pantallas muy pequeñas */
        @media (max-width: 480px) {
            .map-buttons {
                gap: 4px;
            }
            .map-button {
                padding: 4px 8px;
                font-size: 11px;
            }
            .distance-filter {
                width: 180px;
                padding: 8px;
            }
            .distance-filter h3 {
                font-size: 14px;
            }
            .clear-filter {
                padding: 6px 12px;
                font-size: 12px;
            }
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
            <div class="filters-card">
                <h3>Filtrar por Tags</h3>
                <div id="tagsList" class="tags-grid">
                    <!-- Los tags se cargarán dinámicamente aquí como "chips" -->
                </div>
                <h3>Filtro por Distancia</h3>
                <div class="distance-controls">
                    <label for="distanceSlider">Distancia máxima: <span id="distanceValue">5</span> km</label>
                    <input type="range" id="distanceSlider" min="0.5" max="20" step="0.5" value="5">
                </div>
                <button id="clearDistanceFilter" class="clear-filter">Limpiar Filtro</button>
                <button id="applyFilters" class="apply-filters-btn">Aplicar Filtros</button>
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
        // Inicializar el mapa con opciones de caché
        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false,
            maxZoom: 19,
            minZoom: 3,
            maxBounds: [[-90, -180], [90, 180]],
            maxBoundsViscosity: 1.0,
            preferCanvas: true, // Mejor rendimiento en móviles
            renderer: L.canvas({
                padding: 0.5,
                tolerance: 3,
                className: '',
                pane: 'overlayPane',
                attribution: null,
                zoomAnimation: true,
                markerZoomAnimation: true,
                fadeAnimation: true,
                trackResize: true,
                updateWhenIdle: 'ifNotMoving',
                updateWhenZooming: false,
                updateInterval: 25,
                zIndex: 0,
                maxZoom: null,
                maxNativeZoom: null,
                minNativeZoom: null,
                maxBounds: null,
                maxBoundsViscosity: null,
                preferCanvas: true,
                renderer: null,
                rendererOptions: null,
                rendererPane: 'overlayPane',
                rendererAttribution: null,
                rendererZoomAnimation: true,
                rendererMarkerZoomAnimation: true,
                rendererFadeAnimation: true,
                rendererTrackResize: true,
                rendererUpdateWhenIdle: 'ifNotMoving',
                rendererUpdateWhenZooming: false,
                rendererUpdateInterval: 25,
                rendererZIndex: 0,
                rendererMaxZoom: null,
                rendererMaxNativeZoom: null,
                rendererMinNativeZoom: null,
                rendererMaxBounds: null,
                rendererMaxBoundsViscosity: null,
                rendererPreferCanvas: true
            })
        }).setView([41.3851, 2.1734], 13);

        // Añadir capa de OpenStreetMap con opciones de caché
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            minZoom: 3,
            maxNativeZoom: 19,
            minNativeZoom: 0,
            maxBounds: [[-90, -180], [90, 180]],
            maxBoundsViscosity: 1.0,
            preferCanvas: true,
            renderer: L.canvas({
                padding: 0.5,
                tolerance: 3,
                className: '',
                pane: 'overlayPane',
                attribution: null,
                zoomAnimation: true,
                markerZoomAnimation: true,
                fadeAnimation: true,
                trackResize: true,
                updateWhenIdle: 'ifNotMoving',
                updateWhenZooming: false,
                updateInterval: 25,
                zIndex: 0,
                maxZoom: null,
                maxNativeZoom: null,
                minNativeZoom: null,
                maxBounds: null,
                maxBoundsViscosity: null,
                preferCanvas: true
            })
        }).addTo(map);

        // Añadir controles de zoom en la esquina inferior derecha
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // Marcador del usuario
        let userMarker;

        // Variables globales
        let places = []; // Almacenará todos los places
        let markers = []; // Almacenará los marcadores del mapa
        let selectedTags = new Set(); // Almacenará los tags seleccionados
        let favoritePlaces = new Set(); // Almacenará los IDs de los lugares favoritos
        let userPosition = null;
        let maxDistance = 5; // Distancia máxima en kilómetros
        let isFilteringByDistance = false;

        // Función para calcular la distancia entre dos puntos en kilómetros
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radio de la Tierra en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Función para cargar los places
        async function loadPlaces() {
            try {
                let response;
                if (isFilteringByDistance && userPosition) {
                    response = await fetch(`/api/places/distance?latitude=${userPosition.lat}&longitude=${userPosition.lng}&distance=${maxDistance}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } else {
                    response = await fetch('/api/places', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                }
                
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('Error al cargar los lugares');
                }
                
                const data = await response.json();
                places = data;
                places = places.map(place => ({
                    ...place,
                    tags: place.tags || []
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
                    // Limpiar el contenido actual antes de añadir los nuevos tags
                    tagsList.innerHTML = '';
                    // Limpiar los tags seleccionados
                    selectedTags.clear();
                    
                    // Crear un Set para evitar duplicados
                    const uniqueTags = new Set();
                    
                    // Ordenar los tags por ID para mantener un orden consistente
                    const sortedTags = [...tags].sort((a, b) => a.id - b.id);
                    
                    sortedTags.forEach(tag => {
                        if (!uniqueTags.has(tag.id)) {
                            uniqueTags.add(tag.id);
                            const tagElement = document.createElement('div');
                            tagElement.className = `tag-chip ${selectedTags.has(tag.id) ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'} 
                                px-4 py-2 rounded-full cursor-pointer transition-colors duration-200`;
                            tagElement.dataset.id = tag.id;
                            tagElement.textContent = tag.name;
                            tagElement.onclick = () => toggleTag(tag.id);
                            tagsList.appendChild(tagElement);
                        }
                    });
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
            const isHidden = filtersSection.classList.contains('hidden');
            filtersSection.classList.toggle('hidden');
            
            if (!isHidden) {
                // Si estamos cerrando los filtros, limpiar los tags seleccionados
                selectedTags.clear();
                updateMapMarkers();
            } else {
                // Si estamos abriendo los filtros, cargar los tags
                loadTags();
            }
        });

        // Evento para aplicar los filtros
        document.getElementById('applyFilters').addEventListener('click', () => {
            updateMapMarkers();
        });

        // Evento para el control deslizante de distancia
        document.getElementById('distanceSlider').addEventListener('input', function(e) {
            maxDistance = parseFloat(e.target.value);
            document.getElementById('distanceValue').textContent = maxDistance;
            isFilteringByDistance = true;
            loadPlaces();
        });

        // Obtener y mostrar la ubicación del usuario
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    userPosition = { lat: latitude, lng: longitude };

                    // Si ya existe un marcador, actualizar su posición
                    if (userMarker) {
                        userMarker.setLatLng([latitude, longitude]);
                    } else {
                        // Crear nuevo marcador circular
                        userMarker = L.circleMarker([latitude, longitude], {
                            radius: 10,
                            fillColor: '#4285F4',
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 1
                        }).addTo(map);
                        map.setView([latitude, longitude], 15);
                    }

                    // Si estamos filtrando por distancia, actualizar los lugares
                    if (isFilteringByDistance) {
                        loadPlaces();
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

        // Función para limpiar la caché
        function clearCache() {
            // Limpiar la caché de lugares
            places = [];
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            // Recargar los lugares
            loadPlaces();
            
            // Limpiar la caché de tags
            selectedTags.clear();
            loadTags();
            
            // Limpiar la caché de lugares favoritos
            favoritePlaces.clear();
            loadFavoritePlaces();
        }

        // Limpiar la caché cada 5 minutos
        setInterval(clearCache, 5 * 60 * 1000);

        // Limpiar la caché cuando se cambia de pestaña
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                clearCache();
            }
        });

        // Cargar lugares favoritos al iniciar
        loadFavoritePlaces();

        // Cargar los places al iniciar
        loadPlaces();

        // Función para limpiar el filtro de distancia
        function clearDistanceFilter() {
            maxDistance = 5;
            isFilteringByDistance = false;
            document.getElementById('distanceSlider').value = maxDistance;
            document.getElementById('distanceValue').textContent = maxDistance;
            loadPlaces();
        }

        // Evento para el botón de limpiar filtro de distancia
        document.getElementById('clearDistanceFilter').addEventListener('click', clearDistanceFilter);

        // Evento para el botón de Gimcanas
        document.getElementById('lobbiesBtn').addEventListener('click', async () => {
            try {
                const response = await fetch('/api/gimcanas', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar las gimcanas');
                }

                const gimcanas = await response.json();
                const gimcanaList = document.getElementById('gimcanaList');
                gimcanaList.innerHTML = '';

                gimcanas.forEach(gimcana => {
                    const gimcanaCard = document.createElement('div');
                    gimcanaCard.className = 'bg-white p-4 rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow';
                    gimcanaCard.innerHTML = `
                        <h3 class="font-bold text-lg mb-2">${gimcana.name}</h3>
                        <p class="text-gray-600 text-sm mb-2">${gimcana.description || 'Sin descripción'}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>${gimcana.places_count || 0} lugares</span>
                            <span>${gimcana.duration || 'N/A'} min</span>
                        </div>
                    `;
                    gimcanaCard.onclick = () => showGimcanaDetails(gimcana);
                    gimcanaList.appendChild(gimcanaCard);
                });

                document.getElementById('gimcanaModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar las gimcanas'
                });
            }
        });

        // Evento para cerrar el modal de gimcanas
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('gimcanaModal').classList.add('hidden');
        });

        // Función para mostrar detalles de una gimcana
        async function showGimcanaDetails(gimcana) {
            try {
                const response = await fetch(`/api/gimcanas/${gimcana.id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar los detalles de la gimcana');
                }

                const details = await response.json();
                const detailsContent = document.getElementById('gimcanaDetailsContent');
                
                detailsContent.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">${gimcana.name}</h2>
                        <button id="closeGimcanaDetails" class="text-gray-500 hover:text-gray-700">&times;</button>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600">${gimcana.description || 'Sin descripción'}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="font-bold mb-2">Detalles:</h3>
                        <ul class="list-disc list-inside">
                            <li>Duración: ${gimcana.duration || 'N/A'} minutos</li>
                            <li>Lugares: ${gimcana.places_count || 0}</li>
                        </ul>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button id="joinGimcana" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Unirse a la Gimcana
                        </button>
                    </div>
                `;

                // Evento para cerrar el modal de detalles
                document.getElementById('closeGimcanaDetails').addEventListener('click', () => {
                    document.getElementById('gimcanaDetailsModal').classList.add('hidden');
                });

                // Evento para unirse a la gimcana
                document.getElementById('joinGimcana').addEventListener('click', async () => {
                    try {
                        const response = await fetch('/api/group/join', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ gimcana_id: gimcana.id })
                        });

                        if (!response.ok) {
                            throw new Error('Error al unirse a la gimcana');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Te has unido a la gimcana correctamente',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Cerrar los modales
                        document.getElementById('gimcanaModal').classList.add('hidden');
                        document.getElementById('gimcanaDetailsModal').classList.add('hidden');
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo unir a la gimcana'
                        });
                    }
                });

                document.getElementById('gimcanaDetailsModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los detalles de la gimcana'
                });
            }
        }
    </script>
    <script src="{{ asset('js/toolsUser.js') }}"></script>
</body>
</html>