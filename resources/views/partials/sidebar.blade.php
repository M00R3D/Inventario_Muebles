<!DOCTYPE html>
@php
    $current = null;
    if (session()->has('usuario_id')) {
        $current = \App\Models\Usuario::find(session('usuario_id'));
    }
    $isAdmin = $current && ($current->rol === 'admin');
@endphp
<div class="sidebar" id="sidebar" role="navigation" aria-label="Barra lateral">
    <div class="sidebar-card">
        <header class="sidebar-header">
            <h2 class="brand">Menú</h2>
        </header>
        <nav class="sidebar-nav" aria-label="Navegación principal">
            <ul>
                <li><a href="{{ url('/dashboard') }}"><span class="icon">🏠</span><span class="label">Dashboard</span></a></li>
                <li><a href="{{ url('/usuarios') }}"><span class="icon">👥</span><span class="label">Usuarios</span></a></li>
                <li><a href="{{ url('/muebles') }}"><span class="icon">🪑</span><span class="label">Inventario</span></a></li>
                @if($isAdmin)
                    <li><a href="{{ url('/imagenes') }}"><span class="icon">🖼️</span><span class="label">Imágenes</span></a></li>
                @endif
                <li><a href="{{ url('/solicitudes') }}"><span class="icon">📩</span><span class="label">Solicitudes</span></a></li>
            </ul>
        </nav>
    </div>
</div>
<style>
:root{
    --bg-1: #e0e7ff;
    --bg-2: #f0fdfa;
    --card-bg: rgba(255,255,255,0.98);
    --accent-1: #6366f1;
    --accent-2: #0ea5e9;
    --muted: #6b7280;
    --text: #0f172a;
    --radius: 12px;
    --shadow: 0 8px 28px rgba(15,23,42,0.06);
    --ease: cubic-bezier(.16,.84,.44,1);
}
.sidebar{
    width: 260px;
    min-width: 72px;
    box-sizing: border-box;
    padding: 1rem;
    transition: width 260ms var(--ease), transform 220ms var(--ease), box-shadow 220ms var(--ease);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background: linear-gradient(180deg,var(--card-bg), rgba(255,255,255,0.96));
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transform-origin: left center;
    position: sticky;
    top: 52px; 
    height: calc(100vh - 52px);
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}
.sidebar.open{ transform: translateY(0); }
.sidebar-card{
    display:flex;
    flex-direction:column;
    gap:0.6rem;
    width:100%;
    background:transparent;
    padding:0;
    box-shadow:none;
    border-radius:0;
}
.brand{ margin:0; font-size:0.98rem; color:var(--text); font-weight:700; display:flex; align-items:center; gap:8px; }
.sidebar-header{ padding-bottom:0.4rem; border-bottom: 1px solid rgba(99,102,241,0.06); }
.sidebar-nav ul{ list-style:none; margin:0; padding:0; display:grid; gap:0.45rem; }
.sidebar-nav a{
    display:flex;
    align-items:center;
    gap:0.75rem;
    text-decoration:none;
    padding:0.6rem;
    border-radius:10px;
    color:var(--text);
    background:transparent;
    transition:background 180ms var(--ease), transform 160ms var(--ease);
    font-weight:700;
    overflow:hidden;
}
.sidebar-nav a .icon{
    display:inline-grid;
    place-items:center;
    width:40px;
    min-width:40px;
    height:40px;
    border-radius:10px;
    background: linear-gradient(180deg, rgba(99,102,241,0.12), rgba(14,165,233,0.06));
    color:var(--accent-1);
    font-size:18px;
    flex-shrink:0;
}
.sidebar-nav a .label{
    color:var(--muted);
    font-size:0.98rem;
    white-space:nowrap;
    transition:opacity 200ms var(--ease), transform 200ms var(--ease);
}
.sidebar-nav a:hover{ background: linear-gradient(90deg, rgba(99,102,241,0.06), rgba(14,165,233,0.03)); transform:translateY(-2px); }
.sidebar-nav a:active{ transform:translateY(-1px) scale(0.998); }
.sidebar-nav a.active{ background: linear-gradient(90deg, var(--accent-1) 0%, var(--accent-2) 100%); color:#fff; }
.sidebar-nav a.active .icon{ background: rgba(255,255,255,0.12); color:#fff; }
.sidebar-nav a.active .label{ color:#fff; }
.sidebar.collapsed{ width:72px; }
.sidebar.collapsed .label{ opacity:0; transform:translateX(-6px); pointer-events:none; display:none; }
.sidebar.collapsed .brand{ opacity:0; transform:translateX(-6px); height:0; overflow:hidden; pointer-events:none; }
@media (max-width:768px){
    .sidebar{ position:fixed; left:12px; top:64px; width:calc(100% - 24px); max-width:360px; transform:translateY(-120%); z-index:60; }
    .sidebar.open{ transform:translateY(0); }
    .sidebar.collapsed{ width:calc(100% - 24px); }
    .sidebar.collapsed .label{ display:inline; opacity:1; transform:none; pointer-events:auto; }
}
.sidebar-nav a:focus{ outline:2px solid rgba(99,102,241,0.16); outline-offset:2px; }
@media (min-width:769px){
    #dashboard-root .sidebar{ margin-right:16px; }
}
</style>