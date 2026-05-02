<?php

class ClienteRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerClientesFiltrados(string $busqueda, string $tipoFiltro): array
    {
        $sql = "
        SELECT 
            id_cliente,
            nombres,
            apellidos,
            telefono,
            direccion,
            identificacion,
            tipo_cliente
        FROM Cliente
        WHERE 1 = 1
    ";

        $params = [];

        if ($busqueda !== "") {
            $busquedaLimpia = preg_replace('/\s+/', ' ', trim($busqueda));

            if (ctype_digit($busquedaLimpia)) {
                $sql .= "
                AND (
                    id_cliente = :id_cliente
                    OR telefono ILIKE :q
                    OR identificacion ILIKE :q
                )
            ";

                $params[":id_cliente"] = (int)$busquedaLimpia;
                $params[":q"] = "%" . $busquedaLimpia . "%";
            } else {
                $palabras = preg_split('/\s+/', $busquedaLimpia);

                foreach ($palabras as $index => $palabra) {
                    $palabra = trim($palabra);

                    if ($palabra === "") {
                        continue;
                    }

                    $param = ":q" . $index;

                    $sql .= "
                    AND (
                        nombres ILIKE {$param}
                        OR apellidos ILIKE {$param}
                        OR telefono ILIKE {$param}
                        OR direccion ILIKE {$param}
                        OR identificacion ILIKE {$param}
                        OR tipo_cliente ILIKE {$param}
                        OR CONCAT_WS(' ', nombres, apellidos) ILIKE {$param}
                        OR CONCAT_WS(' ', apellidos, nombres) ILIKE {$param}
                        OR CONCAT_WS(' ', nombres, apellidos, telefono, direccion, identificacion, tipo_cliente) ILIKE {$param}
                    )
                ";

                    $params[$param] = "%" . $palabra . "%";
                }
            }
        }

        if ($tipoFiltro === "Mayorista" || $tipoFiltro === "Detallista") {
            $sql .= " AND tipo_cliente = :tipo";
            $params[":tipo"] = $tipoFiltro;
        }

        $sql .= " ORDER BY id_cliente DESC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

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
        FROM Cliente
        WHERE id_cliente = :id_cliente
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
        UPDATE Cliente
        SET 
            nombres = :nombres,
            apellidos = :apellidos,
            telefono = :telefono,
            direccion = :direccion,
            identificacion = :identificacion,
            tipo_cliente = :tipo_cliente
        WHERE id_cliente = :id_cliente
    ");

        $statement->execute([
            ":nombres" => $datos["nombres"],
            ":apellidos" => $datos["apellidos"],
            ":telefono" => $datos["telefono"],
            ":direccion" => $datos["direccion"],
            ":identificacion" => $datos["identificacion"],
            ":tipo_cliente" => $datos["tipo_cliente"],
            ":id_cliente" => $idCliente,
        ]);
    }
}
