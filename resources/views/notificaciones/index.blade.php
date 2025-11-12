<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notificaciones | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Notificaciones | Inventario Muebles')

@section('content')
@php
    $current = null;
    if (session()->has('usuario_id')) {
        $current = \App\Models\Usuario::find(session('usuario_id'));
    }
    $isAdmin = $current && ($current->rol === 'admin');

    use Carbon\Carbon;
    Carbon::setLocale('es');

    $estadoOrder = ['cerrada' => 0, 'abierta' => 1, 'vista' => 2];
    $tmp = $notificaciones->sortByDesc('fecha_creacion');
    $notificaciones_sorted = $tmp->sortBy(function($n) use ($estadoOrder) {
        return $estadoOrder[$n->estado] ?? 99;
    })->values();

    $perPage = 10;
    $icons = ['prueba'=>'🧪','aprobada'=>'✅','rechazada'=>'❌','otra'=>'🔔'];

    $ligadas = collect();
    $aud_todos = collect();
    $aud_admins = collect();
    $aud_usuarios = collect();
    $cerradas = collect();
    $abiertas = collect();
    $vistas = collect();

    if ($isAdmin && $current) {
        $ligadas = $notificaciones_sorted->filter(fn($x) => isset($x->id_admin) && $x->id_admin == $current->id)->values();
        $aud_todos = $notificaciones_sorted->filter(fn($x) => ($x->audiencia ?? '') === 'todos')->values();
        $aud_admins = $notificaciones_sorted->filter(fn($x) => ($x->audiencia ?? '') === 'admins')->values();
        $aud_usuarios = $notificaciones_sorted->filter(fn($x) => ($x->audiencia ?? '') === 'usuarios')->values();
        $cerradas = $notificaciones_sorted->filter(fn($x) => ($x->estado ?? '') === 'cerrada')->values();
        $abiertas = $notificaciones_sorted->filter(fn($x) => ($x->estado ?? '') === 'abierta')->values();
        $vistas   = $notificaciones_sorted->filter(fn($x) => ($x->estado ?? '') === 'vista')->values();
    } else {
        if ($current) {
            $visible = $notificaciones_sorted->filter(fn($n) => !empty($n->id_usuario) && $n->id_usuario == $current->id)->values();
            $cerradas = $visible->filter(fn($x) => ($x->estado ?? '') === 'cerrada')->values();
            $abiertas = $visible->filter(fn($x) => ($x->estado ?? '') === 'abierta')->values();
            $vistas   = $visible->filter(fn($x) => ($x->estado ?? '') === 'vista')->values();
        }
    }
@endphp

