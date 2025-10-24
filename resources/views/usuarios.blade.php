<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Usuarios | Inventario Muebles')

@section('content')
@php
    $current = null;
    if (session()->has('usuario_id')) {
        $current = \App\Models\Usuario::find(session('usuario_id'));
    }
    $isAdmin = $current && ($current->rol === 'admin');
@endphp

<style>
#notifications { position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:8px; }
#notifications .notif { min-width:220px; max-width:420px; padding:10px 14px; border-radius:10px; color:#fff; font-weight:700; transform:translateY(-6px); opacity:0; transition:transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease; box-shadow:0 8px 24px rgba(2,6,23,0.08); }
#notifications .notif.visible { transform:none; opacity:1; }
#notifications .notif-success { background: linear-gradient(90deg,#10b981,#059669); }
#notifications .notif-error { background: linear-gradient(90deg,#ef4444,#b91c1c); }
#notifications .notif-info { background: linear-gradient(90deg,#6366f1,#06b6d4); }
.modal-card { transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease; transform-origin: top center; opacity:1; max-height:1200px; overflow:hidden; }
.modal-card.closing { transform: scaleY(0.86) translateY(-6px); opacity:0; padding-top:2px; padding-bottom:2px; max-height:0; overflow:hidden; }
.modal-card.collapsed { transform: scaleY(0.98); opacity:0; max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; }
#users-table {
    transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease;
    transform-origin: top center;
    opacity: 1;
    max-height: 2000px;
    overflow: hidden;
}
#users-table.closing {
    transform: scaleY(0.96) translateY(-6px);
    opacity: 0;
    padding-top: 2px;
    padding-bottom: 2px;
    max-height: 0;
    pointer-events: none;
}
#users-table.collapsed {
    transform: scaleY(0.98);
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    overflow: hidden;
}

.btn-edit{
  background: linear-gradient(90deg,#6366f1,#06b6d4);
  color: #fff;
  padding: 8px 10px;
  border-radius: 8px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 28px rgba(99,102,241,0.10);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
}
.btn-edit:hover{ transform: translateY(-3px); }
.btn-edit:active{ transform: translateY(-1px); }
.btn-edit:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }
</style>

