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
<style>
:root{ --bg:#f8fafc; --card:#fff; --muted:#6b7280; --accent1:#6366f1; --accent2:#06b6d4; }
.container{max-width:1200px;margin:0 auto;padding:18px;}
.header-hero{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
.header-hero h1{margin:0;font-size:1.25rem}
.grid{ display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:18px; }
.card{ background:var(--card); border-radius:12px; padding:14px; box-shadow:0 12px 34px rgba(2,6,23,0.08); display:flex; flex-direction:column; gap:12px; }
.modal-card{ transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease; transform-origin: top center; opacity:1; }
.modal-card.collapsed{ transform:scaleY(.98); opacity:0; max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; }
#confirm-overlay{ display:none; }

.estado-badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:4px 10px;
  border-radius:999px;
  font-size:0.78rem;
  font-weight:700;
  min-width:64px;
  text-align:center;
  box-shadow:0 2px 6px rgba(2,6,23,0.06);
}
.estado-bueno{ background:#10b981; color:#ffffff; }    
.estado-regular{ background:#f59e0b; color:#0b0b0b; }  
.estado-malo{ background:#ef4444; color:#ffffff; }     
.estado-en_reparacion{ background:#6366f1; color:#ffffff; } 

.preview-wrapper{
  display:flex;
  align-items:center;
  justify-content:center;
  background:#f8fafc;
  border-radius:8px;
  padding:6px;
  max-height:180px;      /* adjust if you want larger preview */
  overflow:hidden;
}
#ruta-preview{
  display:block;
  width:100%;
  max-width:320px;      /* max width inside preview box */
  max-height:160px;     /* keeps modal compact */
  object-fit:contain;   /* ensure full image fits inside */
  border-radius:6px;
  box-shadow:0 6px 18px rgba(2,6,23,0.06);
}
/* ensure modal doesn't forcibly crop the preview box (override if needed) */
#user-form-card .preview-wrapper{ overflow:visible; }
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

    <div style="display:flex;gap:8px;align-items:center">
      @if(!empty($isAdmin) && $isAdmin)
        <button id="btn-new" class="btn" type="button" style="background:linear-gradient(90deg,var(--accent1),var(--accent2));color:#fff;padding:8px 12px;border-radius:10px;border:0;cursor:pointer;font-weight:800">Nuevo mueble</button>
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
        <label style="display:block;font-weight:600;font-size:0.9rem;">Responsable</label>
        <select name="persona_id" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
          <option value="">Todos</option>
          @foreach($usuarios as $u)
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
      <button type="submit" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Buscar</button>
      <button type="button" id="btn-clear" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Limpiar</button>
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
        <div style="min-width:160px;">
          <label>Monto unitario</label>
          <input name="monto_unitario" id="f-monto" type="number" step="0.01" required style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
        </div>
        <div style="flex:1;min-width:160px;">
          <label>Responsable</label>
          <select name="persona_id" id="f-persona" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            <option value="">Selecciona</option>
            @foreach($usuarios as $u)
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
                <!-- added preview image element -->
                <div class="preview-wrapper">
                    <img id="ruta-preview" src="{{ url('/imgs/default.webp') }}" alt="Preview" />
                </div>
            </div>
          </div>
        </div>
        <input type="hidden" name="ruta_img" id="f-ruta-img" value="">
      </div>

      <div style="display:flex;gap:8px;margin-top:12px;">
        <button type="submit" id="btn-save" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Guardar</button>
        <button id="btn-cancel" type="button" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
      </div>
    </form>
  </div>

  @if($muebles->isEmpty())
    <div class="card">No hay muebles registrados aún.</div>
  @else
    <div class="grid" role="list">
      @foreach($muebles as $m)
        <article class="card" role="listitem" aria-labelledby="mueble-{{ $m->id }}">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="max-width:100%;height:auto;">
             @if($m->ruta_img)
               <img src="<?php echo e(asset($m->ruta_img ?? 'imgs/default.webp')); ?>" alt="Imagen mueble" style="max-width:100%;height:auto;border-radius:6px;">
             @else
               <div style="max-width:100%;height:auto;background:linear-gradient(180deg,#f3f4f6,#e5e7eb);display:flex;align-items:center;justify-content:center;border-radius:8px;color:#9ca3af;margin-bottom:8px;">
                 Sin imagen
               </div>
             @endif
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="font-weight:800">{{ $m->codigo ?? 'ID '.$m->id }}</div>
                <?php $estadoClass = 'estado-'.($m->estado ?? ''); ?>
                <span class="estado-badge <?php echo e($estadoClass); ?>">
                  <?php echo e(ucfirst(str_replace('_',' ', $m->estado ?? '-'))); ?>
                </span>
              </div>
              <div style="color:var(--muted)">{{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 120) }}</div>
              <div style="color:var(--muted);font-size:0.95rem;">
                <?php
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
                ?>
                <?php if($showRead): ?>
                  <span class="desc-ellipsis">…</span>
                  <a href="#" class="read-more" data-full="<?php echo e($rest); ?>" style="margin-left:8px;color:#06b6d4;font-weight:700;text-decoration:none;">Leer más</a>
                <?php endif; ?>
                <span class="desc-full" style="display:none;"><?php echo e($rest); ?></span>
               </div>
               <div style="margin-top:8px;font-weight:700">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</div>
               <div style="font-size:0.86rem;color:var(--muted)">{{ $m->usuario->nombre ?? '-' }} {{ $m->usuario->apellido ?? '' }}</div>
               <?php $nota = trim($m->nota ?? ''); ?>
               <?php if($nota): ?>
                <span class="mueble-nota" style="
                  display:flex;
                  flex-wrap:wrap;
                  white-space:normal;
                  word-break:break-word;
                  overflow-wrap:break-word;
                  max-width:100%;
                  margin-top:6px;
                  font-size:0.9rem;
                  color:#374151;
                  background:#f8fafc;
                  padding:6px 8px;
                  border-radius:8px;
                ">
                  <?php echo e(\Illuminate\Support\Str::limit($nota, 120)); ?>
                </span>
               <?php endif; ?>
             </div>
             <div style="display:flex;flex-direction:column;gap:8px;">
                    @if(!empty($isAdmin) && $isAdmin)
                      <button type="button" class="btn-edit" data-mueble='@json($m)' style="background:#06b6d4;color:#fff;padding:8px;border-radius:8px;border:0;cursor:pointer;">Editar</button>
                      <form action="{{ url('/muebles/'.$m->id) }}" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" data-confirm="¿Eliminar mueble {{ addslashes($m->codigo ?? 'ID '.$m->id) }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:8px;border-radius:8px;border:none;cursor:pointer;" data-confirm-type="delete">Eliminar</button>
                      </form>
                    @else
                      <a href="{{ url('/solicitudes/create') }}?mueble_id={{ $m->id }}" style="display:inline-block;text-align:center;background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;">Solicitar</a>
                    @endif
                  </div>
                </div>
              </article>
            @endforeach
    </div>
  @endif
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
    card.classList.add('collapsed');
    card.addEventListener('transitionend', function handler(){
      card.style.display = 'none';
      card.classList.remove('collapsed');
      showPageForModal();
      card.removeEventListener('transitionend', handler);
    });
  });

  document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click', function(){
      try {
        const m = JSON.parse(this.getAttribute('data-mueble'));
        openEdit(m);
      } catch(e){ console.error(e); alert('Datos inválidos'); }
    });
  });
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
  if (btnClear) btnClear.addEventListener('click', function(){
    document.getElementById('filters').querySelectorAll('input,select').forEach(i=> i.value = '');
    document.getElementById('filters').submit();
  });
});
</script>
@endsection

</body>
</html>