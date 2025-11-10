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
.modal-card { transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease; transform-origin: top center; opacity:1; max-height:1200px; overflow:hidden; }
.modal-card.closing { transform: scaleY(0.86) translateY(-6px); opacity:0; padding-top:2px; padding-bottom:2px; max-height:0; overflow:hidden; }
.modal-card.collapsed { transform: scaleY(0.98); opacity:0; max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; }

#notifs-table {
    transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease;
    transform-origin: top center;
    opacity: 1;
    max-height: 2000px;
    overflow: hidden;
}
#notifs-table.closing {
    transform: scaleY(0.96) translateY(-6px);
    opacity: 0;
    padding-top: 2px;
    padding-bottom: 2px;
    max-height: 0;
    pointer-events: none;
}
#notifs-table.collapsed {
    transform: scaleY(0.98);
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    overflow: hidden;
}

#notifications { position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:8px; }
#notifications .notif { min-width:220px; max-width:420px; padding:10px 14px; border-radius:10px; color:#fff; font-weight:700; transform:translateY(-6px); opacity:0; transition:transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease; box-shadow:0 8px 24px rgba(2,6,23,0.08); }
#notifications .notif.visible { transform:none; opacity:1; }
#notifications .notif-success { background: linear-gradient(90deg,#10b981,#059669); }
#notifications .notif-error { background: linear-gradient(90deg,#ef4444,#b91c1c); }
#notifications .notif-info { background: linear-gradient(90deg,#6366f1,#06b6d4); }
.table-pager { text-align:right;color:#6b7280;font-size:0.85rem;margin-top:6px;margin-bottom:12px; display:flex; gap:8px; align-items:center; justify-content:flex-end; }
.table-pager button.pager-btn { background:#f3f4f6;border:1px solid #e5e7eb;padding:6px 8px;border-radius:6px;cursor:pointer;font-weight:700; }
.table-pager button.pager-btn:disabled { opacity:0.5; cursor:default; }
.table-pager .page-indicator { min-width:90px; text-align:center; color:#374151; font-weight:700; }
</style>

<div style="padding:16px;max-width:1100px;margin:0 auto;">
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
    const perPage = {{ $perPage }};
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    const icons = @json($icons);

    function escapeHtml(s){ if(!s && s!==0) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function formatDate(d){
        if(!d) return '-';
        try { const dt = new Date(d); if(isNaN(dt)) return escapeHtml(d); return dt.toLocaleString('es-ES', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' }); }
        catch(e){ return escapeHtml(d); }
    }

    function renderRow(n){
        const tipoIcon = icons[n.tipo] ?? '🔔';
        const usuarioNombre = (n.usuario && n.usuario.nombre) ? (escapeHtml(n.usuario.nombre) + ' ' + escapeHtml(n.usuario.apellido ?? '')) : 'Todos';
        const descripcion = escapeHtml(n.descripcion || '');
        const fecha = n.fecha_creacion ? formatDate(n.fecha_creacion) : '-';
        const ruta = n.ruta ? escapeHtml(n.ruta) : '';
        return `<tr style="border-bottom:1px solid #f3f4f6;">
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
                <button type="button" class="btn-edit" data-notif='${escapeHtml(JSON.stringify(n))}' style="background:linear-gradient(90deg,#6366f1,#06b6d4);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Editar</button>
                <form action="/notificaciones/${escapeHtml(n.id)}" method="POST" style="display:inline">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" data-confirm="¿Eliminar notificación #${escapeHtml(n.id)}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;cursor:pointer;" data-confirm-type="delete">Eliminar</button>
                </form>
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
            const dir = btn.classList.contains('prev') ? 'prev' : 'next';
            if(dir === 'prev') btn.disabled = (page <= 1);
            else btn.disabled = (page >= totalPages);
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
            let page = parseInt(tbody.dataset.page || '1', 10);
            if(btn.classList.contains('prev')) page = Math.max(1, page - 1);
            else page = page + 1;
            tbody.dataset.page = String(page);
            renderTable(key, page);
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