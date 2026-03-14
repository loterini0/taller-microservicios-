<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProductoController;
 

Route::post('/register',[UserController::class, 'register']);
Route::post('/login',[UserController::class, 'login']);
 

Route::middleware('auth:api')->group(function () {

    Route::post('/logout',[UserController::class, 'logout']);

    Route::post('/productos',[ProductoController::class, 'registrar']);
    Route::get('/productos', [ProductoController::class, 'listar']);
    Route::get('/productos/{id}/stock',[ProductoController::class, 'stock']);

    Route::post('/ventas',[VentaController::class, 'registrarVenta']);
    Route::get('/ventas', [VentaController::class, 'listar']);
    Route::get('/ventas/usuario/{usuario}',[VentaController::class, 'porUsuario']);
    Route::get('/ventas/fecha/{fecha}',[VentaController::class, 'porFecha']);
 
});