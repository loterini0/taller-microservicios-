<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
 
class VentaController extends Controller
{
    
    public function registrarVenta(Request $request)
    {
        $flask   = env('FLASK_URL');
        $express = env('EXPRESS_URL');
        $headers = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
 
        $id       = $request->producto_id;
        $cantidad = $request->cantidad;
        $usuario  = $request->usuario;
 
        
        $stock = Http::withHeaders($headers)->get("$flask/api/inventario/$id/stock");
 
        if ($stock->failed()) {
            return response()->json(['message' => 'Error al consultar inventario'], 502);
        }
 
        $stockData = $stock->json();
 
        if (!isset($stockData['stock'])) {
            return response()->json(['message' => 'Respuesta inesperada del inventario'], 502);
        }
 
        if ($stockData['stock'] < $cantidad) {
            return response()->json([
                'message'          => 'Stock insuficiente',
                'stock_disponible' => $stockData['stock']
            ], 400);
        }
 
        
        $venta = Http::withHeaders($headers)->post("$express/api/ventas", [
            'producto_id' => $id,
            'cantidad'    => $cantidad,
            'usuario'     => $usuario
        ]);
 
        if ($venta->failed()) {
            return response()->json(['message' => 'Error al registrar la venta'], 502);
        }
 
        
        $inventario = Http::withHeaders($headers)->put("$flask/api/inventario/$id/reducir", [
            'cantidad' => $cantidad
        ]);
 
        if ($inventario->failed()) {
            return response()->json(['message' => 'Venta registrada pero error al actualizar inventario'], 502);
        }
 
        return response()->json([
            'message'    => 'Venta registrada correctamente',
            'venta'      => $venta->json(),
            'inventario' => $inventario->json()
        ], 201);
    }
 
    
    public function listar()
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->get(env('EXPRESS_URL') . '/api/ventas');
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al obtener ventas'], 502);
        }
 
        return response()->json($response->json());
    }
 
    
    public function porUsuario($usuario)
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->get(env('EXPRESS_URL') . "/api/ventas/usuario/$usuario");
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al obtener ventas por usuario'], 502);
        }
 
        return response()->json($response->json());
    }
 
    
    public function porFecha($fecha)
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->get(env('EXPRESS_URL') . "/api/ventas/fecha/$fecha");
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al obtener ventas por fecha'], 502);
        }
 
        return response()->json($response->json());
    }
}
 