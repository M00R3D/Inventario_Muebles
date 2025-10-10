<?php
// routes/api.php
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MuebleController;
use App\Http\Controllers\SolicitudController;
Route::apiResource('areas', AreaController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('muebles', MuebleController::class);
Route::apiResource('solicitudes', SolicitudController::class);