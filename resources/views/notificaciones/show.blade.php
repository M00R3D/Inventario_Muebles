<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
@extends('layouts.app')

@section('title', 'Detalle de notificación | Inventario Muebles')

@section('content')
@php
use Carbon\Carbon;
Carbon::setLocale('es');
$notificacion = $notificacion ?? null;
$routeId = request()->route('notificacion') ?? request('id') ?? null;
if (!$notificacion && $routeId) {
    try {
        $notificacion = \App\Models\Notificacion::find($routeId);
    } catch (\Throwable $e) {
        $notificacion = null;
    }
}

$destinatario = null;
if ($notificacion) {
    try {
        if (!empty($notificacion->id_usuario)) {
            $destinatario = \App\Models\Usuario::find($notificacion->id_usuario);
        } elseif (!empty($notificacion->id_admin)) {
            $destinatario = \App\Models\Usuario::find($notificacion->id_admin);
        }
    } catch (\Throwable $e) {
        $destinatario = null;
    }

    $relacion = null;
    if (!empty($notificacion->ruta)) {
        if (preg_match('#/solicitudes/(\d+)#', $notificacion->ruta, $m)) {
            $relacion = \App\Models\Solicitud::find($m[1]);
        } elseif (preg_match('#/muebles/(\d+)#', $notificacion->ruta, $m)) {
            $relacion = \App\Models\Mueble::find($m[1]);
        }
    }
    try {
        $fecha_hace = $notificacion->fecha_creacion ? Carbon::parse($notificacion->fecha_creacion)->diffForHumans() : ($notificacion->created_at ? Carbon::parse($notificacion->created_at)->diffForHumans() : null);
        $fecha_full = $notificacion->fecha_creacion ? Carbon::parse($notificacion->fecha_creacion)->toDateTimeString() : ($notificacion->created_at ? Carbon::parse($notificacion->created_at)->toDateTimeString() : null);
    } catch (\Throwable $e) {
        $fecha_hace = null;
        $fecha_full = null;
    }
}
@endphp

<style>
.notif-card-wrap{max-width:920px;margin:36px auto;padding:18px}
.notif-card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 18px 48px rgba(3,10,30,0.06);overflow:auto}
.notif-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px}
.notif-title{font-size:1.15rem;font-weight:700}
.notif-body{color:#374151;font-size:0.95rem;line-height:1.45}
.notif-meta{color:#6b7280;font-size:0.9rem;margin-top:12px}
.kv{color:#374151;font-weight:600;margin-right:6px}
.btn-return{display:inline-block;text-decoration:none;color:#fff;background:linear-gradient(90deg,#6366f1,#06b6d4);padding:8px 12px;border-radius:8px}
</style>

@if(!$notificacion)
  <div class="notif-card-wrap">
    <div class="notif-card">
      <h2 style="margin:0 0 8px 0">Notificación no encontrada</h2>
      <p style="color:#6b7280">No se encontró la notificación solicitada (id: {{ $routeId ?? 'N/A' }}).</p>
      <p><a href="javascript:history.back()" class="btn-return">Volver a notificaciones</a></p>
    </div>
  </div>
@else
  <div class="notif-card-wrap">
    <div class="notif-card" role="region" aria-labelledby="notif-title">
      <div class="notif-header">
        <div id="notif-title" class="notif-title">Notificación — {{ ucfirst($notificacion->tipo ?? 'detalle') }}</div>
        <div><a href="javascript:history.back()" class="btn-return" aria-label="Cerrar">Volver</a></div>
      </div>

      <div class="notif-body">
        <p><span class="kv">Para:</span> {{ $destinatario ? ($destinatario->nombre . ' ' . $destinatario->apellido) : ($notificacion->audiencia ?? 'N/A') }}</p>

        <p><span class="kv">Tipo:</span> {{ $notificacion->tipo ?? '-' }}</p>

        <p><span class="kv">Estado:</span> {{ $notificacion->estado ?? '-' }}</p>

        <p><span class="kv">Audiencia:</span> {{ $notificacion->audiencia ?? '-' }}</p>

        <p><span class="kv">Descripción:</span></p>
        <div style="background:#f8fafc;padding:12px;border-radius:8px;color:#111827">{{ $notificacion->descripcion ?? '-' }}</div>

        @if($relacion)
          <div style="margin-top:12px;">
            <strong>Relacionado:</strong>
            @if($relacion instanceof \App\Models\Solicitud)
              <div>Solicitud #{{ $relacion->id }} — <a href="{{ url('/solicitudes/'.$relacion->id) }}">Ver solicitud</a></div>
            @elseif($relacion instanceof \App\Models\Mueble)
              <div>Mueble #{{ $relacion->id }} — <a href="{{ url('/muebles/'.$relacion->id) }}">Ver mueble</a></div>
            @endif
          </div>
        @endif

        <div class="notif-meta">
          <div><span class="kv">Creado:</span> {{ $fecha_full ?? 'N/A' }} ({{ $fecha_hace ?? 'N/A' }})</div>
          @if(!empty($notificacion->ruta))
            <div style="margin-top:8px;"><a href="{{ url($notificacion->ruta) }}" class="btn-return" style="background:linear-gradient(90deg,#10b981,#059669);">Ir a destino</a></div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endif

@endsection