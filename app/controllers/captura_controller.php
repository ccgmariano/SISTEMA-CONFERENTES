<?php
// app/controllers/captura_controller.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

// Precisamos ter operação e período na sessão
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<div class='alert alert-warning'>Operação ou período não encontrado na sessão.</div>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

// Pegamos os dados que realmente importam para a consulta no Poseidon
$navio  = $op['navio']   ?? '';
$inicio = $per['inicio'] ?? '';
$fim    = $per['fim']    ?? '';

// Segurança básica: se faltar algo essencial, nem tentamos consultar
if (!$navio || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta (navio / início / fim).</div>";
    exit;
}

// O Poseidon espera datas no formato "YYYY-MM-DD HH:MM"
// Se em algum ponto usarmos datetime-local (com 'T'), trocamos 'T' por espaço
$inicioFormatado = str_replace('T', ' ', $inicio);
$fimFormatado    = str_replace('T', ' ', $fim);

// URL do teste.php no conferentes.app.br
$baseUrl = "https://conferentes.app.br/teste.php";

// Só com os parâmetros necessários para a consulta
$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioFormatado,
    'termino' => $fimFormatado,
]);

$url = $baseUrl . '?' . $query;

// (Opcional) deixa a URL visível em comentário HTML para depuração futura
echo "<!-- DEBUG URL: " . htmlspecialchars($url) . " -->";

// Faz a requisição servidor -> conferentes.app.br
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// Se der erro na chamada, mostramos
if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>
            Falha ao consultar conferentes.app.br/teste.php<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// Aqui simplesmente devolvemos o HTML que o teste.php retornou
// (a tabela de pesagens). Depois, mais pra frente, podemos
// tratar/formatar/armazenar isso no nosso banco.
echo $response;
