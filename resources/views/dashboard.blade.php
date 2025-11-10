<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Dashboard | Inventario Muebles')

@section('content')
@php
    $current = session()->has('usuario_id') ? \App\Models\Usuario::find(session('usuario_id')) : null;
    $isAdmin = $current && ($current->rol === 'admin');

    $texts = [
        'usuarios' => [
            'Gestiona usuarios, roles y áreas de forma centralizada.',
            'Revisa cuentas, asigna roles y controla accesos.',
            'Administra perfiles y áreas del personal rápidamente.'
        ],
        'muebles' => [
            'Explora el inventario, filtra por estado y solicita artículos.',
            'Añade, edita o solicita muebles — seguimiento claro del stock.',
            'Consulta fichas de muebles y su historial de uso.'
        ],
        'solicitudes' => [
            'Revisa solicitudes, aprueba o rechaza con un clic.',
            'Gestiona las peticiones y controla el estado de los préstamos.',
            'Visualiza solicitudes recientes y responde rápidamente.'
        ],
        'imagenes' => [
            'Sube y organiza imágenes públicas para los muebles.',
            'Administra carpetas e imágenes visibles en el sitio.',
            'Carga imágenes desde tu equipo y actualiza previews.'
        ],
    ];

    function pickRandom(array $arr) {
        return $arr[array_rand($arr)];
    }
@endphp

