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
      <button type="button" id="btn-apply-colors" class="btn-ghost" title="Aplicar colores seleccionados a todos los iconos">Aplicar colores a todos</button>
      <a href="{{ route('configuracion.index') }}" class="btn-ghost">Cancelar</a>
      <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
  </form>
  <form id="apply-colors-form" action="{{ route('configuracion.applyColors') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="normal_color" id="apply_normal_color" value="">
    <input type="hidden" name="hover_color" id="apply_hover_color" value="">
  </form>
</div>
@endsection

{{-- Modal + styles --}}
<style>
.btn-primary {
  background: linear-gradient(90deg,#06b6d4,#6366f1);
  color:#fff;
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:700;
  cursor:pointer;
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  box-shadow:0 8px 20px rgba(2,6,23,0.06);
}
.btn-primary:hover{ transform: translateY(-3px); opacity:0.98; }
.btn-ghost {
  background: transparent;
  color: #374151;
  padding:8px 12px;
  border-radius:8px;
  border:1px solid #e5e7eb;
  font-weight:700;
  cursor:pointer;
}
.btn-danger {
  background: linear-gradient(90deg,#ef4444,#d94660);
  color:#fff;
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:700;
  cursor:pointer;
}
#apply-confirm-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background: rgba(0,0,0,0.4); }
#apply-confirm-modal .modal-card { background:#fff; padding:16px; border-radius:10px; max-width:520px; width:92%; box-shadow:0 12px 36px rgba(2,6,23,0.18); }
.color-sample { width:44px; height:44px; border-radius:8px; box-shadow:0 6px 18px rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.04); display:inline-block; vertical-align:middle; margin-right:8px; }
.modal-actions{ display:flex; gap:8px; justify-content:flex-end; margin-top:12px; }
</style>
<div id="apply-confirm-modal" aria-hidden="true">
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="apply-confirm-title">
    <h4 id="apply-confirm-title" style="margin:0 0 8px 0">Aplicar colores a todas las configuraciones</h4>
    <p style="margin:0 0 12px 0;color:#374151">Vas a sobrescribir los colores de todos los iconos. Confirma para continuar.</p>
    <div style="display:flex;align-items:center;gap:12px;">
      <div>
        <div style="font-size:.85rem;color:#6b7280">Color normal</div>
        <div id="preview-normal" class="color-sample"></div>
      </div>
      <div>
        <div style="font-size:.85rem;color:#6b7280">Color hover</div>
        <div id="preview-hover" class="color-sample"></div>
      </div>
    </div>
    <div class="modal-actions">
      <button type="button" id="apply-cancel" class="btn-ghost">Cancelar</button>
      <button type="button" id="apply-confirm" class="btn-primary">Aplicar colores</button>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btnApply = document.getElementById('btn-apply-colors');
  if (!btnApply) return;
  const normalInput = document.querySelector('input[name="normal_color"]');
  const hoverInput = document.querySelector('input[name="hover_color"]');
  const modal = document.getElementById('apply-confirm-modal');
  const previewNormal = document.getElementById('preview-normal');
  const previewHover = document.getElementById('preview-hover');
  const btnCancel = document.getElementById('apply-cancel');
  const btnConfirm = document.getElementById('apply-confirm');
  const applyForm = document.getElementById('apply-colors-form');
  const applyNormal = document.getElementById('apply_normal_color');
  const applyHover = document.getElementById('apply_hover_color');
  const csrfToken = '{{ csrf_token() }}';

  function openModal() {
    previewNormal.style.background = normalInput?.value || '#f59e0b';
    previewHover.style.background = hoverInput?.value || (normalInput?.value || '#f59e0b');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden','false');
  }
  function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
  }

  btnApply.addEventListener('click', function(e){
    e.preventDefault();
    if (!normalInput || !normalInput.value) { alert('Selecciona un color normal'); return; }
    openModal();
  });
  btnCancel.addEventListener('click', closeModal);

  btnConfirm.addEventListener('click', function(){
    const normal = normalInput?.value?.trim() || '';
    const hover = hoverInput?.value?.trim() || normal;
    fetch('{{ route("configuracion.applyColors") }}', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ normal_color: normal, hover_color: hover })
    }).then(async r => {
      if (!r.ok) {
        if (applyForm && applyNormal && applyHover) {
          applyNormal.value = normal;
          applyHover.value = hover;
          HTMLFormElement.prototype.submit.call(applyForm);
        } else {
          alert('Error al aplicar colores');
        }
        return;
      }
      const json = await r.json().catch(()=>({}));
      closeModal();
      window.location.href = '{{ route("configuracion.index") }}';
    }).catch(err=>{
      if (applyForm && applyNormal && applyHover) {
        applyNormal.value = normal;
        applyHover.value = hover;
        HTMLFormElement.prototype.submit.call(applyForm);
      } else {
        console.error(err);
        alert('No se pudo aplicar colores. Revisa la consola.');
      }
    });
  });
});
</script>