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
@php $areas = $areas ?? \App\Models\Area::all(); @endphp

<div style="padding:16px;max-width:1100px;margin:0 auto;">
    <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Usuarios</h1>
            @if(auth()->check())
                <div style="font-weight:700;color:#111;">Conectado: {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                <div style="font-size:0.9rem;color:#6b7280;">{{ auth()->user()->email }}</div>
            @endif
        </div>

        <div style="display:flex;gap:10px;align-items:center;">
            <button id="btn-new" class="btn" type="button" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">
                Nuevo usuario
            </button>
        </div>
    </header>

    @if(session('success'))
        <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    <div id="user-form-card" style="display:none;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
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
                    <th style="padding:10px 12px;">ID</th>
                    <th style="padding:10px 12px;">Nombre</th>
                    <th style="padding:10px 12px;">Apellido</th>
                    <th style="padding:10px 12px;">Email</th>
                    <th style="padding:10px 12px;">Rol</th>
                    <th style="padding:10px 12px;">Área</th>
                    <th style="padding:10px 12px;width:190px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->id }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->nombre }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->apellido }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->email }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->rol }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $u->area->nombre ?? '-' }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <button type="button"
                                        class="btn-edit"
                                        data-user='@json($u)'
                                        style="background:#06b6d4;color:#fff;padding:6px 10px;border-radius:8px;border:0;cursor:pointer;font-size:0.9rem;">
                                    Editar
                                </button>

                                <form action="{{ url('/usuarios/'.$u->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar usuario {{ addslashes($u->nombre.' '.$u->apellido) }}?');"
                                            style="background:#ef4444;color:#fff;padding:6px 10px;border-radius:8px;border:none;cursor:pointer;font-size:0.9rem;">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:14px 12px;text-align:center;color:#6b7280;">No hay usuarios registrados.</td>
                    </tr>
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

    function hideTable() { if (usersTable) usersTable.style.display = 'none'; }
    function showTable() { if (usersTable) usersTable.style.display = 'block'; }

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
        chkChangePassword.checked = false;

        card.style.display = 'block';
        hideTable();
        card.scrollIntoView({behavior:'smooth', block:'center'});
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
        chkChangePassword.checked = false;
        pwdInput.value = '';
        pwdInput.disabled = true;
        pwdInput.required = false;

        card.style.display = 'block';
        hideTable();
        card.scrollIntoView({behavior:'smooth', block:'center'});
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
        card.style.display = 'none';
        showTable();
    });

    document.querySelectorAll('.btn-edit').forEach(btn=>{
        btn.addEventListener('click', function(){
            try {
                const user = JSON.parse(this.getAttribute('data-user'));
                openEdit(user);
            } catch(e) {
                console.error('invalid user json', e);
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
                    alert('Operación realizada correctamente.');
                    window.location.href = "{{ url('/usuarios') }}";
                    return;
                }

                if (resp.status === 422 && data && data.errors) {
                    const messages = Object.values(data.errors).flat().join('\\n');
                    alert('Errores de validación:\\n' + messages);
                } else {
                    const msg = (data && data.message) ? data.message : 'Error al guardar el usuario.';
                    alert(msg);
                }
            } catch (err) {
                console.error(err);
                alert('Error de red o del servidor. Revisa la consola.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (originalText) submitBtn.textContent = originalText;
                }
            }
        });
    }
});
</script>
@endsection
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');});
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');}
        });
    });
    </script>
</body>
</html>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
.dashboard-container {
    display: flex;
}
.sidebar {
    width: 250px;
    background-color: #333;
    color: #fff;
    height: 100vh;
    padding: 20px;
}
.sidebar h2 {
    color: #fff;
    text-align: center;
}
.sidebar ul {
    list-style: none;
    padding: 0;
}
.sidebar ul li {
    margin: 15px 0;
}
.sidebar ul li a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
}
.sidebar ul li a:hover {
    background-color: #575757;
    padding: 10px;
    border-radius: 5px;
}
.sidebar ul li a .icon {
    margin-right: 10px;
}
.content {
    flex: 1;
    padding: 20px;
}
.header {
    background-color: #fff;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.header h1 {
    margin: 0;
}
@media (max-width: 768px) {
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
.dashboard-container {
    display: flex;
}
.sidebar {
    width: 250px;
    background-color: #333;
    color: #fff;
    height: 100vh;
    padding: 20px;
}
.sidebar h2 {
    color: #fff;
    text-align: center;
}
.sidebar ul {
    list-style: none;
    padding: 0;
}
.sidebar ul li {
    margin: 15px 0;
}
.sidebar ul li a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
}
.sidebar ul li a:hover {
    background-color: #575757;
    padding: 10px;
    border-radius: 5px;
}
.sidebar ul li a .icon {
    margin-right: 10px;
}
.content {
    flex: 1;
    padding: 20px;
}
.header {
    background-color: #fff;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.header h1 {
    margin: 0;
}
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
    }
    .dashboard-container {
        flex-direction: column;
    }
}
</style>