<style>
:root{
  --bg: #f7fafc;
  --card: #ffffff;
  --muted: #6b7280;
  --accent: #0f172a;
  --radius: 12px;
  --row-h: 56px;
  --shadow-sm: 0 6px 20px rgba(6,15,28,0.06);
  --shadow-md: 0 18px 48px rgba(3,10,30,0.08);
  --glass: rgba(255,255,255,0.72);
}
body { background: linear-gradient(180deg,#fbfdff 0%, #f3f7fb 100%); color:var(--accent); font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
.container { max-width: 1100px; margin: 20px auto; padding: 18px; box-sizing: border-box; }
.list-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(245,249,255,0.9));
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.list-card { overflow: visible; }
.list-card table { width:100%; table-layout: auto; min-width: 0 !important; border-collapse: separate; border-spacing:0; }
.list-card thead, .list-card tbody, .list-card tr { width:100%; box-sizing: border-box; }
.list-card th, .list-card td {
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: anywhere;
  hyphens: auto;
}
.list-card tbody {
  display: table-row-group;
  max-height: none;
  overflow: visible;
}
.list-card thead {
  display: table-header-group;
  width:100%;
  table-layout: auto;
}
.list-card tbody tr {
  display: table-row;
  table-layout: auto;
  width:100%;
  height: var(--row-h);
  transition: transform .18s ease, box-shadow .18s ease;
  border-radius:10px;
}
.list-card tbody tr td:first-child { width:64px; font-weight:700; color:var(--muted); }
.list-card tbody tr td:last-child { width:230px; text-align:right; }
.table-pager { display:flex; align-items:center; justify-content:center; gap:10px; padding:8px 6px; }
.pager-btn {
  background: var(--glass);
  border: 1px solid rgba(15,23,42,0.06);
  padding:8px 10px; border-radius:10px; cursor:pointer; font-weight:700; color:var(--accent);
  min-width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center;
}
.pager-btn:disabled { opacity:.45; cursor:default; }
.page-indicator {
  min-width:140px; text-align:center; font-weight:700; color:var(--muted); padding:6px 12px; border-radius:10px;
  background: transparent;
}
#notifications { position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:10px; }
#notifications .notif {
  border-radius:10px; padding:10px 14px; font-weight:700; color:#fff; box-shadow:var(--shadow-sm);
}
#notifications .notif-success { background: linear-gradient(90deg,#10b981,#059669); }
#notifications .notif-error { background: linear-gradient(90deg,#ef4444,#d43f3f); }
#notifications .notif-info { background: linear-gradient(90deg,#6366f1,#06b6d4); }
.modal-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,255,0.98));
  border-radius: 12px; box-shadow: var(--shadow-md); padding:16px;
}
.modal-card .modal-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:8px; }
.table-row.tipo-prueba {
  border-radius: 13%; box-shadow: var(--shadow-md); padding:16px;
  background: linear-gradient(90deg, rgba(137,133,139,0.18) 0%, rgba(95,22,138,0.18) 100%);
}
.table-row.tipo-aprobada {
  border-radius: 13%; box-shadow: var(--shadow-md); padding:16px;
  background: linear-gradient(90deg, rgba(148,158,147,0.18) 0%, rgba(55,94,53,0.18) 100%);
}
.table-row.tipo-rechazada {
  border-radius: 63%; box-shadow: var(--shadow-md); padding:16px;
  background: linear-gradient(90deg, rgba(199,189,189,0.18) 0%, rgba(139,0,0,0.18) 100%);
}
.table-row.tipo-otra {
  border-radius: 13%; box-shadow: var(--shadow-md); padding:16px;
  background: linear-gradient(90deg, rgba(247,236,246,0.22) 0%, rgba(139,70,148,0.18) 100%);
}
.table-row td {
  background: transparent !important;
}
.list-card th,
.list-card td {
  font-size: 0.88rem;
  line-height: 1.2;
}
.list-card tbody tr td { padding: 10px 12px; }
.list-card thead th { padding: 10px 12px; }
@media (max-width:900px) {
  .list-card tbody tr td { padding:10px 8px; }
  .page-indicator { min-width:100px; font-size:0.9rem; }
}
@media (max-width:640px) {
  :root { --row-h:54px; }
  .list-card thead { display:none; }
  .list-card tbody tr { display:block; margin-bottom:10px; height:auto; }
  .list-card tbody tr td { display:flex; justify-content:space-between; padding:10px; white-space:normal; }
  .list-card tbody tr td:first-child { width:auto; font-weight:700; }
  .list-card tbody tr td:last-child { width:auto; text-align:right; }
  .table-pager { gap:8px; }
}
</style>

