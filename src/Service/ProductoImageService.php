<?php

declare(strict_types=1);

namespace App\Service;

class ProductoImageService
{
    public function __construct(private string $directorioUploads) {}

    public function procesarImagenOpcional(?array $file, ?string $imagenActual): array
    {
        if ($file === null || empty($file["name"])) {
            return ["success" => true, "message" => null, "imagen" => $imagenActual];
        }

        if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ["success" => false, "message" => "Error al subir la imagen.", "imagen" => $imagenActual];
        }

        $maxBytes = 10 * 1024 * 1024;

        if (($file["size"] ?? 0) > $maxBytes) {
            return ["success" => false, "message" => "La imagen excede 10MB.", "imagen" => $imagenActual];
        }

        $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"], true)) {
            return ["success" => false, "message" => "Formato no permitido. Usa JPG, PNG, GIF o WEBP.", "imagen" => $imagenActual];
        }

        if (!is_dir($this->directorioUploads)) {
            mkdir($this->directorioUploads, 0775, true);
        }

        $nuevoNombre = uniqid("prod_", true) . "." . $extension;
        $rutaDestino = $this->directorioUploads . "/" . $nuevoNombre;

        if (!move_uploaded_file($file["tmp_name"], $rutaDestino)) {
            return ["success" => false, "message" => "No se pudo guardar la imagen.", "imagen" => $imagenActual];
        }

        $this->eliminarImagenAnterior($imagenActual);

        return ["success" => true, "message" => null, "imagen" => $nuevoNombre];
    }

    private function eliminarImagenAnterior(?string $imagenActual): void
    {
        if (empty($imagenActual)) {
            return;
        }

        $ruta = $this->directorioUploads . "/" . $imagenActual;

        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}
