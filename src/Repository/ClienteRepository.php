<?php

declare(strict_types=1);

namespace App\Repository;

final class ClienteRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerClientesFiltrados(string $busqueda, ?string $tipoFiltro, int $pagina = 1, int $porPagina = 15): array
    {
        $tipoCliente = ($tipoFiltro === "Mayorista" || $tipoFiltro === "Detallista")
            ? $tipoFiltro
            : null;

        $offset = ($pagina - 1) * $porPagina;

        $statement = $this->connection->prepare("
            SELECT
                id_cliente, nombres, apellidos, telefono,
                direccion, identificacion, tipo_cliente
            FROM buscar_clientes_filtrados(:busqueda, :tipo_cliente, :limit, :offset)
        ");

        $statement->execute([
            ":busqueda"     => $busqueda,
            ":tipo_cliente" => $tipoCliente,
            ":limit"        => $porPagina,
            ":offset"       => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarClientesFiltrados(string $busqueda, ?string $tipoFiltro): int
    {
        $tipoCliente = ($tipoFiltro === "Mayorista" || $tipoFiltro === "Detallista")
            ? $tipoFiltro
            : null;

        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM buscar_clientes_filtrados(:busqueda, :tipo_cliente, 999999, 0)
        ");

        $statement->execute([
            ":busqueda"     => $busqueda,
            ":tipo_cliente" => $tipoCliente,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function obtenerClientePorId(int $idCliente): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_cliente, nombres, apellidos, telefono,
                direccion, identificacion, tipo_cliente
            FROM obtener_cliente_edicion_por_id(:id_cliente)
        ");

        $statement->execute([":id_cliente" => $idCliente]);
        $cliente = $statement->fetch(\PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function actualizarCliente(int $idCliente, array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT actualizar_cliente_sistema(
                :id_cliente, :nombres, :apellidos, :telefono,
                :direccion, :identificacion, :tipo_cliente
            ) AS actualizado
        ");

        $statement->execute([
            ":id_cliente"    => $idCliente,
            ":nombres"       => $datos["nombres"],
            ":apellidos"     => $datos["apellidos"],
            ":telefono"      => $datos["telefono"],
            ":direccion"     => $datos["direccion"],
            ":identificacion" => $datos["identificacion"],
            ":tipo_cliente"  => $datos["tipo_cliente"],
        ]);
    }

    public function obtenerClientesHabituales(): array
    {
        $statement = $this->connection->query("
            SELECT
                id_cliente, nombre, telefono, identificacion, tipo_cliente
            FROM listar_clientes_habituales()
            ORDER BY nombre ASC
        ");

        $clientes = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(static function (array $cliente): array {
            $nombreCompleto = trim((string) ($cliente["nombre"] ?? ""));
            $partesNombre = preg_split('/\s+/', $nombreCompleto) ?: [];

            $nombres = $nombreCompleto;
            $apellidos = "";

            if (count($partesNombre) >= 2) {
                $nombres = array_shift($partesNombre);
                $apellidos = implode(" ", $partesNombre);
            }

            return [
                "id_cliente"    => (int) $cliente["id_cliente"],
                "nombres"       => $nombres,
                "apellidos"     => $apellidos,
                "nombre"        => $nombreCompleto,
                "telefono"      => $cliente["telefono"] ?? "",
                "direccion"     => "",
                "identificacion" => $cliente["identificacion"] ?? "",
                "tipo_cliente"  => $cliente["tipo_cliente"] ?? "",
            ];
        }, $clientes);
    }
}