<div id="notifs-lists" style="padding:16px;max-width:1100px;margin:0 auto;">
    <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Notificaciones</h1>
            @if($current)
                <div style="font-weight:700;color:#111;">Conectado: {{ $current->nombre }} {{ $current->apellido }}</div>
                <div style="font-size:0.9rem;color:#6b7280;">{{ $current->email }}</div>
            @endif
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            @if($isAdmin)
                <button id="btn-new" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Nueva notificación</button>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    @php
        $cerradas = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'cerrada')->values();
        $abiertas = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'abierta')->values();
        $vistas   = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'vista')->values();
    @endphp
    @if($isAdmin)
        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Notificaciones ligadas a mi (ID {{ $current->id }})</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-ligadas" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:10px 12px;">ID</th>
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        <th style="padding:10px 12px;">Usuario</th>
                        <th style="padding:10px 12px;width:190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-ligadas" data-items='@json($ligadas)'></tbody>
            </table>
        </div>
        @if($ligadas->count() > $perPage)
            <div class="table-pager" data-target="ligadas">
                <button class="pager-btn prev" data-target="ligadas" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-ligadas">Página <strong>1</strong> de <strong>{{ ceil($ligadas->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="ligadas" aria-label="Siguiente">›</button>
            </div>
        @endif

        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Audiencia: todos</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-aud-todos" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:10px 12px;">ID</th>
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        <th style="padding:10px 12px;">Usuario</th>
                        <th style="padding:10px 12px;width:190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-aud_todos" data-items='@json($aud_todos)'></tbody>
            </table>
        </div>
        @if($aud_todos->count() > $perPage)
            <div class="table-pager" data-target="aud_todos">
                <button class="pager-btn prev" data-target="aud_todos" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-aud_todos">Página <strong>1</strong> de <strong>{{ ceil($aud_todos->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="aud_todos" aria-label="Siguiente">›</button>
            </div>
        @endif

        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Audiencia: admins</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-aud-admins" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:10px 12px;">ID</th>
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        <th style="padding:10px 12px;">Usuario</th>
                        <th style="padding:10px 12px;width:190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-aud_admins" data-items='@json($aud_admins)'></tbody>
            </table>
        </div>
        @if($aud_admins->count() > $perPage)
            <div class="table-pager" data-target="aud_admins">
                <button class="pager-btn prev" data-target="aud_admins" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-aud_admins">Página <strong>1</strong> de <strong>{{ ceil($aud_admins->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="aud_admins" aria-label="Siguiente">›</button>
            </div>
        @endif

        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Audiencia: usuarios</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-aud-usuarios" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:10px 12px;">ID</th>
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        <th style="padding:10px 12px;">Usuario</th>
                        <th style="padding:10px 12px;width:190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-aud_usuarios" data-items='@json($aud_usuarios)'></tbody>
            </table>
        </div>
        @if($aud_usuarios->count() > $perPage)
            <div class="table-pager" data-target="aud_usuarios">
                <button class="pager-btn prev" data-target="aud_usuarios" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-aud_usuarios">Página <strong>1</strong> de <strong>{{ ceil($aud_usuarios->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="aud_usuarios" aria-label="Siguiente">›</button>
            </div>
        @endif
    @else
        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Cerradas</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-cerradas" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                        @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody id="tbody-cerradas" data-items='@json($cerradas)'></tbody>
            </table>
        </div>
        @if($cerradas->count() > $perPage)
            <div class="table-pager" data-target="cerradas">
                <button class="pager-btn prev" data-target="cerradas" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-cerradas">Página <strong>1</strong> de <strong>{{ ceil($cerradas->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="cerradas" aria-label="Siguiente">›</button>
            </div>
        @endif

        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Abiertas</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-abiertas" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                        @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody id="tbody-abiertas" data-items='@json($abiertas)'></tbody>
            </table>
        </div>
        @if($abiertas->count() > $perPage)
            <div class="table-pager" data-target="abiertas">
                <button class="pager-btn prev" data-target="abiertas" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-abiertas">Página <strong>1</strong> de <strong>{{ ceil($abiertas->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="abiertas" aria-label="Siguiente">›</button>
            </div>
        @endif

        <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Vistas</h3>
        <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:6px;">
            <table id="table-vistas" style="width:100%;border-collapse:collapse;min-width:720px;">
                <thead>
                    <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                        @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                        <th style="padding:10px 12px;">Tipo</th>
                        <th style="padding:10px 12px;">Descripción</th>
                        <th style="padding:10px 12px;">Fecha creación</th>
                        @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                        @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody id="tbody-vistas" data-items='@json($vistas)'></tbody>
            </table>
        </div>
        @if($vistas->count() > $perPage)
            <div class="table-pager" data-target="vistas">
                <button class="pager-btn prev" data-target="vistas" aria-label="Anterior">‹</button>
                <div class="page-indicator" id="indicator-vistas">Página <strong>1</strong> de <strong>{{ ceil($vistas->count() / $perPage) }}</strong></div>
                <button class="pager-btn next" data-target="vistas" aria-label="Siguiente">›</button>
            </div>
        @endif
    @endif
