<?php

declare(strict_types=1);

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private string $host;
    private int    $port;
    private string $user;
    private string $pass;
    private string $from;
    private string $fromName;

    public function __construct()
    {
        $this->host     = $_ENV["SMTP_HOST"] ?? "smtp.gmail.com";
        $this->port     = (int) ($_ENV["SMTP_PORT"] ?? 587);
        $this->user     = $_ENV["SMTP_USER"] ?? "";
        $this->pass     = $_ENV["SMTP_PASS"] ?? "";
        $this->from     = $_ENV["SMTP_FROM"] ?? "";
        $this->fromName = $_ENV["SMTP_FROM_NAME"] ?? "Panda Estampados y Kitsune";
    }

    public function sendPasswordChangedNotification(string $toEmail, string $toName): true|string
    {
        if (empty($this->from)) {
            error_log("EmailService: SMTP_FROM not configured");
            return "El servicio de correo no está configurado. Contacte al administrador.";
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->Port       = $this->port;
            $mail->CharSet    = "UTF-8";

            if (!empty($this->user)) {
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->user;
                $mail->Password   = $this->pass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = "Tu contraseña ha sido cambiada — Panda Estampados / Kitsune";

            $mail->Body    = $this->buildPasswordChangedBody();
            $mail->AltBody = $this->buildPasswordChangedPlainText();

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("EmailService: Failed to send password changed notification: " . $e->getMessage());
            return "Error al enviar el correo de notificación.";
        }
    }

    private function buildPasswordChangedBody(): string
    {
        $appName = "Panda Estampados / Kitsune";
        $now = date("d/m/Y H:i:s");
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; background: #f3f5f9; font-family: Arial, sans-serif; }
        .container { max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .header { background: #166534; padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 1.3rem; font-weight: 800; }
        .body { padding: 32px; color: #374151; line-height: 1.6; }
        .body p { margin: 0 0 16px; font-size: 0.95rem; }
        .info { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 14px 18px; font-size: 0.85rem; color: #166534; margin: 16px 0; }
        .warning { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 14px 18px; font-size: 0.85rem; color: #92400e; margin: 16px 0; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 0.8rem; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Contraseña Cambiada</h1>
        </div>
        <div class="body">
            <p><strong>¡Hola!</strong></p>
            <p>Te informamos que la contraseña de tu cuenta en <strong>{$appName}</strong> fue cambiada exitosamente.</p>
            <div class="info">
                📅 Fecha del cambio: <strong>{$now}</strong>
            </div>
            <div class="warning">
                ⚠️ Si no realizaste este cambio, contacta al administrador de inmediato.
            </div>
        </div>
        <div class="footer">
            © {$appName} — Este es un correo automático, no respondas a este mensaje.
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function buildPasswordChangedPlainText(): string
    {
        $appName = "Panda Estampados / Kitsune";
        $now = date("d/m/Y H:i:s");
        return <<<TEXT
Contraseña Cambiada — {$appName}

¡Hola!

Te informamos que la contraseña de tu cuenta fue cambiada exitosamente.

Fecha del cambio: {$now}

Si no realizaste este cambio, contacta al administrador de inmediato.

© {$appName}
TEXT;
    }

    /**
     * Send a password reset email.
     *
     * @return true on success, error message string on failure
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): true|string
    {
        if (empty($this->from)) {
            error_log("EmailService: SMTP_FROM not configured");
            return "El servicio de correo no está configurado. Contacte al administrador.";
        }

        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->Port       = $this->port;
            $mail->CharSet    = "UTF-8";

            // Only enable auth if credentials are provided (MailPit doesn't need auth)
            if (!empty($this->user)) {
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->user;
                $mail->Password   = $this->pass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Sender / recipient
            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Recuperación de contraseña — Panda Estampados / Kitsune";

            $expiresMinutes = (int) ($_ENV["RESET_PASSWORD_EXPIRES_MINUTES"] ?? 30);

            $mail->Body = $this->buildResetEmailBody($resetUrl, $expiresMinutes);
            $mail->AltBody = $this->buildResetEmailPlainText($resetUrl, $expiresMinutes);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("EmailService: Failed to send email: " . $e->getMessage());
            return "Error al enviar el correo. Intente nuevamente más tarde.";
        }
    }

    private function buildResetEmailBody(string $resetUrl, int $expiresMinutes): string
    {
        $appName = "Panda Estampados / Kitsune";
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; background: #f3f5f9; font-family: Arial, sans-serif; }
        .container { max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .header { background: #111827; padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 1.3rem; font-weight: 800; }
        .body { padding: 32px; color: #374151; line-height: 1.6; }
        .body p { margin: 0 0 16px; font-size: 0.95rem; }
        .btn { display: inline-block; padding: 14px 32px; background: #111827; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 0.95rem; margin: 8px 0 16px; }
        .warning { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 14px 18px; font-size: 0.85rem; color: #92400e; margin: 16px 0; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 0.8rem; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 Recuperación de Contraseña</h1>
        </div>
        <div class="body">
            <p><strong>¡Hola!</strong></p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>{$appName}</strong>.</p>
            <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
            <p style="text-align: center;">
                <a href="{$resetUrl}" class="btn">Restablecer mi contraseña</a>
            </p>
            <div class="warning">
                ⏱ Este enlace expira en <strong>{$expiresMinutes} minutos</strong>. Si no lo usas a tiempo, deberás solicitar uno nuevo.
            </div>
            <p style="font-size: 0.85rem; color: #6b7280;">
                Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña actual permanecerá sin cambios.
            </p>
        </div>
        <div class="footer">
            © {$appName} — Este es un correo automático, no respondas a este mensaje.
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function buildResetEmailPlainText(string $resetUrl, int $expiresMinutes): string
    {
        $appName = "Panda Estampados / Kitsune";
        return <<<TEXT
Recuperación de Contraseña — {$appName}

¡Hola!

Recibimos una solicitud para restablecer la contraseña de tu cuenta.

Copia y pega este enlace en tu navegador para crear una nueva contraseña:
{$resetUrl}

Este enlace expira en {$expiresMinutes} minutos.

Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña actual permanecerá sin cambios.

© {$appName}
TEXT;
    }
}
