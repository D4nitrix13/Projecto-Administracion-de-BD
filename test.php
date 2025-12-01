<?php
// generar_hashes.php
// Genera hashes para password0 ... password29 usando bcrypt cost 12

for ($i = 0; $i < 30; $i++) {
    $plain = "password{$i}";
    $hash  = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

    echo "----------------------------------------\n";
    echo "Contraseña en texto plano : {$plain}\n";
    echo "Hash para usar en SQL     : {$hash}\n";
    echo "\n";
}
