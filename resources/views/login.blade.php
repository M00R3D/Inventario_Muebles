<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventario Muebles</title>
<style>
    *, *::before, *::after { box-sizing: border-box; }
    :root{
        --bg-1:#e0e7ff; --bg-2:#f0fdfa; --card:#fff; --accent:#6366f1;
        --muted:#6b7280; --radius:12px; --input-bg:#f8fafc; --shadow:0 8px 28px rgba(15,23,42,0.06);
    }
    html,body{height:100%;margin:0;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;}
    body{
        min-height:100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:1rem;
        background:linear-gradient(135deg,var(--bg-1),var(--bg-2));
        color:#0f172a;
        overflow:hidden;
    }
    .login-container{
        width:100%;
        max-width:720px;
        background:var(--card);
        padding:1rem;
        border-radius:var(--radius);
        box-shadow:var(--shadow);
        display:flex;
        flex-direction:column;
        gap:0.9rem;
        max-height:calc(100vh - 2rem);
        overflow:auto; 
        -ms-overflow-style:none; scrollbar-width:none;
    }
    .login-container::-webkit-scrollbar{ display:none; }
    .login-title{ margin:0; font-weight:700; font-size:1.25rem; text-align:center; }
    .form-stack{ width:100%; display:flex; flex-direction:column; gap:1rem; }
    .form{ width:100%; display:flex; flex-direction:column; gap:0.9rem; padding:0; box-sizing:border-box; }
    .form.hidden{ opacity:0; transform:translateY(6px); pointer-events:none; height:0; overflow:hidden; }
    .fields{ display:flex; flex-direction:column; gap:0.75rem; width:100%; }
    .row{ display:flex; gap:0.75rem; }
    .row > *{ flex:1; min-width:0; } 
    label{ display:block; margin-bottom:6px; color:var(--muted); font-weight:600; font-size:0.95rem; }
    input, select, textarea{
        width:100%;
        max-width:100%;
        min-width:0;
        border:1px solid rgba(0,0,0,0.06);
        border-radius:10px;
        padding:0.6rem;
        background:var(--input-bg);
        font-size:1rem;
    }
    input:focus, select:focus{ outline:none; box-shadow:0 6px 22px rgba(99,102,241,0.06); border-color:var(--accent); background:#fff; }
    .btn, .btn-alt{
        width:100%;
        padding:0.7rem;
        border-radius:10px;
        border:none;
        color:#fff;
        font-weight:700;
        font-size:1rem;
        cursor:pointer;
    }
    .btn{ background:linear-gradient(90deg,var(--accent),#38bdf8); }
    .btn-alt{ background:linear-gradient(90deg,#f472b6,#34d399); }
    .toggle-link{ display:block; text-align:center; color:var(--accent); text-decoration:underline; cursor:pointer; margin-top:0.25rem; }
    .alert{ background:#fee2e2; color:#b91c1c; padding:0.5rem; border-radius:8px; text-align:center; font-weight:600; }
    .confirm-overlay{ position:fixed; inset:0; background:rgba(2,6,23,0.45); display:none; align-items:center; justify-content:center; z-index:9999; padding:1rem; }
    .confirm-card{ background:var(--card); padding:1rem; border-radius:12px; box-shadow:var(--shadow); width:clamp(280px,420px,520px); text-align:left; }
    .confirm-title{ margin:0 0 .25rem 0; font-weight:700; font-size:1.05rem; }
    .confirm-msg{ color:var(--muted); margin-bottom:1rem; font-size:0.98rem; }
    .confirm-actions{ display:flex; gap:.5rem; justify-content:flex-end; }
    .btn-danger{ background:linear-gradient(90deg,#ef4444,#f97316); }
    @media (max-width:640px){
        .row{ flex-direction:column; }
        .login-container{ padding:0.75rem; max-height:calc(100vh - 1rem); }
        body{ align-items:flex-start; padding-top:0.6rem; padding-bottom:0.6rem; }
    }
    @media (prefers-reduced-motion: reduce){
        .login-container, .form, .btn, input, select{ transition:none !important; transform:none !important; animation:none !important; }
    }
</style>

    <script>
        function setInert(el, inert) {
            try {
                if ('inert' in HTMLElement.prototype) {
                    el.inert = !!inert;
                } else {
                    if (inert) el.setAttribute('data-inert','true');
                    else el.removeAttribute('data-inert');
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
            if (el.contains(document.activeElement)) {
                document.activeElement.blur();
                const fallback = document.querySelector('.toggle-link, #login-form input, #register-form input');
                if (fallback) fallback.focus?.();
            }
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
            setInert(el, true);
            setTimeout(()=> { el.style.display = 'none'; }, 420);
        }
        function showElement(el) {
            el.style.display = 'block';
            setTimeout(()=>{
                el.classList.remove('hidden');
                el.removeAttribute('aria-hidden');
                setInert(el, false);
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

<div id="confirm-overlay" class="confirm-overlay" role="dialog" aria-modal="true" aria-hidden="true" style="display:none;">
  <div class="confirm-card" role="document" aria-labelledby="confirm-title">
    <h2 id="confirm-title" class="confirm-title">Confirmar eliminación</h2>
    <p id="confirm-msg" class="confirm-msg">¿Estás seguro que deseas eliminar este elemento?</p>
    <div class="confirm-actions">
      <button type="button" id="confirm-cancel" class="btn btn-alt">Cancelar</button>
      <button type="button" id="confirm-ok" class="btn btn-danger">Eliminar</button>
    </div>
  </div>
</div>

<script>
(function(){
  let pendingAction = null;

  function showConfirm(message, onConfirm){
    const overlay = document.getElementById('confirm-overlay');
    document.getElementById('confirm-msg').textContent = message || '¿Estás seguro que deseas eliminar este elemento?';
    overlay.style.display = 'flex';
    overlay.setAttribute('aria-hidden','false');
    document.getElementById('confirm-cancel').focus();
    pendingAction = onConfirm;
  }
  function hideConfirm(){
    const overlay = document.getElementById('confirm-overlay');
    overlay.style.display = 'none';
    overlay.setAttribute('aria-hidden','true');
    pendingAction = null;
  }

  document.getElementById('confirm-cancel').addEventListener('click', hideConfirm);
  document.getElementById('confirm-ok').addEventListener('click', function(){
    if(typeof pendingAction === 'function') pendingAction();
    hideConfirm();
  });

  document.getElementById('confirm-overlay').addEventListener('click', function(e){
    if(e.target === this) hideConfirm();
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') hideConfirm();
  });

  document.addEventListener('click', function(e){
    const el = e.target.closest('[data-confirm]');
    if(!el) return;
    e.preventDefault();
    const msg = el.getAttribute('data-confirm') || '¿Estás seguro?'
