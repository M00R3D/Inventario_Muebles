<?php

use Illuminate\Support\Facades\Route;
use App\Models\Area;

Route::get('/', function () {
    $areas = \App\Models\Area::all();
    return view('login', compact('areas'));
});
