<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VentaController extends Controller
{
    public function registrarVenta(Request $request)
    {
        $producto_id = $request->producto_id;
        $cantidad = $request->cantidad;

        $flaskUrl = env('FLASK_URL');
        $expressUrl = env('EXPRESS_URL');

        $stockResponse = Http::get("$flaskUrl/api/inventario/$producto_id/stock");

        if ($stockResponse->failed()) {
            return response()->json([
                'message' => 'Error consultando inventario'
            ], 500);
        }

        $stock = $stockResponse->json()['stock'];

        if ($stock < $cantidad) {
            return response()->json([
                'message' => 'Stock insuficiente'
            ], 400);
        }


        $ventaData = [
            'producto_id' => $producto_id,
            'cantidad' => $cantidad,
            'usuario' => $request->usuario
        ];

        $expressResponse = Http::post("$expressUrl/api/ventas", $ventaData);

        if ($expressResponse->failed()) {
            return response()->json([
                'message' => 'Error al registrar la venta'
            ], 500);
        }

        $updateInventory = Http::put("$flaskUrl/api/inventario/$producto_id/reducir", [
            'cantidad' => $cantidad
        ]);

        if ($updateInventory->failed()) {
            return response()->json([
                'message' => 'Error al actualizar inventario'
            ], 500);
        }

        return response()->json([
            'message' => 'Venta registrada correctamente',
            'venta' => $expressResponse->json(),
            'inventario' => $updateInventory->json()
        ]);
    }
}