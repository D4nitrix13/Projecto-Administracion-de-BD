<?php

declare(strict_types=1);

if (!defined('BOOTSTRAP_LOADED')) {
    require_once __DIR__ . "/../bootstrap.php";
}

function getDbConfig(): array
{
    return [
        "host"     => $_ENV["DB_HOST"] ?? "localhost",
        "port"     => $_ENV["DB_PORT"] ?? "5432",
        "database" => $_ENV["DB_DATABASE"] ?? "pandas_estampados_y_kitsune",
        "username" => $_ENV["DB_USERNAME"] ?? "postgres",
        "password" => $_ENV["DB_PASSWORD"] ?? "",
    ];
}
