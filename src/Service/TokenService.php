<?php

declare(strict_types=1);

namespace App\Service;

class TokenService
{
    private string $secret;
    private int $expiresMinutes;

    public function __construct()
    {
        $this->secret = $_ENV["RESET_PASSWORD_SECRET"] ?? "";
        $this->expiresMinutes = (int) ($_ENV["RESET_PASSWORD_EXPIRES_MINUTES"] ?? 30);
    }

    /**
     * Generate a signed reset password token.
     * Token format: base64url(payload).hmac_signature
     */
    public function generate(int $userId, string $email, string $passwordHash): string
    {
        $payload = [
            "uid"   => $userId,
            "email" => $email,
            "exp"   => time() + ($this->expiresMinutes * 60),
            "pwdv"  => $this->derivePasswordVersion($passwordHash),
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $payloadB64  = $this->base64UrlEncode($payloadJson);

        $signature = hash_hmac("sha256", $payloadB64, $this->secret);

        return $payloadB64 . "." . $signature;
    }

    /**
     * Validate a token and return the decoded payload, or null if invalid.
     */
    public function validate(string $token, string $currentPasswordHash): ?array
    {
        $parts = explode(".", $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$payloadB64, $signature] = $parts;

        // Verify signature using constant-time comparison
        $expectedSignature = hash_hmac("sha256", $payloadB64, $this->secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        if ($payloadJson === false) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }

        // Validate required fields
        if (empty($payload["uid"]) || empty($payload["email"]) || empty($payload["exp"])) {
            return null;
        }

        // Check expiration
        if (!is_int($payload["exp"]) || $payload["exp"] < time()) {
            return null;
        }

        // Verify password version matches (invalidates link after password change)
        $expectedPwdv = $this->derivePasswordVersion($currentPasswordHash);
        if (!hash_equals($expectedPwdv, $payload["pwdv"] ?? "")) {
            return null;
        }

        return $payload;
    }

    /**
     * Derive a short version identifier from the password hash.
     * This ensures the token becomes invalid if the password changes.
     */
    private function derivePasswordVersion(string $passwordHash): string
    {
        return hash_hmac("sha256", substr($passwordHash, 0, 32), $this->secret);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    private function base64UrlDecode(string $data): string|false
    {
        return base64_decode(strtr($data, "-_", "+/"));
    }
}
