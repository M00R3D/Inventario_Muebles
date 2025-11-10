@extends('layouts.app')

@section('title', 'Solicitud #' . ($solicitud->id ?? 'Detalle') . ' | Inventario Muebles')

@section('content')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $currentUser = session()->has('usuario_id') ? \App\Models\Usuario::find(session('usuario_id')) : null;
    $isAdmin = $currentUser && $currentUser->rol === 'admin';
    $solicitante = $solicitud->usuario ?? $solicitud->persona ?? null;
    $mueble = $solicitud->mueble ?? null;
    $creado_hace = null;
    if (!empty($solicitud->created_at)) {
        try { $creado_hace = $solicitud->created_at->locale('es')->diffForHumans(); } catch(\Throwable $e) { $creado_hace = Carbon::parse($solicitud->created_at)->locale('es')->diffForHumans(); }
    }
@endphp
<div style="max-width:1000px;margin:18px auto;padding:12px;">
    <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Detalle de solicitud</h1>
            <div style="color:#6b7280;margin-top:6px;">
                Solicitud #{{ $solicitud->id }}
                @if($creado_hace) — <small style="font-weight:700">{{ $creado_hace }}</small>@endif
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ url('/solicitudes') }}" class="btn" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;">Volver</a>
            @if($isAdmin)
                <a href="{{ url('/solicitudes/' . $solicitud->id . '/edit') }}" class="btn" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;">Editar</a>
            @endif
        </div>
    </header>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:12px;">
        <aside style="background:#fff;padding:12px;border-radius:10px;box-shadow:0 8px 24px rgba(2,6,23,0.06);">
            @if($mueble)
                @php $img = $mueble->ruta_img ? url($mueble->ruta_img) : asset('imgs/default.webp'); @endphp
                <a href="{{ url('/muebles/' . $mueble->id) }}" style="display:block;text-decoration:none;color:inherit;">
                    <div style="width:100%;height:220px;display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:8px;background:#f8fafc;">
                        <img src="{{ $img }}" alt="{{ $mueble->codigo ?? 'mueble' }}" style="max-width:100%;max-height:100%;object-fit:contain;" onerror="this.src='{{ asset('imgs/default.webp') }}'">
                    </div>
                    <div style="margin-top:10px;font-weight:800;">{{ $mueble->codigo ?? 'Mueble #' . ($mueble->id ?? '-') }}</div>
                    <div style="color:#6b7280;margin-top:6px;">{{ \Illuminate\Support\Str::limit($mueble->descripcion ?? '-', 140) }}</div>
                    <div style="margin-top:10px;">
                        <a href="{{ url('/muebles/' . $mueble->id) }}" class="btn-ghost" style="text-decoration:none;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;display:inline-flex;align-items:center;">Ver detalle del mueble</a>
                    </div>
                </a>
            @else
                <div style="padding:18px;text-align:center;color:#6b7280;">No hay mueble asociado a esta solicitud.</div>
            @endif
        </aside>
        <section style="background:#fff;padding:16px;border-radius:10px;box-shadow:0 8px 24px rgba(2,6,23,0.06);">
            <dl style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-weight:700;color:#374151;">ID</label>
                    <div style="margin-top:6px;">{{ $solicitud->id }}</div>
                </div>

                <div>
                    <label style="font-weight:700;color:#374151;">Estado</label>
                    <div style="margin-top:6px;font-weight:800;color:{{ $solicitud->estado === 'aprobada' ? '#059669' : ($solicitud->estado === 'rechazada' ? '#ef4444' : '#92400e') }};">
                        {{ ucfirst($solicitud->estado ?? 'pendiente') }}
                    </div>
                </div>

                <div>
                    <label style="font-weight:700;color:#374151;">Solicitante</label>
                    <div style="margin-top:6px;">
                        @if($solicitante && ($solicitante->nombre ?? null))
                            {{ $solicitante->nombre }} {{ $solicitante->apellido ?? '' }}
                            <div style="color:#6b7280;font-size:0.9rem;">{{ $solicitante->email ?? '' }}</div>
                        @else
                            {{ $solicitud->persona_id ?? '-' }}
                        @endif
                    </div>
                </div>

                <div>
                    <label style="font-weight:700;color:#374151;">Período solicitado</label>
                    <div style="margin-top:6px;">
                        {{ $solicitud->fecha_inicio ? \Carbon\Carbon::parse($solicitud->fecha_inicio)->toDateString() : '-' }}
                         — 
                        {{ $solicitud->fecha_fin ? \Carbon\Carbon::parse($solicitud->fecha_fin)->toDateString() : '-' }}
                    </div>
                </div>

                <div style="grid-column:1 / -1;">
                    <label style="font-weight:700;color:#374151;">Nota</label>
                    <div style="margin-top:6px;background:#f8fafc;padding:10px;border-radius:8px;border:1px solid #eef2ff;">
                        {!! nl2br(e($solicitud->nota ?? '-')) !!}
                    </div>
                </div>

                <div style="grid-column:1 / -1;">
                    <label style="font-weight:700;color:#374151;">Información adicional del mueble</label>
                    <div style="margin-top:6px;color:#374151;">
                        @if($mueble)
                            <div><strong>Marca / Modelo:</strong> {{ $mueble->marca ?? '-' }} {{ $mueble->modelo ? ' / '.$mueble->modelo : '' }}</div>
                            <div style="margin-top:6px;"><strong>Monto:</strong> ${{ number_format($mueble->monto_unitario ?? 0,2,',','.') }}</div>
                            <div style="margin-top:6px;"><strong>Estado mueble:</strong> {{ ucfirst(str_replace('_',' ',$mueble->estado ?? '-')) }}</div>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div style="grid-column:1 / -1;display:flex;gap:8px;justify-content:flex-end;">
                    <small style="color:#6b7280;">Creada: {{ $solicitud->created_at ?? $solicitud->fecha_creacion ?? '-' }}</small>
                    @if($solicitud->updated_at)
                        <small style="color:#6b7280;">Última actualización: {{ $solicitud->updated_at }}</small>
                    @endif
                </div>
            </dl>
            @if($mueble && $mueble->comentarios && $mueble->comentarios->isNotEmpty())
                <hr style="margin:14px 0;border:none;border-top:1px solid #f3f4f6;">
                <h3 style="margin:0 0 8px 0;">Comentarios del mueble</h3>
                <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                    @foreach($mueble->comentarios->take(6) as $c)
                        <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #eef2ff;display:flex;gap:8px;align-items:flex-start;">
                            <div style="flex:1">
                                <div style="font-weight:800;color:#0f172a;">{{ $c->usuario ? ($c->usuario->nombre . ' ' . $c->usuario->apellido) : 'Anon' }}</div>
                                <div style="color:#374151;margin-top:6px;">{{ \Illuminate\Support\Str::limit($c->comentario, 300) }}</div>
                                <div style="color:#6b7280;font-size:0.85rem;margin-top:6px;">{{ optional($c->created_at)->locale('es')->diffForHumans() }}</div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                                <a href="{{ url('/muebles/' . ($mueble->id ?? '')) }}" class="btn-ghost" style="text-decoration:none;padding:6px 10px;border:1px solid #e5e7eb;border-radius:8px;">Ver mueble</a>
                                @if($isAdmin)
                                    <form method="POST" action="{{ url('/comentarios/' . $c->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete" style="background:linear-gradient(90deg,#ef4444,#d94660);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if($mueble->comentarios->count() > 6)
                        <div style="text-align:center;color:#6b7280;">Mostrando 6 de {{ $mueble->comentarios->count() }} comentarios — <a href="{{ url('/muebles/' . $mueble->id) }}" style="font-weight:800;color:#0ea5e9;">Ver todos</a></div>
                    @endif
                </div>
            @endif

        </section>
    </div>
</div>
@endsection