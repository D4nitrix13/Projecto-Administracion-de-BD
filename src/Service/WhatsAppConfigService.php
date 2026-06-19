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

        $numeroLimpio = $this->normalizarFormato($nuevoNumero);

        if (file_put_contents($this->archivoWhatsApp, $numeroLimpio) === false) {
            return ["success" => false, "message" => "No se pudo guardar. Verifica permisos.", "numero" => $this->obtenerNumeroActual()];
        }

        return ["success" => true, "message" => "Número actualizado correctamente.", "numero" => $numeroLimpio];
    }

    private function esNumeroValido(string $numero): bool
    {
        $sinEspacios = str_replace(" ", "", $numero);
        return preg_match("/^\\+?505\\d{8}$/", $sinEspacios) === 1;
    }

    private function normalizarFormato(string $numero): string
    {
        $limpio = preg_replace("/[^0-9+]/", "", $numero);

        if (str_starts_with($limpio, "+")) {
            $limpio = substr($limpio, 1);
        }

        if (strlen($limpio) === 10 && str_starts_with($limpio, "505")) {
            $digitos = substr($limpio, 3);
        } elseif (strlen($limpio) === 8) {
            $digitos = $limpio;
        } else {
            $digitos = $limpio;
        }

        if (strlen($digitos) === 8) {
            return "+505 " . substr($digitos, 0, 4) . " " . substr($digitos, 4, 4);
        }

        return $limpio;
    }
}
