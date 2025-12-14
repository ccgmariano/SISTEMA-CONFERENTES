// experimental/poseidonController.js
const { loginPoseidon } = require("./poseidonService");

async function testeLogin(req, res) {
  try {
    const cpf = process.env.POSEIDON_CPF;
    const senha = process.env.POSEIDON_SENHA;

    if (!cpf || !senha) {
      return res.status(400).json({
        erro: "Defina POSEIDON_CPF e POSEIDON_SENHA nas variáveis de ambiente."
      });
    }

    const resultado = await loginPoseidon(cpf, senha);

    res.json({
      ok: true,
      mensagem: "Login bem-sucedido no Poseidon",
      resultado: resultado
    });

  } catch (e) {
    res.status(500).json({
      ok: false,
      erro: e.message
    });
  }
}

module.exports = { testeLogin };
