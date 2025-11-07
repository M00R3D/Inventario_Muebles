@extends('layouts.app')
@section('title','Crear categoría')
@section('content')
<div class="card" style="max-width:720px">
    <h3>Crear categoría</h3>
    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf
        @include('categorias.form')
        <div style="margin-top:12px;display:flex;gap:.5rem;justify-content:flex-end">
            <a href="{{ route('categorias.index') }}" style="padding:8px 12px;border-radius:8px;background:transparent;border:1px solid #e5e7eb;color:#374151;text-decoration:none">Cancelar</a>
            <button type="submit" class="btn-logout" style="background:linear-gradient(90deg,#06b6d4,#2563eb)">Guardar</button>
        </div>
    </form>
</div>
@endsection