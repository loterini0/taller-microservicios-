const express = require('express')
const mongoose = require('mongoose')
const cors = require('cors')
require('dotenv').config() 


const app = express()

app.use(cors())
app.use(express.json())


mongoose.connect('mongodb://localhost:27017/ventasdb')

const VentaSchema = new mongoose.Schema({
    producto_id: String,
    cantidad: Number,
    usuario: String,
    fecha: {
        type: Date,
        default: Date.now
    }
})

const Venta = mongoose.model('Venta', VentaSchema)

const verificarToken = (req, res, next) => {
    const authHeader = req.headers['authorization']

    if (!authHeader || !authHeader.startsWith('Token ')) {
        return res.status(401).json({ error: 'No autorizado' })
    }

    const token = authHeader.split(' ')[1]

    if (token !== process.env.MICROSERVICE_TOKEN) {
        return res.status(401).json({ error: 'No autorizado' })
    }

    next()
}


app.use(verificarToken)

app.post('/api/ventas', async (req, res) => {

    try {

        const venta = new Venta(req.body)
        await venta.save()

        res.json({
            message: "Venta registrada",
            venta
        })

    } catch (error) {

        res.status(500).json({
            message: "Error al registrar venta"
        })

    }

})



app.get('/api/ventas', async (req, res) => {

    const ventas = await Venta.find()

    res.json(ventas)

})



app.get('/api/ventas/usuario/:usuario', async (req, res) => {

    const ventas = await Venta.find({
        usuario: req.params.usuario
    })

    res.json(ventas)

})



app.get('/api/ventas/fecha/:fecha', async (req, res) => {

    const fecha = new Date(req.params.fecha)

    const ventas = await Venta.find({
        fecha: {
            $gte: fecha
        }
    })

    res.json(ventas)

})


app.listen(3000, () => {
    console.log("Microservicio ventas corriendo en puerto 3000")
})