<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

// ======================================================
// CONFIG LOGIN POSEIDON
// ======================================================
$POSEIDON_LOGIN_URL = "https://poseidon.pimb.net.br/";
$POSEIDON_RELATORIO_URL = "https://poseidon.pimb.net.br/consultas/view/83"; 
$LOGIN_CPF = "01774863928";
$LOGIN_SENHA = "cristiano017";

// ======================================================
// 1. FAZ LOGIN NO POSEIDON E PEGA O COOKIE DA SESSÃO
// ======================================================
$cookieFile = tempnam(sys_get_temp_dir(), 'cookie_');

$loginPostFields = http_build_query([
    "_method" => "POST",
    "cpf"     => $LOGIN_CPF,
    "senha"   => $LOGIN_SENHA
]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $POSEIDON_LOGIN_URL,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $loginPostFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_COOKIEJAR      => $cookieFile,
    CURLOPT_COOKIEFILE     => $cookieFile,
    CURLOPT_HEADER         => true
]);

$loginResponse = curl_exec($ch);
$httpCode      = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($loginResponse === false || $httpCode != 302) {
    echo "<div class='alert alert-danger'>Falha no login do Poseidon. HTTP: $httpCode</div>";
    exit;
}

curl_close($ch);

// ======================================================
// 2. AGORA FAZ A CONSULTA AUTENTICADA NO RELATÓRIO
// ======================================================
$ch2 = curl_init();
curl_setopt_array($ch2, [
    CURLOPT_URL            => $POSEIDON_RELATORIO_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEFILE     => $cookieFile,
    CURLOPT_COOKIEJAR      => $cookieFile
]);

$response = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

unlink($cookieFile);

// ======================================================
// 3. VALIDA CONSULTA
// ======================================================
if ($response === false || $httpCode2 !== 200) {
    echo "<div class='alert alert-danger'>
            Erro ao consultar relatório do Poseidon<br>
            HTTP: {$httpCode2}
          </div>";
    exit;
}

// ======================================================
// 4. EXIBE O HTML COMPLETO DO RELATÓRIO
// ======================================================
echo $response;
