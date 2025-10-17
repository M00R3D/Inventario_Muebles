<div class="nav-notifications" id="nav-notifications">
    <button id="notif-toggle" aria-haspopup="true" aria-expanded="false" title="Notificaciones">
        🔔 <span id="notif-count" class="badge">0</span>
    </button>

    <div id="notif-menu" class="notif-menu" style="display:none;">
        <div id="notif-list">Cargando...</div>
        <div style="padding:6px;text-align:center">
            <a href="/notificaciones">Ver todas</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('notif-toggle');
    const menu = document.getElementById('notif-menu');
    const list = document.getElementById('notif-list');
    const count = document.getElementById('notif-count');

    async function loadNotifs() {
        try {
            const res = await axios.get('/api/notificaciones?tipo=prueba'); // ajustar filtro si hace falta
            const nots = res.data.filter(n => n.id_usuario === (window.currentUserId || null) || n.id_usuario === null);
            count.textContent = nots.filter(n => n.estado !== 'vista').length;
            if (!nots.length) { list.innerHTML = '<div style="padding:8px">Sin notificaciones</div>'; return; }
            list.innerHTML = nots.map(n => {
                const cls = n.estado === 'vista' ? 'seen' : 'unseen';
                return `<div class="notif-item ${cls}" data-id="${n.id}">
                    <div class="notif-desc">${n.descripcion || '(sin descripción)'}</div>
                    <div class="notif-meta">${n.fecha_creacion}</div>
                    <div class="notif-actions">
                        <button class="mark-visto" data-id="${n.id}">Marcar visto</button>
                        <a href="/notificaciones/${n.id}/edit">Abrir</a>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            list.innerHTML = '<div style="padding:8px;color:#c00">Error cargando</div>';
        }
    }

    menu.addEventListener('click', async function (ev) {
        const t = ev.target;
        if (t.matches('.mark-visto')) {
            const id = t.dataset.id;
            await axios.post(`/notificaciones/${id}/usuario/marcar-visto`);
            await loadNotifs();
        }
    });

    btn.addEventListener('click', function () {
        const visible = menu.style.display === 'block';
        menu.style.display = visible ? 'none' : 'block';
        if (!visible) loadNotifs();
    });
    loadNotifs();
});
</script>

<style>
.notif-menu{ position:absolute; right:10px; top:50px; width:320px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 6px 20px rgba(0,0,0,0.08); }
.notif-item{ padding:8px; border-bottom:1px solid #f1f1f1; }
.notif-item.unseen{ background:#f9fafb; }
.notif-actions{ margin-top:6px; display:flex; gap:8px; }
</style>