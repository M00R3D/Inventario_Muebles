<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Muebles | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Muebles | Inventario Muebles')

@section('content')
<style>
:root{
  --bg:#f8fafc; --card:#fff; --muted:#6b7280; --accent1:#6366f1; --accent2:#06b6d4;
  --radius:14px; --shadow:0 12px 34px rgba(2,6,23,0.08);
}
.container{max-width:1200px;margin:0 auto;padding:18px;}
.header-hero{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
.header-hero h1{margin:0;font-size:1.25rem}
.grid{
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap:18px;
}
.card{
  background:linear-gradient(180deg, var(--card), #fff);
  border-radius:var(--radius);
  padding:14px;
  box-shadow:var(--shadow);
  overflow:hidden;
  display:flex;
  flex-direction:column;
  gap:12px;
  transition:transform .22s ease, box-shadow .22s ease;
  border:1px solid rgba(15,23,42,0.04);
}
.card:hover{ transform:translateY(-6px); box-shadow:0 20px 40px rgba(2,6,23,0.10); }
.card-media{
  height:140px;
  border-radius:10px;
  background:linear-gradient(135deg, rgba(99,102,241,0.08), rgba(6,182,212,0.06));
  display:flex;align-items:center;justify-content:center;overflow:hidden;
}
.card-media img{ width:100%; height:100%; object-fit:cover; display:block; }
.badge{
  display:inline-block;padding:6px 8px;border-radius:999px;font-weight:700;font-size:0.78rem;
  color:#fff;background:linear-gradient(90deg,var(--accent1),var(--accent2));
}
.meta{display:flex;justify-content:space-between;gap:8px;align-items:center;font-weight:700;color:var(--muted);font-size:0.92rem}
.title{font-weight:800;color:#0f172a;font-size:1rem;line-height:1.1}
.desc{color:var(--muted);font-size:0.92rem;min-height:42px;overflow:hidden}
.price{font-weight:900;color:#0f172a}
.footer{display:flex;justify-content:space-between;align-items:center;gap:8px}
.owner{font-size:0.86rem;color:var(--muted);font-weight:700}
.empty{padding:28px;text-align:center;color:var(--muted);background:#fff;border-radius:10px}
@media (max-width:640px){ .card-media{height:160px} }
</style>

<div class="container">
  <div class="header-hero">
    <h1>Muebles</h1>
    <div style="display:flex;gap:8px;align-items:center">
      <a href="{{ url('/muebles/create') }}" style="background:linear-gradient(90deg,var(--accent1),var(--accent2));color:#fff;padding:8px 12px;border-radius:10px;text-decoration:none;font-weight:800">Nuevo mueble</a>
    </div>
  </div>

  @if($muebles->isEmpty())
    <div class="empty">No hay muebles registrados aún.</div>
  @else
    <div class="grid" role="list">
      @foreach($muebles as $m)
        <article class="card" role="listitem" aria-labelledby="mueble-{{ $m->id }}">
          <div class="card-media" aria-hidden="true">
            @if($m->ruta_img)
              <img src="{{ asset($m->ruta_img) }}" alt="Imagen mueble {{ $m->codigo ?? $m->id }}">
            @else
              <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--muted);gap:8px;padding:12px;">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h18M5 7v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div style="font-weight:700">Sin imagen</div>
              </div>
            @endif
          </div>

          <div>
            <div class="meta">
              <div class="title" id="mueble-{{ $m->id }}">{{ $m->codigo ?? 'ID '.$m->id }}</div>
              <div class="badge">{{ ucfirst(str_replace('_',' ', $m->estado ?? 'desconocido')) }}</div>
            </div>

            <div class="desc" style="margin-top:8px;">
              {{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 120) }}
            </div>
          </div>

          <div class="footer">
            <div>
              <div class="price">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</div>
              <div class="owner">{{ $m->usuario->nombre ?? '-' }} {{ $m->usuario->apellido ?? '' }}</div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;">
              <a href="{{ url('/muebles/'.$m->id.'/edit') }}" style="background:#06b6d4;color:#fff;padding:8px 10px;border-radius:8px;text-decoration:none;text-align:center;font-weight:800">Editar</a>
              <form action="{{ url('/muebles/'.$m->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:8px 10px;border-radius:8px;border:0;cursor:pointer;font-weight:800">Eliminar</button>
              </form>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif
</div>
@endsection
</body>
</html>