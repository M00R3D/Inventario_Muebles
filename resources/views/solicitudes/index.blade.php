@extends('layouts.app')

@section('title','Solicitudes | Inventario Muebles')

@section('content')
<style>
.container{max-width:1200px;margin:0 auto;padding:18px;}
.split { display:flex; gap:18px; align-items:flex-start; }
.left { flex:1; min-width:420px; }
.right { width:420px; }
.card-wide{ background:#fff;padding:14px;border-radius:12px;box-shadow:0 12px 34px rgba(2,6,23,0.06); }
.table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; padding:8px; }
.table th, .table td{ padding:8px 10px; text-align:left; border-bottom:1px solid #f3f4f6; }
.mueble-grid{ display:grid; grid-template-columns: repeat(auto-fill,minmax(120px,1fr)); gap:8px; max-height:360px; overflow:auto; padding:6px; }
.mueble-item{ background:#f8fafc;padding:6px;border-radius:8px;cursor:pointer; display:flex;flex-direction:column;align-items:center;gap:6px; }
.mueble-item.selected{ outline:3px solid #06b6d4; background:#ecfeff; }
.mueble-thumb{ width:100%;height:80px;object-fit:cover;border-radius:6px;background:#e5e7eb; }
.badge { padding:6px 10px;border-radius:999px;font-weight:700;font-size:0.85rem; }
.badge-pendiente{ background:#f59e0b;color:#111; }
.badge-aprobada{ background:#10b981;color:#fff; }
.badge-rechazada{ background:#ef4444;color:#fff; }

.btn-edit{
  background: linear-gradient(90deg,#6366f1,#06b6d4);
  color: #fff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-edit:hover{ transform: translateY(-3px); }
.btn-edit:active{ transform: translateY(-1px); }
.btn-edit:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-new-soli{
  background: linear-gradient(90deg,#6366f1,#7364f5ff);
  color: #ffffffff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-new-soli:hover{ transform: translateY(-3px); }
.btn-new-soli:active{ transform: translateY(-1px); }
.btn-new-soli:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }


.btn-delete{
  background: linear-gradient(90deg,#810a0aff,#d63867ff);
  color: #fff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-delete:hover{ transform: translateY(-3px); }
.btn-delete:active{ transform: translateY(-1px); }
.btn-delete:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-aprobar{
  background: linear-gradient(90deg,#10b981,#17743eff);
  color: #fff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-aprobar:hover{ transform: translateY(-3px); }
.btn-aprobar:active{ transform: translateY(-1px); }
.btn-aprobar:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-pendiente{
  background: linear-gradient(90deg,#f59e0b,#f5900bff);
  color: #000000ff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-pendiente:hover{ transform: translateY(-3px); }
.btn-pendiente:active{ transform: translateY(-1px); }
.btn-pendiente:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-cancel{
  background: linear-gradient(90deg,#ef4444,#5c1313ff);
  color: #ffffffff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-cancel:hover{ transform: translateY(-3px); }
.btn-cancel:active{ transform: translateY(-1px); }
.btn-cancel:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }


</style>

<div class="container">
  <h1>Solicitudes</h1>

  @php
    $currentUser = $currentUser ?? (session()->has('usuario_id') ? \App\Models\Usuario::find(session('usuario_id')) : null);
    $isAdmin = $isAdmin ?? ($currentUser && ($currentUser->rol === 'admin'));
  @endphp

  @if(session('success'))
    <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin:8px 0;font-weight:700;">{{ session('success') }}</div>
  @endif

  <div class="split">
    <div class="left">
      <div class="card-wide" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <h2 style="margin:0;font-size:1.05rem">Lista de solicitudes</h2>
          @if($isAdmin)
            <div style="font-size:0.85rem;color:#6b7280;">(Administración)</div>
            <button id="btn-new"  class="btn-new-soli">Nueva solicitud</button>
          @endif
        </div>

        <form id="sol-filters" method="GET" action="{{ url('/solicitudes') }}" style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            @if($isAdmin)
              <div>
                <label style="display:block;font-weight:600;">Solicitante</label>
                <select name="persona_id" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                  <option value="">Todos</option>
                  @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('persona_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre }} {{ $u->apellido }}</option>
                  @endforeach
                </select>
              </div>
            @endif
            <div>
              <label style="display:block;font-weight:600;">Mueble (código)</label>
              <input name="codigo" type="search" value="{{ request('codigo') }}" placeholder="buscar código" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
            <div>
              <label style="display:block;font-weight:600;">Estado</label>
              <select name="estado" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                <option value="">Todos</option>
                <option value="pendiente" {{ request('estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada" {{ request('estado')=='aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="rechazada" {{ request('estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
              </select>
            </div>
          </div>
          <div style="display:flex;gap:8px;">
            <button type="submit" class="btn-new-soli">Aplicar</button>
            <button type="button" id="sol-clear-filters" class="btn-delete">Limpiar</button>
          </div>
        </form>

        <div style="margin-top:12px;overflow:auto;">
          <table class="table" aria-label="Solicitudes">
            <thead>
              <tr><th>ID</th><th>Mueble</th>@if($isAdmin)<th>Solicitante</th>@endif<th>Periodo</th><th>Estado</th>@if($isAdmin)<th>Acciones</th>@endif</tr>
            </thead>
            <tbody>
              @forelse($solicitudes as $s)
                <tr>
                  <td>{{ $s->id }}</td>
                  <td style="display:flex;align-items:center;gap:10px;">
                    <div style="width:48px;height:40px;flex:0 0 48px;">
                      @if(!empty($s->mueble->ruta_img))
                        <img src="{{ asset($s->mueble->ruta_img) }}" alt="{{ $s->mueble->codigo ?? '' }}" style="width:48px;height:40px;object-fit:cover;border-radius:6px;">
                      @else
                        <img src="{{ asset('imgs/default.webp') }}" alt="sin imagen" style="width:48px;height:40px;object-fit:cover;border-radius:6px;">
                      @endif
                    </div>
                    <div style="min-width:0;">
                      <div style="font-weight:700;font-size:0.95rem;">{{ $s->mueble->codigo ?? ('ID '.$s->mueble_id) }}</div>
                      <div style="color:#6b7280;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ \Illuminate\Support\Str::limit($s->mueble->descripcion ?? '-', 40) }}
                      </div>
                    </div>
                  </td>
                  @if($isAdmin)
                   <td>{{ $s->usuario->nombre ?? '-' }} {{ $s->usuario->apellido ?? '' }}</td>
                   @endif
                   <td>{{ $s->fecha_inicio ?? '-' }} → {{ $s->fecha_fin ?? '-' }}</td>
                   <td>
                    @php $cls = 'badge-'.($s->estado ?? 'pendiente'); @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst($s->estado) }}</span>
                  </td>
                  @if($isAdmin)
                  <td style="white-space:nowrap">
                      <button type="button" class="btn-edit" data-solicitud='@json($s)' style="margin-right:6px;">Editar</button>

                      <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                        @csrf
                        <input type="hidden" name="estado" value="aprobada">
                        <button type="submit" title="Aprobar" class="btn-aprobar">Aprobar</button>
                      </form>

                      <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                        @csrf
                        <input type="hidden" name="estado" value="pendiente">
                        <button type="submit" title="Poner pendiente" class="btn-pendiente">Pendiente</button>
                      </form>

                      <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                        @csrf
                        <input type="hidden" name="estado" value="rechazada">
                        <button type="submit" title="Rechazar" class="btn-cancel">Rechazar</button>
                      </form>

                      <form action="{{ url('/solicitudes/'.$s->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" data-confirm="¿Eliminar solicitud #{{ $s->id }}?" data-confirm-type="delete" class="btn-delete" data-confirm-type="delete">Eliminar</button>
                      </form>
                    @else
                      <span style="color:#6b7280;font-weight:700;">-</span>
                    </td>
                    @endif
                </tr>
              @empty
                <tr><td colspan="6">No hay solicitudes aún.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <aside class="right">
      <div class="card-wide" style="padding:12px;">
        <h2 style="margin:0;font-size:1.05rem;">Resumen</h2>

        <div style="margin-top:12px;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div style="font-weight:700;">Total solicitudes</div>
            <div style="font-size:1.2rem;">{{ $solicitudes->count() }}</div>
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
            <div style="font-weight:700;">Pendientes</div>
            <div class="badge badge-pendiente" style="font-size:0.9rem;">{{ $solicitudes->where('estado', 'pendiente')->count() }}</div>
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
            <div style="font-weight:700;">Aprobadas</div>
            <div class="badge badge-aprobada" style="font-size:0.9rem;">{{ $solicitudes->where('estado', 'aprobada')->count() }}</div>
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
            <div style="font-weight:700;">Rechazadas</div>
            <div class="badge badge-rechazada" style="font-size:0.9rem;">{{ $solicitudes->where('estado', 'rechazada')->count() }}</div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</div>

<div id="sol-modal" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.5);z-index:220;align-items:center;justify-content:center;padding:20px;">
  <div style="width:100%;max-width:920px;background:#fff;border-radius:10px;padding:16px;box-shadow:0 12px 40px rgba(2,6,23,0.2);max-height:90vh;overflow:auto;">
    <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <h2 id="sol-modal-title" style="margin:0;font-size:1.05rem">Crear / Editar solicitud</h2>
      <button id="sol-cancel" type="button" class="btn-base btn-clear">Cerrar</button>
    </header>

    <form id="sol-form" method="POST" action="{{ url('/solicitudes') }}">
      @csrf
      <input type="hidden" id="sol-method" name="_method" value="POST">
      <input type="hidden" id="sol-id" name="id" value="">
      <input type="hidden" id="sol-mueble-id" name="mueble_id" value="">

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:300px;">
          <label>Solicitante</label>
          @if($isAdmin)
            <select id="sol-persona" name="persona_id" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
              <option value="">Selecciona</option>
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
              @endforeach
            </select>
          @else
            <input type="hidden" id="sol-persona" name="persona_id" value="{{ session('usuario_id') ?? '' }}">
            <div style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;background:#fafafa;font-weight:700;">
              {{ $currentUser ? ($currentUser->nombre.' '.$currentUser->apellido) : 'Usuario' }}
            </div>
          @endif

          <label style="margin-top:8px;">Fecha inicio</label>
          <input id="sol-fecha-inicio" name="fecha_inicio" type="date" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Fecha fin</label>
          <input id="sol-fecha-fin" name="fecha_fin" type="date" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Nota</label>
          <textarea id="sol-nota" name="nota" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>
          @if($isAdmin)
            <label style="margin-top:8px;">Estado</label>
            <select id="sol-estado" name="estado" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
              <option value="pendiente">Pendiente</option>
              <option value="aprobada">Aprobada</option>
              <option value="rechazada">Rechazada</option>
            </select>
          @else
            <input type="hidden" id="sol-estado" name="estado" value="pendiente">
          @endif
        </div>

        <div style="flex:1;min-width:260px;">
          <div style="font-weight:700;margin-bottom:6px;">Mueble seleccionado</div>
          <div id="sol-selected" style="padding:10px;border-radius:8px;border:1px solid #e5e7eb;background:#fafafa;min-height:60px;"></div>

          <div style="margin-top:12px;font-weight:700;">Selecciona mueble</div>
          <div id="muebles-grid" class="mueble-grid" style="margin-top:8px;">
            @foreach($muebles as $m)
              <div class="mueble-item" data-id="{{ $m->id }}" data-codigo="{{ $m->codigo }}" data-nombre="{{ e($m->descripcion) }}" title="{{ $m->codigo }}" style="padding:8px;border-radius:8px;">
                <div style="width:100%;height:70px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:6px;overflow:hidden;">
                  @if($m->ruta_img)
                    <img src="{{ asset($m->ruta_img) }}" alt="{{ $m->codigo }}" style="max-width:100%;max-height:70px;object-fit:contain;">
                  @else
                    <div style="color:#9ca3af;font-weight:700;">Sin imagen</div>
                  @endif
                </div>
                <div style="margin-top:6px;text-align:center;">
                  <div style="font-weight:700;">{{ $m->codigo }}</div>
                  <div style="font-size:0.85rem;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 34) }}</div>
                </div>
              </div>
            @endforeach
           </div>
        </div>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;">
        <button type="submit" class="btn-base btn-save">Guardar</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btnNew = document.getElementById('btn-new');
  const modal = document.getElementById('sol-modal');
  const solCancel = document.getElementById('sol-cancel');
  const mueblesGrid = document.getElementById('muebles-grid');
  const solMuebleId = document.getElementById('sol-mueble-id');
  const solSelected = document.getElementById('sol-selected');
  const solForm = document.getElementById('sol-form');
  const solMethod = document.getElementById('sol-method');
  const solId = document.getElementById('sol-id');
  const solFilters = document.getElementById('sol-filters');
  function selectMueble(el){
    if(!el) return;
    document.querySelectorAll('.mueble-item').forEach(x=> x.classList.remove('selected'));
    el.classList.add('selected');
    const id = el.getAttribute('data-id') || '';
    const codigo = el.getAttribute('data-codigo') || '';
    const nombre = el.getAttribute('data-nombre') || '';
    if(solMuebleId) solMuebleId.value = id;
    if(solSelected) solSelected.innerHTML = `<strong>${codigo}</strong>${ nombre ? ' — <span style="color:#6b7280;font-weight:700;">'+nombre+'</span>' : '' }`;
  }
  function openModal() {
    modal.style.display = 'flex';
    if(solFilters) solFilters.style.display = 'none';
    solMethod.value = 'POST';
    solId.value = '';
    solForm.action = "{{ url('/solicitudes') }}";
    solForm.reset();
    solSelected.textContent = '';
    document.querySelectorAll('.mueble-item').forEach(el=> el.classList.remove('selected'));
    modal.scrollTop = 0;
  }
  function closeModal() { modal.style.display = 'none'; if(solFilters) solFilters.style.display = 'flex'; }

  if (btnNew) btnNew.addEventListener('click', openModal);
  if (solCancel) solCancel.addEventListener('click', closeModal);

  mueblesGrid?.addEventListener('click', function(e){
    const item = e.target.closest('.mueble-item');
    if(!item) return;
    document.querySelectorAll('.mueble-item').forEach(el=> el.classList.remove('selected'));
    item.classList.add('selected');
    solMuebleId.value = item.getAttribute('data-id');
    solSelected.textContent = 'Mueble seleccionado: ' + item.getAttribute('data-codigo');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  solForm?.addEventListener('submit', function(evt){
    function todayStr(offsetDays = 0){
      const d = new Date();
      d.setDate(d.getDate() + offsetDays);
      return d.toISOString().slice(0,10);
    }
    const inicioEl = document.getElementById('sol-fecha-inicio');
    const finEl = document.getElementById('sol-fecha-fin');
    if (inicioEl && !inicioEl.value) inicioEl.value = todayStr(0);
    if (finEl && !finEl.value) finEl.value = todayStr(1);

    if(!solMuebleId.value) {
      evt.preventDefault();
      alert('Selecciona primero un mueble para la solicitud.');
      return;
    }
  });

  document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click', function(){
      try {
        const s = JSON.parse(this.getAttribute('data-solicitud'));
        openModal();
        const personaSel = document.getElementById('sol-persona');
        if(personaSel) personaSel.value = s.persona_id || '';
        document.getElementById('sol-fecha-inicio').value = s.fecha_inicio || '';
        document.getElementById('sol-fecha-fin').value = s.fecha_fin || '';
        document.getElementById('sol-nota').value = s.nota || '';
        const estadoSel = document.getElementById('sol-estado');
        if(estadoSel) estadoSel.value = s.estado || 'pendiente';
        solMethod.value = 'PUT';
        solId.value = s.id;
        solForm.action = "{{ url('/solicitudes') }}/" + s.id;
        const sel = document.querySelector('.mueble-item[data-id="'+s.mueble_id+'"]');
        if(sel){ selectMueble(sel); sel.scrollIntoView({behavior:'smooth', block:'center'}); }
      } catch(e){ console.error(e); alert('Error al abrir edición'); }
    });
  });

  const clearBtn = document.getElementById('sol-clear-filters');
  if (clearBtn) {
    clearBtn.addEventListener('click', function(evt){
      evt.preventDefault();
      const form = document.getElementById('sol-filters');
      if (form) {
        form.querySelectorAll('input,select').forEach(i=>{
          if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
          else if (i.type !== 'submit' && i.type !== 'button') i.value = '';
        });
      }
      window.location.href = "{{ url('/solicitudes') }}";
    });
  }
});
</script>
@endsection