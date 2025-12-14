const express = require("express");
const app = express();

// Permite o Express interpretar body de formulários (necessário para POST)
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// ROTA EXPERIMENTAL (Poseidon) – AQUI ESTÁ A LINHA QUE VOCÊ PEDIU
app.use("/experimental/poseidon", require("./experimental/poseidonroutes"));

// Rota simples para testar se o servidor está no ar
app.get("/", (req, res) => {
  res.send("Servidor ativo. Rota experimental: /experimental/poseidon/teste");
});

// Porta automática do Render
const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
  console.log("Servidor rodando na porta " + PORT);
});
