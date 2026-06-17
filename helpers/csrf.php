<?php

declare(strict_types=1);

function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function csrfValidate(): bool
{
    $token = $_POST["_token"] ?? "";

    if ($token === "" || !hash_equals(csrfToken(), $token)) {
        return false;
    }

    return true;
}

function csrfRequire(): void
{
    if (!csrfValidate()) {
        http_response_code(403);
        die("Token CSRF inválido.");
    }
}
