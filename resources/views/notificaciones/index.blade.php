<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notificaciones | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Notificaciones | Inventario Muebles')

@section('content')
@php
    $current = null;
    if (session()->has('usuario_id')) {$current = \App\Models\Usuario::find(session('usuario_id'));}
    $isAdmin = $current && ($current->rol === 'admin');
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $estadoOrder = ['cerrada' => 0, 'abierta' => 1, 'vista' => 2];
    $tmp = $notificaciones->sortByDesc('fecha_creacion');
    $notificaciones_sorted = $tmp->sortBy(function($n) use ($estadoOrder) {return $estadoOrder[$n->estado] ?? 99;})->values();
@endphp

<style>
.modal-card { transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease; transform-origin: top center; opacity:1; max-height:1200px; overflow:hidden; }
.modal-card.closing { transform: scaleY(0.86) translateY(-6px); opacity:0; padding-top:2px; padding-bottom:2px; max-height:0; overflow:hidden; }
.modal-card.collapsed { transform: scaleY(0.98); opacity:0; max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; }

#notifs-table {
    transition: transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease, max-height .28s ease, padding .28s ease;
    transform-origin: top center;
    opacity: 1;
    max-height: 2000px;
    overflow: hidden;
}
#notifs-table.closing {
    transform: scaleY(0.96) translateY(-6px);
    opacity: 0;
    padding-top: 2px;
    padding-bottom: 2px;
    max-height: 0;
    pointer-events: none;
}
#notifs-table.collapsed {
    transform: scaleY(0.98);
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    overflow: hidden;
}

#notifications { position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:8px; }
#notifications .notif { min-width:220px; max-width:420px; padding:10px 14px; border-radius:10px; color:#fff; font-weight:700; transform:translateY(-6px); opacity:0; transition:transform .28s cubic-bezier(.16,.84,.44,1), opacity .28s ease; box-shadow:0 8px 24px rgba(2,6,23,0.08); }
#notifications .notif.visible { transform:none; opacity:1; }
#notifications .notif-success { background: linear-gradient(90deg,#10b981,#059669); }
#notifications .notif-error { background: linear-gradient(90deg,#ef4444,#b91c1c); }
#notifications .notif-info { background: linear-gradient(90deg,#6366f1,#06b6d4); }
</style>

