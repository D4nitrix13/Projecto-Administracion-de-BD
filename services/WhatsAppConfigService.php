<?php

class WhatsAppConfigService
{
    private string $archivoWhatsApp;

    public function __construct(string $archivoWhatsApp)
    {
        $this->archivoWhatsApp = $archivoWhatsApp;
    }

    public function obtenerNumeroActual(): string
    {
        if (!is_readable($this->archivoWhatsApp)) {
            return "No configurado";
        }

        $numero = trim((string)file_get_contents($this->archivoWhatsApp));

        return $numero !== "" ? $numero : "No configurado";
    }

    public function actualizarNumero(string $nuevoNumero): array
    {
        if ($nuevoNumero === "") {
            return [
                "success" => false,
                "message" => "El número no puede estar vacío.",
                "numero" => $this->obtenerNumeroActual()
            ];
        }

        if (!$this->esNumeroValido($nuevoNumero)) {
            return [
                "success" => false,
                "message" => "Formato inválido. Ejemplos válidos: +505 7696 3266, +50576963266, 50576963266.",
                "numero" => $this->obtenerNumeroActual()
            ];
        }

        $numeroLimpio = $this->limpiarNumero($nuevoNumero);

        if (file_put_contents($this->archivoWhatsApp, $numeroLimpio) === false) {
            return [
                "success" => false,
                "message" => "No se pudo guardar el nuevo número. Verifica permisos.",
                "numero" => $this->obtenerNumeroActual()
            ];
        }

        return [
            "success" => true,
            "message" => "Número de WhatsApp actualizado correctamente.",
            "numero" => $numeroLimpio
        ];
    }

    private function esNumeroValido(string $numero): bool
    {
        /*
            Formatos permitidos:

            +505 7696 3266
            +50576963266
            +505 76963266
            5057696 3266
            505 7696 3266
            7696 3266
            76963266
        */

        $regex = "/^(?:\\+?505)?\\s?\\d{4}\\s?\\d{4}$/";

        return preg_match($regex, $numero) === 1;
    }

    private function limpiarNumero(string $numero): string
    {
        return str_replace(" ", "", $numero);
    }
}
