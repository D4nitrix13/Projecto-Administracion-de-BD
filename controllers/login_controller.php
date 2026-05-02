<?php

function obtenerDatosLogin(): array
{
    $error = $_SESSION["error"] ?? null;
    $success = $_SESSION["success"] ?? null;

    unset($_SESSION["error"], $_SESSION["success"]);

    return [
        "error" => $error,
        "success" => $success,
    ];
}
