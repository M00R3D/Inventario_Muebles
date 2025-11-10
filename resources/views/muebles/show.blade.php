@extends('layouts.app')

@section('title', 'Mueble — ' . ($mueble->codigo ?? 'detalle'))

@section('content')
<style>
.detail-wrap{max-width:980px;margin:18px auto;padding:18px}
.detail-card{display:flex;gap:18px;background:#fff;border-radius:12px;padding:18px;box-shadow:0 18px 48px rgba(3,10,30,0.06);align-items:flex-start}
.detail-media{width:360px;flex:0 0 360px;border-radius:10px;overflow:hidden;background:#f8fafc;padding:12px;display:flex;align-items:center;justify-content:center}
.detail-media img{width:100%;height:auto;object-fit:cover;border-radius:8px;box-shadow:0 8px 30px rgba(2,6,23,0.06)}
.detail-info{flex:1;display:flex;flex-direction:column;gap:12px}
.title-row{display:flex;align-items:center;justify-content:space-between;gap:12px}
.title-row h2{margin:0;font-size:1.25rem;font-weight:900;color:#0f172a}
.meta-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;color:#475569;font-weight:700}
.badge{display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:800;font-size:.85rem}
.badge-estado{background:linear-gradient(90deg,#f1f5f9,#eef2ff);color:#0f172a;box-shadow:0 6px 20px rgba(2,6,23,0.04)}
.badge-cat{background:linear-gradient(90deg,#e6f7ff,#f0f9ff);color:#075985;border:1px solid rgba(3,105,161,0.06)}
.estado-badge{ display:inline-flex; align-items:center; justify-content:center; padding:4px 10px; border-radius:999px; font-size:0.78rem; font-weight:700; min-width:94px; text-align:center; box-shadow:0 2px 6px rgba(2,6,23,0.06); }
.estado-bueno{ background:#10b981; color:#ffffff; }    
.estado-regular{ background:#f59e0b; color:#0b0b0b; }  
.estado-malo{ background:#ef4444; color:#ffffff; }     
.estado-en_reparacion{ background:#6366f1; color:#ffffff; } 
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-top:6px}
.info-item{background:#fbfdff;padding:10px;border-radius:8px;border:1px solid #eef2ff;color:#0f172a}
.desc{color:#475569;padding:10px;background:#fbfdff;border-radius:8px;border:1px solid #eef2ff}
.comments{margin-top:12px}
.comment{background:#fff;padding:10px;border-radius:10px;border:1px solid #eef2ff;margin-bottom:8px;display:flex;gap:10px}
.comment .who{font-weight:800;color:#0f172a;width:160px}
.comment .what{color:#334155;flex:1}
.comment-form{margin-top:12px;display:flex;flex-direction:column;gap:8px}
.btn-primary{background:linear-gradient(90deg,#06b6d4,#2563eb);color:#fff;padding:10px 14px;border-radius:10px;border:0;font-weight:800;cursor:pointer}
.btn-ghost{background:transparent;border:1px solid #e6eef9;padding:8px 10px;border-radius:8px;cursor:pointer}
.btn-delete-comment{
  background: linear-gradient(90deg,#ef4444,#d94660);
  color: #fff;
  padding:8px 10px;
  border-radius:8px;
  border:0;
  font-weight:700;
  cursor:pointer;
  box-shadow:0 8px 20px rgba(217,70,86,0.12);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  display:inline-flex;
  align-items:center;
  gap:8px;
}
.btn-delete-comment:hover{ transform: translateY(-3px); opacity:0.98; box-shadow:0 12px 30px rgba(217,70,86,0.14); }
.btn-delete-comment:active{ transform: translateY(-1px); }
.btn-delete-comment:focus{ outline:3px solid rgba(220,38,38,0.12); outline-offset:2px; }
.small-muted{font-size:0.9rem;color:#64748b}
</style>

<div class="detail-wrap">
  <div class="detail-card" role="region" aria-label="Detalle de mueble">
    <div class="detail-media">
      @php $img = $mueble->ruta_img ? url($mueble->ruta_img) : asset('imgs/default.webp'); @endphp
      <img src="{{ $img }}" alt="{{ $mueble->codigo }}" onerror="this.src='{{ asset('imgs/default.webp') }}'">
    </div>

    <div class="detail-info">
      <div class="title-row">
        <div>
          <h2>{{ $mueble->codigo ?? 'ID '.$mueble->id }} — {{ $mueble->descripcion }}</h2>
          <div class="small-muted">Registrado: {{ optional($mueble->fecha_registro)->format('Y-m-d') ?? '-' }}</div>
        </div>

        <div style="text-align:right">
          <div class="meta-row">
            <div><span class="estado-badge estado-{{ $mueble->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $mueble->estado ?? '-')) }}</span></div>
            @if($mueble->categoria)
              <div class="badge badge-cat">Categoría: {{ $mueble->categoria->nombre }}</div>
            @endif
          </div>
        </div>
      </div>

      <div class="info-grid" aria-hidden="false">
        <div class="info-item"><strong>Responsable</strong><div class="small-muted">{{ $mueble->responsable ? $mueble->responsable->nombre.' '.$mueble->responsable->apellido : 'ninguno' }}</div></div>
        <div class="info-item"><strong>Solicitante</strong><div class="small-muted">{{ $mueble->usuario ? $mueble->usuario->nombre.' '.$mueble->usuario->apellido : 'ninguno' }}</div></div>
        <div class="info-item"><strong>Monto</strong><div class="small-muted">${{ number_format($mueble->monto_unitario ?? 0,2,',','.') }}</div></div>
        <div class="info-item"><strong>Marca / Modelo</strong><div class="small-muted">{{ $mueble->marca ?? '-' }} {{ $mueble->modelo ? ' / '.$mueble->modelo : '' }}</div></div>
      </div>

      <div class="desc" style="margin-top:8px">
        <strong>Nota</strong>
        <div style="margin-top:6px;color:#334155">{{ $mueble->nota ?? '—' }}</div>
      </div>

      <div class="comments" id="comments-section">
        <strong>Comentarios</strong>
        <div id="comments-list" style="margin-top:8px">
          @forelse($mueble->comentarios()->with('usuario')->latest()->get() as $c)
            <div class="comment" data-id="{{ $c->id }}">
              <div class="who">
                {{ $c->usuario ? ($c->usuario->nombre . ' ' . $c->usuario->apellido) : 'Anon' }}
                <div class="small-muted" style="font-weight:600">{{ optional($c->created_at)->diffForHumans() }}</div>
              </div>
              <div class="what">{{ $c->comentario }}</div>
              @if(!empty($isAdmin))
                <div style="margin-left:8px;display:flex;align-items:flex-start;gap:6px;">
                  <button type="button" class="btn-delete-comment btn-delete" data-id="{{ $c->id }}"  aria-label="Eliminar comentario">Eliminar</button>
                </div>
              @endif
            </div>
          @empty
            <div class="small-muted" id="no-comments">No hay comentarios.</div>
          @endforelse
        </div>

        @if(!empty($isAdmin))
          <form id="comment-form" class="comment-form" action="{{ url('/comentarios') }}" method="POST">
            @csrf
            <input type="hidden" name="mueble_id" value="{{ $mueble->id }}">
            <label style="font-weight:700">Agregar comentario (admins)</label>
            <textarea name="comentario" id="comentario-input" rows="3" style="padding:10px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
            <div style="display:flex;gap:8px;justify-content:flex-end">
              <button type="button" id="btn-comment-cancel" class="btn-ghost">Regresar</button>
               <button type="submit" id="btn-comment-send" class="btn-primary">Publicar comentario</button>
             </div>
           </form>
         @endif
      </div>
    </div>
  </div>
</div>
<div id="confirm-modal-global" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);">
  <div style="background:#fff;padding:16px;border-radius:10px;max-width:420px;width:92%;box-shadow:0 12px 36px rgba(2,6,23,0.18);">
    <div id="confirm-modal-global-msg" style="font-weight:700;margin-bottom:12px;">¿Confirmar acción?</div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button type="button" id="confirm-modal-global-cancel" class="btn-ghost">Cancelar</button>
      <button type="button" id="confirm-modal-global-ok" class="btn-primary">Confirmar</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const commentsList = document.getElementById('comments-list');
  const noComments = document.getElementById('no-comments');
  @if(!empty($isAdmin))
  const form = document.getElementById('comment-form');
  const textarea = document.getElementById('comentario-input');
  const btnCancel = document.getElementById('btn-comment-cancel');
  const btnSend = document.getElementById('btn-comment-send');
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const BACK_URL = "{{ url('/muebles') }}";
  const modal = document.getElementById('confirm-modal-global');
  const modalMsg = document.getElementById('confirm-modal-global-msg');
  const modalOk = document.getElementById('confirm-modal-global-ok');
  const modalCancel = document.getElementById('confirm-modal-global-cancel');
  let pendingConfirmAction = null;

  btnCancel && btnCancel.addEventListener('click', function(e){ e && e.preventDefault(); window.location.href = BACK_URL; });

  function openConfirm(message, action){
    pendingConfirmAction = action;
    modalMsg.textContent = message || '¿Confirmar acción?';
    modal.style.display = 'flex';
    modalOk.focus();
  }
  function closeConfirm(){
    modal.style.display = 'none';
    pendingConfirmAction = null;
  }
  modalCancel.addEventListener('click', function(){ closeConfirm(); });
  modalOk.addEventListener('click', function(){
    try { if (typeof pendingConfirmAction === 'function') pendingConfirmAction(); }
    catch(e){ console.error('confirm action error', e); }
    closeConfirm();
  });
  document.addEventListener('click', function(ev){
    const del = ev.target.closest && ev.target.closest('.btn-delete-comment');
    if (!del) return;
    ev.preventDefault();
    ev.stopPropagation();
    const id = del.dataset.id;
    const confirmMsg = del.dataset.confirm || '¿Eliminar comentario?';
    openConfirm(confirmMsg, function(){
      fetch("{{ url('/comentarios') }}/" + encodeURIComponent(id), {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(r => {
        if (!r.ok) return r.json().then(j => Promise.reject(j));
        return r.json();
      }).then(json => {
        const el = document.querySelector('.comment[data-id="'+id+'"]');
        if (el) el.remove();
      }).catch(err => {
        console.error('error eliminando comentario', err);
        alert((err && err.message) ? err.message : 'Error al eliminar comentario');
      });
    });
  });
  form && form.addEventListener('submit', function(e){
    e.preventDefault();
    const text = (textarea.value || '').trim();
    if (!text) return alert('Escribe un comentario antes de enviar.');
    btnSend.disabled = true;
    const data = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      body: data
    }).then(r => {
      btnSend.disabled = false;
      if (!r.ok) return r.json().then(j => Promise.reject(j));
      return r.json();
    }).then(json => {
      const who = (json.usuario && (json.usuario.nombre || json.usuario.apellido)) ? ((json.usuario.nombre||'') + ' ' + (json.usuario.apellido||'')) : '{{ auth()->user()->nombre ?? "Admin" }}';
      const createdAt = json.created_at ? new Date(json.created_at).toLocaleString() : 'ahora';
      const div = document.createElement('div');
      div.className = 'comment';
      const realId = json.id ?? ('new-' + Date.now());
      div.dataset.id = realId;
      const whoHtml = `<div class="who">${escapeHtml(who)}<div class="small-muted" style="font-weight:600">${escapeHtml(createdAt)}</div></div>`;
      const whatHtml = `<div class="what">${escapeHtml(json.comentario || text)}</div>`;
      div.innerHTML = whoHtml + whatHtml;
      @if(!empty($isAdmin))
      const ctrl = document.createElement('div');
      ctrl.style.marginLeft = '8px';
      ctrl.style.display = 'flex';
      ctrl.style.alignItems = 'flex-start';
      ctrl.style.gap = '6px';
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn-delete-comment btn-delete';
      btn.textContent = 'Eliminar';
      btn.setAttribute('aria-label','Eliminar comentario');
      btn.setAttribute('data-confirm','¿Eliminar comentario?');
      btn.dataset.id = realId;
      ctrl.appendChild(btn);
      div.appendChild(ctrl);
      @endif
      if (noComments) noComments.remove();
      commentsList.insertBefore(div, commentsList.firstChild);
      textarea.value = '';
    }).catch(err => {
      console.error('error creando comentario', err);
      alert((err && err.message) ? err.message : 'Error al publicar comentario');
      btnSend.disabled = false;
    });
  });
  @endif
  function escapeHtml(s){ if (!s && s !== 0) return ''; return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;'); }
});
</script>
@endsection