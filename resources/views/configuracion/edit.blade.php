@extends('layouts.app')

@section('title','Editar configuración')

@section('content')
<style>
.uploader { border:2px dashed #e5e7eb; border-radius:10px; padding:18px; display:flex; flex-direction:column; gap:10px; align-items:center; text-align:center; background:#fff; }
</style>

<div class="card" style="max-width:720px">
  <h3>Editar configuración — {{ $configuracion->clave }}</h3>

  <form action="{{ route('configuracion.update', $configuracion->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('configuracion.form')
    <div style="margin-top:12px;display:flex;gap:.5rem;justify-content:flex-end">
      <a href="{{ route('configuracion.index') }}" class="btn-ghost" style="padding:8px 12px;border-radius:8px;border:1px solid #e5e7eb;color:#374151;text-decoration:none">Cancelar</a>
      <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
  </form>
</div>
@endsection