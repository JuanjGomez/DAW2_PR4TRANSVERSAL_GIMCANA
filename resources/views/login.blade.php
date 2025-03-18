<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Login</title>
    <script src="{{ asset('js/login.js') }}" defer></script>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="{{ asset('img/logo.png') }}" alt="logo">
        </div>
        <div class="form-section">
            <h1>Login</h1>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                    <span class="error-message"></span>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password">
                    <span class="error-message"></span>
                </div>
                @if ($errors->any())
                    <div class="form-error-message">
                        {{ $errors->first() }}
                    </div>
                @endif
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300">Login</button>
            </form>
            <div class="mt-6 text-center">
                <p class="text-gray-600">¿No tienes una cuenta?</p>
                <a href="{{ route('register') }}" class="mt-2 inline-block bg-transparent text-blue-500 font-semibold py-2 px-4 border border-blue-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">
                    Regístrate aquí
                </a>
            </div>
        </div>
    </div>
</body>
</html>
