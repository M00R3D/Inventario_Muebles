<!doctype html>
@extends('layouts.app')
@section('title','Categorías')
@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h3 style="margin:0">Categorías</h3>
        <a href="{{ route('categorias.create') }}" class="btn-logout" style="background:linear-gradient(90deg,#10b981,#059669);">Nueva categoría</a>
    </div>

    @if(session('success'))
        <div style="padding:10px;border-radius:8px;background:#ecfeff;color:#065f46;margin-bottom:12px;">{{ session('success') }}</div>
    @endif

    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="text-align:left;border-bottom:1px solid #e5e7eb">
                <th style="padding:8px;width:80px">Imagen</th>
                <th style="padding:8px">Nombre</th>
                <th style="padding:8px">Descripción</th>
                <th style="padding:8px;width:180px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $c)
                <tr>
                    <td style="padding:8px;vertical-align:middle;">
                        @php
                            $imgPath = $c->ruta_img ?? '';
                            $imgUrl = $imgPath ? asset($imgPath) : asset('imgs/default.webp');
                        @endphp
                        <img src="{{ $imgUrl }}" alt="{{ $c->nombre }}" style="width:64px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #e6e7eb;">
                    </td>
                    <td style="padding:8px;vertical-align:middle">{{ $c->nombre }}</td>
                    <td style="padding:8px;vertical-align:middle">{{ $c->descripcion }}</td>
                    <td style="padding:8px;vertical-align:middle">
                        <a href="{{ route('categorias.edit', $c->id) }}" style="margin-right:8px;">Editar</a>
                        <form action="{{ route('categorias.destroy', $c->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-confirm="¿Eliminar categoría {{ $c->nombre }}?" onclick="showConfirmFor(this)" style="background:transparent;border:0;color:#ef4444;cursor:pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:12px;color:#6b7280">No hay categorías.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection