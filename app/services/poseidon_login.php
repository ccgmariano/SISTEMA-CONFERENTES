<?php
// Servidor de login do Poseidon
define('POSEIDON_LOGIN_URL', 'https://poseidon.pimb.net.br/');
define('POSEIDON_COOKIE_JAR', __DIR__ . '/poseidon_cookie.txt');

// Credenciais FIXAS por enquanto
define('POSEIDON_CPF',   '01774863928');
define('POSEIDON_SENHA', 'cristiano017');

/**
 * Faz login no Poseidon e salva o cookie de sessão CAKEPHP
 */
function poseidon_login()
{
    // Limpa cookies antigos
    @unlink(POSEIDON_COOKIE_JAR);

    $postFields = http_build_query([
        '_method'  => 'POST',
        'cpf'      => POSEIDON_CPF,
        'senha'    => POSEIDON_SENHA,
        'uuid'     => '',
        'hostname' => ''
    ]);

    $ch = curl_init(POSEIDON_LOGIN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_COOKIEJAR, POSEIDON_COOKIE_JAR);
    curl_setopt($ch, CURLOPT_COOKIEFILE, POSEIDON_COOKIE_JAR);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Login correto redireciona para /inicio
    return ($code === 200 || $code === 302);
}

/**
 * Executa um request GET autenticado
 * Se a sessão estiver expirada, reloga automaticamente
 */
function poseidon_get($url)
{
    // Se cookie não existir → login obrigatório
    if (!file_exists(POSEIDON_COOKIE_JAR)) {
        poseidon_login();
    }

    inicio:

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_COOKIEFILE, POSEIDON_COOKIE_JAR);
    curl_setopt($ch, CURLOPT_COOKIEJAR,  POSEIDON_COOKIE_JAR);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Se receber 302 de volta para login → sessão expirada → reloga
    if ($code === 302 && str_contains($response, "login")) {
        poseidon_login();
        goto inicio;
    }

    return $response;
}
