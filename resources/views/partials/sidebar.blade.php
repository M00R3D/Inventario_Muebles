<!DOCTYPE html>
<div class="sidebar" id="sidebar" role="navigation" aria-label="Barra lateral">
    <h2 style="color:#fff;margin:0 0 12px 0">Menú</h2>
    <ul>
        <li><a href="{{ url('/dashboard') }}"><span class="icon">🏠</span><span class="label">Dashboard</span></a></li>
        <li><a href="{{ url('/usuarios') }}"><span class="icon">👥</span><span class="label">Usuarios</span></a></li>
        <li><a href="{{ url('/muebles') }}"><span class="icon">🪑</span><span class="label">Inventario</span></a></li>
        <li><a href="{{ url('/solicitudes') }}"><span class="icon">📩</span><span class="label">Solicitudes</span></a></li>
        <li><a href="{{ url('/logout') }}"><span class="icon">⎋</span><span class="label">Cerrar sesión</span></a></li>
    </ul>
</div>