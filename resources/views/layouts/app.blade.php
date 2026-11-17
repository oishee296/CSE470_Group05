<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SHARE')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">SHARE</a>
        @auth
            <div class="d-flex align-items-center gap-3 text-white">
                <span>{{ auth()->user()->name }}
                    <span class="badge bg-light text-danger">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>