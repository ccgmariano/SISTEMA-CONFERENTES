<?php
$path = '/var/data/db/teste.txt';

$result = @file_put_contents($path, 'ok');

if ($result === false) {
    echo 'FALHOU';
} else {
    echo 'GRAVOU';
}
