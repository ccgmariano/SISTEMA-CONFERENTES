const express = require("express");
const app = express();

// Permite processar form-urlencoded (necessário para POST)
app.use(express.urlencoded({ extended: true }));

// Permite json (não obrigatório, mas útil)
app.use(express.json());

// Importa rotas do Poseidon (caminho relativo ao server.js)
app.use(
  "/experimental/poseidon",
  require("./sistema-conferentes/experimental/poseidonroutes")
);

// Rota simples para confirmar que o servidor subiu
app.get("/", (req, res) => {
  res.send("Servidor ativo. Use /experimental/poseidon/teste");
});

// Porta do Render
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Servidor rodando na porta " + PORT);
});
