<?php

class AuditoriaRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public static function obtenerTablasRestaurables(): array
    {
        return [
            "producto" => "Productos",
            "cliente" => "Clientes",
            "categoria" => "Categorías",
            "proveedor" => "Proveedores",
        ];
    }

    public function obtenerRegistrosEliminados(array $filtros): array
    {
        $busqueda = trim($filtros["busqueda"] ?? "");
        $tablaFiltro = $filtros["tablaFiltro"] ?? "";
        $fechaDesde = $filtros["fechaDesde"] ?? "";
        $fechaHasta = $filtros["fechaHasta"] ?? "";

        $sql = "
            SELECT
                id_auditoria,
                usuario,
                accion,
                tabla_afectada,
                descripcion,
                fecha,
                registro_id,
                datos_anteriores
            FROM auditoria
            WHERE accion = 'DELETE'
              AND datos_anteriores IS NOT NULL
        ";

        $params = [];

        if ($tablaFiltro !== "") {
            $sql .= " AND tabla_afectada = :tabla";
            $params[":tabla"] = $tablaFiltro;
        }

        if ($busqueda !== "") {
            $sql .= "
                AND (
                    registro_id ILIKE :busqueda
                    OR tabla_afectada ILIKE :busqueda
                    OR usuario ILIKE :busqueda
                    OR descripcion ILIKE :busqueda
                    OR datos_anteriores::TEXT ILIKE :busqueda
                )
            ";
            $params[":busqueda"] = "%" . $busqueda . "%";
        }

        if ($fechaDesde !== "") {
            $sql .= " AND fecha::DATE >= :fecha_desde";
            $params[":fecha_desde"] = $fechaDesde;
        }

        if ($fechaHasta !== "") {
            $sql .= " AND fecha::DATE <= :fecha_hasta";
            $params[":fecha_hasta"] = $fechaHasta;
        }

        $sql .= " ORDER BY fecha DESC, id_auditoria DESC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenEliminados(): array
    {
        $sql = "
            SELECT
                COUNT(*) FILTER (WHERE accion = 'DELETE' AND datos_anteriores IS NOT NULL) AS total_eliminados,
                COUNT(*) FILTER (WHERE accion = 'DELETE' AND datos_anteriores IS NOT NULL AND tabla_afectada = 'producto') AS productos,
                COUNT(*) FILTER (WHERE accion = 'DELETE' AND datos_anteriores IS NOT NULL AND tabla_afectada = 'cliente') AS clientes,
                COUNT(*) FILTER (WHERE accion = 'DELETE' AND datos_anteriores IS NOT NULL AND tabla_afectada = 'categoria') AS categorias,
                COUNT(*) FILTER (WHERE accion = 'DELETE' AND datos_anteriores IS NOT NULL AND tabla_afectada = 'proveedor') AS proveedores
            FROM auditoria
        ";

        $statement = $this->connection->query($sql);
        $resumen = $statement->fetch(PDO::FETCH_ASSOC);

        return $resumen ?: [
            "total_eliminados" => 0,
            "productos" => 0,
            "clientes" => 0,
            "categorias" => 0,
            "proveedores" => 0,
        ];
    }

    public function obtenerRegistroAuditoria(int $idAuditoria): ?array
    {
        $sql = "
            SELECT
                id_auditoria,
                tabla_afectada,
                accion,
                registro_id,
                datos_anteriores
            FROM auditoria
            WHERE id_auditoria = :id_auditoria
              AND accion = 'DELETE'
              AND datos_anteriores IS NOT NULL
            LIMIT 1
        ";

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            ":id_auditoria" => $idAuditoria,
        ]);

        $registro = $statement->fetch(PDO::FETCH_ASSOC);

        return $registro ?: null;
    }

    public function restaurarRegistroEliminado(int $idAuditoria): void
    {
        $registro = $this->obtenerRegistroAuditoria($idAuditoria);

        if (!$registro) {
            throw new RuntimeException("No se encontró el registro eliminado o no tiene datos recuperables.");
        }

        $tabla = $registro["tabla_afectada"];

        if (!array_key_exists($tabla, self::obtenerTablasRestaurables())) {
            throw new RuntimeException("La tabla {$tabla} no está habilitada para restauración.");
        }

        $datosAnteriores = $registro["datos_anteriores"];

        $this->connection->beginTransaction();

        try {
            $sql = "
                INSERT INTO public.{$tabla}
                SELECT *
                FROM jsonb_populate_record(NULL::public.{$tabla}, :datos_anteriores::jsonb)
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute([
                ":datos_anteriores" => $datosAnteriores,
            ]);

            $update = $this->connection->prepare("
                UPDATE auditoria
                SET accion = 'RESTAURADO',
                    descripcion = COALESCE(descripcion, '') || ' | Registro restaurado desde papelera administrativa.'
                WHERE id_auditoria = :id_auditoria
            ");

            $update->execute([
                ":id_auditoria" => $idAuditoria,
            ]);

            $this->connection->commit();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw new RuntimeException(
                "No se pudo restaurar. Puede que el registro ya exista o que falte una relación requerida. Detalle: " . $exception->getMessage()
            );
        }
    }
    public function eliminarAuditoriaPermanentemente(int $idAuditoria): void
    {
        $registro = $this->obtenerRegistroAuditoria($idAuditoria);

        if (!$registro) {
            throw new RuntimeException(
                "No se encontró el registro eliminado."
            );
        }

        $this->connection->beginTransaction();

        try {
            if (($registro["tabla_afectada"] ?? "") === "producto") {
                $datos = json_decode(
                    $registro["datos_anteriores"] ?? "{}",
                    true
                );

                if (is_array($datos)) {
                    $imagen = trim((string)($datos["imagen"] ?? ""));

                    if ($imagen !== "") {
                        $rutaImagen = __DIR__ . "/../uploads/productos/" . basename($imagen);

                        if (is_file($rutaImagen)) {
                            unlink($rutaImagen);
                        }
                    }
                }
            }

            $statement = $this->connection->prepare("
            DELETE FROM auditoria
            WHERE id_auditoria = :id_auditoria
              AND accion = 'DELETE'
        ");

            $statement->execute([
                ":id_auditoria" => $idAuditoria,
            ]);

            $this->connection->commit();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw new RuntimeException(
                "No se pudo eliminar permanentemente el registro. Detalle: "
                    . $exception->getMessage()
            );
        }
    }
}
