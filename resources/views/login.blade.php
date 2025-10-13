<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventario Muebles</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root{
            --bg-1: #e0e7ff;
            --bg-2: #f0fdfa;
            --card-bg: rgba(255,255,255,0.98);
            --accent-1: #6366f1;
            --accent-2: #38bdf8;
            --accent-register-a: #f472b6;
            --accent-register-b: #34d399;
            --muted: #6b7280;
            --text: #0f172a;
            --radius: 12px;
            --input-bg: #f8fafc;
            --shadow: 0 8px 28px rgba(15,23,42,0.06);
            --ease: cubic-bezier(.16,.84,.44,1);
        }

        html { height: 100%; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg,var(--bg-1) 0%, var(--bg-2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: var(--text);
            font-size: clamp(13px, 1.6vw, 16px);
        }

        /* Card: grid with two rows (title + content) to avoid overlap */
        .login-container {
            width: clamp(320px, 86vw, 720px);
            max-width: 720px;
            background: var(--card-bg);
            padding: clamp(0.6rem, 2.4vw, 1.2rem);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-rows: auto 1fr;
            gap: clamp(0.5rem, 1.6vw, 0.9rem);
            align-items: start;
            justify-items: center;
            max-height: calc(100vh - 2rem);
            overflow: visible;
            transition: transform 240ms var(--ease), box-shadow 240ms var(--ease);
        }

        .login-title {
            margin: 0;
            font-weight: 700;
            font-size: clamp(1rem, 2.2vw, 1.4rem);
            text-align: center;
            padding-bottom: clamp(12px, 1.6vw, 40px); /* space so forms never overlap */
            width: 100%;
            color: var(--text);
        }

        /* form stack: keeps forms in flow; min-height changes via JS classes .login-active/.register-active */
        .form-stack {
            width: 100%;
            position: relative;
            display: block;
            min-height: 180px;
        }
        .form-stack.login-active { min-height: 160px; }
        .form-stack.register-active { min-height: 420px; }

        /* Forms: centered, responsive, smooth crossfade (opacity + blur + translate) */
        .form {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            transition: opacity 380ms var(--ease), transform 380ms var(--ease), filter 380ms var(--ease), max-height 380ms var(--ease);
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
            max-height: 100%;
            overflow: visible;
            will-change: opacity, transform, filter;
            background: transparent;
        }
        .form.hidden {
            opacity: 0;
            transform: translateY(10px) scale(0.998);
            filter: blur(6px);
            max-height: 0;
            pointer-events: none;
            visibility: visible; /* JS will set display:none after animation */
        }

        /* On wide screens allow the subtle overlay for crossfade but keep title spacing so no overlap */
        @media (min-width: 720px) {
            .form-stack .form {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                top: 0;
            }
            .form-stack.login-active { min-height: 160px; }
        }
        @media (max-width: 719px) {
            .form-stack .form { position: relative; top: auto; transform: none; margin-bottom: 0.5rem; }
            .form.hidden { transform: translateY(6px); }
            /* avoid cutting the card on short viewports */
            .login-container { max-height: calc(100vh - 1.2rem); padding-top: 0.6rem; padding-bottom: 0.6rem; }
            body { align-items: flex-start; padding-top: 0.6rem; padding-bottom: 0.6rem; }
        }

        .fields { display: grid; gap: 0.7rem; width: 100%; }
        .field-grid { display: grid; grid-template-columns: 1fr; gap: 0.7rem; width: 100%; }
        @media (min-width: 640px) { .field-grid { grid-template-columns: 1fr 1fr; } }

        label { display:block; color:var(--muted); margin-bottom:0.2rem; font-weight:600; font-size:0.95em; }
        input, select {
            width: 100%;
            border: 1px solid rgba(99,102,241,0.10);
            border-radius: 10px;
            padding: clamp(0.44rem, 1.6vw, 0.66rem);
            font-size: clamp(0.9rem, 1.8vw, 0.98rem);
            background: var(--input-bg);
            box-shadow: inset 0 1px 0 rgba(0,0,0,0.02);
        }
        input:focus, select:focus {
            border-color: var(--accent-1);
            outline: none;
            box-shadow: 0 6px 22px rgba(99,102,241,0.06);
            background: #fff;
        }

        .btn, .btn-alt {
            width: 100%;
            padding: clamp(0.56rem, 1.8vw, 0.72rem);
            border-radius: 10px;
            font-size: clamp(0.95rem, 1.8vw, 1.02rem);
            border: none;
            color: #fff;
            font-weight:700;
            cursor: pointer;
            transition: transform 160ms var(--ease), box-shadow 160ms var(--ease);
        }

        .btn {
            background: linear-gradient(90deg, var(--accent-1) 0%, var(--accent-2) 100%);
            box-shadow: 0 8px 24px rgba(56,189,248,0.08);
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 18px 48px rgba(56,189,248,0.12); }

        .btn-alt {
            background: linear-gradient(90deg, var(--accent-register-a) 0%, var(--accent-register-b) 100%);
            box-shadow: 0 8px 24px rgba(52,211,153,0.06);
        }
        .btn-alt:hover { transform: translateY(-3px); box-shadow: 0 18px 48px rgba(52,211,153,0.12); }

        .toggle-link { margin-top: 0.6rem; font-size: clamp(0.9rem,1.6vw,0.98rem); color: var(--accent-1); cursor:pointer; text-decoration:underline; text-align:center; display:block; }
        .alert { background:#fee2e2; color:#b91c1c; padding:0.5rem; border-radius:8px; text-align:center; font-weight:600; }

        @media (prefers-reduced-motion: reduce) {
            .login-container, .form, .btn, input, select { transition: none !important; animation: none !important; transform: none !important; }
        }
    </style>

    <script>
        function setInert(el, inert) {
            try {
                if ('inert' in HTMLElement.prototype) {
                    el.inert = !!inert;
                } else {
                    // fallback: set an attribute so styles / scripts can detect it
                    if (inert) el.setAttribute('data-inert','true');
                    else el.removeAttribute('data-inert');
                    // also remove from tab order as a basic fallback
                    el.querySelectorAll('a,button,input,select,textarea,[tabindex]').forEach(node=>{
                        if (inert) {
                            if (!node.hasAttribute('data-old-tabindex')) node.setAttribute('data-old-tabindex', node.getAttribute('tabindex') ?? '');
                            node.setAttribute('tabindex', '-1');
                            node.setAttribute('aria-hidden','true');
                        } else {
                            if (node.hasAttribute('data-old-tabindex')) {
                                const old = node.getAttribute('data-old-tabindex');
                                if (old === '') node.removeAttribute('tabindex'); else node.setAttribute('tabindex', old);
                                node.removeAttribute('data-old-tabindex');
                            } else {
                                node.removeAttribute('tabindex');
                            }
                            node.removeAttribute('aria-hidden');
                        }
                    });
                }
            } catch(e) {
                // ignore
            }
        }

        function hideElement(el) {
            // if hidden element (or a descendant) has focus, move focus to document body first
            if (el.contains(document.activeElement)) {
                document.activeElement.blur();
                // try to focus an obvious control: first visible toggle-link or first input of the other form
                const fallback = document.querySelector('.toggle-link, #login-form input, #register-form input');
                if (fallback) fallback.focus?.();
            }
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
            setInert(el, true);
            // keep display until animation finishes, then hide
            setTimeout(()=> { el.style.display = 'none'; }, 420);
        }

        function showElement(el) {
            el.style.display = 'block';
            // ensure it's not inert/hidden before focusing
            setTimeout(()=>{
                el.classList.remove('hidden');
                el.removeAttribute('aria-hidden');
                setInert(el, false);
                // focus first input/select/button inside shown element for accessibility
                const first = el.querySelector('input,select,button,[tabindex]');
                if (first) first.focus();
            }, 20);
        }

        function toggleForm(showRegister) {
            const login = document.getElementById('login-form');
            const register = document.getElementById('register-form');
            const stack = document.querySelector('.form-stack');

            if (showRegister) {
                hideElement(login);
                showElement(register);
                stack?.classList.remove('login-active');
                stack?.classList.add('register-active');
            } else {
                hideElement(register);
                showElement(login);
                stack?.classList.remove('register-active');
                stack?.classList.add('login-active');
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            const login = document.getElementById('login-form');
            const register = document.getElementById('register-form');
            const stack = document.querySelector('.form-stack');

            @if($errors->any() || old('nombre') || old('email') || session('show_register'))
                // show register
                login.style.display = 'none';
                login.classList.add('hidden');
                login.setAttribute('aria-hidden','true');
                setInert(login, true);

                register.style.display = 'block';
                setTimeout(function(){
                    register.classList.remove('hidden');
                    register.removeAttribute('aria-hidden');
                    setInert(register, false);
                    const first = register.querySelector('input,select,button');
                    if(first) first.focus();
                    stack?.classList.remove('login-active');
                    stack?.classList.add('register-active');
                }, 20);
            @else
                // show login
                register.style.display = 'none';
                register.classList.add('hidden');
                register.setAttribute('aria-hidden','true');
                setInert(register, true);

                login.style.display = 'block';
                setTimeout(function(){
                    login.classList.remove('hidden');
                    login.removeAttribute('aria-hidden');
                    setInert(login, false);
                    const first = login.querySelector('input,select,button');
                    if(first) first.focus();
                    stack?.classList.remove('register-active');
                    stack?.classList.add('login-active');
                }, 20);
            @endif
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

        <div class="form-stack" aria-live="polite">
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
                    <span class="toggle-link" role="button" tabindex="0" onclick="toggleForm(true)" onkeypress="if(event.key==='Enter')toggleForm(true)">¿No tienes cuenta? Regístrate</span>
                    <button type="submit" class="btn">Ingresar</button>
                </div>
            </form>

            <form id="register-form" class="form hidden" method="POST" action="{{ url('/register') }}" aria-hidden="true">
                @csrf
                @if ($errors->any())
                    <div class="alert">
                        Por favor corrige los errores del formulario.
                    </div>
                @endif
                <div class="fields">
                    <div class="field-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required autocomplete="given-name" value="{{ old('nombre') }}">
                            @error('nombre') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" name="apellido" id="apellido" required autocomplete="family-name" value="{{ old('apellido') }}">
                            @error('apellido') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email_reg">Correo electrónico</label>
                        <input type="email" name="email" id="email_reg" required autocomplete="email" value="{{ old('email') }}">
                        @error('email') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_reg">Contraseña</label>
                        <input type="password" name="password" id="password_reg" required autocomplete="new-password">
                        @error('password') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-grid">
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select name="rol" id="rol" required>
                                <option value="">Selecciona un rol</option>
                                <option value="admin" {{ old('rol')=='admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="empleado" {{ old('rol')=='empleado' ? 'selected' : '' }}>Empleado</option>
                                <option value="tecnico" {{ old('rol')=='tecnico' ? 'selected' : '' }}>Técnico</option>
                            </select>
                            @error('rol') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="area_id">Área</label>
                            <select name="area_id" id="area_id" required>
                                <option value="">Selecciona un área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                            @error('area_id') <div style="color:#b91c1c;margin-top:6px;font-weight:600;font-size:0.92rem;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-alt">Registrarse</button>
                <span class="toggle-link" role="button" tabindex="0" onclick="toggleForm(false)" onkeypress="if(event.key==='Enter')toggleForm(false)">¿Ya tienes cuenta? Inicia sesión</span>
            </form>
        </div>
    </div>
</body>
</html>
