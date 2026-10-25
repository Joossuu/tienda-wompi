
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVA. | Tienda en línea</title>
    <style>
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f7f6f0;
            color: #17231d;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #e5e5dc;
        }

        .announcement {
            padding: 8px 16px;
            background: #17231d;
            color: #fff;
            text-align: center;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .nav {
            width: min(1100px, 92%);
            margin: auto;
            min-height: 66px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: 4px;
            text-decoration: none;
            color: #17231d;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        nav a {
            color: #17231d;
            text-decoration: none;
            font-size: 14px;
        }

        main {
            width: min(1100px, 92%);
            margin: 0 auto;
            padding: 40px 0 60px;
            flex: 1;
        }

        a, button { cursor: pointer; }

        button, .button {
            display: inline-block;
            padding: 12px 18px;
            border: 1px solid #245d47;
            border-radius: 5px;
            background: #245d47;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .button-secondary {
            background: #fff;
            color: #245d47;
        }

        input, textarea, select {
            max-width: 100%;
            padding: 12px;
            border: 1px solid #d6d9d0;
            border-radius: 5px;
            font: inherit;
        }

        footer {
            padding: 28px 16px;
            background: #17231d;
            color: #fff;
            text-align: center;
        }

        footer .brand { color: #fff; }
        footer p { font-size: 12px; }

        @media (max-width: 600px) {
            .nav { flex-wrap: wrap; padding: 12px 0; }
            nav { gap: 12px; }
            main { padding-top: 26px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="announcement">
            DISEÑO, CALIDAD Y ESTILO · TU PRÓXIMA COMPRA EMPIEZA AQUÍ
        </div>

        <div class="nav">
            <a class="brand" href="{{ route('catalogo.index') }}">NOVA.</a>

            <nav>
                <a href="{{ route('catalogo.index') }}">Inicio</a>
                <a href="{{ route('catalogo.index') }}">Productos</a>
                <a class="button button-secondary" href="{{ route('cart.index') }}">
                    🛒 Carrito
                </a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <a class="brand" href="{{ route('catalogo.index') }}">NOVA.</a>
        <p>Una experiencia de compra sencilla y segura.</p>
        <p>© {{ date('Y') }} NOVA. · Tienda en línea</p>
    </footer>
</body>
</html>
