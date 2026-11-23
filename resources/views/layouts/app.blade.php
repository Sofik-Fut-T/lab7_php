<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Складський облік')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">📦 Складський облік</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('home') }}">Головна</a>
                <a class="nav-link" href="{{ route('stock-items.index') }}">Товари</a>
                @auth
                    <a class="nav-link" href="{{ route('stock-items.create') }}">Додати товар</a>
                @endauth
            </div>

            <div class="navbar-nav ms-auto">
                @guest
                    <a class="nav-link" href="{{ route('login') }}">Увійти</a>
                    <a class="nav-link" href="{{ route('register') }}">Реєстрація</a>
                @endguest

                @auth
                    <span class="nav-link text-info">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Вийти</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>