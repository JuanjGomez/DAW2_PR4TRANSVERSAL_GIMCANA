<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 500px; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Panel de Usuario</h1>
        <p class="mb-4">Bienvenido, usuario. Aquí puedes explorar los puntos de interés y obtener rutas.</p>
        
        <!-- Mapa -->
        <div id="map" class="rounded-lg shadow-lg"></div>

        <!-- Detalles del lugar (se llenará dinámicamente) -->
        <div id="place-details" class="mt-6 bg-white p-6 rounded-lg shadow-lg hidden">
            <h2 id="place-name" class="text-2xl font-bold mb-4"></h2>
            <p id="place-description" class="text-gray-600 mb-4"></p>
            <button id="get-route" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Obtener ruta</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa
        const map = L.map('map').setView([41.3851, 2.1734], 13); // Coordenadas de Barcelona (puedes cambiarlas)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Obtener la ubicación del usuario
        let userMarker;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    userMarker = L.marker([latitude, longitude]).addTo(map)
                        .bindPopup('Tu ubicación actual')
                        .openPopup();
                    map.setView([latitude, longitude], 15); // Centrar el mapa en la ubicación del usuario
                },
                (error) => {
                    console.error('Error al obtener la ubicación:', error);
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización.');
        }

        // Cargar puntos de interés desde la base de datos (usando AJAX)
        fetch('/api/places') // Asegúrate de crear esta ruta en tu backend
            .then(response => response.json())
            .then(places => {
                places.forEach(place => {
                    const marker = L.marker([place.latitude, place.longitude]).addTo(map)
                        .bindPopup(`<b>${place.name}</b><br>${place.description}`)
                        .on('click', () => {
                            // Mostrar detalles del lugar
                            document.getElementById('place-name').textContent = place.name;
                            document.getElementById('place-description').textContent = place.description;
                            document.getElementById('place-details').classList.remove('hidden');

                            // Obtener ruta
                            document.getElementById('get-route').onclick = () => {
                                if (userMarker) {
                                    const userLatLng = userMarker.getLatLng();
                                    const placeLatLng = marker.getLatLng();
                                    const routeUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLatLng.lat},${userLatLng.lng}&destination=${placeLatLng.lat},${placeLatLng.lng}`;
                                    window.open(routeUrl, '_blank');
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Ubicación no disponible',
                                        text: 'No se pudo obtener tu ubicación.'
                                    });
                                }
                            };
                        });
                });
            })
            .catch(error => console.error('Error al cargar los puntos de interés:', error));
    </script>
</body>
</html> 