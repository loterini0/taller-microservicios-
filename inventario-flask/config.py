import firebase_admin
from firebase_admin import credentials, db

cred = credentials.Certificate("firebase_config.json")

firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://api-software2-default-rtdb.firebaseio.com'
})

def get_db():
    return db