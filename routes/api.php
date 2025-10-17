<?php
// routes/api.php
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MuebleController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\NotificacionController;
Route::apiResource('areas', AreaController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('muebles', MuebleController::class);
Route::apiResource('solicitudes', SolicitudController::class);
Route::apiResource('notificaciones', NotificacionController::class);