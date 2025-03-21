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
        <h1 class="text-3xl font-bold mb-8 text-center">Panel de Administración de Gimcanas</h1>

        <!-- Pestañas -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <div class="flex justify-between">
                    <div>
                        <button onclick="showTab('places')" class="tab-btn py-4 px-6 border-b-2 font-medium border-blue-500 text-blue-600" data-tab="places">
                            Lugares
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
                            <label for="icon" class="block text-gray-700">Icono (opcional)</label>
                            <input type="text" id="icon" name="icon" class="w-full px-4 py-2 border rounded-lg">
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
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="cp-gimcana" class="block text-gray-700">Gimcana</label>
                            <select id="cp-gimcana" name="gimcana_id" class="w-full px-4 py-2 border rounded-lg" required>
                                <option value="">Selecciona una gimcana</option>
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
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>