# Sistema de Ventas con Microservicios
**Estudiante:** Juan Manuel Lotero Rojo  
**Identificacion:** 1054398881

```
Cliente
  │  JWT
  ▼
API Gateway — Laravel 12 (Puerto 8000)
  │  Token interno
  ├──► Microservicio Inventario — Flask + Firebase (Puerto 5000)
  └──► Microservicio Ventas — Express + MongoDB (Puerto 3000)
```




Gateway Laravel

```bash
cd gateway-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

Variables de entorno en `.env`:
```env
FLASK_URL=http://127.0.0.1:5000
EXPRESS_URL=http://127.0.0.1:3000
MICROSERVICE_TOKEN=miclave123
```

---
Microservicio Inventario — Flask

```bash
cd inventario-flask
pip install -r requirements.txt
python app.py
```
Crear archivo `.env`:
```env
API_TOKEN=miclave123
```

> Requiere el archivo `firebase_config.json` en esta carpeta.

---

Microservicio Ventas — Express

```bash
cd ventas-express
npm install
node server.js
```

Crear archivo `.env`:
```env
MICROSERVICE_TOKEN=miclave123
```

---

Endpoints (todos por el Gateway en puerto 8000)

### Autenticación

| Método | Endpoint |
|---|---|
| POST | `/api/register` |
| POST | `/api/login` | 

### Productos

| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/productos` | Registrar producto |
| GET | `/api/productos` | Listar productos |
| GET | `/api/productos/{id}/stock` | Verificar stock |

### Ventas

| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/ventas` | Registrar venta |
| GET | `/api/ventas` | Listar ventas |
| GET | `/api/ventas/usuario/{usuario}` | Ventas por usuario |
| GET | `/api/ventas/fecha/{fecha}` | Ventas por fecha |

> Todos los endpoints excepto `/register` y `/login` requieren:  
> `Authorization: Bearer <token>`

---

## Flujo de una venta

**1. Registrar usuario**
```
POST /api/register
{ "name": "Juan", "email": "juan@test.com", "password": "123456" }
```

**2. Iniciar sesión**
```
POST /api/login
{ "email": "juan@test.com", "password": "123456" }
```
Devuelve un `access_token` que se usa en los siguientes pasos.

**3. Registrar venta**
```
POST /api/ventas
Authorization: Bearer <access_token>
{ "producto_id": "p001", "cantidad": 2, "usuario": "juan" }
```

Internamente Laravel:
1. Valida el JWT
2. Consulta stock en Flask
3. Registra la venta en Express
4. Reduce el inventario en Flask


