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
        .map-buttons {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            gap: 10px;
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
    </style>
</head>
<body class="m-0 p-0">
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

        document.getElementById('filtrosBtn').addEventListener('click', () => {
            // Aquí irá la lógica para abrir el modal de Filtros
            console.log('Abrir modal de Filtros');
        });
    </script>
    <script src="{{ asset('js/toolsUser.js') }}"></script>
</body>
</html>
