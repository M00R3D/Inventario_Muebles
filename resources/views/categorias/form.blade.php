<div style="display:grid;gap:.5rem">
    <label>
        <div style="font-weight:700;font-size:0.95rem">Nombre</div>
        <input name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required maxlength="150" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb" />
        @error('nombre') <div style="color:#ef4444;font-size:.9rem">{{ $message }}</div> @enderror
    </label>

    <label>
        <div style="font-weight:700;font-size:0.95rem">Descripción</div>
        <textarea name="descripcion" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
        @error('descripcion') <div style="color:#ef4444;font-size:.9rem">{{ $message }}</div> @enderror
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
            <input id="f-ruta-img" name="ruta_img" value="{{ old('ruta_img', $categoria->ruta_img ?? '') }}" placeholder="folder/archivo.jpg" maxlength="200" style="flex:1;padding:8px;border-radius:8px;border:1px solid #e5e7eb" />
        </div>
        <div id="ruta-preview" style="margin-top:8px">
            @php
                $imgPath = old('ruta_img', $categoria->ruta_img ?? '');
                if ($imgPath) $imgUrl = (\Illuminate\Support\Str::startsWith($imgPath, ['http://','https://']) ? $imgPath : asset($imgPath));
                else $imgUrl = asset('imgs/default.webp');
            @endphp
            <img src="{{ $imgUrl }}" alt="{{ $categoria->nombre ?? 'preview' }}" style="max-width:160px;border-radius:8px;box-shadow:0 6px 18px rgba(15,23,42,0.06);">
        </div>
        @error('ruta_img') <div style="color:#ef4444;font-size:.9rem">{{ $message }}</div> @enderror
    </label>
</div>
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
            if (!resp.ok) throw new Error('No se pudieron cargar las carpetas');
            const data = await resp.json();
            const dirs = data.dirs || [];
            folderSelect.innerHTML = '';
            if (dirs.length === 0) {
                const o = document.createElement('option'); o.value = ''; o.textContent = '-- no hay carpetas en public --'; folderSelect.appendChild(o);
                fileSelect.innerHTML = '<option value="">-- --</option>';
                fileSelect.disabled = true;
                return;
            }
            dirs.forEach(d => {
                const o = document.createElement('option'); o.value = d; o.textContent = d; folderSelect.appendChild(o);
            });
            const existing = (rutaInput.value || '').trim();
            if (existing) {
                const parts = existing.split('/');
                if (parts.length >= 2) {
                    const f = parts.shift();
                    const opt = Array.from(folderSelect.options).find(x => x.value === f);
                    if (opt) folderSelect.value = f;
                }
            }
            if (folderSelect.value) {
                loadFilesForFolder(folderSelect.value);
            }
        } catch (e) {
            folderSelect.innerHTML = '<option value="">-- error cargando carpetas --</option>';
            fileSelect.innerHTML = '<option value="">-- --</option>';
            fileSelect.disabled = true;
            console.error(e);
        }
    })();
    async function loadFilesForFolder(folder){
        fileSelect.disabled = true;
        fileSelect.innerHTML = '<option value="">Cargando…</option>';
        try {
            const u = new URL('/imagenes/list', window.location.origin);
            u.searchParams.set('folder', folder || '');
            const resp = await fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            if (!resp.ok) { fileSelect.innerHTML = '<option value="">Error</option>'; return; }
            const json = await resp.json();
            const files = json.files || [];
            fileSelect.innerHTML = '<option value="">-- elegir archivo --</option>';
            files.forEach(f => {
                const o = document.createElement('option');
                o.value = f;
                o.textContent = f;
                fileSelect.appendChild(o);
            });
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
        } catch (err) {
            console.error('loadFilesForFolder error', err);
            fileSelect.innerHTML = '<option value="">Error de red</option>';
            fileSelect.disabled = true;
        }
    }
    folderSelect.addEventListener('change', function(){
        const folder = this.value || '';
        if (!folder) {
            fileSelect.innerHTML = '<option value="">-- elegir carpeta primero --</option>';
            fileSelect.disabled = true;
            return;
        }
        loadFilesForFolder(folder);
    });
    fileSelect.addEventListener('change', function(){
        const file = this.value || '';
        const folder = folderSelect.value || '';
        if (!file || !folder) return;
        const path = folder + '/' + file;
        rutaInput.value = path;
        setPreview(path);
    });
    rutaInput.addEventListener('input', function(){
        const val = (this.value || '').trim();
        setPreview(val);
    });
    setPreview(rutaInput.value);
});
</script>
@endsection