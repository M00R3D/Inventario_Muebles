<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventario Muebles</title>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f0fdfa 100%);
        }
        .login-container {
            background: rgba(255,255,255,0.95);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(80,80,160,0.10);
            width: 100%;
            max-width: 370px;
            transition: box-shadow 0.3s;
        }
        .login-container:hover {
            box-shadow: 0 8px 32px rgba(80,80,160,0.18);
        }
        .login-title {
            font-size: 1.7rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #374151;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        label {
            display: block;
            color: #374151;
            margin-bottom: 0.3rem;
            font-weight: 500;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            border: 1px solid #c7d2fe;
            border-radius: 0.4rem;
            padding: 0.55rem 0.8rem;
            margin-top: 0.2rem;
            font-size: 1rem;
            background: #f8fafc;
            transition: border 0.2s, box-shadow 0.2s;
        }
        input:focus {
            border: 1.5px solid #6366f1;
            outline: none;
            box-shadow: 0 0 0 2px #a5b4fc55;
            background: #fff;
        }
        .btn {
            width: 100%;
            background: linear-gradient(90deg, #6366f1 0%, #38bdf8 100%);
            color: #fff;
            padding: 0.7rem 0;
            border: none;
            border-radius: 0.4rem;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px rgba(99,102,241,0.08);
        }
        .btn:hover, .btn:focus {
            background: linear-gradient(90deg, #4338ca 0%, #0ea5e9 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .btn-alt {
            background: linear-gradient(90deg, #f472b6 0%, #30fa15ff 100%);
            color: #374151;
            margin-top: 0.7rem;
        }
        .btn-alt:hover, .btn-alt:focus {
            background: linear-gradient(90deg, #be185d 0%, #60ac34ff 100%);
            color: #fff;
        }
        .alert {
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .toggle-link {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            color: #6366f1;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.98rem;
            transition: color 0.2s;
        }
        .toggle-link:hover {
            color: #0ea5e9;
        }
        .fade-in {
            animation: fadeIn 0.4s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
    </style>
    <script>
        function toggleForm(showRegister) {
            document.getElementById('login-form').style.display = showRegister ? 'none' : 'block';
            document.getElementById('register-form').style.display = showRegister ? 'block' : 'none';
        }
        window.onload = function() {
            toggleForm(false);
        }
    </script>
</head>
<body>
    <div class="login-container fade-in">
        <h1 class="login-title">Inventario Muebles</h1>
        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Login Form -->
        <form id="login-form" method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
            <span class="toggle-link" onclick="toggleForm(true)">¿No tienes cuenta? Regístrate</span>
        </form>

        <!-- Register Form -->
        <form id="register-form" method="POST" action="{{ url('/register') }}" style="display:none;">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" required>
            </div>
            <div class="form-group">
                <label for="email_reg">Correo electrónico</label>
                <input type="email" name="email" id="email_reg" required>
            </div>
            <div class="form-group">
                <label for="password_reg">Contraseña</label>
                <input type="password" name="password" id="password_reg" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select name="rol" id="rol" required style="width:100%;padding:0.5rem 0.8rem;border-radius:0.4rem;border:1px solid #c7d2fe;background:#f8fafc;">
                    <option value="">Selecciona un rol</option>
                    <option value="admin">Administrador</option>
                    <option value="empleado">Empleado</option>
                    <option value="tecnico">Técnico</option>
                </select>
            </div>
            <div class="form-group">
                <label for="area_id">Área</label>
                <input type="number" name="area_id" id="area_id" min="1" required>
            </div>
            <button type="submit" class="btn btn-alt">Registrarse</button>
            <span class="toggle-link" onclick="toggleForm(false)">¿Ya tienes cuenta? Inicia sesión</span>
        </form>
    </div>
</body>
</html>
