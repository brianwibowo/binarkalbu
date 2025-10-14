<?php

use Illuminate\Support\Facades\Route;

// Atau mungkin redirect ke halaman admin
Route::get('/', function () {
    return redirect('/admin');
});
