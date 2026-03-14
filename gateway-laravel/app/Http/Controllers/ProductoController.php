<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
 
class ProductoController extends Controller
{

    public function registrar(Request $request)
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->post(env('FLASK_URL') . '/api/inventario', $request->all());
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al registrar producto'], 502);
        }
 
        return response()->json($response->json(), 201);
    }
 

    public function listar()
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->get(env('FLASK_URL') . '/api/inventario');
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al obtener productos'], 502);
        }
 
        return response()->json($response->json());
    }
 
    
    public function stock($id)
    {
        $headers  = ['Authorization' => 'Token ' . env('MICROSERVICE_TOKEN')];
        $response = Http::withHeaders($headers)->get(env('FLASK_URL') . "/api/inventario/$id/stock");
 
        if ($response->failed()) {
            return response()->json(['message' => 'Error al verificar stock'], 502);
        }
 
        return response()->json($response->json());
    }
}