</div>

<div id="notifs-filters" style="max-width:1100px;margin:0 auto 12px;padding:12px;background:#fff;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.04);">
  <form id="notifs-filters-form" method="GET" action="{{ url('/notificaciones') }}" style="display:flex;flex-wrap:wrap;gap:8px;align-items:end;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <div>
        <label style="display:block;font-weight:700">ID</label>
        <input type="search" name="id" value="{{ request('id') }}" placeholder="id" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>

      @if($isAdmin)
        <div>
          <label style="display:block;font-weight:700">ID admin</label>
          <select name="id_admin" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">Cualquiera</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}" {{ request('id_admin') == $u->id ? 'selected' : '' }}>{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="display:block;font-weight:700">ID usuario</label>
          <select name="id_usuario" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">Cualquiera</option>
            <option value="none" {{ request('id_usuario') === 'none' ? 'selected' : '' }}>Ninguno</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}" {{ (string)request('id_usuario') === (string)$u->id ? 'selected' : '' }}>{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
      @else
        <input type="hidden" name="id_usuario" value="{{ $current ? $current->id : '' }}">
      @endif

      <div>
        <label style="display:block;font-weight:700">Estado</label>
        <select name="estado" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach(['cerrada','abierta','vista'] as $e)
            <option value="{{ $e }}" {{ request('estado') == $e ? 'selected' : '' }}>{{ $e }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block;font-weight:700">Tipo</label>
        <select name="tipo" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach(['prueba','aprobada','rechazada','otra'] as $t)
            <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block;font-weight:700">Audiencia</label>
        <select name="audiencia" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Cualquiera</option>
          <option value="todos" {{ request('audiencia')=='todos' ? 'selected' : '' }}>todos</option>
          <option value="admins" {{ request('audiencia')=='admins' ? 'selected' : '' }}>admins</option>
          <option value="usuarios" {{ request('audiencia')=='usuarios' ? 'selected' : '' }}>usuarios</option>
        </select>
      </div>

      <div>
        <label style="display:block;font-weight:700">Descripción (contiene)</label>
        <input type="search" name="descripcion" value="{{ request('descripcion') }}" placeholder="texto" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>

      <div>
        <label style="display:block;font-weight:700">Ruta (contiene)</label>
        <input type="search" name="ruta" value="{{ request('ruta') }}" placeholder="/solicitudes/..." style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>

      <div>
        <label style="display:block;font-weight:700">Fecha creación desde</label>
        <input type="date" name="fecha_creacion_from" value="{{ request('fecha_creacion_from') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:700">Fecha creación hasta</label>
        <input type="date" name="fecha_creacion_to" value="{{ request('fecha_creacion_to') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>

      <div>
        <label style="display:block;font-weight:700">Fecha visto desde</label>
        <input type="date" name="fecha_visto_from" value="{{ request('fecha_visto_from') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:700">Fecha visto hasta</label>
        <input type="date" name="fecha_visto_to" value="{{ request('fecha_visto_to') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
    </div>

    <div style="display:flex;gap:8px;">
      <button type="submit" class="btn-edit" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;font-weight:700;">Aplicar</button>
      <button type="button" id="notifs-clear-filters" class="btn-delete" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;font-weight:700;">Limpiar</button>
    </div>
  </form>
</div>

<div id="notif-form-card" class="modal-card collapsed" style="display:none;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin:16px auto;max-width:1100px;">
    <h2 id="notif-form-title" style="margin:0 0 8px 0;font-size:1.05rem;">Nueva notificación</h2>
    <form id="notif-form" method="POST" action="{{ url('/notificaciones') }}">
        @csrf
        <input type="hidden" name="_method" id="notif-form-method" value="POST">
        <input type="hidden" name="id" id="notif-id" value="">

        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            <div style="flex:1 1 220px;min-width:180px;">
                <label>Para (usuario)</label>
                <select name="id_usuario" id="f-id_usuario" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div style="flex:1 1 180px;min-width:160px;">
                <label>Estado</label>
                <select name="estado" id="f-estado" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="cerrada">cerrada</option>
                    <option value="abierta">abierta</option>
                    <option value="vista">vista</option>
                </select>
            </div>

            <div style="flex:1 1 180px;min-width:160px;">
                <label>Tipo</label>
                <select name="tipo" id="f-tipo" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="prueba">prueba</option>
                    <option value="aprobada">aprobada</option>
                    <option value="rechazada">rechazada</option>
                    <option value="otra">otra</option>
                </select>
            </div>

            <div style="flex:1 1 260px;min-width:200px;">
                <label>Ruta (opcional)</label>
                <input name="ruta" id="f-ruta" type="text" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>

            <div style="flex:1 1 420px;min-width:220px;">
                <label>Descripción</label>
                <textarea name="descripcion" id="f-descripcion" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>
            </div>

            <div id="f-fecha_visto_row" style="flex:1 1 220px;min-width:180px;display:none;">
                <label>Fecha visto</label>
                <input name="fecha_visto" id="f-fecha_visto" type="datetime-local" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:12px;">
            <button type="submit" id="notif-save" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Guardar</button>
            <button id="notif-cancel" type="button" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
        </div>
    </form>
</div>

<div id="notifications" aria-live="polite" style="position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:8px;"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  (function(){
    const lists = Array.from(document.querySelectorAll('.list-card'));
    if (!lists.length) return;
    const supportedRO = typeof ResizeObserver !== 'undefined';
    const lastHeights = new WeakMap();

    function isVisible(el){
      const r = el.getBoundingClientRect();
      return !(r.bottom < 0 || r.top > window.innerHeight);
    }

    function compensateScroll(el, oldH, newH){
      const delta = newH - oldH;
      if (delta === 0) return;
      if (!isVisible(el)) return;
      const shift = oldH - newH;
      if (Math.abs(shift) < 1) return;
      const maxDown = document.documentElement.scrollHeight - window.innerHeight - window.scrollY;
      const clamped = Math.max(-window.scrollY, Math.min(shift, maxDown));
      if (Math.abs(clamped) < 1) return;
      window.scrollBy({ top: clamped, left: 0, behavior: 'smooth' });
    }

    if (supportedRO) {
      const ro = new ResizeObserver(entries => {
        entries.forEach(entry => {
          const el = entry.target;
          const prev = lastHeights.get(el) ?? (entry.contentRect ? entry.contentRect.height : el.offsetHeight);
          const curr = entry.contentRect ? entry.contentRect.height : el.offsetHeight;
          lastHeights.set(el, curr);
          setTimeout(() => compensateScroll(el, prev, curr), 30);
        });
      });
      lists.forEach(l => { lastHeights.set(l, l.offsetHeight); ro.observe(l); });
    } else {
      lists.forEach(l => lastHeights.set(l, l.offsetHeight));
      setInterval(() => {
        lists.forEach(l => {
          const prev = lastHeights.get(l) || l.offsetHeight;
          const curr = l.offsetHeight;
          if (curr !== prev) {
            lastHeights.set(l, curr);
            compensateScroll(l, prev, curr);
          }
        });
      }, 250);
    }
  })();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const perPage = {{ $perPage }};
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    const icons = @json($icons);

    function escapeHtml(s){ if(!s && s!==0) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function formatDate(d){ if(!d) return '-'; try { const dt = new Date(d); if(isNaN(dt)) return escapeHtml(d); return dt.toLocaleString('es-ES', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' }); } catch(e){ return escapeHtml(d); } }
    const modalCard = document.getElementById('notif-form-card');
    const modalForm = document.getElementById('notif-form');
    const notifFormMethod = document.getElementById('notif-form-method');
    const notifIdInput = document.getElementById('notif-id');
    const formTitle = document.getElementById('notif-form-title');
    const btnSave = document.getElementById('notif-save');
    const btnCancel = document.getElementById('notif-cancel');
    const btnNew = document.getElementById('btn-new');
    const listsContainer = document.getElementById('notifs-lists');
    const notificationsFloat = document.getElementById('notifications');

    function showToast(msg, type = 'info', timeout = 2200) {
        if(!notificationsFloat) return alert(msg);
        const el = document.createElement('div');
        el.className = 'notif notif-' + (type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'));
        el.textContent = msg;
        el.style.padding = '10px 14px';
        el.style.borderRadius = '10px';
        el.style.color = '#fff';
        el.style.fontWeight = '700';
        el.style.boxShadow = '0 8px 24px rgba(2,6,23,0.08)';
        el.style.transform = 'translateY(-6px)';
        el.style.opacity = '0';
        notificationsFloat.appendChild(el);
        requestAnimationFrame(()=> { el.classList.add('visible'); el.style.opacity='1'; el.style.transform='none'; });
        setTimeout(()=> {
            el.classList.remove('visible');
            el.addEventListener('transitionend', ()=> el.remove(), { once: true });
        }, timeout);
    }

    function showModal() {
        if (!modalCard) return;
        if (listsContainer) listsContainer.style.display = 'none';
        modalCard.style.display = 'block';
        modalCard.classList.remove('collapsed','closing');
        setTimeout(()=> modalCard.style.transform = '', 20);
        modalCard.scrollIntoView({behavior:'smooth', block:'center'});
    }
    function hideModal() {
        if (!modalCard) return;
        modalCard.classList.add('closing');
        setTimeout(()=>{ modalCard.style.display = 'none'; modalCard.classList.remove('closing'); if (listsContainer) listsContainer.style.display = ''; }, 260);
    }

    if (btnNew) {
        btnNew.addEventListener('click', function(){
            notifFormMethod.value = 'POST';
            notifIdInput.value = '';
            modalForm.action = '{{ url('/notificaciones') }}';
            formTitle.textContent = 'Nueva notificación';
            ['f-id_usuario','f-estado','f-tipo','f-ruta','f-descripcion','f-fecha_visto'].forEach(id=>{
                const el = document.getElementById(id);
                if (!el) return;
                if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.value = '';
            });
            const selE = document.getElementById('f-estado');
            if (selE) selE.value = 'cerrada';
            showModal();
        });
    }
    window.openEdit = function(notif){
        if(!notif || !modalForm) return;
        formTitle.textContent = 'Editar notificación #' + (notif.id ?? '');
        notifFormMethod.value = 'PUT';
        notifIdInput.value = notif.id ?? '';
        modalForm.action = '/notificaciones/' + (notif.id ?? '');
        const selUsuario = document.getElementById('f-id_usuario');
        const selEstado = document.getElementById('f-estado');
        const selTipo = document.getElementById('f-tipo');
        const inpRuta = document.getElementById('f-ruta');
        const txtDesc = document.getElementById('f-descripcion');
        const fechaRow = document.getElementById('f-fecha_visto_row');
        const fechaInp = document.getElementById('f-fecha_visto');
        if (selUsuario) { selUsuario.value = notif.id_usuario ?? ''; }
        if (selEstado) { selEstado.value = notif.estado ?? 'cerrada'; }
        if (selTipo) { selTipo.value = notif.tipo ?? 'prueba'; }
        if (inpRuta) { inpRuta.value = notif.ruta ?? ''; }
        if (txtDesc) { txtDesc.value = notif.descripcion ?? ''; }
        if (fechaInp) {
            if (notif.fecha_visto) {
                try {
                    const d = new Date(notif.fecha_visto);
                    const pad = n => String(n).padStart(2,'0');
                    const isoLocal = d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
                    fechaInp.value = isoLocal;
                } catch(e){ fechaInp.value = ''; }
            } else fechaInp.value = '';
        }
        if (fechaRow) {
            fechaRow.style.display = (notif.estado === 'vista') ? 'block' : 'none';
        }
        showModal();
    };
    if (btnCancel) {
        btnCancel.addEventListener('click', function(ev){
            ev.preventDefault();
            notifFormMethod.value = 'POST';
            notifIdInput.value = '';
            modalForm.action = '{{ url('/notificaciones') }}';
            formTitle.textContent = 'Nueva notificación';
            hideModal();
        });
    }
    if (modalForm) {
        modalForm.addEventListener('submit', async function(ev){
            ev.preventDefault();
            try {
                const url = modalForm.action;
                const method = (notifFormMethod.value === 'PUT') ? 'POST' : (notifFormMethod.value === 'POST' ? 'POST' : 'POST');
                const data = new FormData(modalForm);
                const resp = await fetch(url, {
                    method: method,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: data
                });
                if (resp.ok) {
                    const json = await resp.json().catch(()=> null);
                    hideModal();
                    showToast('Notificación guardada correctamente', 'success');
                    setTimeout(()=> location.reload(), 900);
                } else {
                    let errText = 'Error al guardar';
                    try {
                        const j = await resp.json();
                        if (j && j.errors) {
                            const first = Object.values(j.errors)[0];
                            errText = Array.isArray(first) ? first[0] : String(first);
                        } else if (j && j.message) errText = j.message;
                    } catch(e){}
                    hideModal();
                    showToast(errText, 'error');
                    if (listsContainer) listsContainer.style.display = '';
                }
            } catch (e) {
                hideModal();
                showToast('Error de red al guardar', 'error');
                if (listsContainer) listsContainer.style.display = '';
                console.error('save notif error', e);
            }
        });
    }

    const selEstadoGlobal = document.getElementById('f-estado');
    if (selEstadoGlobal) {
        selEstadoGlobal.addEventListener('change', function(){
            const fechaRow = document.getElementById('f-fecha_visto_row');
            if (!fechaRow) return;
            fechaRow.style.display = (this.value === 'vista') ? 'block' : 'none';
        });
    }

    function renderRow(n){
        const tipoIcon = icons[n.tipo] ?? '🔔';
        const usuarioNombre = (n.usuario && n.usuario.nombre) ? (escapeHtml(n.usuario.nombre) + ' ' + escapeHtml(n.usuario.apellido ?? '')) : 'Todos';
        const descripcion = escapeHtml(n.descripcion || '');
        const fecha = n.fecha_creacion ? formatDate(n.fecha_creacion) : '-';
        const estadoCls = escapeHtml(n.estado || 'cerrada');
        const tipoCls = escapeHtml(n.tipo || 'otra');
        return `<tr class="table-row estado-${estadoCls} tipo-${tipoCls}" data-estado="${estadoCls}" data-tipo="${tipoCls}" style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:10px 12px;">${escapeHtml(n.id)}</td>
            <td style="padding:10px 12px;">
                <span style="display:inline-flex;gap:8px;align-items:center;">
                    <span aria-hidden="true">${tipoIcon}</span>
                    <span style="font-weight:700;text-transform:capitalize;">${escapeHtml(n.tipo)}</span>
                </span>
            </td>
            <td style="padding:10px 12px;">${descripcion}</td>
            <td style="padding:10px 12px;">${fecha}</td>
            <td style="padding:10px 12px;">${usuarioNombre}</td>
            <td style="padding:10px 12px;width:190px;">
                <button type="button" class="btn-view-route" onclick="window.location.href='${'/notificaciones/' + escapeHtml(n.id)}'" style="background:linear-gradient(90deg,#10b981,#059669);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Ver Detalle</button>
                @if($isAdmin)
                <button type="button" class="btn-edit" data-notif='${escapeHtml(JSON.stringify(n))}' style="background:linear-gradient(90deg,#6366f1,#06b6d4);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Editar</button>
                <form action="/notificaciones/${escapeHtml(n.id)}" method="POST" style="display:inline">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" data-confirm="¿Eliminar notificación #${escapeHtml(n.id)}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;cursor:pointer;" data-confirm-type="delete">Eliminar</button>
                </form>
                @endif 
            </td>
        </tr>`;
    }

    function renderTable(key, page){
        const tbody = document.getElementById('tbody-' + key);
        if(!tbody) return;
        const items = JSON.parse(tbody.getAttribute('data-items') || '[]');
        const total = items.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        page = Math.max(1, Math.min(page, totalPages));
        const start = (page -1) * perPage;
        const slice = items.slice(start, start + perPage);
        tbody.innerHTML = slice.map(n => renderRow(n)).join('') || `<tr><td colspan="${isAdmin ? 6 : 4}" style="padding:12px;">No hay resultados.</td></tr>`;

        const indicator = document.getElementById('indicator-' + key);
        if(indicator) indicator.innerHTML = `Página <strong>${page}</strong> de <strong>${totalPages}</strong> — total ${total}`;
        document.querySelectorAll(`.pager-btn[data-target="${key}"]`).forEach(btn=>{
            const isPrev = btn.classList.contains('prev');
            const shouldDisable = isPrev ? (page <= 1) : (page >= totalPages);
            btn.disabled = shouldDisable;
            btn.classList.toggle('disabled', shouldDisable);
            btn.style.opacity = shouldDisable ? '0.45' : '1';
            btn.style.cursor = shouldDisable ? 'default' : 'pointer';
            btn.setAttribute('aria-disabled', shouldDisable ? 'true' : 'false');
            if (shouldDisable) {btn.setAttribute('title', isPrev ? 'No hay página anterior' : 'No hay página siguiente');} else {btn.removeAttribute('title');}
        });
        document.querySelectorAll(`#tbody-${key} .btn-edit`).forEach(btn=>{
            btn.removeEventListener('click', btn._handler);
            const handler = function(){
                try {
                    const notif = JSON.parse(this.getAttribute('data-notif'));
                    if(typeof openEdit === 'function') { openEdit(notif); }
                    else { console.log('Editar', notif); }
                } catch(e) { console.error('invalid notif json', e); }
            };
            btn._handler = handler;
            btn.addEventListener('click', handler);
        });
    }

    document.querySelectorAll('tbody[id^="tbody-"]').forEach(tbody=>{
        const key = tbody.id.replace('tbody-', '');
        tbody.dataset.page = tbody.dataset.page || '1';
        renderTable(key, parseInt(tbody.dataset.page,10) || 1);
    });
    document.querySelectorAll('.table-pager').forEach(pager=>{
        pager.addEventListener('click', function(ev){
            const btn = ev.target.closest('button.pager-btn');
            if(!btn) return;
            const key = btn.getAttribute('data-target');
            const tbody = document.getElementById('tbody-' + key);
            if(!tbody) return;
            const pagerElem = pager;
            const beforeRect = pagerElem.getBoundingClientRect();
            const beforeDistBottom = window.innerHeight - beforeRect.bottom;

            let page = parseInt(tbody.dataset.page || '1', 10);
            if(btn.classList.contains('prev')) page = Math.max(1, page - 1);
            else page = page + 1;
            tbody.dataset.page = String(page);

            renderTable(key, page);
            setTimeout(() => {
                const afterRect = pagerElem.getBoundingClientRect();
                const desiredAfterBottom = window.innerHeight - beforeDistBottom;
                const deltaViewport = afterRect.bottom - desiredAfterBottom;
                if (Math.abs(deltaViewport) > 1) {
                    window.scrollBy({ top: deltaViewport, left: 0, behavior: 'smooth' });
                }
            }, 40);
        });
    });
    document.querySelectorAll('tbody[id^="tbody-"]').forEach(tbody=>{
        const key = tbody.id.replace('tbody-', '');
        const page = parseInt(tbody.dataset.page || '1', 10) || 1;
        renderTable(key, page);
    });
    window.renderTable = renderTable;
});
</script>
@endsection
</body>
</html>