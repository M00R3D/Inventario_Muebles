<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Muebles | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Muebles | Inventario Muebles')

@section('content')
@php
    $current = null;
    if (session()->has('usuario_id')) {$current = \App\Models\Usuario::find(session('usuario_id'));}
    $isAdmin = $current && ($current->rol === 'admin');

    $visibleMuebles = $isAdmin ? $muebles : $muebles->filter(function($m){
        return empty($m->usuario);
    });
@endphp
<style>
:root{ --bg:#f8fafc; --card:#fff; --muted:#6b7280; --accent1:#6366f1; --accent2:#06b6d4; }
.container{max-width:1200px;margin:0 auto;padding:18px;}
.header-hero{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
.header-hero h1{margin:0;font-size:3.25rem}
.grid{ display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:18px; }

.card{ background:var(--card); border-radius:12px; padding:14px; box-shadow:0 12px 34px rgba(2,6,23,0.08); display:flex; flex-direction:column; gap:12px; min-height:220px; }
.card-inner{ display:flex; flex-direction:column; gap:12px; align-items:stretch; }
.card-media{ width:100%; display:flex; align-items:center; justify-content:center; background:#f8fafc; border-radius:8px; padding:6px; max-height:240px; overflow:hidden; }
.card-media img{ max-width:100%; height:auto; max-height:200px; object-fit:contain; border-radius:6px; box-shadow:0 6px 18px rgba(2,6,23,0.06); }

.preview-wrapper img { display:block; max-width:320px; max-height:180px; width:auto; height:auto; object-fit:contain; border-radius:6px; }

.card-info{ flex:1 1 auto; display:flex; flex-direction:column; gap:8px; }
.card-top{ display:flex; align-items:center; justify-content:space-between; gap:12px; }
.card-title{ font-weight:800; font-size:1rem; color:#111; max-width:60%; word-break:break-word; }
.card-desc{ color:var(--muted); font-size:0.95rem; line-height:1.25; }
.card-meta{
  display:flex;
  gap:12px;
  align-items:flex-start;
  margin-top:6px;
  color:var(--muted);
  font-size:0.9rem;
  flex-wrap:wrap;
}
.card-meta > div {
  flex: 1 1 140px;
  min-width: 0;
  word-break: break-word;
  overflow-wrap: anywhere;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.card-meta strong { display:block; font-weight:700; margin-bottom:2px; }
.card-responsable, .card-solicitante { color:var(--muted); font-size:0.9rem; }

.mueble-nota{ display:flex; flex-wrap:wrap; white-space:normal; word-break:break-word; overflow-wrap:break-word; max-width:100%; margin-top:6px; font-size:0.9rem; color:#374151; background:#f8fafc; padding:6px 8px; border-radius:8px; }

.card-actions{ display:flex;gap:50%;  justify-content:flex-start; align-items:center; margin-top:auto; }

.estado-badge{ display:inline-flex; align-items:center; justify-content:center; padding:4px 10px; border-radius:999px; font-size:0.78rem; font-weight:700; min-width:94px; text-align:center; box-shadow:0 2px 6px rgba(2,6,23,0.06); }
.estado-bueno{ background:#10b981; color:#ffffff; }    
.estado-regular{ background:#f59e0b; color:#0b0b0b; }  
.estado-malo{ background:#ef4444; color:#ffffff; }     
.estado-en_reparacion{ background:#6366f1; color:#ffffff; } 

.btn-base{
  color:#fff;
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 8px 20px rgba(2,6,23,0.06);
  transition: transform .12s ease, box-shadow .12s ease;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}
.btn-base:hover{ transform: translateY(-3px); }
.btn-base:active{ transform: translateY(-1px); }
.btn-base:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-new, .btn-save { background: linear-gradient(90deg,var(--accent1),var(--accent2)); color:#fff; }
.btn-clear, .btn-cancel { background: linear-gradient(90deg,#ef4444,#f97316); color:#fff; }

.btn-save, .btn-cancel {
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 8px 20px rgba(99,102,241,0.08);
  transition: transform .12s ease, box-shadow .12s ease;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}
.btn-save:hover, .btn-cancel:hover{ transform: translateY(-3px); }
.btn-save:active, .btn-cancel:active{ transform: translateY(-1px); }
.btn-save:focus, .btn-cancel:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-new, .btn-save { background: linear-gradient(90deg,var(--accent1),var(--accent2)); }
.btn-clear, .btn-cancel { background: linear-gradient(90deg,#ef4444,#f97316); }

.btn-clear.light {
  background:transparent;color:#ef4444;border:1px solid #ef4444;font-weight:700;
}

.btn-edit{ background: linear-gradient(90deg,var(--accent1),var(--accent2)); color:#fff; padding:8px 10px; border-radius:8px; border:0; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(99,102,241,0.08); transition: transform .12s ease; }
.btn-edit:hover{ transform: translateY(-3px); }
.btn-edit:active{ transform: translateY(-1px); }
.btn-edit:focus{ outline:3px solid rgba(99,102,241,0.12); }
.btn-delete{ background: linear-gradient(90deg,#810a0aff,#d63867ff); color:#fff; padding:8px 10px; border-radius:8px; border:0; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(99,102,241,0.08); transition: transform .12s ease; }
.btn-delete:hover{ transform: translateY(-3px); }
.btn-delete:active{ transform: translateY(-1px); }
.btn-delete:focus{ outline:3px solid rgba(99,102,241,0.12); }
.read-more{ margin-left:8px; color:#06b6d4; font-weight:700; text-decoration:none; }

@media (max-width:700px){
  .card-inner{ flex-direction:column; }
  .card-media{ width:100%; max-height:240px; }
  .card-top{ flex-direction:row; gap:8px; }
  .card-actions{ justify-content:flex-start; }
}

.hide-admin .admin-only { display: none !important; }
#table-wrapper.minimal { margin:0; padding:0; }
#table-view {
  width:100%;
  border-collapse:collapse;
  font-size:0.78rem;
  background:transparent;
  box-shadow:none;
}
#table-view thead th {
  font-weight:600;
  padding:6px 6px;
  border-bottom:1px solid #e6e6e6;
  text-align:left;
}
#table-view td {
  padding:6px 6px;
  border-bottom:1px solid #f1f1f1;
  vertical-align:middle;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
#table-view td.small-desc { max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
#table-view .estado-badge { min-width:0; padding:3px 6px; font-size:0.72rem; }
#table-view img { max-width:36px; max-height:24px; object-fit:cover; margin-right:6px; vertical-align:middle; }
#table-wrapper.minimal table, #table-wrapper.minimal thead, #table-wrapper.minimal tbody, #table-wrapper.minimal tr, #table-wrapper.minimal th, #table-wrapper.minimal td { border-spacing:0; margin:0; }
</style>

<div class="container">
  <div class="header-hero">
    <div>
      <h1>Muebles</h1>
      @if(session('success'))
        <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-top:8px;font-weight:700;">
          {{ session('success') }}
        </div>
      @endif
    </div>

    <div style="display:flex;gap:12px;align-items:center">
      <label style="display:flex;align-items:center;gap:8px;font-weight:700;">
        <span style="font-size:0.9rem;color:#374151;">Vista comprimida</span>
        <input type="checkbox" id="view-toggle" style="width:44px;height:26px;appearance:none;background:#e5e7eb;border-radius:999px;position:relative;cursor:pointer;outline:none;display:inline-block;">
        <style>
          #view-toggle{position:relative;padding:0;margin:0 4px;}
          #view-toggle:before{content:'';position:absolute;left:3px;top:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform .18s ease, background .18s;}
          #view-toggle:checked{background:linear-gradient(90deg,#6366f1,#06b6d4);box-shadow:0 6px 18px rgba(99,102,241,0.12);}
          #view-toggle:checked:before{transform:translateX(18px);}
        </style>
      </label>

      @if(!empty($isAdmin) && $isAdmin)
        <label style="display:flex;align-items:center;gap:8px;font-weight:700;">
          <span style="font-size:0.9rem;color:#374151;">Ver como usuario</span>
          <input type="checkbox" id="admin-toggle" style="width:44px;height:26px;appearance:none;background:#e5e7eb;border-radius:999px;position:relative;cursor:pointer;outline:none;display:inline-block;">
          <style>
            #admin-toggle{position:relative;padding:0;margin:0 4px;}
            #admin-toggle:before{content:'';position:absolute;left:3px;top:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform .18s ease, background .18s;}
            #admin-toggle:checked{background:linear-gradient(90deg,#ef4444,#f97316);box-shadow:0 6px 18px rgba(239,68,68,0.08);}
            #admin-toggle:checked:before{transform:translateX(18px);}
          </style>
        </label>
      @endif

      @if(!empty($isAdmin) && $isAdmin)
        <button id="btn-new" class="btn-base btn-new admin-only" type="button">Nuevo mueble</button>
      @endif
    </div>
  </div>

  <form id="filters" method="GET" action="{{ url('/muebles') }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;align-items:end;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Código</label>
        <input name="codigo" type="search" value="{{ request('codigo') }}" placeholder="buscar código" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Descripción</label>
        <input name="descripcion" type="search" value="{{ request('descripcion') }}" placeholder="buscar descripción" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Estado</label>
        <select name="estado" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          <option value="bueno" {{ request('estado')=='bueno' ? 'selected' : '' }}>Bueno</option>
          <option value="regular" {{ request('estado')=='regular' ? 'selected' : '' }}>Regular</option>
          <option value="malo" {{ request('estado')=='malo' ? 'selected' : '' }}>Malo</option>
          <option value="en_reparacion" {{ request('estado')=='en_reparacion' ? 'selected' : '' }}>En reparación</option>
        </select>
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.7rem;">Responsable(admin)</label>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Solicitante(usuario)</label>
        <select name="persona_id" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach($usuarios->where('rol','!=','admin') as $u)
            <option value="{{ $u->id }}" {{ request('persona_id')==$u->id ? 'selected' : '' }}>{{ $u->nombre }} {{ $u->apellido }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Desde</label>
        <input type="date" name="desde" value="{{ request('desde') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Hasta</label>
        <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
    </div>

    <div style="display:flex;gap:8px;">
      <button type="submit" class="btn-base btn-save">Buscar</button>
      <button type="button" id="btn-clear" class="btn-base btn-clear">Limpiar</button>
    </div>
  </form>

  <div id="user-form-card" class="modal-card collapsed" style="display:none;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
    <h2 id="form-title" style="margin:0 0 8px 0;font-size:1.05rem;">Nuevo mueble</h2>
    <form id="mueble-form" method="POST" action="{{ url('/muebles') }}">
      @csrf
      <input type="hidden" name="_method" id="form-method" value="POST">
      <input type="hidden" name="id" id="mueble-id" value="">
      <div style="display:flex;flex-wrap:wrap;gap:10px;">
        <div style="flex:1;min-width:160px;">
          <label>Código</label>
          <input name="codigo" id="f-codigo" type="text" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1;min-width:200px;">
          <label>Descripción</label>
          <input name="descripcion" id="f-descripcion" type="text" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>
        <div style="min-width:160px;">
          <label>Fecha registro</label>
          <input name="fecha_registro" id="f-fecha" type="date" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>
        @if($isAdmin)
        <div style="min-width:160px;">
          <label>Monto unitario</label>
          <input name="monto_unitario" id="f-monto" type="number" step="0.01" required style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>
        @endif
        <div style="flex:1;min-width:160px;">
          <label>Responsable</label>
          <label>Solicitante</label>
          <select name="persona_id" id="f-persona" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">(ninguno)</option>
            @foreach($usuarios->where('rol','!=','admin') as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1;min-width:160px;">
          <label>Responsable</label>
          <select name="responsable_id" id="f-responsable" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">(ninguno)</option>
            @foreach($usuarios->where('rol','admin') as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
        <div style="min-width:160px;">
          <label>Estado</label>
          <select name="estado" id="f-estado" required style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="bueno">Bueno</option>
            <option value="regular">Regular</option>
            <option value="malo">Malo</option>
            <option value="en_reparacion">En reparación</option>
          </select>
        </div>
        <div style="width:100%;">
          <label>Nota</label>
          <input name="nota" id="f-nota" type="text" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>

        <div style="display:flex;gap:8px;align-items:center;margin-top:8px;">
          <div style="flex:1;min-width:160px;">
            <label>Carpeta pública</label>
            <select id="select-folder" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
              <option value="">Selecciona carpeta (public)</option>
              @isset($dirs)
                @foreach($dirs as $d)
                  <option value="{{ $d }}">{{ $d }}</option>
                @endforeach
              @endisset
            </select>
          </div>
          <div style="flex:1;min-width:160px;">
            <label>Archivo</label>
            <select id="select-file" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
              <option value="">-- elegir --</option>
            </select>
          </div>
          <div style="min-width:120px;text-align:center;">
            <label>Preview</label>
            <div style="margin-top:6px;">
                <div class="preview-wrapper">
                    <img id="ruta-preview" src="{{ url('/imgs/default.webp') }}" alt="Preview" />
                </div>
            </div>
          </div>
        </div>
        <input type="hidden" name="ruta_img" id="f-ruta-img" value="">
      </div>

      <div style="display:flex;gap:8px;margin-top:12px;">
        <button type="submit" id="btn-save" class="btn-base btn-save">Guardar</button>
        <button id="btn-cancel" type="button" class="btn-base btn-cancel">Cancelar</button>
      </div>
    </form>
  </div>

  @if($visibleMuebles->isEmpty())
    <div class="card">No hay muebles registrados aún.</div>
  @else
    <div class="grid" role="list">
      @foreach($visibleMuebles as $m)
        <article class="card" role="listitem" aria-labelledby="mueble-{{ $m->id }}">
          <div class="card-inner">
            <div class="card-media">
              @if($m->ruta_img)
                <img src="{{ asset($m->ruta_img ?? 'imgs/default.webp') }}" alt="Imagen mueble">
              @else
                <div style="background:linear-gradient(180deg,#f3f4f6,#e5e7eb);display:flex;align-items:center;justify-content:center;border-radius:8px;color:#9ca3af;padding:18px;">
                  Sin imagen
                </div>
              @endif
            </div>

            <div class="card-info">
              <div class="card-top">
                <div class="card-title">{{ $m->codigo ?? 'ID '.$m->id }}</div>
                <?php $estadoClass = 'estado-'.($m->estado ?? ''); ?>
                <span class="estado-badge {{ $estadoClass }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span>
              </div>

              <div class="card-desc">
                @php
                  $full = trim($m->descripcion ?? '');
                  if (preg_match('/\R/', $full)) {
                      $parts = preg_split('/\R+/', $full);
                      $firstPara = trim($parts[0] ?? '');
                      $rest = trim(implode("\n\n", array_slice($parts, 1)));
                      $showRead = $rest !== '';
                  } else {
                      $firstPara = \Illuminate\Support\Str::limit($full, 160);
                      $rest = $full;
                      $showRead = strlen($full) > strlen($firstPara);
                  }
                @endphp
                <div class="desc-short">{{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 120) }}</div>
                @if($showRead)
                  <a href="#" class="read-more" data-full="{{ $rest }}">Leer más</a>
                @endif
                <div class="desc-full" style="display:none;">{{ $rest }}</div>
              </div>
              <div class="card-meta">
                @if($isAdmin)
                <div class="card-price admin-only">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</div>
                <div class="card-responsable admin-only"><strong>Responsable(admin):</strong>
                  {{ $m->responsable ? ($m->responsable->nombre . ' ' . $m->responsable->apellido) : 'ninguno' }}
                </div>
                <div class="card-solicitante admin-only"><strong>Solicitante(usuario):</strong>
                  {{ $m->usuario ? ($m->usuario->nombre . ' ' . $m->usuario->apellido) : 'ninguno' }}
                </div>
              </div>
              @endif
              @php $nota = trim($m->nota ?? '') @endphp
              @if($nota)
                <div class="mueble-nota">{{ \Illuminate\Support\Str::limit($nota, 120) }}</div>
              @endif
            </div>
          </div>
          <div class="card-actions">
            @if(!empty($isAdmin) && $isAdmin)
              <button type="button" class="btn-edit admin-only" data-mueble='@json($m)'>Editar</button>
              <form action="{{ url('/muebles/'.$m->id) }}" method="POST" style="margin:0;" class="admin-only">
                @csrf
                @method('DELETE')
                <button type="submit" data-confirm="¿Eliminar mueble {{ addslashes($m->codigo ?? 'ID '.$m->id) }}?" class="btn-delete" data-confirm-type="delete">Eliminar</button>
              </form>
            @else
              <a href="{{ url('/solicitudes/create') }}?mueble_id={{ $m->id }}" class="btn-edit" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">Solicitar</a>
            @endif
          </div>
        </article>
         @endforeach
    </div>
  @endif

  <div id="table-wrapper" class="minimal" style="display:none;">
    <table id="table-view" role="table" aria-label="Listado comprimido">
      <thead>
        <tr>
          <th>Código</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Responsable</th>
          <th>Solicitante</th>
          @if($isAdmin)<th class="admin-only">Monto</th>@endif
          <th>Acc.</th>
        </tr>
      </thead>
      <tbody>
        @forelse($muebles as $m)
          <tr>
            <td>{{ $m->codigo ?? 'ID '.$m->id }}</td>
            <td class="small-desc">{{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 60) }}</td>
            <td><span class="estado-badge estado-{{ $m->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span></td>
            <td>{{ $m->responsable ? ($m->responsable->nombre . ' ' . $m->responsable->apellido) : 'ninguno' }}</td>
            <td>{{ $m->usuario ? ($m->usuario->nombre . ' ' . $m->usuario->apellido) : 'ninguno' }}</td>
            @if($isAdmin)<td class="admin-only">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</td>@endif
            <td>
              @if(!empty($isAdmin) && $isAdmin)
                <button type="button" class="btn-edit admin-only" data-mueble='@json($m)'>Editar</button>
                <form action="{{ url('/muebles/'.$m->id) }}" method="POST" style="display:inline;margin:0;" class="admin-only">
                  @csrf
                  @method('DELETE')
                  <button type="submit" data-confirm="¿Eliminar mueble {{ addslashes($m->codigo ?? 'ID '.$m->id) }}?" class="btn-delete" data-confirm-type="delete">Eliminar</button>
                </form>
              @else
                <a href="{{ url('/solicitudes/create') }}?mueble_id={{ $m->id }}" class="btn-edit" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;font-size:0.78rem;">Solicitar</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="{{ $isAdmin ? 7 : 6 }}" style="padding:8px;">No hay muebles</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const card = document.getElementById('user-form-card');
  const form = document.getElementById('mueble-form');
  const btnNew = document.getElementById('btn-new');
  const btnCancel = document.getElementById('btn-cancel');
  const btnSave = document.getElementById('btn-save');
  const methodInput = document.getElementById('form-method');
  const idInput = document.getElementById('mueble-id');
  const title = document.getElementById('form-title');
  const filtersEl = document.getElementById('filters');
  const cardsGrid = document.querySelector('.grid');
  const folderSelect = document.getElementById('select-folder');
  const fileSelect = document.getElementById('select-file');
  const rutaInput = document.getElementById('f-ruta-img');
  const preview = document.getElementById('ruta-preview');
  const baseUrl = "{{ url('/') }}";
  const API_BASE = "{{ url('/muebles') }}";
  const IS_ADMIN = {!! json_encode(!empty($isAdmin) && $isAdmin) !!};
  const STORAGE_KEY = 'muebles_filters_v1';
  const VIEW_KEY = 'muebles_view_mode_v1'; 
  const ADMIN_VIEW_KEY = 'muebles_admin_view_v1'; 
  const viewToggle = document.getElementById('view-toggle');
  const adminToggle = document.getElementById('admin-toggle');
  const tableWrapper = document.getElementById('table-wrapper');
  const gridEl = document.querySelector('.grid');

  function setView(mode){
    if(mode === 'table'){
      if(tableWrapper) tableWrapper.style.display = 'block';
      if(gridEl) gridEl.style.display = 'none';
      if(viewToggle) viewToggle.checked = true;
    } else {
      if(tableWrapper) tableWrapper.style.display = 'none';
      if(gridEl) gridEl.style.display = 'grid';
      if(viewToggle) viewToggle.checked = false;
    }
    try { localStorage.setItem(VIEW_KEY, mode); } catch(e){}
  }

  function setAdminView(mode){
    const container = document.querySelector('.container');
    if (!container) return;
    if (mode === 'user') {
      container.classList.add('hide-admin');
      if (adminToggle) adminToggle.checked = true;
    } else {
      container.classList.remove('hide-admin');
      if (adminToggle) adminToggle.checked = false;
    }
    try { localStorage.setItem(ADMIN_VIEW_KEY, mode); } catch(e){}
  }

  try {
    const storedView = localStorage.getItem(VIEW_KEY) || 'cards';
    setView(storedView);
  } catch(e){ setView('cards'); }

  try {
    if (IS_ADMIN && adminToggle) {
      const storedAdminView = localStorage.getItem(ADMIN_VIEW_KEY) || 'admin';
      setAdminView(storedAdminView);
      adminToggle.addEventListener('change', function(){
        setAdminView(this.checked ? 'user' : 'admin');
      });
    } else {
      const container = document.querySelector('.container');
      if (container) container.classList.remove('hide-admin');
    }
  } catch(e){ if (IS_ADMIN) setAdminView('admin'); }

  if (viewToggle) {
    viewToggle.addEventListener('change', function(){
      setView(this.checked ? 'table' : 'cards');
    });
  }

  async function fetchAndRenderMuebles(filters = {}) {
    const grid = document.querySelector('.grid');
    const table = document.querySelector('#table-view tbody');
    if (!grid && !table) return;
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([k,v])=>{
      if (v === null || v === undefined) return;
      const s = String(v).trim();
      if (s.length === 0) return;
      params.set(k, s);
    });
    const url = API_BASE + (params.toString() ? ('?' + params.toString()) : '');
    try {
      const resp = await fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      let items = await resp.json();
      // si no es admin, ocultar en cliente los muebles que ya tienen solicitante
      if (!IS_ADMIN && Array.isArray(items)) {
        items = items.filter(m => !m.usuario);
      }
      const esc = s => String(s ?? '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      if (grid) {
        grid.innerHTML = items.length ? items.map(m => {
          const imgUrl = m.ruta_img ? (`${baseUrl}/${esc(m.ruta_img)}`) : (`${baseUrl}/imgs/default.webp`);
          const nota = m.nota ? `<div class="mueble-nota">${esc(m.nota)}</div>` : '';
          const solicitante = m.usuario ? esc((m.usuario.nombre||'') + ' ' + (m.usuario.apellido||'')) : 'ninguno';
          const responsable = m.responsable ? esc((m.responsable.nombre||'') + ' ' + (m.responsable.apellido||'')) : 'ninguno';
          const priceHtml = IS_ADMIN ? (m.monto_unitario ? `<div class="card-price admin-only">$${Number(m.monto_unitario).toFixed(2)}</div>` : `<div class="card-price admin-only"></div>`) : '';
          const actionsHtml = IS_ADMIN
            ? `<button type="button" class="btn-base btn-edit admin-only" data-mueble='${esc(JSON.stringify(m))}'>Editar</button>
               <button type="button" class="btn-base btn-delete admin-only" data-id="${esc(m.id)}" data-confirm="¿Eliminar mueble ${esc(m.codigo || ('ID ' + m.id))}?" data-confirm-type="delete" data-confirm-callback="confirmDeleteById">Eliminar</button>`
            : `<a class="btn-base btn-new" href="${baseUrl}/solicitudes/create?mueble_id=${m.id}">Solicitar</a>`;
          return `<article class="card" role="listitem" aria-labelledby="mueble-${esc(m.id)}">
              <div class="card-inner">
                <div class="card-media"><img src="${imgUrl}" alt="${esc(m.codigo||'')}" /></div>
                <div class="card-info">
                  <div class="card-top">
                    <div class="card-title">${esc(m.codigo || ('ID ' + m.id))}</div>
                    <span class="estado-badge estado-${esc(m.estado || '')}">${esc((m.estado || '').replace('_',' '))}</span>
                  </div>
                  <div class="card-desc">${esc(m.descripcion || '-')}</div>
                  ${nota}
                  <div class="card-meta">
                    ${priceHtml}
                    @if($isAdmin)
                    <div class="card-responsable"><strong>Responsable(admin):</strong> ${responsable}</div>
                    <div class="card-solicitante"><strong>Solicitante(usuario):</strong> ${solicitante}</div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="card-actions">
                ${actionsHtml}
              </div>
            </article>`;
        }).join('') : '<div class="card">No hay muebles</div>';
      }

      if (table) {
        table.innerHTML = items.length ? items.map(m => {
          const codigo = esc(m.codigo || ('ID ' + m.id));
          const descripcion = esc((m.descripcion || '-').slice(0, 120));
          const estado = esc((m.estado || '').replace('_',' '));
          const responsable = m.responsable ? esc((m.responsable.nombre||'') + ' ' + (m.responsable.apellido||'')) : 'ninguno';
          const solicitante = m.usuario ? esc((m.usuario.nombre||'') + ' ' + (m.usuario.apellido||'')) : 'ninguno';
          const monto = IS_ADMIN ? (`<td class="admin-only" style="padding:8px 6px;">$${Number(m.monto_unitario||0).toFixed(2)}</td>`) : '';
          const actions = IS_ADMIN
            ? `<td style="padding:6px;">
                <button type="button" class="btn-edit admin-only" data-mueble='${esc(JSON.stringify(m))}' style="padding:6px 8px;font-size:0.85rem;">Editar</button>
                <button type="button" class="btn-delete admin-only" data-id="${esc(m.id)}" data-confirm="¿Eliminar mueble ${codigo}?" style="padding:6px 8px;font-size:0.85rem;">Eliminar</button>
               </td>`
            : `<td style="padding:6px;"><a class="btn-edit" href="${baseUrl}/solicitudes/create?mueble_id=${m.id}" style="padding:6px 8px;font-size:0.85rem;">Solicitar</a></td>`;
          return `<tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:8px 6px;white-space:nowrap;">${codigo}</td>
                    <td style="padding:8px 6px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${descripcion}</td>
                    <td style="padding:8px 6px;"><span class="estado-badge estado-${esc(m.estado||'')}" style="padding:4px 8px;font-size:0.8rem;">${estado}</span></td>
                    <td style="padding:8px 6px;">${responsable}</td>
                    <td style="padding:8px 6px;">${solicitante}</td>
                    ${monto}
                    ${actions}
                  </tr>`;
        }).join('') : `<tr><td colspan="${IS_ADMIN ? 7 : 6}" style="padding:12px;">No hay muebles</td></tr>`;
      }

      document.querySelectorAll('.btn-edit[data-mueble]').forEach(btn=>{
        btn.addEventListener('click', function(){
          try { const obj = JSON.parse(this.getAttribute('data-mueble')); openEdit(obj); } catch(e){ console.error(e); }
        });
      });
      document.querySelectorAll('.btn-delete[data-id]').forEach(btn=>{
        btn.addEventListener('click', function(evt){
          evt.preventDefault(); evt.stopPropagation();
          if (typeof window.showConfirmFor === 'function') { window.showConfirmFor(this); return; }
          if (!confirm(this.getAttribute('data-confirm'))) return;
          window.confirmDeleteById && window.confirmDeleteById(this);
        });
      });

    } catch (err) {
      console.error('Error cargando muebles:', err);
      if (grid) grid.innerHTML = '<div class="card">Error cargando muebles</div>';
      if (table) table.innerHTML = `<tr><td colspan="${IS_ADMIN ? 7 : 6}" style="padding:12px;">Error cargando muebles</td></tr>`;
    }
  }

  (function wireFilters(){
    const f = document.getElementById('filters');
    if (!f) return;
    f.addEventListener('submit', function(evt){
      evt.preventDefault();
      const filters = readFiltersFromForm();
      try { localStorage.setItem(STORAGE_KEY, JSON.stringify(filters)); } catch(e){}
      if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles(filters);
      if (window.history && history.replaceState) history.replaceState(null, '', '/muebles');
    });

    const btnClear = document.getElementById('btn-clear');
    if (btnClear) {
      btnClear.addEventListener('click', function(evt){
        evt.preventDefault();
        const ff = document.getElementById('filters');
        if (ff) {
          ff.querySelectorAll('input,select').forEach(i=>{
            if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
            else if (i.type !== 'submit' && i.type !== 'button') i.value = '';
          });
        }
        try { localStorage.removeItem(STORAGE_KEY); } catch(e){}
        if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles({});
        if (window.history && history.replaceState) history.replaceState(null,'','/muebles');
      });
    }

    try {
      const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
      if (stored && Object.keys(stored).length > 0) {
        applyFiltersToForm(stored);
        fetchAndRenderMuebles(stored);
        if (window.history && history.replaceState) history.replaceState(null, '', '/muebles');
        return;
      }
    } catch(e){}
    fetchAndRenderMuebles({});
  })();

  async function loadFilesForFolder(folder){
    fileSelect.innerHTML = '<option value="">Cargando…</option>';
    try {
      const u = new URL("{{ url('/imagenes/list') }}", window.location.origin);
      u.searchParams.set('folder', folder || '');
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const resp = await fetch(u.toString(), { headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'}, credentials:'same-origin' });
      if (!resp.ok) { fileSelect.innerHTML = '<option value="">Error</option>'; return; }
      const json = await resp.json();
      const files = json.files || [];
      fileSelect.innerHTML = '<option value="">-- elegir --</option>';
      files.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f;
        opt.textContent = f;
        fileSelect.appendChild(opt);
      });
    } catch(e){
      console.error(e);
      fileSelect.innerHTML = '<option value="">Error de red</option>';
    }
  }

  if (folderSelect) {
    folderSelect.addEventListener('change', function(){
      const folder = this.value || '';
      if (!folder) { fileSelect.innerHTML = '<option value="">-- elegir --</option>'; return; }
      loadFilesForFolder(folder);
    });
  }

  if (fileSelect) {
    fileSelect.addEventListener('change', function(){
      const file = this.value || '';
      const folder = folderSelect ? folderSelect.value : '';
      if (!file || !folder) { rutaInput.value = ''; preview.src = baseUrl + '/imgs/default.webp'; return; }
      const path = folder + '/' + file;
      rutaInput.value = path;
      preview.src = baseUrl + '/' + path;
    });
  }

  function setPreviewFromRuta(ruta) {
    if (!ruta) { preview.src = baseUrl + '/imgs/default.webp'; rutaInput.value = ''; return; }
    rutaInput.value = ruta;
    preview.src = baseUrl + '/' + ruta;
    const parts = ruta.split('/');
    if (parts.length >= 2) {
      const folder = parts.slice(0, parts.length-1).join('/');
      const file = parts[parts.length-1];
      if (folderSelect) {
        const opt = Array.from(folderSelect.options).find(o=>o.value === folder);
        if (opt) {
          folderSelect.value = folder;
          loadFilesForFolder(folder).then(()=> {
            const fo = Array.from(fileSelect.options).find(o=>o.value === file);
            if (fo) fileSelect.value = file;
          });
        }
      }
    }
  }

  function hidePageForModal(){
    if(filtersEl) filtersEl.style.display = 'none';
    if(cardsGrid) cardsGrid.style.display = 'none';
  }
  function showPageForModal(){
    if(filtersEl) filtersEl.style.display = 'flex';
    if(cardsGrid) cardsGrid.style.display = 'grid';
  }

  function openCreate(){
    title.textContent = 'Nuevo mueble';
    form.action = "{{ url('/muebles') }}";
    methodInput.value = 'POST';
    idInput.value = '';
    form.querySelectorAll('input,select').forEach(i=> i.value = '');
    const fResp = document.getElementById('f-responsable');
    if (fResp) fResp.value = '';
    card.style.display = 'block';
    card.classList.add('collapsed');
    hidePageForModal();
    requestAnimationFrame(()=> card.classList.remove('collapsed'));
  }
  function openEdit(m){
    title.textContent = 'Editar mueble — ID '+m.id;
    form.action = "{{ url('/muebles') }}/" + m.id;
    methodInput.value = 'PUT';
    idInput.value = m.id;
    document.getElementById('f-codigo').value = m.codigo || '';
    document.getElementById('f-descripcion').value = m.descripcion || '';
    document.getElementById('f-fecha').value = m.fecha_registro ? m.fecha_registro : '';
    document.getElementById('f-monto').value = m.monto_unitario || '';
    document.getElementById('f-persona').value = m.persona_id || '';
    try {
      const fr = document.getElementById('f-responsable');
      if (fr) fr.value = m.responsable_id || '';
    } catch(e){}
    document.getElementById('f-estado').value = m.estado || 'bueno';
    document.getElementById('f-nota').value = m.nota || '';
    setPreviewFromRuta(m.ruta_img || '');
    card.style.display = 'block';
    card.classList.add('collapsed');
    hidePageForModal();
    requestAnimationFrame(()=> card.classList.remove('collapsed'));
  }

  if (btnNew) btnNew.addEventListener('click', openCreate);
  if (btnCancel) btnCancel.addEventListener('click', function(){
    window.location.href = "{{ url('/muebles') }}";
  });

  document.querySelectorAll('.btn-edit').forEach(btn=>{  });
  document.addEventListener('click', function(e){
    const a = e.target.closest('.read-more');
    if(!a) return;
    e.preventDefault();
    const parent = a.closest('.card');
    const full = a.getAttribute('data-full') || '';
    const shortEl = parent.querySelector('.desc-short');
    const fullEl = parent.querySelector('.desc-full');
    const ell = parent.querySelector('.desc-ellipsis');
    if(fullEl.style.display === 'none' || fullEl.style.display === ''){
      shortEl.style.display = 'none';
      if(ell) ell.style.display = 'none';
      fullEl.style.display = 'inline';
      a.textContent = 'Leer menos';
    } else {
      shortEl.style.display = 'inline';
      if(ell) ell.style.display = 'inline';
      fullEl.style.display = 'none';
      a.textContent = 'Leer más';
    }
  });

  if (form){
    form.addEventListener('submit', async function(evt){
      evt.preventDefault();
      function todayStr(offsetDays = 0){
        const d = new Date();
        d.setDate(d.getDate() + offsetDays);
        return d.toISOString().slice(0,10);
      }
      const fechaInput = document.getElementById('f-fecha');
      if (fechaInput && !fechaInput.value) {
        fechaInput.value = todayStr(0);
      }
      const original = btnSave.textContent;
      btnSave.disabled = true;
      btnSave.textContent = 'Guardando...';
      const fd = new FormData(form);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const resp = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: fd,
          credentials: 'include'
        });
        const ct = resp.headers.get('content-type') || '';
        const data = ct.includes('application/json') ? await resp.json() : await resp.text();
        if (resp.ok) {
          window.location.href = "{{ url('/muebles') }}";
          return;
        }
        if (resp.status === 422 && data && data.errors){
          alert(Object.values(data.errors).flat().join('\n'));
        } else {
          alert((data && data.message) ? data.message : 'Error al guardar');
        }
      } catch(err){
        console.error(err);
        alert('Error de red');
      } finally {
        btnSave.disabled = false;
        btnSave.textContent = original;
      }
    });
  }

  const btnClear = document.getElementById('btn-clear');
  if (btnClear) {
    btnClear.addEventListener('click', function(evt){
      evt.preventDefault();
      const ff = document.getElementById('filters');
      if (ff) {
        ff.querySelectorAll('input,select').forEach(i=>{
          if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
          else if (i.type !== 'submit' && i.type !== 'button') i.value = '';
        });
      }
      try { localStorage.removeItem(STORAGE_KEY); } catch(e){}
      if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles({});
      if (window.history && history.replaceState) history.replaceState(null,'','/muebles');
    });
  }
});
</script>
@endsection

</body>
</html>