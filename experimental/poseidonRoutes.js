// experimental/poseidonRoutes.js
const express = require("express");
const router = express.Router();
const { testeLogin } = require("./poseidonController");

// GET /experimental/poseidon/teste
router.get("/teste", testeLogin);

module.exports = router;
