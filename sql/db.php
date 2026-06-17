<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

function createConnection(): PDO
{
    $config = getDbConfig();

    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s",
        $config["host"],
        $config["port"],
        $config["database"]
    );

    try {
        return new PDO(
            $dsn,
            $config["username"],
            $config["password"],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        error_log("Error de conexión a BD: " . $e->getMessage());
        die("Error al conectar con la base de datos.");
    }
}

function connect(): PDO
{
    return createConnection();
}

return createConnection();