<div style="padding:16px;max-width:1100px;margin:0 auto;">
    <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Usuarios</h1>
            @if($current)
                <div style="font-weight:700;color:#111;">Conectado: {{ $current->nombre }} {{ $current->apellido }}</div>
                <div style="font-size:0.9rem;color:#6b7280;">{{ $current->email }}</div>
            @endif
        </div>

        <div style="display:flex;gap:10px;align-items:center;">
            @if($isAdmin)
                <button id="btn-new" class="btn" type="button" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">
                    Nuevo usuario
                </button>
            @endif
        </div>
    </header>

    <form id="users-filters" method="GET" action="{{ url('/usuarios') }}" autocomplete="off" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;align-items:end;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <div>
                <label style="display:block;font-weight:600;font-size:0.9rem;">Nombre</label>
                <input name="nombre" type="search" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" value="{{ request('nombre') }}" placeholder="buscar nombre" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
            <div>
                <label style="display:block;font-weight:600;font-size:0.9rem;">Apellido</label>
                <input name="apellido" type="search" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" value="{{ request('apellido') }}" placeholder="buscar apellido" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
            <div>
                <label style="display:block;font-weight:600;font-size:0.9rem;">Email</label>
                <input name="email" type="search" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" value="{{ request('email') }}" placeholder="buscar email" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
            <div>
                <label style="display:block;font-weight:600;font-size:0.9rem;">Rol</label>
                <select name="rol" autocomplete="off" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="">Todos</option>
                    <option value="admin" {{ request('rol')=='admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="empleado" {{ request('rol')=='empleado' ? 'selected' : '' }}>Empleado</option>
                    <option value="tecnico" {{ request('rol')=='tecnico' ? 'selected' : '' }}>Técnico</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-weight:600;font-size:0.9rem;">Área</label>
                <select name="area_id" autocomplete="off" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="">Todas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:flex;gap:8px;">
            <button type="submit" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Buscar</button>
            <button type="button" id="btn-clear-filters" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Limpiar</button>
        </div>
    </form>

    <div id="notifications" aria-live="polite"></div>

    @if(session('success'))
        <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    <div id="user-form-card" class="modal-card collapsed" style="display:none;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
        <h2 id="form-title" style="margin:0 0 8px 0;font-size:1.05rem;">Nuevo usuario</h2>
        <form id="user-form" method="POST" action="{{ url('/usuarios') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="id" id="user-id" value="">

            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                <div style="flex:1;min-width:180px;">
                    <label>Nombre</label>
                    <input name="nombre" id="f-nombre" type="text" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                </div>

                <div style="flex:1;min-width:180px;">
                    <label>Apellido</label>
                    <input name="apellido" id="f-apellido" type="text" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                </div>

                <div style="flex:1 1 280px;min-width:200px;">
                    <label>Correo electrónico</label>
                    <input name="email" id="f-email" type="email" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                </div>

                <div style="flex:1 1 160px;min-width:140px;">
                    <label>Rol</label>
                    <select name="rol" id="f-rol" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                        <option value="">Selecciona</option>
                        <option value="admin">Administrador</option>
                        <option value="empleado">Empleado</option>
                        <option value="tecnico">Técnico</option>
                    </select>
                </div>

                <div style="flex:1 1 160px;min-width:140px;">
                    <label>Área</label>
                    <select name="area_id" id="f-area" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                        <option value="">Selecciona</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1 1 220px;min-width:180px;">
                    <label>
                        Contraseña
                        <small id="pwd-note" style="color:#6b7280;font-weight:600;display:inline-block;margin-left:6px;">(Obligatoria al crear)</small>
                    </label>

                    <div id="pwd-edit-row" style="display:none;align-items:center;gap:8px;margin-bottom:6px;">
                        <input type="checkbox" id="chk-change-password" />
                        <label for="chk-change-password" style="margin:0;font-weight:600;color:#374151;">Cambiar contraseña</label>
                    </div>

                    <input name="password" id="f-password" type="password" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="submit" id="btn-save" class="btn" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Guardar</button>
                <button id="btn-cancel" type="button" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
            </div>
        </form>
    </div>

    <div id="users-table" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
        <table style="width:100%;border-collapse:collapse;min-width:720px;">
            <thead>
                <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                    @if($isAdmin)
                        <th style="padding:10px 12px;">ID</th>
                    @endif
                    <th style="padding:10px 12px;">Nombre</th>
                    <th style="padding:10px 12px;">Apellido</th>
                    <th style="padding:10px 12px;">Email</th>
                    <th style="padding:10px 12px;">Rol</th>
                    <th style="padding:10px 12px;">Área</th>
                    @if($isAdmin)
                        <th style="padding:10px 12px;width:190px;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        @if($isAdmin)
                            <td style="padding:10px 12px;">{{ $u->id }}</td>
                        @endif
                        <td style="padding:10px 12px;">{{ $u->nombre }}</td>
                        <td style="padding:10px 12px;">{{ $u->apellido }}</td>
                        <td style="padding:10px 12px;">{{ $u->email }}</td>
                        <td style="padding:10px 12px;">{{ $u->rol }}</td>
                        <td style="padding:10px 12px;">{{ optional($u->area)->nombre }}</td>
                        @if($isAdmin)
                            <td style="padding:10px 12px;width:190px;">
                                <button type="button" class="btn-edit" data-user='@json($u)' style="margin-right:6px;">Editar</button>
                                <form action="{{ url('/usuarios/'.$u->id) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="¿Eliminar usuario {{ addslashes($u->nombre . ' ' . $u->apellido) }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $isAdmin ? 7 : 5 }}">No hay usuarios.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const card = document.getElementById('user-form-card');
    const usersTable = document.getElementById('users-table');
    const filters = document.getElementById('users-filters');
    const btnNew = document.getElementById('btn-new');
    const btnCancel = document.getElementById('btn-cancel');
    const form = document.getElementById('user-form');
    const methodInput = document.getElementById('form-method');
    const idInput = document.getElementById('user-id');
    const title = document.getElementById('form-title');
    const pwdNote = document.getElementById('pwd-note');
    const pwdEditRow = document.getElementById('pwd-edit-row');
    const chkChangePassword = document.getElementById('chk-change-password');
    const pwdInput = document.getElementById('f-password');
    const notifications = document.getElementById('notifications');
    function hideTable() {
        if (!usersTable) return;
        if (usersTable.classList.contains('closing') || getComputedStyle(usersTable).display === 'none') return;
        usersTable.classList.add('closing');
        const onEnd = function() {
            usersTable.style.display = 'none';
            usersTable.classList.remove('closing');
            usersTable.classList.add('collapsed');
            usersTable.removeEventListener('transitionend', onEnd);
        };
        usersTable.addEventListener('transitionend', onEnd);
    }
    function showTable() {
        if (!usersTable) return;
        if (getComputedStyle(usersTable).display !== 'none') {
            usersTable.classList.remove('collapsed');
            if (filters) filters.style.display = 'flex';
            return;
        }
        usersTable.style.display = 'block';
        usersTable.classList.add('collapsed');
        requestAnimationFrame(()=> {
            usersTable.classList.remove('collapsed');
            if (filters) filters.style.display = 'flex';
        });
    }
    function showNotification(message, type = 'info', timeout = 3500) {
        if (!notifications) return;
        const el = document.createElement('div');
        el.className = 'notif notif-'+type;
        el.innerText = message;
        notifications.appendChild(el);
        requestAnimationFrame(()=> el.classList.add('visible'));
        setTimeout(()=> {
            el.classList.remove('visible');
            el.addEventListener('transitionend', ()=> el.remove(), { once: true });
        }, timeout);
    }

    function closeModalAnimated() {
        if (!card) return;
        card.classList.add('closing');
        card.addEventListener('transitionend', function handler() {
            card.style.display = 'none';
            card.classList.remove('closing');
            showTable();
            card.removeEventListener('transitionend', handler);
        });
    }

    function openCreate() {
        title.textContent = 'Nuevo usuario';
        form.action = "{{ url('/usuarios') }}";
        methodInput.value = 'POST';
        idInput.value = '';
        form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(i=> i.value = '');
        form.querySelectorAll('select').forEach(s=> s.selectedIndex = 0);
        pwdNote.textContent = '(Obligatoria al crear)';
        pwdNote.style.display = 'inline-block';
        pwdEditRow.style.display = 'none';
        pwdInput.disabled = false;
        pwdInput.required = true;
        if (chkChangePassword) chkChangePassword.checked = false;

        card.style.display = 'block';
        card.classList.add('collapsed');
        hideTable();
        if (filters) filters.style.display = 'none';
        requestAnimationFrame(()=> {
            card.classList.remove('collapsed');
            card.scrollIntoView({behavior:'smooth', block:'center'});
        });
    }

    function openEdit(user) {
        title.textContent = 'Editar usuario — ID '+user.id;
        form.action = "{{ url('/usuarios') }}/" + user.id;
        methodInput.value = 'PUT';
        idInput.value = user.id;
        document.getElementById('f-nombre').value = user.nombre || '';
        document.getElementById('f-apellido').value = user.apellido || '';
        document.getElementById('f-email').value = user.email || '';
        document.getElementById('f-rol').value = user.rol || '';
        document.getElementById('f-area').value = user.area_id || '';
        pwdNote.style.display = 'none';
        pwdEditRow.style.display = 'flex';
        if (chkChangePassword) chkChangePassword.checked = false;
        pwdInput.value = '';
        pwdInput.disabled = true;
        pwdInput.required = false;

        card.style.display = 'block';
        card.classList.add('collapsed');
        hideTable();
        if (filters) filters.style.display = 'none';
        requestAnimationFrame(()=> {
            card.classList.remove('collapsed');
            card.scrollIntoView({behavior:'smooth', block:'center'});
        });
    }

    if (chkChangePassword) {
        chkChangePassword.addEventListener('change', function(){
            if (this.checked) {
                pwdInput.disabled = false;
                pwdInput.required = true;
            } else {
                pwdInput.disabled = true;
                pwdInput.required = false;
                pwdInput.value = '';
            }
        });
    }

    if (btnNew) btnNew.addEventListener('click', openCreate);
    if (btnCancel) btnCancel.addEventListener('click', function(){
        closeModalAnimated();
    });

    document.querySelectorAll('.btn-edit').forEach(btn=>{
        btn.addEventListener('click', function(){
            try {
                const user = JSON.parse(this.getAttribute('data-user'));
                openEdit(user);
            } catch(e) {
                console.error('invalid user json', e);
                showNotification('Error interno: datos de usuario inválidos', 'error');
            }
        });
    });

    if (form) {
        form.addEventListener('submit', async function(evt) {
            evt.preventDefault();
            const submitBtn = document.getElementById('btn-save') || form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : null;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Guardando...';
            }

            const fd = new FormData(form);

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                    credentials: 'same-origin'
                });

                const contentType = resp.headers.get('content-type') || '';
                let data = null;
                if (contentType.includes('application/json')) data = await resp.json();
                else data = await resp.text();

                if (resp.ok) {
                    showNotification('Operación realizada correctamente.', 'success', 1400);
                    closeModalAnimated();
                    setTimeout(()=> window.location.href = "{{ url('/usuarios') }}", 700);
                    return;
                }

                if (resp.status === 422 && data && data.errors) {
                    const messages = Object.values(data.errors).flat().join('\n');
                    showNotification('Errores de validación: ' + messages, 'error', 5000);
                } else {
                    const msg = (data && data.message) ? data.message : 'Error al guardar el usuario.';
                    showNotification(msg, 'error', 4000);
                }
            } catch (err) {
                console.error(err);
                showNotification('Error de red o del servidor. Revisa la consola.', 'error', 4000);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (originalText) submitBtn.textContent = originalText;
                }
            }
        });
    }

    const btnClear = document.getElementById('btn-clear-filters');
    if (btnClear) {
        btnClear.addEventListener('click', function(){
            const form = document.getElementById('users-filters');
            if (!form) return;
            form.querySelectorAll('input,select').forEach(i=>{
                if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
                else i.value = '';
            });
            form.submit();
        });
    }
});
</script>
@endsection