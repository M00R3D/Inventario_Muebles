<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MuebleController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\NotificacionController;

Route::get('/', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::get('/usuarios/create', [UsuarioController::class, 'create']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit']);
Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);

Route::get('/muebles', [MuebleController::class, 'index']);
Route::get('/muebles/create', [MuebleController::class, 'create']);
Route::post('/muebles', [MuebleController::class, 'store']);
Route::get('/muebles/{mueble}/edit', [MuebleController::class, 'edit']);
Route::put('/muebles/{mueble}', [MuebleController::class, 'update']);
Route::delete('/muebles/{mueble}', [MuebleController::class, 'destroy']);

Route::get('/imagenes', [ImageController::class, 'index']);
Route::get('/imagenes/list', [ImageController::class, 'list']);
Route::post('/imagenes/upload', [ImageController::class, 'upload']);

Route::get('/solicitudes', [SolicitudController::class, 'index']);
Route::get('/solicitudes/create', [SolicitudController::class, 'create']);
Route::post('/solicitudes', [SolicitudController::class, 'store']);
Route::get('/solicitudes/{solicitud}/edit', [SolicitudController::class, 'edit']);
Route::put('/solicitudes/{solicitud}', [SolicitudController::class, 'update']);
Route::delete('/solicitudes/{solicitud}', [SolicitudController::class, 'destroy']);
Route::post('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'changeEstado'])->name('solicitudes.changeEstado');

Route::get('/notificaciones', [NotificacionController::class, 'index']);
Route::get('/notificaciones/create', [NotificacionController::class, 'create']);
Route::post('/notificaciones', [NotificacionController::class, 'store']);
Route::get('/notificaciones/{notificacion}/edit', [NotificacionController::class, 'edit']);
Route::put('/notificaciones/{notificacion}', [NotificacionController::class, 'update']);
Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy']);
Route::post('/notificaciones/{notificacion}/estado', [NotificacionController::class, 'changeEstado'])->name('notificaciones.changeEstado');
Route::post('/notificaciones/{notificacion}/marcar-visto', [NotificacionController::class, 'marcarVisto'])->name('notificaciones.marcarVisto');

// endpoints adicionales (admin / usuario)
Route::post('/notificaciones/{notificacion}/admin/estado', [NotificacionController::class, 'adminSetEstado'])->name('notificaciones.admin.setEstado');
Route::post('/notificaciones/{notificacion}/admin/tipo',  [NotificacionController::class, 'adminSetTipo'])->name('notificaciones.admin.setTipo');

Route::post('/notificaciones/{notificacion}/usuario/marcar-visto', [NotificacionController::class, 'usuarioMarcarVisto'])->name('notificaciones.usuario.marcarVisto');
Route::post('/notificaciones/{notificacion}/usuario/estado',      [NotificacionController::class, 'usuarioSetEstado'])->name('notificaciones.usuario.setEstado');