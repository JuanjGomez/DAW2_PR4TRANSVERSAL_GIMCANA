<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Register</title>
    <script src="{{ asset('js/register.js') }}" defer></script>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="{{ asset('img/logo.png') }}" alt="logo">
        </div>
        <div class="form-section">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Register</h1>
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div class="input-group">
                    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    @if ($errors->has('name'))
                        <span class="error-message">{{ $errors->first('name') }}</span>
                    @else
                        <span class="error-message"></span>
                    @endif
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    @if ($errors->has('email'))
                        <span class="error-message">{{ $errors->first('email') }}</span>
                    @else
                        <span class="error-message"></span>
                    @endif
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    @if ($errors->has('password'))
                        <span class="error-message">{{ $errors->first('password') }}</span>
                    @else
                        <span class="error-message"></span>
                    @endif
                </div>
                <div class="input-group">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Register</button>
            </form>
            <div class="mt-6 text-center">
                <p class="text-gray-600">¿Ya tienes una cuenta?</p>
                <a href="{{ route('login') }}" class="mt-2 inline-block bg-transparent text-blue-500 font-semibold py-2 px-4 border border-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition duration-300">
                    Inicia sesión aquí
                </a>
            </div>
        </div>
    </div>
</body>
</html> 