<style>
.dashboard-grid{ display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:18px; align-items:start; }
.card-cta{ background:linear-gradient(180deg,#ffffff,#fbfdff); border-radius:14px; padding:18px; box-shadow:0 12px 34px rgba(2,6,23,0.06); transition:transform .18s ease, box-shadow .18s ease; display:flex;flex-direction:column;gap:12px; min-height:130px; }
.card-cta:hover{ transform:translateY(-6px); box-shadow:0 18px 44px rgba(2,6,23,0.10); }
.card-cta .title{ display:flex; align-items:center; gap:12px; font-weight:800; font-size:1.05rem; color:#0f172a; }
.card-cta .desc{ color:#475569; font-size:0.94rem; line-height:1.3; }
.card-cta .meta{ margin-top:auto; display:flex; justify-content:space-between; align-items:center; gap:8px; }
.card-btn{ background:linear-gradient(90deg,#06b6d4,#0ea5e9); color:#fff; padding:8px 12px; border-radius:10px; text-decoration:none; font-weight:700; }
.icon-circle{ width:44px; height:44px; border-radius:10px; display:inline-grid; place-items:center; font-size:18px; color:#fff; }
.icon-users{ background:linear-gradient(180deg,#6366f1,#4f46e5); }
.icon-muebles{ background:linear-gradient(180deg,#06b6d4,#0891b2); }
.icon-solicitudes{ background:linear-gradient(180deg,#f59e0b,#f97316); }
.icon-images{ background:linear-gradient(180deg,#10b981,#059669); }
.icon-notificaciones{ background: linear-gradient(180deg,#f97316,#f59e0b); }
.icon-categorias{ background: linear-gradient(180deg,#06b6d4,#0ea5e9); }
.icon-configuracion{ background: linear-gradient(180deg,#374151,#6b7280); }
.summary-row{ display:flex; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
.summary-item{ background:#fff; padding:12px 14px; border-radius:12px; box-shadow:0 8px 20px rgba(2,6,23,0.04); min-width:160px; }
.summary-item .num{ font-weight:800; font-size:1.25rem; color:#0f172a; }
.links-list{ display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
.link-chip{ background:#f1f5f9; padding:8px 10px; border-radius:999px; color:#0b1220; font-weight:700; text-decoration:none; }
</style>

<div style="max-width:1200px;margin:18px auto;padding:12px;">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;">
    <div>
      <h1 style="margin:0;font-size:1.25rem;">Dashboard</h1>
      <div style="color:#6b7280;margin-top:6px;font-weight:700;">
        Bienvenido{{ $current ? ', '.$current->nombre.' '.$current->apellido : '' }} — Rol: {{ $current->rol ?? 'invitado' }}
      </div>
    </div>
    <div style="text-align:right;">
      <div style="color:#6b7280;font-weight:700;">Acciones disponibles</div>
      @php
        $acciones = [
          ['label'=>'Dashboard','url'=>url('/dashboard'),'roles'=>['admin','empleado','tecnico']],
          ['label'=>'Usuarios','url'=>url('/usuarios'),'roles'=>['admin']],
          ['label'=>'Inventario','url'=>url('/muebles'),'roles'=>['admin','empleado','tecnico']],
          ['label'=>'Solicitudes','url'=>url('/solicitudes'),'roles'=>['admin','empleado','tecnico']],
          ['label'=>'Imágenes','url'=>url('/imagenes'),'roles'=>['admin']],
          ['label'=>'Notificaciones','url'=>url('/notificaciones'),'roles'=>['admin','empleado','tecnico']],
          ['label'=>'Categorías','url'=>url('/categorias'),'roles'=>['admin']],
          ['label'=>'Configuración','url'=>route('configuracion.index'),'roles'=>['admin']],
        ];
        $rolActual = $current?->rol ?? 'invitado';
      @endphp
      <div class="links-list" aria-hidden="false" style="justify-content:flex-end;">
        @foreach($acciones as $a)
          @if(in_array($rolActual, $a['roles']))
            <a class="link-chip" href="{{ $a['url'] }}">{{ $a['label'] }}</a>
          @endif
        @endforeach
      </div>
    </div>
  </div>

  @if($isAdmin)
    <div class="summary-row" role="region" aria-label="Resumen rápido">
      <div class="summary-item">
        <div style="color:#6b7280;font-weight:700;">Total muebles</div>
        <div class="num">{{ \App\Models\Mueble::count() }}</div>
      </div>
      <div class="summary-item">
        <div style="color:#6b7280;font-weight:700;">Solicitudes totales</div>
        <div class="num">{{ \App\Models\Solicitud::count() }}</div>
      </div>
      <div class="summary-item">
        <div style="color:#6b7280;font-weight:700;">Usuarios</div>
        <div class="num">{{ \App\Models\Usuario::count() }}</div>
      </div>
    </div>

    <div class="dashboard-grid" role="list">
      <a class="card-cta" href="{{ url('/usuarios') }}" role="listitem" aria-label="Usuarios">
        <div class="title"><span class="icon-circle icon-users" aria-hidden="true"> </span> Gestión de usuarios</div>
        <div class="desc">Administración total del CRUD de usuarios.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Ver usuarios</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      <a class="card-cta" href="{{ url('/muebles') }}" role="listitem" aria-label="Inventario">
        <div class="title"><span class="icon-circle icon-muebles" aria-hidden="true"> </span> Inventario y mueble</div>
        <div class="desc">Administración total del CRUD de muebles.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Gestionar inventario</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      <a class="card-cta" href="{{ url('/solicitudes') }}" role="listitem" aria-label="Solicitudes">
        <div class="title"><span class="icon-circle icon-solicitudes" aria-hidden="true"> </span> Solicitudes</div>
        <div class="desc">Administración total del CRUD de solicitudes.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Revisar solicitudes</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      <a class="card-cta" href="{{ url('/notificaciones') }}" role="listitem" aria-label="Notificaciones">
        <div class="title"><span class="icon-circle icon-notificaciones" aria-hidden="true"> </span> Notificaciones</div>
        <div class="desc">Ver y gestionar notificaciones por audiencia, estado y destino.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Ir a notificaciones</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      <a class="card-cta" href="{{ url('/categorias') }}" role="listitem" aria-label="Categorías">
        <div class="title"><span class="icon-circle icon-categorias" aria-hidden="true"> </span> Categorías</div>
        <div class="desc">Gestiona las categorías utilizadas por los muebles.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Administrar categorías</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      <a class="card-cta" href="{{ route('configuracion.index') }}" role="listitem" aria-label="Configuración">
        <div class="title"><span class="icon-circle icon-configuracion" aria-hidden="true"> </span> Configuración</div>
        <div class="desc">Ajustes de la aplicación: tema, colores y opciones globales.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Configuración</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>

      @if($isAdmin)
      <a class="card-cta" href="{{ url('/imagenes') }}" role="listitem" aria-label="Imágenes">
        <div class="title"><span class="icon-circle icon-images" aria-hidden="true"> </span> Imágenes</div>
        <div class="desc">Administración total del CRUD de imágenes.</div>
        <div class="meta">
          <span style="color:#64748b;font-weight:700;">Administrar imágenes</span>
          <span><span class="card-btn">Ir</span></span>
        </div>
      </a>
      @endif
    </div>

  @else
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
      <div class="card-cta">
        <div class="title"><span class="icon-circle icon-muebles">🪑</span> Inventario</div>
        <div class="desc">Explora los muebles disponibles (se ocultan los que están en reparación) y solicita el que necesites.</div>
        <div class="meta">
          <a class="card-btn" href="{{ url('/muebles') }}">Ver inventario</a>
        </div>
      </div>

      <div class="card-cta">
        <div class="title"><span class="icon-circle icon-solicitudes">📩</span> Mis solicitudes</div>
        <div class="desc">Revisa tus solicitudes actuales, estado y crea nuevas solicitudes rápidamente.</div>
        <div class="meta">
          <a class="card-btn" href="{{ url('/solicitudes') }}">Ver solicitudes</a>
        </div>
      </div>
      @if($isAdmin)
      <div class="card-cta">
        <div class="title"><span class="icon-circle icon-users">👥</span> Directorio</div>
        <div class="desc">Consulta el listado de usuarios y sus áreas (solo lectura).</div>
        <div class="meta">
          <a class="card-btn" href="{{ url('/usuarios') }}">Ver usuarios</a>
        </div>
      </div>
      @endif
    </div>
  @endif
</div>
@endsection
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');});
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');}
        });
    });
    </script>
</body>
</html>

<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.dashboard-container {
    display: flex;
}


.content {
    flex: 1;
    padding: 20px;
}

.header {
    background-color: #fff;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header h1 {
    margin: 0;
}

@media (max-width: 768px) {
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.dashboard-container {
    display: flex;
}

.sidebar {
    width: 250px;
    background-color: #333;
    color: #fff;
    height: 100vh;
    padding: 20px;
}

.sidebar h2 {
    color: #fff;
    text-align: center;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.sidebar ul li a:hover {
    background-color: #575757;
    padding: 10px;
    border-radius: 5px;
}

.sidebar ul li a .icon {
    margin-right: 10px;
}

.content {
    flex: 1;
    padding: 20px;
}

.header {
    background-color: #fff;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header h1 {
    margin: 0;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
    }

    .dashboard-container {
        flex-direction: column;
    }
}
</style>