from flask import Flask, request, jsonify
from routes import inventario_bp  # ✅ Importar el blueprint

app = Flask(__name__)

TOKEN = "miclave123"

@app.before_request
def verificar_token():
    auth_header = request.headers.get("Authorization")

    if not auth_header or not auth_header.startswith("Token "):
        return jsonify({"error": "No autorizado"}), 401

    token = auth_header.split(" ", 1)[1]

    if token != TOKEN:
        return jsonify({"error": "No autorizado"}), 401

app.register_blueprint(inventario_bp)

if __name__ == '__main__':
    app.run(debug=True)