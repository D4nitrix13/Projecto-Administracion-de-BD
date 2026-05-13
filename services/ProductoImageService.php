<?php

class ProductoImageService
{
    private string $directorioUploads;

    public function __construct(string $directorioUploads)
    {
        $this->directorioUploads = $directorioUploads;
    }

    public function procesarImagenOpcional(?array $file, ?string $imagenActual): array
    {
        if ($file === null || empty($file["name"])) {
            return [
                "success" => true,
                "message" => null,
                "imagen" => $imagenActual,
            ];
        }

        if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [
                "success" => false,
                "message" => "Error al subir la imagen.",
                "imagen" => $imagenActual,
            ];
        }

        $maxMegabytes = 10;
        $maxBytes = $maxMegabytes * 1024 * 1024;
        $maxSizeLabel = $maxMegabytes . "MB";

        if (($file["size"] ?? 0) > $maxBytes) {
            return [
                "success" => false,
                "message" => "La imagen excede el tamaño máximo permitido ({$maxSizeLabel}).",
                "imagen" => $imagenActual,
            ];
        }

        $nombreOriginal = $file["name"] ?? "";
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        $extensionesPermitidas = ["jpg", "jpeg", "png", "gif", "webp"];

        if (!in_array($extension, $extensionesPermitidas, true)) {
            return [
                "success" => false,
                "message" => "Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.",
                "imagen" => $imagenActual,
            ];
        }

        if (!is_dir($this->directorioUploads)) {
            mkdir($this->directorioUploads, 0775, true);
        }

        $nuevoNombreImagen = uniqid("prod_", true) . "." . $extension;
        $rutaDestino = $this->directorioUploads . "/" . $nuevoNombreImagen;

        if (!move_uploaded_file($file["tmp_name"], $rutaDestino)) {
            return [
                "success" => false,
                "message" => "No se pudo guardar la nueva imagen en el servidor.",
                "imagen" => $imagenActual,
            ];
        }

        $this->eliminarImagenAnterior($imagenActual);

        return [
            "success" => true,
            "message" => null,
            "imagen" => $nuevoNombreImagen,
        ];
    }

    private function eliminarImagenAnterior(?string $imagenActual): void
    {
        if (empty($imagenActual)) {
            return;
        }

        $rutaAnterior = $this->directorioUploads . "/" . $imagenActual;

        if (is_file($rutaAnterior)) {
            @unlink($rutaAnterior);
        }
    }
}
