<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Muebles | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Muebles | Inventario Muebles')

@section('content')
@php
    $current = null;
    if (session()->has('usuario_id')) {$current = \App\Models\Usuario::find(session('usuario_id'));}
    $isAdmin = $current && ($current->rol === 'admin');

    $visibleMuebles = $isAdmin ? $muebles : $muebles->filter(function($m){
        return empty($m->usuario);
    });
@endphp

@include('muebles.partials.styles')

<div class="container">
  @include('muebles.partials.header_filters')
  @include('muebles.partials.modal_form')
  @if($visibleMuebles->isEmpty())
    <div class="card">No hay muebles registrados aún.</div>
  @else
    @include('muebles.partials.cards', ['visibleMuebles' => $visibleMuebles])
  @endif

  @include('muebles.partials.table_view')
</div>

@endsection

@section('scripts')
    @include('muebles.partials.scripts')
@endsection

</body>
</html>