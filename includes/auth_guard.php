<?php

require_once __DIR__ . "/../helpers/csrf.php";

function requireLogin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        csrfRequire();
    }
}

function requireAdmin(): void
{
    requireLogin();

    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    if ($idRol !== 1) {
        $_SESSION["flash_error"] = "No tienes permisos para acceder a esta sección.";
        header("Location: dashboard.php");
        exit();
    }
}
