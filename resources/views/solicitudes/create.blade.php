@extends('layouts.app')

@section('title','Crear solicitud | Inventario Muebles')

@section('content')
<div style="max-width:900px;margin:18px auto;padding:12px;">
  <h1>Crear solicitud</h1>

  <div style="background:#fff;padding:12px;border-radius:10px;box-shadow:0 12px 34px rgba(2,6,23,0.06);">
    <form id="create-sol-form" method="POST" action="{{ url('/solicitudes') }}">
      @csrf
      <input type="hidden" name="mueble_id" value="{{ $mueble->id ?? '' }}">
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:300px;">
          <label>Mueble</label>
          <div style="display:flex;gap:12px;align-items:center;">
            <div style="width:120px;height:90px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:8px;overflow:hidden;background:#f3f4f6;">
              @if(!empty($mueble->ruta_img))
                <img
                  src="{{ asset($mueble->ruta_img) }}"
                  alt="{{ $mueble->codigo ?? 'imagen mueble' }}"
                  style="display:block;max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;border-radius:6px;"
                />
              @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-weight:700;">Sin imagen</div>
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

  <style>
.solicitud-preview-wrapper{
  display:flex;
  align-items:center;
  justify-content:center;
  background:#f8fafc;
  border-radius:8px;
  padding:8px;
  width:100%;
  max-width:640px;
  max-height: min(70vh, 600px);
  margin:0 auto;
  overflow:hidden;
}
.solicitud-preview-wrapper img{
  display:block;
  width:auto;
  height:auto;
  max-width:100%;
  max-height:100%;
  object-fit:contain;
  border-radius:6px;
  box-shadow:0 8px 22px rgba(2,6,23,0.06);
  transition:opacity .18s ease, transform .18s ease;
}
</style>

<script>
function setSolicitudPreview(ruta) {
  const img = document.getElementById('solicitud-preview');
  if (!img) return;
  if (!ruta) {img.src = "{{ url('/imgs/default.webp') }}";return;}
  img.style.opacity = '0';
  img.src = "{{ url('/') }}/" + ruta.replace(/^\/+/, '');
  img.onload = () => {img.style.opacity = '1';};
  img.onerror = () => {
    img.src = "{{ url('/imgs/default.webp') }}";
    img.style.opacity = '1';
    };
}

document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('create-sol-form');
  if (!form) return;
  function todayStr(offsetDays = 0){
    const d = new Date();
    d.setDate(d.getDate() + offsetDays);
    return d.toISOString().slice(0,10);
  }

  /* helper: tiny floating notification */
  function showToast(msg, type = 'info', timeout = 2500) {
    let container = document.getElementById('float-notifs');
    if (!container) {
      container = document.createElement('div');
      container.id = 'float-notifs';
      container.style.position = 'fixed';
      container.style.top = '84px';
      container.style.right = '20px';
      container.style.zIndex = '160';
      container.style.display = 'flex';
      container.style.flexDirection = 'column';
      container.style.gap = '8px';
      document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.textContent = msg;
    el.style.padding = '10px 14px';
    el.style.borderRadius = '10px';
    el.style.color = '#fff';
    el.style.fontWeight = '700';
    el.style.boxShadow = '0 8px 24px rgba(2,6,23,0.08)';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-6px)';
    if (type === 'success') el.style.background = 'linear-gradient(90deg,#10b981,#059669)';
    else if (type === 'error') el.style.background = 'linear-gradient(90deg,#ef4444,#b91c1c)';
    else el.style.background = 'linear-gradient(90deg,#6366f1,#06b6d4)';
    container.appendChild(el);
    requestAnimationFrame(()=> { el.style.transition = 'transform .28s, opacity .28s'; el.style.opacity = '1'; el.style.transform = 'none'; });
    setTimeout(()=> {
      el.style.opacity = '0';
      el.style.transform = 'translateY(-6px)';
      el.addEventListener('transitionend', ()=> el.remove(), { once: true });
    }, timeout);
  }

  form.addEventListener('submit', async function(evt){
    evt.preventDefault();

    const inicio = form.querySelector('input[name="fecha_inicio"]');
    const fin = form.querySelector('input[name="fecha_fin"]');
    if (inicio && !inicio.value) inicio.value = todayStr(0);
    if (fin && !fin.value) fin.value = todayStr(1);

    const muebleId = form.querySelector('input[name="mueble_id"]')?.value;
    const persona = form.querySelector('input[name="persona_id"], select[name="persona_id"]')?.value;
    if (!muebleId) { alert('Selecciona primero un mueble para la solicitud.'); return; }
    if (!persona) { alert('Selecciona solicitante.'); return; }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.textContent : null;
    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Enviando...'; }

    try {
      const fd = new FormData(form);
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const resp = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: fd,
        credentials: 'same-origin'
      });

      const ct = resp.headers.get('content-type') || '';
      const data = ct.includes('application/json') ? await resp.json() : await resp.text();

      if (resp.ok) {
        showToast('Solicitud creada. Notificación generada.', 'success', 1400);
        setTimeout(()=> window.location.href = "{{ url('/solicitudes') }}", 900);
        return;
      }

      if (resp.status === 422 && data && data.errors) {
        showToast(Object.values(data.errors).flat().join('; '), 'error', 6000);
      } else {
        showToast((data && data.message) ? data.message : 'Error al crear solicitud', 'error', 4000);
      }
    } catch (err) {
      console.error(err);
      showToast('Error de red. Intenta de nuevo.', 'error', 3500);
    } finally {
      if (submitBtn) { submitBtn.disabled = false; if (originalText) submitBtn.textContent = originalText; }
    }
  });
});
</script>
<style>
#float-notifs { pointer-events:none; }
#float-notifs .notif { pointer-events:auto; }
</style>
@endsection