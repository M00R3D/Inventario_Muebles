{{-- Blade template - ensure PHP parser treats this file as a template and avoids stray parse errors --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
        <div class="user-info" style="color:var(--muted)">
            <?php if(isset($usuario)): ?>
                <?php echo e($usuario->nombre ?? ''); ?> <?php echo e($usuario->apellido ?? ''); ?>
            <?php elseif(session()->has('usuario_id')): ?>
                <?php $u = \App\Models\Usuario::find(session('usuario_id')); ?>
                <?php echo e($u->nombre ?? ''); ?> <?php echo e($u->apellido ?? ''); ?>
            <?php endif; ?>
        </div>
    </header>

    <div class="dashboard-container" id="dashboard-root">
        <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <main class="dashboard-content" id="main-content" role="main">
            @yield('content')
        </main>
    </div>

    <script>
    (function(){
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const storageKey = 'sidebar_collapsed_v1';
        if(!toggle || !sidebar) return;

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
    </script>
</body>
</html>