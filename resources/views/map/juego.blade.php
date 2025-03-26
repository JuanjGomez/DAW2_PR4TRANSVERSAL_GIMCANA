<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/juegoGimcana.css') }}">
</head>
<body>
    <div id="map"></div>
    <div id="challengeModal" class="modal hidden">
        <!-- Contenido del modal -->
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/juegoGimcana.js') }}"></script>
</body>
</html>