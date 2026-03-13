<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VentaController extends Controller
{
    public function registrarVenta(Request $request)
    {
        $flaskUrl = env('FLASK_URL');
        $expressUrl = env('EXPRESS_URL');
        $token = env('MICROSERVICE_TOKEN');

        $producto_id = $request->producto_id;
        $cantidad = $request->cantidad;
        $usuario = $request->usuario;

        $headers = ['Authorization' => "Token $token"];


        $stockResponse = Http::withHeaders($headers)
            ->get("$flaskUrl/api/inventario/$producto_id/stock");

        if ($stockResponse->failed()) {
            return response()->json([
                'message' => 'Error al consultar el inventario',
                'detalle' => $stockResponse->body()
            ], 502);
        }

        $data = $stockResponse->json();

        if (!isset($data['stock'])) {
            return response()->json([
                'message' => 'Respuesta inesperada del servicio de inventario',
                'respuesta_recibida' => $data
            ], 502);
        }

        $stock = $data['stock'];


        if ($stock < $cantidad) {
            return response()->json([
                'message' => 'Stock insuficiente',
                'stock_disponible' => $stock,
                'cantidad_solicitada' => $cantidad
            ], 400);
        }

        $ventaResponse = Http::withHeaders($headers)
            ->post("$expressUrl/api/ventas", [
                'producto_id' => $producto_id,
                'cantidad'    => $cantidad,
                'usuario'     => $usuario
            ]);

        if ($ventaResponse->failed()) {
            return response()->json([
                'message' => 'Error al registrar la venta',
                'detalle' => $ventaResponse->body()
            ], 502);
        }


        $inventarioResponse = Http::withHeaders($headers)
            ->put("$flaskUrl/api/inventario/$producto_id/reducir", [
                'cantidad' => $cantidad
            ]);

        if ($inventarioResponse->failed()) {
            return response()->json([
                'message' => 'Venta registrada pero error al actualizar inventario',
                'detalle' => $inventarioResponse->body()
            ], 502);
        }

        return response()->json([
            'message'     => 'Venta registrada correctamente',
            'venta'       => $ventaResponse->json(),
            'inventario'  => $inventarioResponse->json()
        ], 201);
    }
}