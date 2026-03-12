from flask import Flask, jsonify
import firebase_admin
from firebase_admin import credentials, firestore

app = Flask(__name__)

cred = credentials.Certificate("firebase_config.json")
firebase_admin.initialize_app(cred)

db = firestore.client()

@app.route("/products")
def products():
    docs = db.collection("products").stream()
    data = [doc.to_dict() for doc in docs]
    return jsonify(data)

if __name__ == "__main__":
    app.run(port=5000)