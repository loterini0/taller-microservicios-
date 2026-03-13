from config import get_db

db = get_db()

def registrar_producto(data):
    ref = db.reference('productos')

    ref.child(data['id']).set({
        "nombre": data["nombre"],
        "precio": data["precio"],
        "stock": data["stock"]
    })

def obtener_productos():
    ref = db.reference('productos')
    return ref.get()

def obtener_producto(id):
    ref = db.reference('productos').child(id)
    return ref.get()

def actualizar_stock(id, nuevo_stock):
    ref = db.reference('productos').child(id)
    ref.update({
        "stock": nuevo_stock
    })