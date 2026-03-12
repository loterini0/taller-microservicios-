from flask import Flask, request, jsonify
import firebase_admin
from firebase_admin import credentials, db

app = Flask(__name__)

cred = credentials.Certificate("firebase_config.json")

firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://api-software2-default-rtdb.firebaseio.com'
})

@app.route('/api/inventario', methods=['POST'])
def registrar_producto():
    data = request.json

    ref = db.reference('productos')

    ref.child(data['id']).set({
        "nombre": data["nombre"],
        "precio": data["precio"],
        "stock": data["stock"]
    })

    return jsonify({"message": "Producto registrado"})


@app.route('/api/inventario', methods=['GET'])
def obtener_productos():

    ref = db.reference('productos')
    productos = ref.get()

    return jsonify(productos)



@app.route('/api/inventario/<id>/stock', methods=['GET'])
def verificar_stock(id):

    ref = db.reference('productos').child(id)
    producto = ref.get()

    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    return jsonify({
        "id": id,
        "stock": producto["stock"]
    })



@app.route('/api/inventario/<id>/reducir', methods=['PUT'])
def reducir_stock(id):

    data = request.json
    cantidad = data["cantidad"]

    ref = db.reference('productos').child(id)
    producto = ref.get()

    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    if producto["stock"] < cantidad:
        return jsonify({"error": "Stock insuficiente"}), 400

    nuevo_stock = producto["stock"] - cantidad

    ref.update({
        "stock": nuevo_stock
    })

    return jsonify({
        "message": "Stock actualizado",
        "nuevo_stock": nuevo_stock
    })


if __name__ == '__main__':
    app.run(debug=True)