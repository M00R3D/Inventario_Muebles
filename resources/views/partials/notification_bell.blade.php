<div id="{{ $_container_id ?? 'nav-notifications' }}" class="nav-notifications {{ isset($_sidebar_origin) && $_sidebar_origin ? 'sidebar-origin' : '' }}">
    @unless(isset($_only_menu) && $_only_menu)
    <button class="notif-btn" type="button" aria-haspopup="true" aria-expanded="false" title="Notificaciones">
        <span class="notif-ico">🔔</span>
        <span class="notif-count badge">0</span>
    </button>
    @endunless
    <div class="notif-menu" role="menu" aria-hidden="true" aria-label="Lista de notificaciones">
        <div class="notif-list">Cargando...</div>
        <div class="notif-footer">
            <a href="/notificaciones" class="notif-see-all">Ver más</a>
        </div>
    </div>
</div>

<style>
.nav-notifications { position:relative; display:inline-flex; align-items:center; gap:8px; margin-left:12px; }
.notif-btn { background:transparent;border:0;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;transition:background .12s ease, transform .08s ease;color:var(--muted,#6b7280);font-weight:700; }
.notif-ico { font-size:18px; line-height:1; }
.badge, .notif-count { display:inline-block; min-width:20px; padding:2px 6px; border-radius:999px; background: linear-gradient(90deg,#ef4444,#f97316); color:#fff; font-size:12px; font-weight:800; text-align:center; }


.notif-menu {
    position: absolute;
    right: 0;
    top: 44px;
    width: 320px;
    max-height: 420px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 12px 40px rgba(2,6,23,0.12);
    padding: 6px;
    z-index: 120;
    display: none;
    transform-origin: top right;
    transform: scaleY(0.96);
    opacity: 0;
    transition: transform .22s cubic-bezier(.16,.84,.44,1), opacity .18s ease;
    color: #000;
    font-size: 13px;
}
.nav-notifications .notif-menu.open {
    display: block; 
    transform: scaleY(1);
    opacity: 1;
}
.nav-notifications.sidebar-origin .notif-menu {
    left: 100%;
    right: auto;
    top: 8px;
    transform-origin: left top;
}
.notif-list { max-height: 320px; overflow:auto; display:flex; flex-direction:column; gap:6px; padding:4px; }
.notif-list .notif-item {
    display:flex;
    flex-direction:column;
    gap:6px;
    padding:8px 10px;
    border-radius:8px;
    background: #fff;
    color: #000;
    box-shadow: none;
    border: 1px solid rgba(15,23,42,0.03);
}
.notif-list .notif-item.closed { background: #fff; }
.notif-list .notif-item .body { font-weight:700; color:#000; font-size:13px; }
.notif-list .notif-item .meta { font-size:11px; color:#374151; }
.notif-list .notif-item .actions { display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:4px; }
.notif-list .notif-item .actions .mark-visto { background:#06b6d4;color:#fff;padding:6px 8px;border-radius:8px;border:0;font-weight:700;font-size:12px; cursor:pointer; }
.notif-list .notif-item .actions .open-link { font-size:12px;color:#0ea5e9;text-decoration:none; font-weight:700; }
.notif-footer { padding-top:6px; border-top:1px solid #f3f4f6; text-align:center; margin-top:6px; }
.notif-see-all { display:inline-block; padding:8px 12px; background:#111827;color:#fff;border-radius:8px;text-decoration:none;font-weight:800;font-size:13px; }
</style>

<script>
window.initNotificationBell = function(containerIdOrEl) {
    const container = (typeof containerIdOrEl === 'string') ? document.getElementById(containerIdOrEl) : containerIdOrEl;
    if (!container) return null;
    const btn = container.querySelector('.notif-btn');
    const menu = container.querySelector('.notif-menu');
    const list = container.querySelector('.notif-list');
    const countEl = container.querySelector('.notif-count');

    async function loadNotifs() {
        try {
            const apiRes = await fetch('/notificaciones', {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!apiRes.ok) throw new Error('API error: ' + apiRes.status + ' ' + apiRes.statusText);
            const all = await apiRes.json();
            const nots = Array.isArray(all) ? all : [];
            const closed = nots.filter(n => n.estado === 'cerrada');
            const closedTotal = closed.length;
            const display = closed.slice(0, 6);

            if (countEl) countEl.textContent = String(closedTotal);

            if (!display.length) {
                list.innerHTML = '<div class="notif-empty" style="padding:12px;text-align:center;color:#374151;">Sin notificaciones cerradas</div>';
                if (container && container.classList.contains('sidebar-origin')) {
                    setTimeout(()=> { window.location.href = '/notificaciones'; }, 300);
                }
                return;
            }

            list.innerHTML = display.map(n => {
                const fecha = n.fecha_creacion ? `<div class="meta">${(new Date(n.fecha_creacion)).toLocaleString('es-ES', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' })}</div>` : '';
                const descripcion = (n.descripcion || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const openHref = n.ruta ? n.ruta : `/notificaciones/${n.id}`;
                return `<div class="notif-item closed" data-id="${n.id}">
                    <div class="body">${descripcion}</div>
                    ${fecha}
                    <div class="actions">
                        <button class="mark-visto" data-id="${n.id}">Marcar visto</button>
                        <a class="open-link" href="${openHref}">Abrir</a>
                    </div>
                </div>`;
            }).join('');

            if (closedTotal > 6) {
                const moreNote = document.createElement('div');
                moreNote.style.fontSize = '12px';
                moreNote.style.color = '#374151';
                moreNote.style.textAlign = 'center';
                moreNote.style.marginTop = '6px';
                moreNote.textContent = `Mostrando 6 de ${closedTotal} cerradas`;
                list.appendChild(moreNote);
            }
        } catch (e) {
            console.error('Error cargando notificaciones:', e);
            list.innerHTML = `<div class="notif-error" style="padding:12px;color:#fff;background:linear-gradient(90deg,#ef4444,#b91c1c);border-radius:8px;">Error cargando: ${e.message || 'desconocido'}</div>`;
        }
    }

    function openMenu() {
        if (!menu) return;
        menu.style.display = 'block';
        menu.getBoundingClientRect();
        menu.classList.add('open');
        menu.setAttribute('aria-hidden', 'false');
        if (btn) btn.setAttribute('aria-expanded', 'true');
        loadNotifs();
    }
    function closeMenu() {
        if (!menu) return;
        menu.classList.remove('open');
        menu.setAttribute('aria-hidden', 'true');
        if (btn) btn.setAttribute('aria-expanded', 'false');
        const handler = function(){
            menu.style.display = 'none';
            menu.removeEventListener('transitionend', handler);
        };
        menu.addEventListener('transitionend', handler);
    }
    function toggleMenu() {
        if (!menu) return;
        if (menu.classList.contains('open')) closeMenu(); else openMenu();
    }
    menu.addEventListener('click', async function(ev) {
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
    menu.style.display = 'none';
    menu.classList.remove('open');
    if (btn) {
        btn.addEventListener('click', function(ev){
            ev.stopPropagation();
            toggleMenu();
        });
    }
    document.addEventListener('click', function(ev){
        if (!container.contains(ev.target)) closeMenu();
    });
    return { open: openMenu, close: closeMenu, toggle: toggleMenu, load: loadNotifs, container };
};
</script>