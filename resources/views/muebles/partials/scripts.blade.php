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
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const DEFAULT_IMG = "{{ asset('imgs/default.webp') }}";
  const STORAGE_KEY = 'muebles_filters_v1';

  const EXISTING_MUEBLES = {!! $muebles->map(function($x){ return ['id'=>$x->id,'codigo'=>$x->codigo]; })->values()->toJson() !!};

  function clearValidation(){ document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid')); document.querySelectorAll('.field-error').forEach(el => el.remove()); }
  function setFieldError(el, msg){ if (!el) return; el.classList.add('invalid'); const next = el.nextElementSibling; if (next && next.classList && next.classList.contains('field-error')) { next.textContent = msg; return; } const span = document.createElement('div'); span.className = 'field-error'; span.textContent = msg; if (el.parentNode) el.parentNode.insertBefore(span, el.nextSibling); }
  window.MODELOS_POR_MARCA = window.MODELOS_POR_MARCA || {!! json_encode($modelosPorMarca ?? []) !!};
  const marcaModal = document.getElementById('f-marca-modal');
  const modeloModal = document.getElementById('f-modelo-modal');
  function populateModalModeloOptions(selectedMarca = '', selectedModel = ''){
    const sel = modeloModal; if (!sel) return; sel.innerHTML = '<option value="">(sin modelo)</option>';
    const map = window.MODELOS_POR_MARCA || {}; let list = [];
    const key = String(selectedMarca || '').trim();
    if (key && Object.prototype.hasOwnProperty.call(map, key)) { list = map[key] || []; } else { const all = Object.values(map).flat(); list = Array.from(new Set((all || []).map(x => String(x||'').trim()).filter(Boolean))).sort(); }
    list.forEach(v => { const o = document.createElement('option'); o.value = v; o.textContent = v; if (selectedModel && String(selectedModel).trim() === String(v).trim()) o.selected = true; sel.appendChild(o); });
  }
  if (marcaModal) marcaModal.addEventListener('change', function(){ populateModalModeloOptions(this.value); });
  if (btnNew) { btnNew.addEventListener('click', function(e){ e && e.preventDefault(); openCreate(); }); }

  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.btn-edit');
    if (!btn) return;
    const raw = btn.getAttribute('data-mueble') || btn.dataset.mueble || null;
    if (!raw) return;
    e.preventDefault();
    try {
      let js = raw;
      if (typeof js === 'string' && /&quot;|&amp;/.test(js)) {
        js = js.replace(/&quot;/g,'"').replace(/&amp;/g,'&').replace(/&#39;/g,"'");
      }
      const obj = (typeof js === 'object') ? js : JSON.parse(js);
      openEdit(obj);
    } catch (err) {
      console.error('No se pudo parsear data-mueble', err, raw);
      const id = btn.getAttribute('data-id') || btn.dataset.id;
      if (id) {
        fetch(`${API_BASE}/${encodeURIComponent(id)}`, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
          .then(r => r.ok ? r.json() : Promise.reject(r))
          .then(json => openEdit(json))
          .catch(e => console.error('Error cargando mueble para editar', e));
      }
    }
  });

  function clearFormFields(formEl){ if (!formEl) return; formEl.querySelectorAll('input,select,textarea').forEach(i=>{ if (i.name === '_token' || i.name === '_method') return; if (i.type === 'hidden' && i.id !== 'mueble-id' && i.name !== 'ruta_img') return; if (i.type === 'checkbox' || i.type === 'radio') { i.checked = false; return; } try { i.value = ''; } catch(e){} }); }

  async function loadFilesForFolder(folder){
    if (!fileSelect) return;
    fileSelect.innerHTML = '<option value="">Cargando…</option>';
    try {
      const u = new URL("{{ url('/imagenes/list') }}", window.location.origin);
      u.searchParams.set('folder', folder || '');
      const resp = await fetch(u.toString(), {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        credentials: 'same-origin'
      });
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
    } catch (e) {
      console.error('loadFilesForFolder error', e);
      fileSelect.innerHTML = '<option value="">Error de red</option>';
    }
  }
  if (folderSelect) {
    folderSelect.addEventListener('change', function(){
      const folder = this.value || '';
      if (!folder) {
        if (fileSelect) fileSelect.innerHTML = '<option value="">-- elegir --</option>';
        if (rutaInput) rutaInput.value = '';
        if (preview) preview.src = DEFAULT_IMG;
        return;
      }
      loadFilesForFolder(folder);
    });
  }

  if (fileSelect) {
    fileSelect.addEventListener('change', function(){
      const file = this.value || '';
      const folder = folderSelect ? folderSelect.value : '';
      if (!file || !folder) {
        if (rutaInput) rutaInput.value = '';
        if (preview) preview.src = DEFAULT_IMG;
        return;
      }
      const path = folder + '/' + file;
      if (rutaInput) rutaInput.value = path;
      if (preview) preview.src = (path.startsWith('http') ? path : (baseUrl + '/' + path));
    });
  }

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

  function esc(v){ if (v === null || v === undefined) return ''; return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
  function randomNearWhite(){ const hue = Math.floor(Math.random() * 360); const sat = Math.floor(Math.random() * 6); const light = 92 + Math.floor(Math.random() * 7); return `hsl(${hue} ${sat}% ${light}%)`; }

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
        if (folderSelect) folderSelect.value = folder;
        if (typeof loadFilesForFolder === 'function' && folderSelect && fileSelect) {
          loadFilesForFolder(folder).then(() => {
            const fo = Array.from(fileSelect.options).find(o => o.value === file);
            if (fo) fileSelect.value = file;
          }).catch(()=>{});
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

  function hideModalControls(){ try { const vt = document.getElementById('view-toggle'); const at = document.getElementById('admin-toggle'); if (vt && vt.parentElement) vt.parentElement.style.display = 'none'; if (at && at.parentElement) at.parentElement.style.display = 'none'; } catch(e){} }
  function showModalControls(){ try { const vt = document.getElementById('view-toggle'); const at = document.getElementById('admin-toggle'); if (vt && vt.parentElement) vt.parentElement.style.display = ''; if (at && at.parentElement) at.parentElement.style.display = ''; } catch(e){} }

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
      clearFormFields(form);
      form.dataset.originalCodigo = '';
      const ruta = document.getElementById('f-ruta-img'); if (ruta) ruta.value = '';
      const prev = document.getElementById('ruta-preview'); if (prev) prev.src = DEFAULT_IMG;
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
      form.dataset.originalCodigo = (m.codigo ?? '').toString();
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
      if (m.ruta_img) setPreviewFromRuta(m.ruta_img);
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
  function validateModalForm(){
    clearValidation();
    const formEl = document.getElementById('mueble-form');
    if (!formEl) return { ok:false, errors:{}, focus:null };
    const errors = {};
    const get = id => document.getElementById(id);
    const codigoEl = get('f-codigo-modal');
    const descripcionEl = get('f-descripcion-modal');
    const fechaEl = get('f-fecha-modal');
    const montoEl = get('f-monto-modal');
    const responsableEl = get('f-responsable-modal');
    const estadoEl = get('f-estado-modal');
    const rutaEl = get('f-ruta-img');

    const codigo = codigoEl?.value.trim() || '';
    const descripcion = descripcionEl?.value.trim() || '';
    const fecha = fechaEl?.value || '';
    const montoStr = montoEl?.value;
    const monto = montoStr === undefined || montoStr === null || montoStr === '' ? NaN : Number(montoStr);
    const responsable = responsableEl?.value || '';
    const estado = estadoEl?.value || '';
    const ruta = rutaEl?.value || '';
    const currentId = (document.getElementById('mueble-id')?.value || '').toString();

    if (!codigo) errors['f-codigo-modal'] = 'El código es obligatorio.';
    else {
      const found = EXISTING_MUEBLES.find(x => x.codigo && x.codigo.toString().trim().toLowerCase() === codigo.toLowerCase());
      if (found && String(found.id) !== currentId) {
        errors['f-codigo-modal'] = 'Ya existe un mueble con ese código. Use otro código.';
      }
    }
    if (!descripcion) errors['f-descripcion-modal'] = 'La descripción es obligatoria.';
    if (!fecha) errors['f-fecha-modal'] = 'La fecha es obligatoria.';
    if (Number.isNaN(monto)) errors['f-monto-modal'] = 'El monto unitario es obligatorio.';
    else if (monto < 0) errors['f-monto-modal'] = 'El monto unitario no puede ser negativo.';
    if (!responsable) errors['f-responsable-modal'] = 'Debe seleccionar un responsable.';
    if (!estado) errors['f-estado-modal'] = 'El estado es obligatorio.';
    if (!ruta) errors['f-ruta-img'] = 'Seleccione una imagen (carpeta + archivo) para el mueble.';
    if (Object.keys(errors).length) {
      let firstEl = null;
      Object.entries(errors).forEach(([fid, msg])=>{
        const el = document.getElementById(fid) || document.querySelector('[name="'+fid+'"]');
        setFieldError(el, msg);
        if (!firstEl) firstEl = el;
      });
      if (firstEl && typeof firstEl.focus === 'function') firstEl.focus();
      return { ok:false, errors, focus:firstEl };
    }
    return { ok:true, errors:{} };
  }

   if (btnSave) {
     btnSave.addEventListener('click', function(){
       const formEl = document.getElementById('mueble-form');
       if (!formEl) return;
       const v = validateModalForm();
       if (!v.ok) return;
       formEl.submit();
     });
   }
   if (btnCancel) {
     btnCancel.addEventListener('click', function(e){
       e && e.preventDefault();
       const cardEl = document.getElementById('user-form-card');
       if (cardEl) {
         cardEl.classList.add('collapsed');
         requestAnimationFrame(() => {
           cardEl.style.display = 'none';
           cardEl.classList.remove('collapsed');
         });
       }
       const f = document.getElementById('mueble-form');
       if (f) {
         try { f.reset(); } catch(e){}
         try { clearFormFields(f); } catch(e){}
       }
       populateModalModeloOptions('');
       try { showModalControls(); } catch(e){}
       if (filtersEl) filtersEl.style.display = 'flex';
       if (cardsGrid) cardsGrid.style.display = 'grid';
       const tableWrapper = document.getElementById('table-wrapper');
       if (tableWrapper) tableWrapper.style.display = 'none';
     });
   }
});
</script>