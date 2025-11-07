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