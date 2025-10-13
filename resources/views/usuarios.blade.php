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
<div class="page-container" style="padding:16px;max-width:1100px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <h1 style="margin:0;font-size:1.25rem;">Usuarios</h1>
        <a href="{{ url('/usuarios/create') }}" class="btn" style="background:#6366f1;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;">Nuevo usuario</a>
    </div>
    <div style="overflow-x:auto;background:#fff;border-radius:10px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
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
                @forelse($usuarios as $usuario)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->id }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->nombre }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->apellido }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->email }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->rol }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">{{ $usuario->area->nombre ?? '-' }}</td>
                        <td style="padding:10px 12px;vertical-align:middle;">
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <a href="{{ url('/usuarios/'.$usuario->id.'/edit') }}" style="background:#06b6d4;color:#fff;padding:6px 10px;border-radius:8px;text-decoration:none;font-size:0.9rem;">Editar</a>

                                <form action="{{ url('/usuarios/'.$usuario->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar usuario {{ addslashes($usuario->nombre.' '.$usuario->apellido) }}?');"
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
    @if(method_exists($usuarios, 'links'))
        <div style="margin-top:12px;">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>
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