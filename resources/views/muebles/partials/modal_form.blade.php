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
    </div>

    <div style="display:flex;gap:8px;margin-top:10px;">
      <button type="button" id="btn-save" class="btn-save btn-base">Guardar</button>
      <button type="button" id="btn-cancel" class="btn-cancel btn-base">Cancelar</button>
    </div>
  </form>
</div>