// experimental/poseidonService.js

const axios = require("axios");
const { wrapper } = require("axios-cookiejar-support");
const tough = require("tough-cookie");

// cria cookie jar para manter sessão
const jar = new tough.CookieJar();
const client = wrapper(axios.create({ jar }));

async function loginPoseidon(cpf, senha) {
  const loginUrl = "https://poseidon.pimb.net.br/";

  // envia o POST de login
  const response = await client.post(
    loginUrl,
    new URLSearchParams({
      cpf: cpf,
      senha: senha
    }),
    {
      maxRedirects: 0, 
      validateStatus: (status) => status === 302 || status === 200
    }
  );

  // servidor redireciona para /inicio se ok
  const redirectLocation = response.headers.location;

  if (!redirectLocation || !redirectLocation.includes("inicio")) {
    throw new Error("Falha no login — credenciais inválidas ou mudança no formulário.");
  }

  // acessa a página protegida
  const inicio = await client.get("https://poseidon.pimb.net.br/inicio");

  return {
    status: "OK",
    html: inicio.data
  };
}

module.exports = { loginPoseidon };
