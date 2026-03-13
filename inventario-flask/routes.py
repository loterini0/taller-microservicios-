from flask import Blueprint, request, jsonify
from models import *

inventario_bp = Blueprint('inventario', __name__)

@inventario_bp.route('/api/inventario', methods=['POST'])
def registrar_producto_route():
    data = request.json
    registrar_producto(data)
    return jsonify({"message": "Producto registrado"})


@inventario_bp.route('/api/inventario', methods=['GET'])
def obtener_productos_route():
    productos = obtener_productos()
    return jsonify(productos)


@inventario_bp.route('/api/inventario/<id>/stock', methods=['GET'])
def verificar_stock(id):

    producto = obtener_producto(id)

    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    return jsonify({
        "id": id,
        "stock": producto["stock"]
    })


@inventario_bp.route('/api/inventario/<id>/reducir', methods=['PUT'])
def reducir_stock(id):

    data = request.json
    cantidad = data["cantidad"]

    producto = obtener_producto(id)

    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    if producto["stock"] < cantidad:
        return jsonify({"error": "Stock insuficiente"}), 400

    nuevo_stock = producto["stock"] - cantidad

    actualizar_stock(id, nuevo_stock)

    return jsonify({
        "message": "Stock actualizado",
        "nuevo_stock": nuevo_stock
    })