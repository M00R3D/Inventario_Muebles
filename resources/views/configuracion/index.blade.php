@extends('layouts.app')

@section('title','Configuración')

@section('content')
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <h3 style="margin:0">Configuración</h3>
  </div>

  @if(session('success'))
    <div style="padding:10px;border-radius:8px;background:#ecfeff;color:#065f46;margin-bottom:12px;">{{ session('success') }}</div>
  @endif

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="text-align:left;border-bottom:1px solid #e5e7eb">
        <th style="padding:8px;width:80px">Icono</th>
        <th style="padding:8px">Clave</th>
        <th style="padding:8px">Nombre</th>
        <th style="padding:8px;width:160px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($configuraciones as $cfg)
        <tr>
          <td style="padding:8px;vertical-align:middle">
            @php $img = $cfg->ruta_img ? asset($cfg->ruta_img) : asset('imgs/default.webp'); @endphp
            <img src="{{ $img }}" alt="{{ $cfg->clave }}" style="width:48px;height:32px;object-fit:cover;border-radius:6px;border:1px solid #e6e7eb;">
          </td>
          <td style="padding:8px;vertical-align:middle">{{ $cfg->clave }}</td>
          <td style="padding:8px;vertical-align:middle">{{ $cfg->nombre }}</td>
          <td style="padding:8px;vertical-align:middle">
            <a href="{{ route('configuracion.edit', $cfg->id) }}" class="btn-edit" style="text-decoration:none">Editar</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" style="padding:12px;color:#6b7280">No hay configuraciones.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection