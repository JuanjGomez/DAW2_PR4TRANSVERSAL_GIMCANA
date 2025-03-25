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
    <script src="{{ asset('js/toolsUser.js') }}"></script>
</body>
</html>