<div style="padding:16px;max-width:1100px;margin:0 auto;">
    <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Notificaciones</h1>
            @if($current)
                <div style="font-weight:700;color:#111;">Conectado: {{ $current->nombre }} {{ $current->apellido }}</div>
                <div style="font-size:0.9rem;color:#6b7280;">{{ $current->email }}</div>
            @endif
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            @if($isAdmin)
                <button id="btn-new" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Nueva notificación</button>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    @php
        $cerradas = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'cerrada')->values();
        $abiertas = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'abierta')->values();
        $vistas   = $notificaciones_sorted->filter(fn($x)=> ($x->estado ?? '') === 'vista')->values();
        $icons = ['prueba'=>'🧪','aprobada'=>'✅','rechazada'=>'❌','otra'=>'🔔'];
    @endphp

    {{-- Tabla: Cerradas --}}
    <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Cerradas</h3>
    <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
        <table style="width:100%;border-collapse:collapse;min-width:720px;">
            <thead>
                <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                    @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                    <th style="padding:10px 12px;">Tipo</th>
                    <th style="padding:10px 12px;">Descripción</th>
                    <th style="padding:10px 12px;">Fecha creación</th>
                    @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                    @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($cerradas as $n)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        @if($isAdmin)<td style="padding:10px 12px;">{{ $n->id }}</td>@endif
                        <td style="padding:10px 12px;">
                            <span style="display:inline-flex;gap:8px;align-items:center;">
                                <span aria-hidden="true">{{ $icons[$n->tipo] ?? '🔔' }}</span>
                                <span style="font-weight:700;text-transform:capitalize;">{{ $n->tipo }}</span>
                            </span>
                        </td>
                        <td style="padding:10px 12px;">{{ Str::limit($n->descripcion, 120) }}</td>
                        <td style="padding:10px 12px;">
                            @if(!empty($n->fecha_creacion))
                                {{ ucfirst(\Carbon\Carbon::parse($n->fecha_creacion)->locale('es')->isoFormat('dddd, D [de] MMMM YYYY, HH:mm')) }}
                            @else - @endif
                        </td>
                        @if($isAdmin)
                            <td style="padding:10px 12px;">{{ optional($n->usuario)->nombre ? optional($n->usuario)->nombre . ' ' . optional($n->usuario)->apellido : 'Todos' }}</td>
                            <td style="padding:10px 12px;width:190px;">
                                <button type="button" class="btn-edit" data-notif='@json($n)' style="background:linear-gradient(90deg,#6366f1,#06b6d4);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Editar</button>
                                <form action="{{ url('/notificaciones/'.$n->id) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="¿Eliminar notificación #{{ $n->id }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;cursor:pointer;" data-confirm-type="delete">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $isAdmin ? 6 : 4 }}" style="padding:12px;">No hay notificaciones cerradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tabla: Abiertas --}}
    <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Abiertas</h3>
    <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
        <table style="width:100%;border-collapse:collapse;min-width:720px;">
            <thead>
                <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                    @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                    <th style="padding:10px 12px;">Tipo</th>
                    <th style="padding:10px 12px;">Descripción</th>
                    <th style="padding:10px 12px;">Fecha creación</th>
                    @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                    @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($abiertas as $n)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        @if($isAdmin)<td style="padding:10px 12px;">{{ $n->id }}</td>@endif
                        <td style="padding:10px 12px;">
                            <span style="display:inline-flex;gap:8px;align-items:center;">
                                <span aria-hidden="true">{{ $icons[$n->tipo] ?? '🔔' }}</span>
                                <span style="font-weight:700;text-transform:capitalize;">{{ $n->tipo }}</span>
                            </span>
                        </td>
                        <td style="padding:10px 12px;">{{ Str::limit($n->descripcion, 120) }}</td>
                        <td style="padding:10px 12px;">
                            @if(!empty($n->fecha_creacion))
                                {{ ucfirst(\Carbon\Carbon::parse($n->fecha_creacion)->locale('es')->isoFormat('dddd, D [de] MMMM YYYY, HH:mm')) }}
                            @else - @endif
                        </td>
                        @if($isAdmin)
                            <td style="padding:10px 12px;">{{ optional($n->usuario)->nombre ? optional($n->usuario)->nombre . ' ' . optional($n->usuario)->apellido : 'Todos' }}</td>
                            <td style="padding:10px 12px;width:190px;">
                                <button type="button" class="btn-edit" data-notif='@json($n)' style="background:linear-gradient(90deg,#6366f1,#06b6d4);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Editar</button>
                                <form action="{{ url('/notificaciones/'.$n->id) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="¿Eliminar notificación #{{ $n->id }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;cursor:pointer;">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $isAdmin ? 6 : 4 }}" style="padding:12px;">No hay notificaciones abiertas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tabla: Vistas --}}
    <h3 style="margin-top:8px;margin-bottom:6px;color:#374151;">Vistas</h3>
    <div class="list-card" style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px;">
        <table style="width:100%;border-collapse:collapse;min-width:720px;">
            <thead>
                <tr style="text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;">
                    @if($isAdmin)<th style="padding:10px 12px;">ID</th>@endif
                    <th style="padding:10px 12px;">Tipo</th>
                    <th style="padding:10px 12px;">Descripción</th>
                    <th style="padding:10px 12px;">Fecha creación</th>
                    @if($isAdmin)<th style="padding:10px 12px;">Usuario</th>@endif
                    @if($isAdmin)<th style="padding:10px 12px;width:190px;">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($vistas as $n)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        @if($isAdmin)<td style="padding:10px 12px;">{{ $n->id }}</td>@endif
                        <td style="padding:10px 12px;">
                            <span style="display:inline-flex;gap:8px;align-items:center;">
                                <span aria-hidden="true">{{ $icons[$n->tipo] ?? '🔔' }}</span>
                                <span style="font-weight:700;text-transform:capitalize;">{{ $n->tipo }}</span>
                            </span>
                        </td>
                        <td style="padding:10px 12px;">{{ Str::limit($n->descripcion, 120) }}</td>
                        <td style="padding:10px 12px;">
                            @if(!empty($n->fecha_creacion))
                                {{ ucfirst(\Carbon\Carbon::parse($n->fecha_creacion)->locale('es')->isoFormat('dddd, D [de] MMMM YYYY, HH:mm')) }}
                            @else - @endif
                        </td>
                        @if($isAdmin)
                            <td style="padding:10px 12px;">{{ optional($n->usuario)->nombre ? optional($n->usuario)->nombre . ' ' . optional($n->usuario)->apellido : 'Todos' }}</td>
                            <td style="padding:10px 12px;width:190px;">
                                <button type="button" class="btn-edit" data-notif='@json($n)' style="background:linear-gradient(90deg,#6366f1,#06b6d4);color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;margin-right:6px;cursor:pointer;">Editar</button>
                                <form action="{{ url('/notificaciones/'.$n->id) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="¿Eliminar notificación #{{ $n->id }}?" style="background:linear-gradient(90deg,#ef4444,#f97316);color:#fff;padding:6px 8px;border-radius:8px;border:0;cursor:pointer;">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $isAdmin ? 6 : 4 }}" style="padding:12px;">No hay notificaciones vistas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- end split tables --}}
