<?php
require_once "./sql/config.php";

function connect(
    string $dsnDb,
    string $hostDb,
    string $portDb,
    string $nameDb,
    string $userDb,
    string $passwordDb
): PDO {
    try {
        $pdo = new PDO(
            "pgsql:host={$hostDb};port={$portDb};dbname={$nameDb};",
            $userDb,
            $passwordDb,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        if ($pdo) return $pdo;
    } catch (PDOException $e) {
        echo ("[x] Error Database $e!" . PHP_EOL);
        die($e->getMessage());
    }
}

return connect($dsnDb, $hostDb, $portDb, $nameDb, $userDb, $passwordDb);
