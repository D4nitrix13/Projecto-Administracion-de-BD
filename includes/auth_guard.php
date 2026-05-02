<?php

function requireLogin(): void
{
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
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
