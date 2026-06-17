<?php

declare(strict_types=1);

namespace App\Service;

class WhatsAppConfigService
{
    public function __construct(private string $archivoWhatsApp) {}

    public function obtenerNumeroActual(): string
    {
        if (!is_readable($this->archivoWhatsApp)) {
            return "No configurado";
        }

        $numero = trim((string) file_get_contents($this->archivoWhatsApp));

        return $numero !== "" ? $numero : "No configurado";
    }

    public function actualizarNumero(string $nuevoNumero): array
    {
        if ($nuevoNumero === "") {
            return ["success" => false, "message" => "El número no puede estar vacío.", "numero" => $this->obtenerNumeroActual()];
        }

        if (!$this->esNumeroValido($nuevoNumero)) {
            return ["success" => false, "message" => "Formato inválido. Ej: +505 7696 3266.", "numero" => $this->obtenerNumeroActual()];
        }

        $numeroLimpio = str_replace(" ", "", $nuevoNumero);

        if (file_put_contents($this->archivoWhatsApp, $numeroLimpio) === false) {
            return ["success" => false, "message" => "No se pudo guardar. Verifica permisos.", "numero" => $this->obtenerNumeroActual()];
        }

        return ["success" => true, "message" => "Número actualizado correctamente.", "numero" => $numeroLimpio];
    }

    private function esNumeroValido(string $numero): bool
    {
        return preg_match("/^(?:\\+?505)?\\s?\\d{4}\\s?\\d{4}$/", $numero) === 1;
    }
}
