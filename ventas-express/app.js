const express = require("express");
const mongoose = require("mongoose");

const app = express();
app.use(express.json());

mongoose.connect("mongodb://localhost:27017/products");

app.get("/orders", (req, res) => {
    res.json({ message: "Microservicio Express funcionando" });
});

app.listen(3000, () => {
    console.log("Server running on port 3000");
});