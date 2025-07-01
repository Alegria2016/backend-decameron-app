<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/check-migrations', function () {
    try {
        // Verifica conexión a la BD
        DB::connection()->getPdo();
        
        // Ejecuta migrate:status y captura el output
        Artisan::call('migrate:status');
        $output = Artisan::output();
        
        return "<pre>Migraciones:\n" . $output . "</pre>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});