</div>

{{-- Modal / formulario para crear / editar --}}
<div id="notif-form-card" class="modal-card collapsed" style="display:none;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin:16px auto;max-width:1100px;">
    <h2 id="notif-form-title" style="margin:0 0 8px 0;font-size:1.05rem;">Nueva notificación</h2>
    <form id="notif-form" method="POST" action="{{ url('/notificaciones') }}">
        @csrf
        <input type="hidden" name="_method" id="notif-form-method" value="POST">
        <input type="hidden" name="id" id="notif-id" value="">

        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            <div style="flex:1 1 220px;min-width:180px;">
                <label>Para (usuario)</label>
                <select name="id_usuario" id="f-id_usuario" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div style="flex:1 1 180px;min-width:160px;">
                <label>Estado</label>
                <select name="estado" id="f-estado" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="cerrada">cerrada</option>
                    <option value="abierta">abierta</option>
                    <option value="vista">vista</option>
                </select>
            </div>

            <div style="flex:1 1 180px;min-width:160px;">
                <label>Tipo</label>
                <select name="tipo" id="f-tipo" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
                    <option value="prueba">prueba</option>
                    <option value="aprobada">aprobada</option>
                    <option value="rechazada">rechazada</option>
                    <option value="otra">otra</option>
                </select>
            </div>

            <div style="flex:1 1 260px;min-width:200px;">
                <label>Ruta (opcional)</label>
                <input name="ruta" id="f-ruta" type="text" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>

            <div style="flex:1 1 420px;min-width:220px;">
                <label>Descripción</label>
                <textarea name="descripcion" id="f-descripcion" rows="4" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>
            </div>

            <div id="f-fecha_visto_row" style="flex:1 1 220px;min-width:180px;display:none;">
                <label>Fecha visto</label>
                <input name="fecha_visto" id="f-fecha_visto" type="datetime-local" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:12px;">
            <button type="submit" id="notif-save" style="background:#06b6d4;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700;">Guardar</button>
            <button id="notif-cancel" type="button" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Cancelar</button>
        </div>
    </form>
</div>

