<?php
// * Stored function or procedure has been executed

class ClienteRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerClientesFiltrados(string $busqueda, string $tipoFiltro): array
    {
        $tipoCliente = ($tipoFiltro === "Mayorista" || $tipoFiltro === "Detallista")
            ? $tipoFiltro
            : null;

        $statement = $this->connection->prepare("
            SELECT
                id_cliente,
                nombres,
                apellidos,
                telefono,
                direccion,
                identificacion,
                tipo_cliente
            FROM buscar_clientes_filtrados(
                :busqueda,
                :tipo_cliente
            )
        ");

        $statement->execute([
            ":busqueda" => $busqueda,
            ":tipo_cliente" => $tipoCliente,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientePorId(int $idCliente): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_cliente,
                nombres,
                apellidos,
                telefono,
                direccion,
                identificacion,
                tipo_cliente
            FROM obtener_cliente_edicion_por_id(:id_cliente)
        ");

        $statement->execute([
            ":id_cliente" => $idCliente,
        ]);

        $cliente = $statement->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function actualizarCliente(int $idCliente, array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT actualizar_cliente_sistema(
                :id_cliente,
                :nombres,
                :apellidos,
                :telefono,
                :direccion,
                :identificacion,
                :tipo_cliente
            ) AS actualizado
        ");

        $statement->execute([
            ":id_cliente" => $idCliente,
            ":nombres" => $datos["nombres"],
            ":apellidos" => $datos["apellidos"],
            ":telefono" => $datos["telefono"],
            ":direccion" => $datos["direccion"],
            ":identificacion" => $datos["identificacion"],
            ":tipo_cliente" => $datos["tipo_cliente"],
        ]);
    }

    public function obtenerClientesHabituales(): array
    {
        $statement = $this->connection->query("
            SELECT
                id_cliente,
                nombres,
                apellidos,
                telefono,
                direccion,
                identificacion,
                tipo_cliente
            FROM listar_clientes_habituales()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
