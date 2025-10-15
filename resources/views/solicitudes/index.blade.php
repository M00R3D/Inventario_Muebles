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
</style>

<div class="container">
  <h1>Solicitudes</h1>

  @if(session('success'))
    <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin:8px 0;font-weight:700;">{{ session('success') }}</div>
  @endif

  <div class="split">
    <div class="left">
      <div class="card-wide" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <h2 style="margin:0;font-size:1.05rem">Lista de solicitudes</h2>
          <button id="btn-new" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Nueva solicitud</button>
        </div>

        <div style="margin-top:12px;overflow:auto;">
          <table class="table" aria-label="Solicitudes">
            <thead>
              <tr><th>ID</th><th>Mueble</th><th>Solicitante</th><th>Periodo</th><th>Estado</th><th>Acciones</th></tr>
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
                   <td>{{ $s->usuario->nombre ?? '-' }} {{ $s->usuario->apellido ?? '' }}</td>
                   <td>{{ $s->fecha_inicio ?? '-' }} → {{ $s->fecha_fin ?? '-' }}</td>
                   <td>
                    @php $cls = 'badge-'.($s->estado ?? 'pendiente'); @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst($s->estado) }}</span>
                  </td>
                  <td style="white-space:nowrap">
                    <button type="button" class="btn-edit" data-solicitud='@json($s)' style="margin-right:6px;">Editar</button>

                    <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                      @csrf
                      <input type="hidden" name="estado" value="aprobada">
                      <button type="submit" title="Aprobar" style="background:#10b981;color:#fff;padding:6px 8px;border-radius:8px;border:0;">Aprobar</button>
                    </form>

                    <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                      @csrf
                      <input type="hidden" name="estado" value="pendiente">
                      <button type="submit" title="Poner pendiente" style="background:#f59e0b;color:#111;padding:6px 8px;border-radius:8px;border:0;">Pendiente</button>
                    </form>

                    <form action="{{ route('solicitudes.changeEstado', $s->id) }}" method="POST" style="display:inline;margin-right:6px;">
                      @csrf
                      <input type="hidden" name="estado" value="rechazada">
                      <button type="submit" title="Rechazar" style="background:#ef4444;color:#fff;padding:6px 8px;border-radius:8px;border:0;">Rechazar</button>
                    </form>

                    <form action="{{ url('/solicitudes/'.$s->id) }}" method="POST" style="display:inline;">
                      @csrf @method('DELETE')
                      <button type="submit" data-confirm="¿Eliminar solicitud #{{ $s->id }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;">Eliminar</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6">No hay solicitudes aún.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    
  </div>
</div>

<div id="sol-modal" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.45);align-items:center;justify-content:center;z-index:9999;padding:12px;">
  <div style="background:#fff;border-radius:10px;padding:12px;max-width:980px;width:100%;max-height:90vh;overflow:auto;">
    <h2 id="modal-title">Nueva solicitud</h2>
    <form id="sol-form" method="POST" action="{{ url('/solicitudes') }}">
      @csrf
      <input type="hidden" name="_method" id="sol-method" value="POST">
      <input type="hidden" name="id" id="sol-id" value="">
      <input type="hidden" name="mueble_id" id="sol-mueble-id" value="">

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:320px;">
          <label>Seleccionar mueble</label>
          <div class="mueble-grid" id="muebles-grid">
            @foreach($muebles as $m)
              <div class="mueble-item" data-id="{{ $m->id }}" data-codigo="{{ $m->codigo }}" data-ruta="{{ $m->ruta_img }}">
                @if($m->ruta_img)
                  <img class="mueble-thumb" src="{{ asset($m->ruta_img) }}" alt="{{ $m->codigo }}">
                @else
                  <div class="mueble-thumb"></div>
                @endif
                <div style="font-weight:700;font-size:0.9rem;">{{ $m->codigo }}</div>
                <div style="font-size:0.85rem;color:#6b7280;">{{ \Illuminate\Support\Str::limit($m->descripcion,40) }}</div>
              </div>
            @endforeach
          </div>
        </div>

        <div style="flex:1;min-width:260px;">
          <label>Solicitante</label>
          <select name="persona_id" id="sol-persona" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">Selecciona</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>

          <label style="margin-top:8px;">Fecha inicio</label>
          <input type="date" name="fecha_inicio" id="sol-fecha-inicio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Fecha fin</label>
          <input type="date" name="fecha_fin" id="sol-fecha-fin" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Nota</label>
          <textarea name="nota" id="sol-nota" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>

          <label style="margin-top:8px;">Estado</label>
          <select name="estado" id="sol-estado" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="pendiente">Pendiente</option>
            <option value="aprobada">Aprobada</option>
            <option value="rechazada">Rechazada</option>
          </select>

          <div style="margin-top:12px;display:flex;gap:8px;">
            <button type="submit" id="sol-save" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Guardar</button>
            <button type="button" id="sol-cancel" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
          </div>
          <div id="sol-selected" style="margin-top:12px;color:#6b7280;font-weight:700;"></div>
        </div>
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

  function openModal() {
    modal.style.display = 'flex';
    solMethod.value = 'POST';
    solId.value = '';
    solForm.action = "{{ url('/solicitudes') }}";
    solForm.reset();
    solSelected.textContent = '';
    document.querySelectorAll('.mueble-item').forEach(el=> el.classList.remove('selected'));
    modal.scrollTop = 0;
  }
  function closeModal() { modal.style.display = 'none'; }

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
        document.getElementById('sol-persona').value = s.persona_id || '';
        document.getElementById('sol-fecha-inicio').value = s.fecha_inicio || '';
        document.getElementById('sol-fecha-fin').value = s.fecha_fin || '';
        document.getElementById('sol-nota').value = s.nota || '';
        document.getElementById('sol-estado').value = s.estado || 'pendiente';
        solMethod.value = 'PUT';
        solId.value = s.id;
        solForm.action = "{{ url('/solicitudes') }}/" + s.id;
        const sel = document.querySelector('.mueble-item[data-id="'+s.mueble_id+'"]');
        if(sel){ sel.click(); sel.scrollIntoView({behavior:'smooth', block:'center'}); }
      } catch(e){ console.error(e); alert('Error al abrir edición'); }
    });
  });
});
</script>
@endsection