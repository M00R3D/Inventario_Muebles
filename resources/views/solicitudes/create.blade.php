@extends('layouts.app')

@section('title','Crear solicitud | Inventario Muebles')

@section('content')
<div style="max-width:900px;margin:18px auto;padding:12px;">
  <h1>Crear solicitud</h1>

  <div style="background:#fff;padding:12px;border-radius:10px;box-shadow:0 12px 34px rgba(2,6,23,0.06);">
    <form method="POST" action="{{ url('/solicitudes') }}">
      @csrf
      <input type="hidden" name="mueble_id" value="{{ $mueble->id ?? '' }}">
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:300px;">
          <label>Mueble</label>
          <div style="display:flex;gap:12px;align-items:center;">
            <div style="width:120px;height:90px;flex-shrink:0;">
              @if(!empty($mueble->ruta_img))
                <img src="{{ asset($mueble->ruta_img) }}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
              @else
                <div style="width:100%;height:100%;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;">Sin imagen</div>
              @endif
            </div>
            <div>
              <div style="font-weight:800">{{ $mueble->codigo ?? 'Selecciona mueble en la lista' }}</div>
              <div style="color:#6b7280">{{ \Illuminate\Support\Str::limit($mueble->descripcion ?? '-', 160) }}</div>
            </div>
          </div>
        </div>

        <div style="flex:1;min-width:240px;">
          <label>Solicitante</label>

          @if(!empty($currentUser) && ($currentUser->rol ?? '') !== 'admin')
            <input type="hidden" name="persona_id" value="{{ $currentUser->id }}">
            <div style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;background:#fafafa;font-weight:700;">
              {{ $currentUser->nombre }} {{ $currentUser->apellido }} (Conectado)
            </div>
          @else
            <select name="persona_id" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
              <option value="">Selecciona</option>
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}" {{ (!empty($currentUser) && $currentUser->id == $u->id) ? 'selected' : '' }}>
                  {{ $u->nombre }} {{ $u->apellido }}
                </option>
              @endforeach
            </select>
          @endif

          <label style="margin-top:8px;">Fecha inicio</label>
          <input type="date" name="fecha_inicio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Fecha fin</label>
          <input type="date" name="fecha_fin" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">

          <label style="margin-top:8px;">Nota</label>
          <textarea name="nota" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>

          <label style="margin-top:8px;">Estado</label>
          @if(!empty($currentUser) && ($currentUser->rol ?? '') !== 'admin')
            <input type="hidden" name="estado" value="pendiente">
            <div style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;background:#fff9ed;font-weight:700;color:#92400e;">Pendiente (automático)</div>
          @else
            <select name="estado" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;margin-bottom:12px;">
              <option value="pendiente">Pendiente</option>
              <option value="aprobada">Aprobada</option>
              <option value="rechazada">Rechazada</option>
            </select>
          @endif

          <div style="display:flex;gap:8px;">
            <button type="submit" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Crear solicitud</button>
            <a href="{{ url('/muebles') }}" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Cancelar</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection