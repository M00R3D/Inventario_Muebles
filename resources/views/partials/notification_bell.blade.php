<div class="nav-notifications" id="nav-notifications">
    <button id="notif-toggle" aria-haspopup="true" aria-expanded="false" title="Notificaciones" class="notif-btn">
        <span class="notif-ico">🔔</span>
        <span id="notif-count" class="badge">0</span>
    </button>

    <div id="notif-menu" class="notif-menu" role="menu" aria-hidden="true">
        <div id="notif-list" class="notif-list">Cargando...</div>
        <div class="notif-footer">
            <a href="/notificaciones" class="notif-see-all">Ver todas</a>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('notif-toggle');
    const menu = document.getElementById('notif-menu');
    const list = document.getElementById('notif-list');
    const count = document.getElementById('notif-count');
    function openMenu() {
        menu.style.display = 'block';
        menu.setAttribute('aria-hidden', 'false');
        btn.setAttribute('aria-expanded', 'true');
        loadNotifs();
        menu.focus && menu.focus();
    }
    function closeMenu() {
        menu.style.display = 'none';
        menu.setAttribute('aria-hidden', 'true');
        btn.setAttribute('aria-expanded', 'false');
    }
    function toggleMenu() {
        const opened = menu.style.display === 'block';
        if (opened) closeMenu(); else openMenu();
    }
    document.addEventListener('click', function (ev) {
        const target = ev.target;
        if (!menu.contains(target) && !btn.contains(target)) {
            closeMenu();
        }
    });
    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') closeMenu();
    });
    menu.addEventListener('click', function (ev) {
        ev.stopPropagation();
    });
    btn.addEventListener('click', function (ev) {
        ev.stopPropagation();
        toggleMenu();
    });

    async function loadNotifs() {
        try {
            const apiRes = await fetch('/notificaciones?tipo=prueba', {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!apiRes.ok) throw new Error('API error: ' + apiRes.status + ' ' + apiRes.statusText);
            const data = await apiRes.json();
            const all = Array.isArray(data) ? data : [];
            const uid = (typeof window.currentUserId !== 'undefined' && window.currentUserId !== null) ? Number(window.currentUserId) : null;
            const nots = all.filter(n => (n.id_usuario === null) || (uid !== null && Number(n.id_usuario) === uid));

            const unseen = nots.filter(n => n.estado !== 'vista').length;
            count.textContent = unseen;

            if (!nots.length) {
                list.innerHTML = '<div class="notif-empty">Sin notificaciones</div>';
                return;
            }

            list.innerHTML = nots.map(n => {
                const cls = n.estado === 'vista' ? 'notif-item seen' : 'notif-item unseen';
                const fecha = n.fecha_creacion ? `<div class="meta">${n.fecha_creacion}</div>` : '';
                const descripcion = (n.descripcion || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                return `<div class="${cls}" data-id="${n.id}">
                    <div class="body">${descripcion}</div>
                    ${fecha}
                    <div class="actions">
                        <button class="mark-visto" data-id="${n.id}">Marcar visto</button>
                        <a class="open-link" href="/notificaciones/${n.id}/edit">Abrir</a>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            console.error('Error cargando notificaciones:', e);
            list.innerHTML = `<div class="notif-error">Error cargando: ${e.message || 'desconocido'}</div>`;
        }
    }

    menu.addEventListener('click', async function (ev) {
        const t = ev.target;
        if (t.matches('.mark-visto')) {
            const id = t.dataset.id;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch(`/notificaciones/${id}/usuario/marcar-visto`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });
                if (!resp.ok) throw new Error('Error marcando visto: ' + resp.status);
                await loadNotifs();
            } catch (err) {
                console.error(err);
                list.innerHTML = '<div class="notif-error">Error marcando visto</div>';
            }
        }
    });
    closeMenu();
});
</script>

<style>
.nav-notifications { position:relative; display:inline-flex; align-items:center; gap:8px; margin-left:12px; }
.notif-btn {
    background:transparent;
    border:0;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 8px;
    border-radius:8px;
    transition:background .12s ease, transform .08s ease;
    color:var(--muted, #6b7280);
    font-weight:700;
}
.notif-btn:hover { background: rgba(99,102,241,0.06); transform:translateY(-1px); }
.notif-ico { font-size:18px; line-height:1; }
.badge {
    display:inline-block;
    min-width:20px;
    padding:2px 6px;
    border-radius:999px;
    background: linear-gradient(90deg,#ef4444,#f97316);
    color:#fff;
    font-size:12px;
    font-weight:800;
    text-align:center;
}

/* menu */
.notif-menu {
    position:absolute;
    right:0;
    top:44px;
    width:340px;
    max-height:420px;
    overflow:auto;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius:10px;
    box-shadow: 0 12px 40px rgba(2,6,23,0.12);
    padding:8px;
    z-index:120;
    display:none;
}
.notif-list { display:flex; flex-direction:column; gap:8px; padding:6px; }
.notif-item {
    padding:10px;
    border-radius:8px;
    background: #ffffff;
    border: 1px solid #f3f4f6;
    box-shadow: 0 6px 18px rgba(2,6,23,0.04);
}
.notif-item.unseen { background:#fbfdff; border-left:4px solid #6366f1; }
.notif-item .body { font-weight:600; color:#111827; margin-bottom:6px; }
.notif-item .meta { font-size:12px; color:#6b7280; margin-bottom:6px; }
.notif-item .actions { display:flex; gap:8px; align-items:center; }
.notif-item .actions .mark-visto {
    background:#06b6d4; color:#fff; border:0; padding:6px 8px; border-radius:8px; cursor:pointer; font-weight:700;
}
.notif-item .actions .open-link {
    background:linear-gradient(90deg,#6366f1,#06b6d4); color:#fff; padding:6px 8px; border-radius:8px; text-decoration:none; font-weight:700;
}
.notif-footer { text-align:center; padding:8px; border-top:1px dashed #f3f4f6; margin-top:6px; }
.notif-see-all { color:#374151; text-decoration:none; font-weight:700; padding:6px 10px; border-radius:8px; display:inline-block; }
.notif-empty, .notif-error { padding:10px; color:#6b7280; text-align:center; }

@media (max-width:600px){
    .notif-menu { right:8px; left:8px; width:auto; top:52px; }
}
</style>