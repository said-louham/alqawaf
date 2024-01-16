<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\Test\Controllers\TestController;

Route::get('plugin/test', [TestController::class, 'test']);
Route::get('plugin/test2', [TestController::class, 'test2']);
