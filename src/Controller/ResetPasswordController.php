<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TokenService;

class ResetPasswordController
{
    private TokenService $tokenService;

    public function __construct()
    {
        $this->tokenService = new TokenService();
    }

    /**
     * Validate a reset token and return user data if valid.
     * Returns null if token is invalid/expired.
     */
    public function validateToken(string $token): ?array
    {
        // Extract user ID from token payload to look up current password
        $payload = $this->decodeTokenPayload($token);
        if (!$payload) {
            return null;
        }

        $user = $this->findUserById((int) $payload["uid"]);
        if (!$user) {
            return null;
        }

        // Validate token against current password hash
        $validPayload = $this->tokenService->validate($token, $user["password"]);
        if (!$validPayload) {
            return null;
        }

        // Verify email matches
        if (!hash_equals($user["email"], $validPayload["email"])) {
            return null;
        }

        return [
            "id_usuario" => (int) $user["id_usuario"],
            "email"      => $user["email"],
            "nombre"     => $user["nombre"],
        ];
    }

    /**
     * Process the password reset form submission.
     */
    public function handleReset(string $token, string $newPassword, string $confirmPassword): array
    {
        // Validate passwords match
        if ($newPassword !== $confirmPassword) {
            return ["success" => false, "error" => "Las contraseñas no coinciden."];
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            return ["success" => false, "error" => "La contraseña debe tener al menos 8 caracteres."];
        }

        // Validate token and get user
        $user = $this->validateToken($token);
        if (!$user) {
            return ["success" => false, "error" => "El enlace no es válido o ha expirado. Solicite uno nuevo."];
        }

        // Hash new password with bcrypt (cost 12)
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ["cost" => 12]);

        // Update password in database
        $updated = $this->updatePassword($user["id_usuario"], $newHash);
        if (!$updated) {
            return ["success" => false, "error" => "Error al actualizar la contraseña. Intente nuevamente."];
        }

        // Invalidate any existing session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->logAudit($user["email"], "Contraseña cambiada exitosamente");

        return [
            "success" => true,
            "error"   => null,
            "message" => "Contraseña actualizada correctamente. Inicia sesión nuevamente.",
        ];
    }

    private function decodeTokenPayload(string $token): ?array
    {
        $parts = explode(".", $token);
        if (count($parts) !== 2) {
            return null;
        }

        $payloadB64 = $parts[0];
        $payloadJson = base64_decode(strtr($payloadB64, "-_", "+/"));
        if ($payloadJson === false) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        return is_array($payload) ? $payload : null;
    }

    private function getConnection(): \PDO
    {
        static $connection = null;
        if ($connection === null) {
            $connection = require __DIR__ . "/../../sql/db.php";
        }
        return $connection;
    }

    private function findUserById(int $userId): ?array
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("
            SELECT id_usuario, nombre, email, password
            FROM usuario
            WHERE id_usuario = :id
        ");
        $stmt->bindValue(":id", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    private function updatePassword(int $userId, string $newHash): bool
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("
            SELECT actualizar_password_usuario_login(
                :id_usuario,
                :password_hash
            ) AS actualizado
        ");
        $stmt->execute([
            ":id_usuario"    => $userId,
            ":password_hash" => $newHash,
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return isset($result["actualizado"]) && $result["actualizado"];
    }

    private function logAudit(string $email, string $description): void
    {
        try {
            $connection = $this->getConnection();
            $stmt = $connection->prepare("CALL registrar_auditoria(:usuario, :accion, :tabla, :desc)");
            $stmt->execute([
                ":usuario" => $email,
                ":accion"  => "CAMBIO_CONTRASENA",
                ":tabla"    => "usuario",
                ":desc"     => $description,
            ]);
        } catch (\Throwable $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}
