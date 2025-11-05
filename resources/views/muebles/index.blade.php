<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

.card-comments { margin-top:8px; font-size:0.85rem; color:var(--muted); display:flex; flex-direction:column; gap:6px; }
.card-comments .comment { background:#ffffff; border:1px solid #eef2ff; padding:6px 8px; border-radius:8px; color:#374151; display:flex; gap:8px; align-items:flex-start; font-size:0.85rem; }
.card-comments .comment .author { font-weight:700; color:#111; margin-right:6px; white-space:nowrap; font-size:0.78rem; }
.card-comments .comment .text { color:var(--muted); word-break:break-word; font-size:0.8rem; }
.card-comments .comment.more { background:transparent; border:none; color:var(--muted); font-weight:700; padding:0 6px; }
.card-comments .comment.small { padding:6px 8px; font-size:0.78rem; }
.card-comments { max-height: calc(3 * 3.2rem); overflow:hidden; }
.card-brand { margin-top:6px; display:flex; gap:8px; align-items:baseline; }
.card-brand .marca { font-weight:900; font-size:1.05rem; color:#111; }
.card-brand .modelo { font-weight:700; font-size:0.95rem; color:#374151; opacity:0.95; }
.card-brand .marca-label { font-weight:700; font-size:0.85rem; color:#374151; }
.card-brand .marca-value { font-weight:900; font-size:1.05rem; color:#111; }
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
        <label style="display:block;font-weight:600;font-size:0.9rem;">Marca</label>
        <select name="marca" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach(($marcas ?? collect()) as $ma)
            <option value="{{ $ma }}" {{ (string)request('marca') === (string)$ma ? 'selected' : '' }}>{{ $ma }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Modelo</label>
        <select name="modelo" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach(($modelos ?? collect()) as $mo)
            <option value="{{ $mo }}" {{ (string)request('modelo') === (string)$mo ? 'selected' : '' }}>{{ $mo }}</option>
          @endforeach
        </select>
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
        <label style="display:block;font-weight:600;font-size:0.9rem;">Desde</label>
        <input type="date" name="desde" value="{{ request('desde') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Hasta</label>
        <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
      </div>
      <div>
        <label style="display:block;font-weight:600;font-size:0.9rem;">Solicitante</label>
        <select name="persona_id" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          <option value="none" {{ request('persona_id') === 'none' ? 'selected' : '' }}>Ninguno</option>
          @foreach($usuarios->where('rol','!=','admin') as $u)
            <option value="{{ $u->id }}" {{ (string)request('persona_id')===(string)$u->id ? 'selected' : '' }}>{{ $u->nombre }} {{ $u->apellido }}</option>
          @endforeach
        </select>
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

      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <div style="flex:1 1 220px;">
          <label>Código</label>
          <input id="f-codigo-modal" name="codigo" required maxlength="50" type="text" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1 1 220px;">
          <label>Descripción</label>
          <input id="f-descripcion-modal" name="descripcion" maxlength="500" type="text" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1 1 160px;">
          <label>Fecha</label>
          <input id="f-fecha-modal" name="fecha_registro" type="date" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1 1 160px;">
          <label>Monto unitario</label>
          <input id="f-monto-modal" name="monto_unitario" required type="number" step="0.01" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1 1 200px;">
          <label>Marca</label>
          <select id="f-marca-modal" name="marca" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">(sin marca)</option>
            @foreach(($marcas ?? collect()) as $ma)
              <option value="{{ trim($ma) }}">{{ $ma }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1 1 200px;">
          <label>Modelo</label>
          <select id="f-modelo-modal" name="modelo" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">(sin modelo)</option>
            {{-- se rellenará dinámicamente según marca --}}
          </select>
        </div>
        <div style="flex:1 1 220px;">
          <label>Categoria</label>
          <select id="f-categoria-modal" name="categoria_id" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">Sin categoría</option>
            @foreach(\App\Models\Categoria::orderBy('nombre')->get() as $c)
              <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1 1 220px;">
          <label>Solicitante</label>
          <select id="f-persona-modal" name="persona_id" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">(ninguno)</option>
            @foreach($usuarios->where('rol','!=','admin') as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1 1 220px;">
          <label>Responsable</label>
          <select id="f-responsable-modal" name="responsable_id" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">(ninguno)</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1 1 160px;">
          <label>Estado</label>
          <select id="f-estado-modal" name="estado" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="bueno">Bueno</option>
            <option value="regular">Regular</option>
            <option value="malo">Malo</option>
            <option value="en_reparacion">En reparación</option>
          </select>
        </div>
        <div style="flex:1 1 320px;">
          <label>Nota</label>
          <textarea id="f-nota-modal" name="nota" rows="2" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;"></textarea>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-top:10px;">
        <button type="button" id="btn-save" class="btn-save btn-base">Guardar</button>
        <button type="button" id="btn-cancel" class="btn-cancel btn-base">Cancelar</button>
      </div>
    </form>
  </div>

  @if($visibleMuebles->isEmpty())
    <div class="card">No hay muebles registrados aún.</div>
  @else
    <div class="grid" role="list">
      @foreach($visibleMuebles as $m)
        <div class="card" role="listitem" data-id="{{ $m->id }}">
          <div class="card-inner">
            <div class="card-media">
              <img src="{{ $m->ruta_img ? url($m->ruta_img) : asset('imgs/default.webp') }}" alt="{{ $m->codigo ?? 'mueble' }}" onerror="this.src='{{ asset('imgs/default.webp') }}'">
            </div>
            <div class="card-info">
              <div class="card-top">
                <div class="card-title">{{ $m->codigo ?? 'ID '.$m->id }} — {{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 80) }}</div>
                <div><span class="estado-badge estado-{{ $m->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span></div>
              </div>

              @if(!empty($m->marca) || !empty($m->modelo))
                <div class="card-brand">
                  @if(!empty($m->marca)) <div class="marca">{{ $m->marca }}</div> @endif
                  @if(!empty($m->modelo)) <div class="modelo">{{ $m->modelo }}</div> @endif
                </div>
              @endif

              <div class="card-meta">
                @if(!empty($isAdmin) && $isAdmin)
                  <div class="card-price">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</div>
                  <div class="card-responsable"><strong>Responsable:</strong> {{ $m->responsable ? ($m->responsable->nombre . ' ' . $m->responsable->apellido) : 'ninguno' }}</div>
                @endif
                <div class="card-solicitante"><strong>Solicitante:</strong> {{ $m->usuario ? ($m->usuario->nombre . ' ' . $m->usuario->apellido) : 'ninguno' }}</div>
              </div>

              @php $commentsToShow = ($m->comentarios ?? collect())->take(3); @endphp
              <div class="card-comments">
                <strong>Comentarios:</strong>
                @if($commentsToShow->isNotEmpty())
                  @foreach($commentsToShow as $c)
                    @php $randColor = 'hsl('.rand(0,360).' '.rand(0,6).'% '.(90+rand(0,8)).'%)'; @endphp
                    <div class="comment small" style="background: {{ $randColor }};">
                      <div class="author">{{ $c->usuario ? ($c->usuario->nombre . ' ' . $c->usuario->apellido) : 'anonimo' }}</div>
                      <div class="text">{{ \Illuminate\Support\Str::limit($c->comentario, 200) }}</div>
                    </div>
                  @endforeach
                  @if(($m->comentarios->count() ?? 0) > 3)
                    <div class="comment more">+{{ $m->comentarios->count() - 3 }} más</div>
                  @endif
                @else
                  <div class="comment small">ninguno</div>
                @endif
              </div>

              <div class="card-actions">
                @if(!empty($isAdmin) && $isAdmin)
                  <form method="POST" action="{{ url('/muebles/'.$m->id) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="button" class="btn-edit" data-mueble='@json($m)'>Editar</button>
                    <button type="button" class="btn-delete" data-id="{{ $m->id }}" data-confirm="¿Eliminar mueble {{ $m->codigo ?? $m->id }}?" data-confirm-callback="confirmDeleteById">Eliminar</button>
                  </form>
                @else
                  <a class="btn-base btn-new" href="{{ url('/solicitudes/create?mueble_id=' . $m->id) }}">Solicitar</a>
                @endif
              </div>

            </div>
          </div>
        </div>
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
         <td class="table-comment">
           @if($m->comentarios && $m->comentarios->isNotEmpty())
             @php $first = $m->comentarios->first(); $author = $first->usuario ? ($first->usuario->nombre . ' ' . $first->usuario->apellido) : 'anonimo'; @endphp
             <strong>{{ $author }}:</strong> {{ \Illuminate\Support\Str::limit($first->comentario, 80) }}
             @if($m->comentarios->count() > 1) <span style="margin-left:6px;color:var(--muted);font-weight:700;">(+{{ $m->comentarios->count()-1 }})</span> @endif
           @else
             ninguno
           @endif
         </td>
            <td><span class="estado-badge estado-{{ $m->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span></td>
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
  // obtener btnNew UNA vez (no volver a declararlo más abajo)
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
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const DEFAULT_IMG = "{{ asset('imgs/default.webp') }}";
  const STORAGE_KEY = 'muebles_filters_v1';

  function readFiltersFromForm(){
    const f = document.getElementById('filters');
    if (!f) return {};
    const get = name => {
      const el = f.querySelector('[name="'+name+'"]');
      if (!el) return '';
      if (el.type === 'checkbox') return el.checked ? (el.value || true) : '';
      return el.value ?? '';
    };
    const persona = get('persona_id');
    return {
      codigo: get('codigo'),
      descripcion: get('descripcion'),
      marca: get('marca'),
      modelo: get('modelo'),
      estado: get('estado'),
      persona_id: persona === 'none' ? 'none' : (persona || ''),
      desde: get('desde'),
      hasta: get('hasta'),
    };
  }
  
  function applyFiltersToForm(filters = {}){
    try {
      const f = document.getElementById('filters');
      if (!f) return;
      Object.entries(filters).forEach(([k,v])=>{
        if (v === null || v === undefined) return;
        const el = f.querySelector('[name="'+k+'"]') || document.getElementById(k);
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!v;
        else el.value = String(v);
      });
    } catch(e){ console.error('applyFiltersToForm', e); }
  }

  (function wireFilters(){
    const f = document.getElementById('filters');
    if (!f) return;
    f.addEventListener('submit', function(evt){
      evt.preventDefault();
      const filters = readFiltersFromForm();
      try { localStorage.setItem(STORAGE_KEY, JSON.stringify(filters)); } catch(e){}
      if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles(filters);
    });

    const btnClear = document.getElementById('btn-clear');
    if (btnClear) {
      btnClear.addEventListener('click', function(evt){
        evt.preventDefault();
        const ff = document.getElementById('filters');
        if (ff) {
          ff.querySelectorAll('input,select,textarea').forEach(i=>{
            if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
            else if (i.type !== 'submit' && i.type !== 'button') i.value = '';
          });
        }
        try { localStorage.removeItem(STORAGE_KEY); } catch(e){}
        if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles({});
      });
    }

    try {
      const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
      if (stored && Object.keys(stored).length > 0) {
        applyFiltersToForm(stored);
        if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles(stored);
        return;
      }
    } catch(e){}
    if (typeof fetchAndRenderMuebles === 'function') fetchAndRenderMuebles({});
  })();

  function esc(v){
    if (v === null || v === undefined) return '';
    return String(v)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }

  function randomNearWhite(){
    const hue = Math.floor(Math.random() * 360);
    const sat = Math.floor(Math.random() * 6); // 0..5%
    const light = 92 + Math.floor(Math.random() * 7); // 92..98%
    return `hsl(${hue} ${sat}% ${light}%)`;
  }

  function setPreviewFromRuta(ruta){
    try {
      const previewEl = document.getElementById('ruta-preview');
      const rutaInputEl = document.getElementById('f-ruta-img');
      if (!previewEl && !rutaInputEl) return;
      if (!ruta) {
        if (previewEl) previewEl.src = DEFAULT_IMG;
        if (rutaInputEl) rutaInputEl.value = '';
        return;
      }
      if (rutaInputEl) rutaInputEl.value = ruta;
      if (previewEl) previewEl.src = (ruta.startsWith('http') ? ruta : (baseUrl + '/' + ruta));

      const parts = String(ruta).split('/');
      if (parts.length >= 2) {
        const folder = parts.slice(0, parts.length - 1).join('/');
        const file = parts[parts.length - 1];
        if (typeof loadFilesForFolder === 'function' && folderSelect && fileSelect) {
          folderSelect.value = folder;
          loadFilesForFolder(folder).then(() => {
            const fo = Array.from(fileSelect.options).find(o => o.value === file);
            if (fo) fileSelect.value = file;
          }).catch(()=>{});
        } else {
          if (folderSelect) {
            const optF = Array.from(folderSelect.options).find(o => o.value === folder);
            if (optF) folderSelect.value = folder;
          }
          if (fileSelect) {
            const optFile = Array.from(fileSelect.options).find(o => o.value === file);
            if (optFile) fileSelect.value = file;
          }
        }
      }
    } catch(e){
      console.error('setPreviewFromRuta error', e);
    }
  }

  async function fetchAndRenderMuebles(filters = {}) {
    const grid = document.querySelector('.grid');
    const tableBody = document.querySelector('#table-view tbody');
    const params = new URLSearchParams(filters || {});
    const url = API_BASE + (params.toString() ? ('?' + params.toString()) : '');
    try {
      const resp = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      const items = await resp.json();

      if (grid) {
        grid.innerHTML = items.length ? items.map(m => {
          const img = m.ruta_img ? (baseUrl + '/' + m.ruta_img) : DEFAULT_IMG;
          const solicitante = m.usuario ? esc((m.usuario.nombre||'') + ' ' + (m.usuario.apellido||'')) : 'ninguno';
          const responsable = m.responsable ? esc((m.responsable.nombre||'') + ' ' + (m.responsable.apellido||'')) : 'ninguno';

          const marcaHtml = m.marca ? `<div class="marca"><div class="marca-label">Marca:</div><div class="marca-value">${esc(m.marca)}</div></div>` : '';
          const modeloHtml = m.modelo ? `<div class="modelo"><div class="modelo-label">Modelo:</div><div class="modelo-value">${esc(m.modelo)}</div></div>` : '';
          const brandHtml = (marcaHtml || modeloHtml) ? `<div class="card-brand">${marcaHtml}${modeloHtml}</div>` : '';

          const comments = (m.comentarios || []).slice(0,3);
          let commentsHtml = comments.length ? comments.map(c => {
            const author = c.usuario ? esc((c.usuario.nombre||'') + ' ' + (c.usuario.apellido||'')) : 'anonimo';
            return `<div class="comment small" style="background:${randomNearWhite()};"><div class="author">${author}</div><div class="text">${esc(c.comentario)}</div></div>`;
          }).join('') : '<div class="comment small">ninguno</div>';
          if ((m.comentarios || []).length > 3) {
            commentsHtml += `<div class="comment more">+${(m.comentarios||[]).length - 3} más</div>`;
          }

          const actionsHtml = IS_ADMIN
            ? `<form method="POST" action="${API_BASE}/${esc(m.id)}" style="display:inline;">
                 <input type="hidden" name="_token" value="${csrfToken}">
                 <input type="hidden" name="_method" value="DELETE">
                 <button type="button" class="btn-edit" data-mueble='${esc(JSON.stringify(m))}'>Editar</button>
                 <button type="button" class="btn-delete" data-id="${esc(m.id)}" data-confirm="¿Eliminar mueble ${esc(m.codigo || ('ID ' + m.id))}?" data-confirm-callback="confirmDeleteById">Eliminar</button>
               </form>`
            : `<a class="btn-base btn-new" href="${baseUrl}/solicitudes/create?mueble_id=${m.id}">Solicitar</a>`;

          return `<div class="card" role="listitem" data-id="${esc(m.id)}">
                    <div class="card-inner">
                      <div class="card-media">
                        <img src="${esc(img)}" alt="${esc(m.codigo||'mueble')}" onerror="this.src='${DEFAULT_IMG}'">
                      </div>
                      <div class="card-info">
                        <div class="card-top">
                          <div class="card-title">${esc(m.codigo||('ID '+m.id))} — ${esc(m.descripcion||'')}</div>
                          <div><span class="estado-badge estado-${esc(m.estado||'')}">${esc((m.estado||'').replace('_',' '))||'-'}</span></div>
                        </div>

                        ${brandHtml}

                        <div class="card-meta">
                          ${ IS_ADMIN ? `<div class="card-price">${m.monto_unitario ? ('$' + Number(m.monto_unitario).toFixed(2)) : ''}</div>
                                         <div class="card-responsable"><strong>Responsable:</strong> ${responsable}</div>` : '' }
                          <div class="card-solicitante"><strong>Solicitante:</strong> ${solicitante}</div>
                        </div>

                        <div class="mueble-nota">${esc(m.nota || '')}</div>

                        <div class="card-comments"><strong>Comentarios:</strong>${commentsHtml}</div>

                        <div class="card-actions">${actionsHtml}</div>
                      </div>
                    </div>
                  </div>`;
        }).join('') : '<div class="card">No hay muebles</div>';
      }

      if (tableBody) {
        tableBody.innerHTML = items.length ? items.map(m => {
          const first = (m.comentarios && m.comentarios[0]) ? m.comentarios[0] : null;
          const author = first && first.usuario ? esc((first.usuario.nombre||'') + ' ' + (first.usuario.apellido||'')) : 'ninguno';
          const preview = first ? esc(first.comentario) : 'ninguno';
          const actionsHtml = IS_ADMIN
            ? `<form method="POST" action="${API_BASE}/${esc(m.id)}" style="display:inline;">
                 <input type="hidden" name="_token" value="${csrfToken}">
                 <input type="hidden" name="_method" value="DELETE">
                 <button type="button" class="btn-edit" data-mueble='${esc(JSON.stringify(m))}'>Editar</button>
                 <button type="button" class="btn-delete" data-id="${esc(m.id)}" data-confirm="¿Eliminar mueble ${esc(m.codigo || ('ID ' + m.id))}?" data-confirm-callback="confirmDeleteById">Eliminar</button>
               </form>`
            : `<a class="btn-base btn-new" href="${baseUrl}/solicitudes/create?mueble_id=${m.id}">Solicitar</a>`;
          return `<tr>
                    <td>${esc(m.codigo||('ID '+m.id))}</td>
                    <td class="small-desc">${esc(m.descripcion||'')}</td>
                    <td class="table-comment"><strong>${author}:</strong> ${preview}</td>
                    <td><span class="estado-badge estado-${esc(m.estado||'')}">${esc((m.estado||'').replace('_',' '))||'-'}</span></td>
                    <td>${actionsHtml}</td>
                  </tr>`;
        }).join('') : `<tr><td colspan="${IS_ADMIN ? 7 : 6}" style="padding:12px">No hay muebles</td></tr>`;
      }

    } catch (err) {
      console.error('fetchAndRenderMuebles error', err);
      if (grid) grid.innerHTML = '<div class="card">Error cargando muebles</div>';
      if (tableBody) tableBody.innerHTML = `<tr><td colspan="${IS_ADMIN ? 7 : 6}" style="padding:12px">Error cargando muebles</td></tr>`;
    }
  }

  try { fetchAndRenderMuebles({}); } catch(e){ console.error(e); }

  function hideModalControls(){
    try {
      const vt = document.getElementById('view-toggle');
      const at = document.getElementById('admin-toggle');
      if (vt && vt.parentElement) vt.parentElement.style.display = 'none';
      if (at && at.parentElement) at.parentElement.style.display = 'none';
    } catch(e){}
  }
  function showModalControls(){
    try {
      const vt = document.getElementById('view-toggle');
      const at = document.getElementById('admin-toggle');
      if (vt && vt.parentElement) vt.parentElement.style.display = '';
      if (at && at.parentElement) at.parentElement.style.display = '';
    } catch(e){}
  }

  function openCreate(){
    const title = document.getElementById('form-title');
    const form = document.getElementById('mueble-form');
    const methodInput = document.getElementById('form-method');
    const idInput = document.getElementById('mueble-id');
    if (title) title.textContent = 'Nuevo mueble';
    if (form) {
      form.action = "{{ url('/muebles') }}";
      methodInput.value = 'POST';
      idInput.value = '';
      form.querySelectorAll('input,select,textarea').forEach(i=> i.value = '');
    }
    hideModalControls();
    const card = document.getElementById('user-form-card');
    if (card) { card.style.display = 'block'; card.classList.add('collapsed'); requestAnimationFrame(()=>card.classList.remove('collapsed')); }
    const filtersEl = document.getElementById('filters');
    const tableWrapper = document.getElementById('table-wrapper');
    if (filtersEl) filtersEl.style.display = 'none';
    if (tableWrapper) tableWrapper.style.display = 'none';
    if (cardsGrid) cardsGrid.style.display = 'none';
    if (marcaModal) populateModalModeloOptions(marcaModal.value);
  }

  function openEdit(m){
    if (!m) return;
    const title = document.getElementById('form-title');
    const form = document.getElementById('mueble-form');
    const methodInput = document.getElementById('form-method');
    const idInput = document.getElementById('mueble-id');
    if (title) title.textContent = 'Editar mueble — ID '+m.id;
    if (form) {
      form.action = "{{ url('/muebles') }}/" + m.id;
      methodInput.value = 'PUT';
      idInput.value = m.id;
      const setIf = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? ''; };
      setIf('f-codigo', m.codigo);
      setIf('f-descripcion', m.descripcion);
      setIf('f-fecha', m.fecha_registro);
      setIf('f-monto', m.monto_unitario);
      setIf('f-persona', m.persona_id);
      setIf('f-responsable', m.responsable_id);
      setIf('f-estado', m.estado);
      setIf('f-nota', m.nota);
      setIf('mueble-id', m.id);
      if (marcaModal) marcaModal.value = m.marca ?? '';
      populateModalModeloOptions(m.marca ?? '', m.modelo ?? '');
    }
    hideModalControls();
    const card = document.getElementById('user-form-card');
    if (card) { card.style.display = 'block'; card.classList.add('collapsed'); requestAnimationFrame(()=>card.classList.remove('collapsed')); }
    const filtersEl = document.getElementById('filters');
    const tableWrapper = document.getElementById('table-wrapper');
    if (filtersEl) filtersEl.style.display = 'none';
    if (tableWrapper) tableWrapper.style.display = 'none';
    if (cardsGrid) cardsGrid.style.display = 'none';
  }

  if (btnNew) btnNew.addEventListener('click', openCreate);

  document.addEventListener('click', function(e){
    const editBtn = e.target.closest('.btn-edit[data-mueble]');
    if (editBtn) {
      e.preventDefault();
      e.stopPropagation();
      try {
        const json = editBtn.getAttribute('data-mueble') || '{}';
        const obj = typeof json === 'string' ? JSON.parse(json) : json;
        openEdit(obj);
      } catch (err) {
        console.error('openEdit parse error', err);
      }
      return;
    }
    const delBtn = e.target.closest('.btn-delete');
    if (delBtn) {
      e.preventDefault(); e.stopPropagation();
      if (typeof window.showConfirmFor === 'function') { window.showConfirmFor(delBtn); return; }
      if (typeof window.confirmDeleteById === 'function') { window.confirmDeleteById(delBtn); return; }
      const f = delBtn.closest('form'); if (f) f.submit();
    }
  });

  if (btnCancel) btnCancel.addEventListener('click', function(){
    showModalControls();
    const card = document.getElementById('user-form-card');
    if (card) card.style.display = 'none';
    const filtersEl = document.getElementById('filters');
    const gridEl = document.querySelector('.grid');
    const tableWrapper = document.getElementById('table-wrapper');
    if (filtersEl) filtersEl.style.display = 'flex';
    if (gridEl) gridEl.style.display = 'grid';
    if (tableWrapper) tableWrapper.style.display = 'none';
    const form = document.getElementById('mueble-form');
    if (form) form.reset();
  });

  const MODELOS_POR_MARCA = {!! json_encode($modelosPorMarca ?? []) !!};

  const marcaModal = document.getElementById('f-marca-modal');
  const modeloModal = document.getElementById('f-modelo-modal');

  function populateModalModeloOptions(selectedMarca, selectedModel = ''){
    if (!modeloModal) return;
    modeloModal.innerHTML = '<option value="">(sin modelo)</option>';
    const key = String(selectedMarca || '').trim();
    let list = [];
    if (key !== '' && MODELOS_POR_MARCA && Object.prototype.hasOwnProperty.call(MODELOS_POR_MARCA, key)) {
      list = MODELOS_POR_MARCA[key];
    } else {
      const all = Object.values(MODELOS_POR_MARCA || {}).flat();
      list = Array.from(new Set((all || []).map(x=>String(x).trim()).filter(Boolean))).sort();
    }
    list.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m;
      opt.textContent = m;
      if (selectedModel && String(selectedModel).trim() === String(m).trim()) opt.selected = true;
      modeloModal.appendChild(opt);
    });
  }

  if (marcaModal) marcaModal.addEventListener('change', function(){ populateModalModeloOptions(this.value); });

  function openCreate(){
    const title = document.getElementById('form-title');
    const form = document.getElementById('mueble-form');
    const methodInput = document.getElementById('form-method');
    const idInput = document.getElementById('mueble-id');
    if (title) title.textContent = 'Nuevo mueble';
    if (form) {
      form.action = "{{ url('/muebles') }}";
      methodInput.value = 'POST';
      idInput.value = '';
      form.querySelectorAll('input,select,textarea').forEach(i=> i.value = '');
    }
    hideModalControls();
    const card = document.getElementById('user-form-card');
    if (card) { card.style.display = 'block'; card.classList.add('collapsed'); requestAnimationFrame(()=>card.classList.remove('collapsed')); }
    const filtersEl = document.getElementById('filters');
    const tableWrapper = document.getElementById('table-wrapper');
    if (filtersEl) filtersEl.style.display = 'none';
    if (tableWrapper) tableWrapper.style.display = 'none';
    if (cardsGrid) cardsGrid.style.display = 'none';
    if (marcaModal) populateModalModeloOptions(marcaModal.value);
  }

  function openEdit(m){
    if (!m) return;
    const title = document.getElementById('form-title');
    const form = document.getElementById('mueble-form');
    const methodInput = document.getElementById('form-method');
    const idInput = document.getElementById('mueble-id');
    if (title) title.textContent = 'Editar mueble — ID '+m.id;
    if (form) {
      form.action = "{{ url('/muebles') }}/" + m.id;
      methodInput.value = 'PUT';
      idInput.value = m.id;
      const setIf = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? ''; };
      setIf('f-codigo-modal', m.codigo);
      setIf('f-descripcion-modal', m.descripcion);
      setIf('f-fecha-modal', m.fecha_registro);
      setIf('f-monto-modal', m.monto_unitario);
      setIf('f-persona-modal', m.persona_id);
      setIf('f-responsable-modal', m.responsable_id);
      setIf('f-estado-modal', m.estado);
      setIf('f-nota-modal', m.nota);
      setIf('mueble-id', m.id);
      if (marcaModal) marcaModal.value = m.marca ?? '';
      populateModalModeloOptions(m.marca ?? '', m.modelo ?? '');
    }
    hideModalControls();
    const card = document.getElementById('user-form-card');
    if (card) { card.style.display = 'block'; card.classList.add('collapsed'); requestAnimationFrame(()=>card.classList.remove('collapsed')); }
    const filtersEl = document.getElementById('filters');
    const tableWrapper = document.getElementById('table-wrapper');
    if (filtersEl) filtersEl.style.display = 'none';
    if (tableWrapper) tableWrapper.style.display = 'none';
    if (cardsGrid) cardsGrid.style.display = 'none';
  }
  if (btnSave) {
    btnSave.addEventListener('click', function(){
      const formEl = document.getElementById('mueble-form');
      if (!formEl) return;
      const meth = document.getElementById('form-method')?.value || 'POST';
      formEl.submit();
    });
  }
  if (btnCancel) {
    btnCancel.addEventListener('click', function(){
      const cardEl = document.getElementById('user-form-card');
      if (cardEl) cardEl.style.display = 'none';
      const f = document.getElementById('mueble-form');
      if (f) f.reset();
      populateModalModeloOptions('');
    });
  }
});
</script>
@endsection

</body>
</html>