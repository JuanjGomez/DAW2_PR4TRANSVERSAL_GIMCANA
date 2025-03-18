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
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
