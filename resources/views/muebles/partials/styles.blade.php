<style>
:root{ --bg:#f8fafc; --card:#fff; --muted:#6b7280; --accent1:#6366f1; --accent2:#06b6d4; }
.container{max-width:1200px;margin:0 auto;padding:18px;}
.header-hero{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
.header-hero h1{margin:0;font-size:3.25rem}
.grid{ display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:18px; }

.card{ background:var(--card); border-radius:12px; padding:14px; box-shadow:0 12px 34px rgba(2,6,23,0.08); display:flex; flex-direction:column; gap:12px; min-height:220px; }
.card-inner{ display:flex; flex-direction:column; gap:12px; align-items:stretch; }
.card-media{ width:100%; display:flex; align-items:center; justify-content:center; background:#f8fafc; border-radius:8px; padding:6px; max-height:240px; overflow:hidden; }
.card-media img{ max-width:100%; height:auto; max-height:200px; object-fit:contain; border-radius:6px; box-shadow:0 6px 18px rgba(2,6,23,0.06); }

.preview-wrapper img { display:block; max-width:320px; max-height:180px; width:auto; height:auto; object-fit:contain; border-radius:6px; }

.card-info{ flex:1 1 auto; display:flex; flex-direction:column; gap:8px; }
.card-top{ display:flex; align-items:center; justify-content:space-between; gap:12px; }
.card-title{ font-weight:800; font-size:1rem; color:#111; max-width:60%; word-break:break-word; }
.card-desc{ color:var(--muted); font-size:0.95rem; line-height:1.25; }
.card-meta{
  display:flex;
  gap:12px;
  align-items:flex-start;
  margin-top:6px;
  color:var(--muted);
  font-size:0.9rem;
  flex-wrap:wrap;
}
.card-meta > div {
  flex: 1 1 140px;
  min-width: 0;
  word-break: break-word;
  overflow-wrap: anywhere;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.card-meta strong { display:block; font-weight:700; margin-bottom:2px; }
.card-responsable, .card-solicitante { color:var(--muted); font-size:0.9rem; }

.mueble-nota{ display:flex; flex-wrap:wrap; white-space:normal; word-break:break-word; overflow-wrap:break-word; max-width:100%; margin-top:6px; font-size:0.9rem; color:#374151; background:#f8fafc; padding:6px 8px; border-radius:8px; }

.card-actions{ display:flex;gap:50%;  justify-content:flex-start; align-items:center; margin-top:auto; }

.estado-badge{ display:inline-flex; align-items:center; justify-content:center; padding:4px 10px; border-radius:999px; font-size:0.78rem; font-weight:700; min-width:94px; text-align:center; box-shadow:0 2px 6px rgba(2,6,23,0.06); }
.estado-bueno{ background:#10b981; color:#ffffff; }    
.estado-regular{ background:#f59e0b; color:#0b0b0b; }  
.estado-malo{ background:#ef4444; color:#ffffff; }     
.estado-en_reparacion{ background:#6366f1; color:#ffffff; } 

.btn-base{
  color:#fff;
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 8px 20px rgba(2,6,23,0.06);
  transition: transform .12s ease, box-shadow .12s ease;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}
.btn-base:hover{ transform: translateY(-3px); }
.btn-base:active{ transform: translateY(-1px); }
.btn-base:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-new, .btn-save { background: linear-gradient(90deg,var(--accent1),var(--accent2)); color:#fff; }
.btn-clear, .btn-cancel { background: linear-gradient(90deg,#ef4444,#f97316); color:#fff; }

.btn-save, .btn-cancel {
  padding:8px 12px;
  border-radius:8px;
  border:0;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 8px 20px rgba(99,102,241,0.08);
  transition: transform .12s ease, box-shadow .12s ease;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}
.btn-save:hover, .btn-cancel:hover{ transform: translateY(-3px); }
.btn-save:active, .btn-cancel:active{ transform: translateY(-1px); }
.btn-save:focus, .btn-cancel:focus{ outline:3px solid rgba(99,102,241,0.12); outline-offset:2px; }

.btn-edit{ background: linear-gradient(90deg,var(--accent1),var(--accent2)); color:#fff; padding:8px 10px; border-radius:8px; border:0; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(99,102,241,0.08); transition: transform .12s ease; }
.btn-edit:hover{ transform: translateY(-3px); }
.btn-edit:active{ transform: translateY(-1px); }
.btn-edit:focus{ outline:3px solid rgba(99,102,241,0.12); }
.btn-delete{ background: linear-gradient(90deg,#810a0aff,#d63867ff); color:#fff; padding:8px 10px; border-radius:8px; border:0; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(99,102,241,0.08); transition: transform .12s ease; }
.btn-delete:hover{ transform: translateY(-3px); }
.btn-delete:active{ transform: translateY(-1px); }
.btn-delete:focus{ outline:3px solid rgba(99,102,241,0.12); }
.read-more{ margin-left:8px; color:#06b6d4; font-weight:700; text-decoration:none; }

.card-comments { margin-top:8px; font-size:0.85rem; color:var(--muted); display:flex; flex-direction:column; gap:6px; }
.card-comments .comment { background:#ffffff; border:1px solid #eef2ff; padding:6px 8px; border-radius:8px; color:#374151; display:flex; gap:8px; align-items:flex-start; font-size:0.85rem; }
.card-comments .comment .author { font-weight:700; color:#111; margin-right:6px; white-space:nowrap; font-size:0.78rem; }
.card-comments .comment .text { color:var(--muted); word-break:break-word; font-size:0.8rem; }
.card-comments .comment.more { background:transparent; border:none; color:var(--muted); font-weight:700; padding:0 6px; }
.card-comments .comment.small { padding:6px 8px; font-size:0.78rem; }
.card-comments { max-height: calc(3 * 3.2rem); overflow:hidden; }
.card-brand { margin-top:6px; display:flex; gap:8px; align-items:baseline; }
.card-brand .marca { font-weight:900; font-size:1.05rem; color:#111; }
.card-brand .modelo { font-weight:700; font-size:0.95rem; color:#374151; opacity:0.95; }
.card-brand .categoria { font-weight:700; font-size:0.95rem; color:#0f172a; opacity:0.9; margin-left:6px; }
.card-brand .marca-label { font-weight:700; font-size:0.85rem; color:#374151; }
.card-brand .marca-value { font-weight:900; font-size:1.05rem; color:#111; }
.invalid { border-color:#ef4444 !important; box-shadow: 0 0 0 4px rgba(239,68,68,0.06); }
.field-error { color:#b91c1c; font-size:0.85rem; margin-top:6px; font-weight:700; }
</style>