<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda en línea</title>
</head>
<body>
    <header>
        <h1>Tienda en línea</h1>
        <nav>
            <a href="{{ route('catalogo.index') }}">Inicio</a>
            <a href="{{ route('cart.index') }}">Carrito</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>