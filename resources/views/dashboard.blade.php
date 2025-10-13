<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Inventario Muebles</title>
</head>
<body>
@extends('layouts.app')

@section('title','Dashboard | Inventario Muebles')

@section('content')
    <div class="card">
        <h1>Bienvenido al Dashboard</h1>
        <p>Aquí puedes ver estadísticas y gestionar la información de inventario.</p>
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