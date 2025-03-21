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
            </div>
        </div>

        <!-- Contenido de Lugares -->
        <div id="places-tab" class="tab-content">
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

                        <!-- Sección de respuestas -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Respuestas</label>
                            <div id="answers-container">
                                <div class="answer-container">
                                    <div class="mb-2">
                                        <label class="block text-gray-700">Respuesta 1</label>
                                        <input type="text" name="answers[0][answer]" class="w-full px-4 py-2 border rounded-lg" required>
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="correct_answer" value="0" class="form-radio" required>
                                                <span class="ml-2">Respuesta correcta</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addAnswer()" class="mt-2 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600">
                                Añadir Respuesta
                            </button>
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
    </script>
</body>
</html>