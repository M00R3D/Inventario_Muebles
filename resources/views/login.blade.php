<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventario Muebles</title>
    <style>
        /* asegurar cálculo de tamaños consistente */
        *, *::before, *::after { box-sizing: border-box; }

        :root{
            --bg-1: #e0e7ff;
            --bg-2: #f0fdfa;
            --card-bg: rgba(255,255,255,0.98);
            --accent-1: #6366f1;
            --accent-2: #0ea5e9;
            --muted: #6b7280;
            --radius: 12px;
            --input-bg: #f8fafc;
            --shadow: 0 8px 28px rgba(15,23,42,0.06);
            --ease: cubic-bezier(.16,.84,.44,1);
        }

        html { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size: 16px; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg,var(--bg-1) 0%, var(--bg-2) 100%);
            padding: 2rem;
            box-sizing: border-box;
            font-size: clamp(14px, 1.8vw, 16px);
        }

        /* contenedor fijo pero totalmente responsivo:
           - width 100% y max-width evita que los inputs se salgan
           - margen lateral para breathing room en pantallas pequeñas */
        .login-container {
            width: 100%;
            max-width: 520px; /* ajusta si quieres más estrecho */
            margin: 0 1rem;
            background: var(--card-bg);
            padding: clamp(0.9rem, 3.2vw, 1.8rem);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: transform 420ms var(--ease), box-shadow 320ms var(--ease), opacity 420ms var(--ease);
            transform-origin: center;
            will-change: transform, opacity;
            overflow: hidden;
            display: grid;
            gap: 0.75rem;
            align-items: start;
        }
        .login-container:hover { transform: translateY(-6px) scale(1.004); box-shadow: 0 18px 48px rgba(15,23,42,0.10); }

        .login-title {
            font-size: clamp(1.2rem, 2.4vw, 1.7rem);
            font-weight: 700;
            margin: 0 0 0.6rem 0;
            text-align: center;
            color: #111827;
            letter-spacing: 0.6px;
        }

        .form {
            display: block;
            transition: opacity 420ms var(--ease), transform 420ms var(--ease), max-height 420ms var(--ease);
            opacity: 1;
            transform: translateY(0);
        }
        .form.hidden {
            opacity: 0;
            transform: translateY(12px) scale(0.996);
            max-height: 0;
            pointer-events: none;
        }

        .fields {
            display: grid;
            gap: 0.9rem;
            min-width: 0; /* evita overflow en children de grid */
        }

        /* two-column for name/apellido on wider screens */
        .field-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.9rem;
            min-width: 0; /* importante para evitar overflow de inputs */
        }
        @media (min-width: 640px){
            .field-grid { grid-template-columns: 1fr 1fr; }
        }

        .form-group { margin: 0; min-width: 0; }

        label {
            display: block;
            color: var(--muted);
            margin-bottom: 0.24rem;
            font-weight: 600;
            font-size: 0.95em;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        select {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            border: 1px solid rgba(99,102,241,0.12);
            border-radius: 10px;
            padding: 0.66rem 0.9rem;
            font-size: 0.98rem;
            background: var(--input-bg);
            transition: box-shadow 220ms var(--ease), border-color 220ms var(--ease), transform 220ms var(--ease);
            box-shadow: 0 1px 0 rgba(15,23,42,0.02) inset;
            appearance: none;
            overflow-wrap: anywhere; /* evita que textos largos rompan el layout */
        }
        input:focus, select:focus {
            border-color: var(--accent-1);
            outline: none;
            box-shadow: 0 6px 22px rgba(99,102,241,0.08);
            transform: translateY(-1px);
            background: #fff;
        }

        .btn {
            width: 100%;
            background: linear-gradient(90deg, var(--accent-1) 0%, #38bdf8 100%);
            color: #fff;
            padding: 0.72rem 0;
            border: none;
            border-radius: 10px;
            font-size: 1.02rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.4rem;
            transition: transform 200ms var(--ease), box-shadow 200ms var(--ease), opacity 200ms var(--ease);
            box-shadow: 0 8px 28px rgba(99,102,241,0.08);
            will-change: transform;
        }
        .btn:hover { transform: translateY(-4px); box-shadow: 0 18px 48px rgba(99,102,241,0.14); }
        .btn:active { transform: translateY(-2px) scale(0.998); }

        .btn-alt {
            background: linear-gradient(90deg, #f472b6 0%, #368c2bff 100%);
            color: #111827;
            margin-top: 0.6rem;
        }
        .btn-alt:hover { filter: brightness(0.96); }

        .alert {
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.56rem;
            border-radius: 8px;
            margin-bottom: 0.6rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .toggle-link {
            display: block;
            text-align: center;
            margin-top: 0.88rem;
            color: var(--accent-1);
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.98rem;
            transition: color 180ms var(--ease), transform 180ms var(--ease);
        }
        .toggle-link:hover { color: var(--accent-2); transform: translateY(-2px); }

        /* subtle entrance animation */
        .fade-in {
            animation: floatIn 560ms var(--ease) both;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(18px) scale(0.998); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Accessibility: respect user reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .login-container, .form, .fade-in, .btn, input, select { transition: none !important; animation: none !important; transform: none !important; }
        }
    </style>

    <script>
        // Smooth toggle using CSS classes so animations stay GPU-accelerated and accessible.
        function toggleForm(showRegister) {
            const login = document.getElementById('login-form');
            const register = document.getElementById('register-form');

            if (showRegister) {
                // show register
                login.classList.add('hidden');
                login.setAttribute('aria-hidden', 'true');

                // ensure register is visible before triggering animation
                register.style.display = 'block';
                setTimeout(() => {
                    register.classList.remove('hidden');
                    register.setAttribute('aria-hidden', 'false');
                }, 20);

                // hide login after transition to remove tab stops
                setTimeout(() => { login.style.display = 'none'; }, 420);
            } else {
                register.classList.add('hidden');
                register.setAttribute('aria-hidden', 'true');

                login.style.display = 'block';
                setTimeout(() => {
                    login.classList.remove('hidden');
                    login.setAttribute('aria-hidden', 'false');
                }, 20);

                setTimeout(() => { register.style.display = 'none'; }, 420);
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            // prepare initial states: login visible, register hidden but present for animation
            const login = document.getElementById('login-form');
            const register = document.getElementById('register-form');
            login.style.display = 'block';
            login.classList.remove('hidden');
            login.setAttribute('aria-hidden', 'false');

            register.style.display = 'none';
            register.classList.add('hidden');
            register.setAttribute('aria-hidden', 'true');
        });
    </script>
</head>
<body>
    <div class="login-container fade-in" role="region" aria-label="Login Inventario Muebles">
        <h1 class="login-title">Inventario Muebles</h1>
        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Login Form -->
        <form id="login-form" class="form" method="POST" action="{{ url('/login') }}" aria-hidden="false">
            @csrf
            <div class="fields">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn">Ingresar</button>
            <span class="toggle-link" role="button" tabindex="0" onclick="toggleForm(true)" onkeypress="if(event.key==='Enter')toggleForm(true)">¿No tienes cuenta? Regístrate</span>
        </form>

        <!-- Register Form -->
        <form id="register-form" class="form hidden" method="POST" action="{{ url('/register') }}" style="display:none;" aria-hidden="true">
            @csrf
            <div class="fields">
                <div class="field-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" required autocomplete="given-name">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" name="apellido" id="apellido" required autocomplete="family-name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email_reg">Correo electrónico</label>
                    <input type="email" name="email" id="email_reg" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password_reg">Contraseña</label>
                    <input type="password" name="password" id="password_reg" required autocomplete="new-password">
                </div>

                <div class="field-grid">
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol" required>
                            <option value="">Selecciona un rol</option>
                            <option value="admin">Administrador</option>
                            <option value="empleado">Empleado</option>
                            <option value="tecnico">Técnico</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="area_id">Área</label>
                        <select name="area_id" id="area_id" required>
                            <option value="">Selecciona un área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-alt">Registrarse</button>
            <span class="toggle-link" role="button" tabindex="0" onclick="toggleForm(false)" onkeypress="if(event.key==='Enter')toggleForm(false)">¿Ya tienes cuenta? Inicia sesión</span>
        </form>
    </div>
</body>
</html>
