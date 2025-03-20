<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear o Unirse a una Gimcana</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Gimcana Turística</h1>

        <!-- Crear Gimcana -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-4">Crear una Gimcana</h2>
            <form action="{{ route('gimcana.create') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nombre de la Gimcana</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Descripción</label>
                    <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg" rows="3" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="max_players" class="block text-gray-700">Número máximo de participantes</label>
                    <input type="number" name="max_players" id="max_players" class="w-full px-4 py-2 border rounded-lg" min="2" required>
                    <p class="text-sm text-gray-500 mt-1">Mínimo 2 jugadores</p>
                </div>
                <div class="mb-4">
                    <label for="num_groups" class="block text-gray-700">Número de grupos</label>
                    <input type="number" name="num_groups" id="num_groups" class="w-full px-4 py-2 border rounded-lg" min="2" required>
                    <p class="text-sm text-gray-500 mt-1">Mínimo 2 grupos</p>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Crear Gimcana</button>
            </form>
        </div>

        <!-- Unirse a Gimcana -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Unirse a una Gimcana</h2>
            <form action="{{ route('gimcana.join') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-gray-700">Código de la Gimcana</label>
                    <input type="text" name="code" id="code" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300">Unirse</button>
            </form>
        </div>
    </div>
</body>
</html> 