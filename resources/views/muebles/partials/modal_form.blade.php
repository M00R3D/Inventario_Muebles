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
        <input id="f-monto-modal" name="monto_unitario" required type="number" step="0.01" min="0" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
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

      <div style="display:flex;gap:8px;align-items:center;margin-top:8px;width:100%;">
        <div style="flex:1;min-width:160px;">
          <label>Carpeta pública</label>
          <select id="select-folder" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
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
          <select id="select-file" style="width:100%;padding:8px;border-radius:6px;border:1px solid #e5e7eb;">
            <option value="">-- elegir --</option>
          </select>
        </div>
        <div style="min-width:140px;text-align:center;">
          <label>Preview</label>
          <div style="margin-top:6px;">
            <div class="preview-wrapper">
              <img id="ruta-preview" src="{{ asset('imgs/default.webp') }}" alt="Preview" />
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" name="ruta_img" id="f-ruta-img" value="">

      @if(!empty($isAdmin) && $isAdmin)
        <div class="comments" id="modal-comments-section" style="margin-top:12px">
          <strong>Comentarios</strong>
          <div id="modal-comments-list" style="margin-top:8px">
            <div class="small-muted" id="modal-no-comments">No hay comentarios.</div>
          </div>

          {{-- NOTA: No usar <form> anidado aquí (evita comportamiento de submit que provoca redirección) --}}
          <div id="modal-comment-form" class="comment-form" data-action="{{ url('/comentarios') }}" data-method="POST" style="margin-top:10px">
            @csrf
            <input type="hidden" id="modal-comment-mueble-id" value="">
            <label style="font-weight:700">Agregar comentario (admins)</label>
            <textarea id="modal-comentario-input" rows="2" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px">
              <button type="button" id="modal-btn-comment-cancel" class="btn-ghost">Regresar</button>
              <button type="button" id="modal-btn-comment-send" class="btn-primary">Publicar comentario</button>
            </div>
          </div>
        </div>
      @endif

      <div class="modal-actions" style="display:flex;gap:8px;margin-top:10px;">
        <button type="button" id="btn-save" class="btn-save btn-base" style="max-height:40px;">Guardar</button>
        <button type="button" id="btn-cancel" class="btn-cancel btn-base" style="max-height:40px;">Cancelar</button>
      </div>
    </div>
  </form>
</div>

@if(!empty($isAdmin) && $isAdmin)
<script>
document.addEventListener('DOMContentLoaded', function(){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const modalCommentsList = document.getElementById('modal-comments-list');
  const modalNoComments = document.getElementById('modal-no-comments');
  const modalForm = document.getElementById('modal-comment-form'); // div, no form
  const modalMuebleId = document.getElementById('modal-comment-mueble-id');
  const modalTextarea = document.getElementById('modal-comentario-input');
  const modalSend = document.getElementById('modal-btn-comment-send');
  const modalCancel = document.getElementById('modal-btn-comment-cancel');

  function esc(s){ if (s === null || s === undefined) return ''; return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;'); }
  function insertModalComment(c){
    if (!modalCommentsList) return null;
    if (modalNoComments) { try { modalNoComments.remove(); } catch(e){} }
    const div = document.createElement('div');
    div.className = 'comment';
    const realId = c.id ?? ('new-' + Date.now());
    div.dataset.id = realId;
    const whoHtml = `<div class="who">${esc((c.usuario?.nombre || '') + ' ' + (c.usuario?.apellido || ''))}<div class="small-muted" style="font-weight:600">${esc(c.created_at ? new Date(c.created_at).toLocaleString() : 'ahora')}</div></div>`;
    const whatHtml = `<div class="what">${esc(c.comentario || '')}</div>`;
    div.innerHTML = whoHtml + whatHtml;
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
    btn.setAttribute('data-confirm', '¿Eliminar comentario?');
    btn.setAttribute('data-confirm-type', 'delete');
    btn.setAttribute('data-confirm-callback', 'confirmDeleteById');
    btn.setAttribute('data-base', '{{ url('/comentarios') }}');
    btn.dataset.id = realId;

    ctrl.appendChild(btn);
    div.appendChild(ctrl);

    modalCommentsList.insertBefore(div, modalCommentsList.firstChild);
    return div;
  }

  if (modalSend) {
    modalSend.addEventListener('click', async function(e){
      e.preventDefault();
      const muebleIdVal = (document.getElementById('modal-comment-mueble-id')?.value) || (document.getElementById('mueble-id')?.value || '');
      if (!muebleIdVal) { alert('Guarda el mueble antes de agregar comentarios.'); return; }
      const text = (document.getElementById('modal-comentario-input')?.value || '').trim();
      if (!text) { alert('Escribe un comentario antes de enviar.'); return; }
      modalSend.disabled = true;

      const actionUrl = (modalForm && modalForm.dataset && modalForm.dataset.action) ? modalForm.dataset.action : '{{ url('/comentarios') }}';

      try {
        const formData = new FormData();
        formData.append('mueble_id', muebleIdVal);
        formData.append('comentario', text);
        const resp = await fetch(actionUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' },
          body: formData
        });
        modalSend.disabled = false;
        if (!resp.ok) {
          const err = await resp.json().catch(()=>({ message: 'Error' }));
          throw err;
        }
        const json = await resp.json();
        const inserted = insertModalComment(json);
        if (inserted && json.id) {
          inserted.dataset.id = json.id;
          const btnDel = inserted.querySelector('.btn-delete-comment');
          if (btnDel) btnDel.dataset.id = json.id;
        }
        const ta = document.getElementById('modal-comentario-input');
        if (ta) ta.value = '';
      } catch (err) {
        console.error('error creando comentario', err);
        alert((err && err.message) ? err.message : 'Error al publicar comentario');
        modalSend.disabled = false;
      }
    });
  }
  if (modalCancel) modalCancel.addEventListener('click', function(e){ e.preventDefault(); try { document.getElementById('btn-cancel')?.click(); } catch(e){} });
});
</script>
@endif