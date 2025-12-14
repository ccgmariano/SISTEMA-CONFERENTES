const express = require("express");
const app = express();

// Permite receber dados form-urlencoded
app.use(express.urlencoded({ extended: true }));
// Permite receber JSON se precisar
app.use(express.json());

// Importa suas rotas experimentais
app.use("/experimental/poseidon", require("./poseidonRoutes"));

// Rota simples para testar se o servidor está rodando
app.get("/", (req, res) => {
  res.send("Servidor ativo. Rota de teste em /experimental/poseidon/teste");
});

// Porta fornecida pelo Render
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});
