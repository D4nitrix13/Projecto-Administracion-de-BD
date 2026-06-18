<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TokenService;
use App\Service\EmailService;

class ForgotPasswordController
{
    private TokenService  $tokenService;
    private EmailService  $emailService;

    public function __construct()
    {
        $this->tokenService = new TokenService();
        $this->emailService = new EmailService();
    }

    /**
     * Handle the forgot password form submission.
     * Always returns the same generic message for security.
     */
    public function handleRequest(): array
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ["error" => null, "success" => null, "sent" => false];
        }

        csrfRequire();

        $email = trim($_POST["email"] ?? "");

        // Basic validation
        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Still show generic message — don't reveal if email exists
            return ["error" => null, "success" => $this->getGenericMessage(), "sent" => true];
        }

        // Look up user by email
        $user = $this->findUserByEmail($email);

        if ($user) {
            $this->sendResetEmail($user);
            $this->logAudit($user["email"], "Solicitud de recuperación de contraseña");
        }

        // Always show the same message regardless of whether user exists
        return ["error" => null, "success" => $this->getGenericMessage(), "sent" => true];
    }

    private function getConnection(): \PDO
    {
        static $connection = null;
        if ($connection === null) {
            $connection = require __DIR__ . "/../../sql/db.php";
        }
        return $connection;
    }

    private function findUserByEmail(string $email): ?array
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("
            SELECT id_usuario, nombre, email, password
            FROM obtener_usuario_login(:email)
        ");
        $stmt->bindValue(":email", $email, \PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    private function sendResetEmail(array $user): void
    {
        $token = $this->tokenService->generate(
            (int) $user["id_usuario"],
            $user["email"],
            $user["password"]
        );

        // Build the reset URL
        $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
        $host   = $_SERVER["HTTP_HOST"] ?? "localhost:8080";
        $resetUrl = $scheme . "://" . $host . "/reset_password.php?token=" . $token;

        $this->emailService->sendPasswordReset(
            $user["email"],
            $user["nombre"],
            $resetUrl
        );
    }

    private function getGenericMessage(): string
    {
        return "Si el correo existe en el sistema, se enviará un enlace de recuperación. Revise su bandeja de entrada y carpeta de spam.";
    }

    private function logAudit(string $email, string $description): void
    {
        try {
            $connection = $this->getConnection();
            $stmt = $connection->prepare("CALL registrar_auditoria(:usuario, :accion, :tabla, :desc)");
            $stmt->execute([
                ":usuario" => $email,
                ":accion"  => "RECUPERACION_CONTRASENA",
                ":tabla"    => "usuario",
                ":desc"     => $description,
            ]);
        } catch (\Throwable $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}
