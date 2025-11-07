<div style="display:grid;gap:.5rem">
  <label>
    <div style="font-weight:700;font-size:0.95rem">Clave (identificador)</div>
    <input name="clave" value="{{ old('clave', $configuracion->clave ?? '') }}" readonly maxlength="100" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;background:#f3f4f6" />
  </label>

  <label>
    <div style="font-weight:700;font-size:0.95rem">Nombre</div>
    <input name="nombre" value="{{ old('nombre', $configuracion->nombre ?? '') }}" maxlength="150" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb" />
    @error('nombre') <div style="color:#ef4444;font-size:.9rem">{{ $message }}</div> @enderror
  </label>

  <label>
    <div style="font-weight:700;font-size:0.95rem">Imagen (ruta pública)</div>
    <div style="display:flex;gap:8px;align-items:center">
      <select id="select-folder" style="width:220px;padding:8px;border-radius:8px;border:1px solid #e5e7eb" aria-label="Carpeta de imágenes">
        <option value="">Cargando…</option>
      </select>
      <select id="select-file" disabled style="width:220px;padding:8px;border-radius:8px;border:1px solid #e5e7eb" aria-label="Archivo de imagen">
        <option value="">-- elegir archivo --</option>
      </select>
      <input id="f-ruta-img" name="ruta_img" value="{{ old('ruta_img', $configuracion->ruta_img ?? '') }}" placeholder="folder/archivo.jpg" maxlength="255" style="flex:1;padding:8px;border-radius:8px;border:1px solid #e5e7eb" />
    </div>

    <div id="ruta-preview" style="margin-top:8px">
      @php
        $imgPath = old('ruta_img', $configuracion->ruta_img ?? '');
        $imgUrl = $imgPath ? (\Illuminate\Support\Str::startsWith($imgPath, ['http://','https://']) ? $imgPath : asset($imgPath)) : asset('imgs/default.webp');
      @endphp
      <img src="{{ $imgUrl }}" alt="{{ $configuracion->clave ?? 'preview' }}" style="max-width:160px;border-radius:8px;box-shadow:0 6px 18px rgba(15,23,42,0.06);">
    </div>
    @error('ruta_img') <div style="color:#ef4444;font-size:.9rem">{{ $message }}</div> @enderror
  </label>

  <label>
    <div style="display:flex;gap:12px;align-items:center;">
      <div>
        <div style="font-weight:700;font-size:0.95rem">Color normal</div>
        <input type="color" name="normal_color" value="{{ old('normal_color', $configuracion->normal_color ?? '#f59e0b') }}" style="width:56px;height:36px;border:0;padding:0;background:transparent;">
      </div>
      <div>
        <div style="font-weight:700;font-size:0.95rem">Color hover</div>
        <input type="color" name="hover_color" value="{{ old('hover_color', $configuracion->hover_color ?? ($configuracion->normal_color ?? '#f97316')) }}" style="width:56px;height:36px;border:0;padding:0;background:transparent;">
      </div>
      <div style="flex:1">
        <div style="font-weight:700;font-size:0.95rem">Vista previa</div>
        <div id="icon-preview" style="width:48px;height:48px;border-radius:8px;margin-top:6px;box-shadow:0 6px 18px rgba(15,23,42,0.06);"></div>
      </div>
    </div>
  </label>
</div>

