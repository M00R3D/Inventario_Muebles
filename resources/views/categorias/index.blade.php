<!doctype html>
@extends('layouts.app')
@section('title','Categorías')
@section('content')
<style>
.uploader { border:2px dashed #e5e7eb; border-radius:10px; padding:18px; display:flex; flex-direction:column; gap:10px; align-items:center; text-align:center; background:#fff; }
.uploader.dragover { background:#f0f9ff; border-color:#06b6d4; }
.cat-actions .cat-edit,
.cat-actions .cat-delete {
  padding:8px 10px;
  border-radius:8px;
  border:0;
  font-weight:700;
  cursor:pointer;
  color:#fff;
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  box-shadow:0 8px 20px rgba(2,6,23,0.06);
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:8px;
}
.cat-actions .cat-edit {background: linear-gradient(90deg,#6366f1,#06b6d4);}
.cat-actions .cat-edit:hover{ transform: translateY(-3px); opacity:0.98; }
.cat-actions .cat-delete {background: linear-gradient(90deg,#ef4444,#d94660);}
.cat-actions .cat-delete:hover{ transform: translateY(-3px); opacity:0.98; }
.table-action-link{ color:inherit; text-decoration:none; display:inline-block; }
.inline-form { display:inline-block; margin:0; padding:0; }
.btn-primary{ background: linear-gradient(90deg,#06b6d4,#2563eb); color:#fff; padding:8px 12px; border-radius:8px; border:0; font-weight:700; }
.btn-ghost{ background:transparent; border:1px solid #e5e7eb; padding:8px 12px; border-radius:8px; color:#374151; text-decoration:none; display:inline-block; }
.btn-danger{ background: linear-gradient(90deg,#ef4444,#d94660); color:#fff; padding:8px 12px; border-radius:8px; border:0; font-weight:700; }
</style>

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
                    <td style="padding:8px;vertical-align:middle" class="cat-actions">
                        <a href="{{ route('categorias.edit', $c->id) }}" class="btn-ghost" style="margin-right:8px;">Editar</a>
                        <form action="{{ route('categorias.destroy', $c->id) }}" method="POST" class="inline-form" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-danger" data-confirm="¿Eliminar categoría {{ $c->nombre }}?" >Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:12px;color:#6b7280">No hay categorías.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="confirm-modal-global" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);">
  <div style="background:#fff;padding:16px;border-radius:10px;max-width:420px;width:92%;box-shadow:0 12px 36px rgba(2,6,23,0.18);">
    <div id="confirm-modal-global-msg" style="font-weight:700;margin-bottom:12px;">¿Confirmar acción?</div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button type="button" id="confirm-modal-global-cancel" class="btn-ghost">Cancelar</button>
      <button type="button" id="confirm-modal-global-ok" class="btn-primary">Confirmar</button>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('confirm-modal-global');
    const msg = document.getElementById('confirm-modal-global-msg');
    const btnOk = document.getElementById('confirm-modal-global-ok');
    const btnCancel = document.getElementById('confirm-modal-global-cancel');
    let pendingAction = null;

    function openConfirm(message, action){
        pendingAction = action;
        msg.textContent = message || '¿Confirmar?';
        modal.style.display = 'flex';
        btnOk.focus();
    }
    function closeConfirm(){
        modal.style.display = 'none';
        pendingAction = null;
    }
    btnCancel.addEventListener('click', closeConfirm);
    btnOk.addEventListener('click', function(){
        if (typeof pendingAction === 'function') pendingAction();
        closeConfirm();
    });

    window.showConfirmFor = function(el){
        try {
            const confirmText = el.getAttribute('data-confirm') || '¿Confirmar acción?';
            let f = el.closest && el.closest('form');
            if (!f) {
                f = el.parentElement && el.parentElement.querySelector('form');
            }
            openConfirm(confirmText, function(){
                if (f) f.submit();
            });
        } catch (e) {
            console.error(e);
            alert('Error al confirmar acción');
        }
    };
});
</script>
@endsection