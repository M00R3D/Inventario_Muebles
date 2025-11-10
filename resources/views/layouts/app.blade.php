{{-- Blade template - ensure PHP parser treats this file as a template and avoids stray parse errors --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Inventario Muebles')</title>
    <style>
        :root{--bg:#FDFDFC;--muted:#6b7280;--accent:#111827;--card:#fff}
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:var(--bg);color:var(--accent)}
        .header{background:var(--card);border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;padding:10px 16px;position:sticky;top:0;z-index:30}
        .brand{display:flex;align-items:center;gap:12px;font-weight:600}
        #sidebar-toggle{background:none;border:0;font-size:20px;cursor:pointer}
        .dashboard-container{display:flex;min-height:calc(100vh - 52px);transition:all .18s ease}
        .sidebar{width:250px;background:#0f172a;color:#fff;padding:18px;flex-shrink:0;transition:width .18s ease, transform .18s ease, opacity .18s ease}
        .sidebar.collapsed{width:72px}
        .sidebar .label{transition:opacity .15s ease, transform .15s ease}
        .sidebar.collapsed .label{opacity:0;transform:translateX(-6px);display:none}
        .sidebar .icon{width:28px;text-align:center}
        .dashboard-content{flex:1;padding:20px}
        .card{background:var(--card);border-radius:8px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.04)}
        .btn-logout{
            background: linear-gradient(90deg,#ef4444,#b91c1c);
            color:#fff;
            padding:6px 10px;
            border-radius:8px;
            border:0;
            font-weight:700;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:8px;
            cursor:pointer;
            transition:transform .12s ease, box-shadow .12s ease, opacity .12s ease;
        }
        .btn-logout:hover{ transform:translateY(-2px); box-shadow:0 8px 18px rgba(185,28,28,0.18); }
        .btn-logout:active{ transform:translateY(-1px) scale(0.998); }
        .btn-logout:focus{ outline:3px solid rgba(239,68,68,0.18); outline-offset:2px; }
        #confirm-overlay{display:none;position:fixed;inset:0;background:rgba(2,6,23,0.45);align-items:center;justify-content:center;z-index:9999;padding:1rem;}
        #confirm-card{background:#fff;padding:12px;border-radius:12px;box-shadow:0 8px 28px rgba(15,23,42,0.06);width:clamp(280px,420px,520px);text-align:left;}
        #confirm-title{margin:0 0 .5rem 0;font-weight:700;font-size:1.05rem;}
        #confirm-msg{color:var(--muted);margin-bottom:12px;font-size:0.98rem;}
        .confirm-actions{display:flex;gap:.5rem;justify-content:flex-end;}
        @media (max-width:768px){
            .dashboard-container{flex-direction:column}
            .sidebar{width:100%;position:fixed;left:0;top:52px;transform:translateY(-120%);z-index:40;border-bottom-left-radius:8px;border-bottom-right-radius:8px}
            .sidebar.open{transform:translateY(0)}
            .sidebar.collapsed{width:100%}
            main{padding-top:16px}
        }
    </style>
</head>
<body>
    <header class="header" role="banner">
        <div class="brand">
            <button id="sidebar-toggle" aria-controls="sidebar" aria-expanded="true">☰</button>
            <span>Inventario Muebles</span>
        </div>
        <div class="user-info" style="color:var(--muted);">
            <div class="user-name">
                <?php if(isset($usuario)): ?>
                    <?php echo 'Bienvenido,'; ?><?php echo e($usuario->nombre ?? ''); ?> <?php echo e($usuario->apellido ?? ''); ?>
                <?php elseif(session()->has('usuario_id')): ?>
                    <?php $u = \App\Models\Usuario::find(session('usuario_id')); ?>
                    <?php echo 'Bienvenido,'; ?> <?php echo e($u->nombre ?? ''); ?> <?php echo e($u->apellido ?? ''); ?>
                <?php endif; ?>
            </div>
            <a href="<?php echo e(url('/logout')); ?>" class="btn-logout" title="Cerrar sesión" aria-label="Cerrar sesión" data-confirm="¿Deseas cerrar sesión ahora?" data-confirm-type="logout">
                <span style="font-size:14px;line-height:1;display:inline-block;">⎋</span>
                <span>Cerrar sesión</span>
            </a>

            <?php
                $__current = null;
                if (session()->has('usuario_id')) {
                    $__current = \App\Models\Usuario::find(session('usuario_id'));
                }
            ?>
            <?php if($__current && ($__current->rol ?? '') !== 'admin'): ?>
                <?php echo $__env->make('partials.notification_bell', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

            <script>window.currentUserId = @json(session('usuario_id') ?? null);</script>
        </div>
    </header>

    <div class="dashboard-container" id="dashboard-root">
        <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <main class="dashboard-content" id="main-content" role="main">
            @yield('content')
        </main>
    </div>
    <div id="confirm-overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div id="confirm-card" role="document" aria-labelledby="confirm-title">
            <h3 id="confirm-title">Confirmar</h3>
            <p id="confirm-msg">¿Estás seguro?</p>
            <div class="confirm-actions">
                <button type="button" id="confirm-cancel" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
                <button type="button" id="confirm-ok" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const storageKey = 'sidebar_collapsed_v1';
        if(!toggle || !sidebar) return;
        (function(){
            const overlay = document.getElementById('confirm-overlay');
            const msg = document.getElementById('confirm-msg');
            const btnOk = document.getElementById('confirm-ok');
            const btnCancel = document.getElementById('confirm-cancel');
            let pendingEl = null;
            const okDefault = { background: btnOk.style.background, color: btnOk.style.color, text: btnOk.textContent };
            const cancelDefault = { background: btnCancel.style.background, color: btnCancel.style.color, text: btnCancel.textContent };
            function applyStyle(button, style) {
                if (!button) return;
                button.style.background = style.background ?? '';
                button.style.color = style.color ?? '';
            }
            function resetButtons() {
                applyStyle(btnOk, okDefault);
                applyStyle(btnCancel, cancelDefault);
                btnOk.textContent = okDefault.text;
                btnCancel.textContent = cancelDefault.text;
            }

            function show(text, el){
                pendingEl = el || null;
                msg.textContent = text || '¿Estás seguro?';
                const type = (el && (el.dataset.confirmType || el.getAttribute('data-confirm-type'))) || 'default';
                resetButtons();
                if (type === 'logout') {
                    applyStyle(btnCancel, { background: 'transparent', color: '#374151' });
                } else if (type === 'delete' || type === 'danger') {
                    applyStyle(btnCancel, { background: 'transparent', color: '#374151' });
                }
                overlay.style.display = 'flex';
                overlay.setAttribute('aria-hidden','false');
                try { btnCancel.focus(); } catch(e){}
            }

            function hide(){
                overlay.style.display = 'none';
                overlay.setAttribute('aria-hidden','true');
                pendingEl = null;
                resetButtons();
            }
            window.showConfirmFor = function(el){
                try {
                    if (!el) return;
                    const text = el.getAttribute('data-confirm') || '¿Estás seguro?';
                    show(text, el);
                } catch(e){
                    console.error('showConfirmFor error', e);
                }
            };
            document.addEventListener('click', function(e){
                const el = e.target.closest('[data-confirm]');
                if(!el) return;
                e.preventDefault();
                const text = el.getAttribute('data-confirm') || '¿Estás seguro?';
                show(text, el);
            }, true);

            btnCancel.addEventListener('click', hide);

            btnOk.addEventListener('click', function(){
                if(!pendingEl) { hide(); return; }
                const callbackName = pendingEl.getAttribute('data-confirm-callback') || pendingEl.dataset.confirmCallback;
                if (callbackName && typeof window[callbackName] === 'function') {
                    try { window[callbackName](pendingEl); } catch (err) { console.error('Error callback confirm:', err); }
                    hide();
                    return;
                }
                const f = pendingEl.closest('form');
                if (f) { try { f.submit(); } catch(e){ console.error(e); } hide(); return; }
                if (pendingEl.tagName === 'A' && pendingEl.href) { window.location.href = pendingEl.href; hide(); return; }
                hide();
            });

            overlay.addEventListener('click', function(ev){
                if(ev.target === overlay) hide();
            });
            document.addEventListener('keydown', function(ev){
                if(ev.key === 'Escape') hide();
            });

        })();

        function setCollapsed(collapsed){
            if(collapsed){
                sidebar.classList.add('collapsed');
                toggle.setAttribute('aria-expanded','false');
            } else {
                sidebar.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded','true');
            }
        }

        function setMobileOpen(open){
            if(open) sidebar.classList.add('open'); else sidebar.classList.remove('open');
        }

        if(window.innerWidth > 768){
            setCollapsed(localStorage.getItem(storageKey) === '1');
        }

        toggle.addEventListener('click', function(e){
            e.stopPropagation();
            if(window.innerWidth <= 768){
                setMobileOpen(!sidebar.classList.contains('open'));
            } else {
                const collapsed = !sidebar.classList.contains('collapsed');
                setCollapsed(collapsed);
                localStorage.setItem(storageKey, collapsed ? '1' : '0');
            }
        });

        document.addEventListener('click', function(e){
            if(window.innerWidth <= 768 && sidebar.classList.contains('open')){
                if(!sidebar.contains(e.target) && !toggle.contains(e.target)){
                    setMobileOpen(false);
                }
            }
        });

        window.addEventListener('resize', function(){
            if(window.innerWidth > 768){
                setCollapsed(localStorage.getItem(storageKey) === '1');
                setMobileOpen(false);
            }
        });
    })();

    window.confirmDeleteById = async function(el){
      if(!el) return;
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const frm = el.closest('form');
        if (frm) {
          const action = frm.action;
          const fd = new FormData(frm);
          if (!fd.has('_method')) fd.append('_method','DELETE');
          const resp = await fetch(action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'X-CSRF-TOKEN': token,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: fd
          });
          if (resp.ok) { location.reload(); return; }
          const ct = resp.headers.get('content-type') || '';
          const data = ct.includes('application/json') ? await resp.json() : await resp.text();
          console.error('Error al eliminar (form):', resp.status, data);
          alert((data && data.message) ? data.message : 'Error al eliminar');
          return;
        }
        const id = el.getAttribute('data-id');
        if (!id) { console.warn('confirmDeleteById: id missing'); return; }
        const urlBase = el.getAttribute('data-base') || `${location.origin}/muebles`;
        const resp2 = await fetch(`${urlBase}/${encodeURIComponent(id)}`, {
          method: 'DELETE',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        if (resp2.ok) { location.reload(); return; }
        const ct2 = resp2.headers.get('content-type') || '';
        const data2 = ct2.includes('application/json') ? await resp2.json() : await resp2.text();
        console.error('Error al eliminar (direct):', resp2.status, data2);
        alert((data2 && data2.message) ? data2.message : 'Error al eliminar');
      } catch(e){ console.error('confirmDeleteById error', e); alert('Error al eliminar'); }
    };
    </script>
    @yield('scripts')
</body>
</html>