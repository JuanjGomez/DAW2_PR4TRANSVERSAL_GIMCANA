<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gimcana Turística</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    </head>
<body class="bg-gray-100">
    <!-- Hero Section -->
    <div class="hero min-h-screen flex items-center justify-center text-center text-white">
        <div class="max-w-4xl px-4">
            <h1 class="text-6xl font-bold mb-6" data-aos="fade-down" data-aos-duration="1000">¡Bienvenido a la Gimcana Turística!</h1>
            <p class="text-xl mb-8" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">Descubre lugares increíbles, supera pruebas y compite con tus amigos.</p>
            <div class="space-x-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">Regístrate</a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mx-auto px-4 py-16 features-section">
        <h2 class="text-4xl font-bold text-center mb-12" data-aos="fade-down" data-aos-duration="1000">¿Qué puedes hacer en nuestra app?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center card" data-aos="fade-up" data-aos-duration="1000">
                <img src="https://img.icons8.com/color/96/000000/map-marker.png" alt="Map Marker" class="feature-icon mx-auto">
                <h3 class="text-xl font-semibold mb-4">Explora Lugares</h3>
                <p class="text-gray-600">Descubre puntos de interés turístico en tu zona. Encuentra lugares históricos, restaurantes, museos y más.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <img src="https://img.icons8.com/color/96/000000/treasure-chest.png" alt="Treasure Chest" class="feature-icon mx-auto">
                <h3 class="text-xl font-semibold mb-4">Supera Pruebas</h3>
                <p class="text-gray-600">Completa desafíos en cada punto de control. Resuelve acertijos, encuentra pistas y gana premios.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <img src="https://img.icons8.com/color/96/000000/group.png" alt="Group" class="feature-icon mx-auto">
                <h3 class="text-xl font-semibold mb-4">Juega en Grupo</h3>
                <p class="text-gray-600">Forma equipos y compite con tus amigos. Trabaja en equipo para superar todas las pruebas y ganar la gimcana.</p>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="bg-gray-800 text-white py-16 testimonials-section">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12" data-aos="fade-down" data-aos-duration="1000">Lo que dicen nuestros usuarios</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-lg text-gray-800 card" data-aos="fade-up" data-aos-duration="1000">
                    <p class="mb-4">"¡La mejor manera de explorar la ciudad! Nunca me había divertido tanto aprendiendo sobre mi ciudad."</p>
                    <p class="font-semibold">- María López</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg text-gray-800 card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <p class="mb-4">"Una experiencia increíble. Las pruebas son desafiantes pero muy divertidas."</p>
                    <p class="font-semibold">- Juan Pérez</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg text-gray-800 card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <p class="mb-4">"Perfecto para hacer en familia o con amigos. ¡Altamente recomendado!"</p>
                    <p class="font-semibold">- Ana Martínez</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2023 Gimcana Turística. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Animations -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    </body>
</html>