<div id="notifications" aria-live="polite" style="position:fixed; top:84px; right:20px; z-index:140; display:flex; flex-direction:column; gap:8px;"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const card = document.getElementById('notif-form-card');
    const notifsTable = document.getElementById('notifs-table');
    const btnNew = document.getElementById('btn-new');
    const btnCancel = document.getElementById('notif-cancel');
    const form = document.getElementById('notif-form');
    const methodInput = document.getElementById('notif-form-method');
    const idInput = document.getElementById('notif-id');
    const title = document.getElementById('notif-form-title');
    const fechaVistoRow = document.getElementById('f-fecha_visto_row');
    const fechaVistoInput = document.getElementById('f-fecha_visto');
    const notifications = document.getElementById('notifications');
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
    function hideTable() {
        if (!notifsTable) return;
        if (notifsTable.classList.contains('closing') || getComputedStyle(notifsTable).display === 'none') return;
        notifsTable.classList.add('closing');
        const onEnd = function() {
            notifsTable.style.display = 'none';
            notifsTable.classList.remove('closing');
            notifsTable.classList.add('collapsed');
            notifsTable.removeEventListener('transitionend', onEnd);
        };
        notifsTable.addEventListener('transitionend', onEnd);
    }
    function showTable() {
        if (!notifsTable) return;
        if (getComputedStyle(notifsTable).display !== 'none') {
            notifsTable.classList.remove('collapsed');
            return;
        }
        notifsTable.style.display = 'block';
        notifsTable.classList.add('collapsed');
        requestAnimationFrame(()=> {
            notifsTable.classList.remove('collapsed');
        });
    }

    function closeModalAnimated() {
        if (!card) return;
        card.classList.add('closing');
        card.addEventListener('transitionend', function handler() {
            card.style.display = 'none';
            card.classList.remove('closing');
            if (form) {
                form.reset();
                methodInput.value = 'POST';
                idInput.value = '';
            }
            showTable();
            card.removeEventListener('transitionend', handler);
        });
    }

    function openCreate() {
        title.textContent = 'Nueva notificación';
        form.action = "{{ url('/notificaciones') }}";
        methodInput.value = 'POST';
        idInput.value = '';
        form.querySelectorAll('input, textarea, select').forEach(i => { if(i.tagName==='SELECT') i.selectedIndex = 0; else i.value = ''; });
        const estadoEl = document.getElementById('f-estado');
        if (estadoEl) estadoEl.value = 'cerrada';
        fechaVistoRow.style.display = 'none';
        if (fechaVistoInput) fechaVistoInput.value = '';
        card.style.display = 'block';
        card.classList.add('collapsed');
        hideTable();
        requestAnimationFrame(()=> {
            card.classList.remove('collapsed');
            card.scrollIntoView({behavior:'smooth', block:'center'});
        });
    }

    function openEdit(notif) {
        title.textContent = 'Editar notificación — ID ' + notif.id;
        form.action = "{{ url('/notificaciones') }}/" + notif.id;
        methodInput.value = 'PUT';
        idInput.value = notif.id || '';
        document.getElementById('f-id_usuario').value = notif.id_usuario || '';
        document.getElementById('f-estado').value = notif.estado || 'cerrada';
        document.getElementById('f-tipo').value = notif.tipo || 'prueba';
        document.getElementById('f-ruta').value = notif.ruta || '';
        document.getElementById('f-descripcion').value = notif.descripcion || '';
        if (notif.fecha_visto) {
            fechaVistoRow.style.display = 'block';
            try {
                const d = new Date(notif.fecha_visto);
                const pad = (n)=> String(n).padStart(2,'0');
                const yyyy = d.getFullYear();
                const mm = pad(d.getMonth()+1);
                const dd = pad(d.getDate());
                const hh = pad(d.getHours());
                const mi = pad(d.getMinutes());
                fechaVistoInput.value = `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
            } catch(e) {
                fechaVistoRow.style.display = 'block';
            }
        } else {
            fechaVistoRow.style.display = 'none';
            fechaVistoInput.value = '';
        }

        card.style.display = 'block';
        card.classList.add('collapsed');
        hideTable();
        requestAnimationFrame(()=> {
            card.classList.remove('collapsed');
            card.scrollIntoView({behavior:'smooth', block:'center'});
        });
    }

    if (btnNew) btnNew.addEventListener('click', openCreate);
    if (btnCancel) btnCancel.addEventListener('click', function(){ closeModalAnimated(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModalAnimated(); });

    document.querySelectorAll('.btn-edit').forEach(btn=>{
        btn.addEventListener('click', function(){
            try {
                const notif = JSON.parse(this.getAttribute('data-notif'));
                openEdit(notif);
            } catch(e) {
                console.error('invalid notif json', e);
                showNotification('Error interno: datos inválidos', 'error');
            }
        });
    });

    if (form) {
        form.addEventListener('submit', async function(evt){
            evt.preventDefault();
            const btn = document.getElementById('notif-save');
            const original = btn ? btn.textContent : null;
            if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

            const fd = new FormData(form);
            const method = methodInput.value || 'POST';
            if (method.toUpperCase() === 'PUT') fd.append('_method', 'PUT');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                    body: fd,
                    credentials: 'include'
                });

                const contentType = resp.headers.get('content-type') || '';
                let data = null;
                if (contentType.includes('application/json')) data = await resp.json();
                else data = await resp.text();

                if (resp.ok) {
                    showNotification('Operación realizada correctamente.', 'success', 1400);
                    closeModalAnimated();
                    setTimeout(()=> window.location.href = "{{ url('/notificaciones') }}", 700);
                    return;
                }

                if (resp.status === 422 && data && data.errors) {
                    const messages = Object.values(data.errors).flat().join('\n');
                    showNotification('Errores de validación: ' + messages, 'error', 5000);
                } else {
                    const msg = (data && data.message) ? data.message : 'Error al guardar.';
                    showNotification(msg, 'error', 4000);
                }
            } catch (err) {
                console.error(err);
                showNotification('Error de red o del servidor.', 'error', 4000);
            } finally {
                if (btn) { btn.disabled = false; if (original) btn.textContent = original; }
            }
        });
    }
});
</script>

<style>
.notif { min-width:220px; max-width:420px; padding:10px 14px; border-radius:10px; color:#fff; font-weight:700; transform:translateY(-6px); opacity:0; transition:transform .28s, opacity .28s; box-shadow:0 8px 24px rgba(2,6,23,0.08); }
.notif.visible { transform:none; opacity:1; }
</style>
@endsection
</body>
</html>