{{-- Small style helpers para inputs y preview --}}
<style>
.btn-primary { background: linear-gradient(90deg,#06b6d4,#6366f1); color:#fff; padding:8px 12px; border-radius:8px; border:0; font-weight:700; cursor:pointer; }
.btn-ghost { background:transparent; border:1px solid #e5e7eb; padding:8px 12px; border-radius:8px; color:#374151; }
#icon-preview { width:48px; height:48px; border-radius:8px; overflow:hidden; display:inline-grid; place-items:center; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const folderSelect = document.getElementById('select-folder');
  const fileSelect = document.getElementById('select-file');
  const rutaInput = document.getElementById('f-ruta-img');
  const previewWrapper = document.getElementById('ruta-preview');
  const baseUrl = window.location.origin;

  function setPreview(src){
    previewWrapper.innerHTML = '';
    if (!src) return;
    const img = document.createElement('img');
    img.src = src.startsWith('http') ? src : (baseUrl + '/' + src.replace(/^\/+/,'')); 
    img.alt = 'preview';
    img.style.maxWidth = '160px';
    img.style.borderRadius = '8px';
    img.style.boxShadow = '0 6px 18px rgba(15,23,42,0.06)';
    previewWrapper.appendChild(img);
  }

  (async function loadDirs(){
    try {
      const resp = await fetch('/imagenes/dirs', { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const data = await resp.json();
      const dirs = data.dirs || [];
      folderSelect.innerHTML = '';
      if (!dirs.length) { folderSelect.innerHTML = '<option value="">-- no hay carpetas --</option>'; fileSelect.innerHTML = '<option value="">-- --</option>'; fileSelect.disabled = true; return; }
      dirs.forEach(d => { const o = document.createElement('option'); o.value = d; o.textContent = d; folderSelect.appendChild(o); });
      const existing = (rutaInput.value || '').trim();
      if (existing) {
        const parts = existing.split('/');
        if (parts.length >= 2) {
          const f = parts.shift();
          const opt = Array.from(folderSelect.options).find(x => x.value === f);
          if (opt) folderSelect.value = f;
        }
      }
      if (folderSelect.value) loadFilesForFolder(folderSelect.value);
    } catch(e){ folderSelect.innerHTML = '<option value="">error</option>'; fileSelect.innerHTML = '<option value="">-- --</option>'; fileSelect.disabled = true; console.error(e); }
  })();

  async function loadFilesForFolder(folder){
    fileSelect.disabled = true; fileSelect.innerHTML = '<option>Cargando…</option>';
    try {
      const u = new URL('/imagenes/list', window.location.origin);
      u.searchParams.set('folder', folder || '');
      const resp = await fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const json = await resp.json();
      const files = json.files || [];
      fileSelect.innerHTML = '<option value="">-- elegir archivo --</option>';
      files.forEach(f => { const o = document.createElement('option'); o.value = f; o.textContent = f; fileSelect.appendChild(o); });
      fileSelect.disabled = false;
      const existing = (rutaInput.value || '').trim();
      if (existing) {
        const parts = existing.split('/');
        if (parts.length >= 2) {
          const file = parts.slice(1).join('/');
          const opt = Array.from(fileSelect.options).find(x => x.value === file);
          if (opt) fileSelect.value = file;
        }
      }
    } catch(err){ fileSelect.innerHTML = '<option value="">Error</option>'; fileSelect.disabled = true; console.error(err); }
  }

  folderSelect.addEventListener('change', function(){ const f = this.value || ''; if (!f) { fileSelect.innerHTML = '<option value="">-- elegir carpeta primero --</option>'; fileSelect.disabled = true; return; } loadFilesForFolder(f); });
  fileSelect.addEventListener('change', function(){ const file = this.value || ''; const folder = folderSelect.value || ''; if (!file || !folder) return; const path = folder + '/' + file; rutaInput.value = path; setPreview(path); });
  rutaInput.addEventListener('input', function(){ setPreview((this.value||'').trim()); });

  const preview = document.getElementById('icon-preview');
  const normalInput = document.querySelector('input[name="normal_color"]');
  const hoverInput = document.querySelector('input[name="hover_color"]');

  function renderPreview(){
    const ruta = (rutaInput && rutaInput.value) ? rutaInput.value : '';
    preview.innerHTML = '';
    const span = document.createElement('span');
    span.style.display = 'inline-block';
    span.style.width = '100%';
    span.style.height = '100%';
    span.style.borderRadius = '8px';
    span.style.backgroundColor = normalInput ? normalInput.value : '#f59e0b';
    span.style.webkitMaskRepeat = 'no-repeat';
    span.style.maskRepeat = 'no-repeat';
    span.style.webkitMaskPosition = 'center';
    span.style.maskPosition = 'center';
    span.style.webkitMaskSize = 'contain';
    span.style.maskSize = 'contain';
    if (ruta) {
      const url = ruta.startsWith('http') ? ruta : (window.location.origin + '/' + ruta.replace(/^\/+/, ''));
      span.style.webkitMaskImage = "url('" + url + "')";
      span.style.maskImage = "url('" + url + "')";
    } else {
      span.textContent = '⚙️';
      span.style.background = 'transparent';
      span.style.fontSize = '18px';
      span.style.display = 'grid';
      span.style.placeItems = 'center';
    }
    preview.appendChild(span);
  }

  if (normalInput) normalInput.addEventListener('input', renderPreview);
  if (hoverInput) hoverInput.addEventListener('input', renderPreview);
  if (rutaInput) rutaInput.addEventListener('input', renderPreview);
  renderPreview();
});
</script>
@endsection