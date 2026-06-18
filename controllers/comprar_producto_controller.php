<?php

require_once __DIR__ . "/../repositories/ProductoRepository.php";
require_once __DIR__ . "/../repositories/CompraRepository.php";

function obtenerDatosComprarProducto(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $productoRepo = new ProductoRepository($connection);
    $compraRepo   = new CompraRepository($connection);

    $idProducto = (int)($_GET["id"] ?? 0);
    $error      = null;
    $success    = null;

    if ($idProducto <= 0) {
        header("Location: catalogo.php");
        exit();
    }

    $producto = $productoRepo->obtenerProductoPorId($idProducto);

    if (!$producto) {
        $_SESSION["flash_error"] = "El producto especificado no existe.";
        header("Location: catalogo.php");
        exit();
    }

    $proveedores = $compraRepo->obtenerProveedores();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = procesarCompraProducto($connection, $producto, $user, $_POST);

        if ($resultado["success"]) {
            $success = $resultado["message"];
            $producto = $productoRepo->obtenerProductoPorId($idProducto);
        } else {
            $error = $resultado["message"];
        }
    }

    return [
        "user"        => $user,
        "producto"    => $producto,
        "proveedores" => $proveedores,
        "error"       => $error,
        "success"     => $success,
    ];
}

function procesarCompraProducto(
    \PDO $connection,
    array $producto,
    array $user,
    array $post
): array {
    $idProveedor  = (int)($post["id_proveedor"] ?? 0);
    $cantidad     = (int)($post["cantidad"] ?? 0);
    $costoUnitario = (float)($post["costo_unitario"] ?? 0);

    if ($idProveedor <= 0) {
        return ["success" => false, "message" => "Debe seleccionar un proveedor."];
    }

    if ($cantidad <= 0) {
        return ["success" => false, "message" => "La cantidad debe ser mayor a cero."];
    }

    if ($costoUnitario < 0) {
        return ["success" => false, "message" => "El costo unitario no puede ser negativo."];
    }

    try {
        $connection->beginTransaction();

        $stmt = $connection->prepare("
            INSERT INTO Compra (id_proveedor, id_usuario, total)
            VALUES (:id_proveedor, :id_usuario, 0)
            RETURNING id_compra
        ");

        $stmt->execute([
            ":id_proveedor" => $idProveedor,
            ":id_usuario"   => (int)$user["id_usuario"],
        ]);

        $idCompra = (int)$stmt->fetchColumn();

        $connection->prepare("CALL agregar_detalle_compra(:id_compra, :id_producto, :cantidad, :costo)")->execute([
            ":id_compra"   => $idCompra,
            ":id_producto" => (int)$producto["id_producto"],
            ":cantidad"    => $cantidad,
            ":costo"       => $costoUnitario,
        ]);

        $connection->prepare("CALL actualizar_total_compra(:id_compra)")->execute([
            ":id_compra" => $idCompra,
        ]);

        $connection->prepare("CALL aumentar_stock_producto(:id_producto, :cantidad)")->execute([
            ":id_producto" => (int)$producto["id_producto"],
            ":cantidad"    => $cantidad,
        ]);

        $connection->commit();

        $total = $cantidad * $costoUnitario;

        return [
            "success" => true,
            "message" => "Compra registrada. Se agregaron {$cantidad} unidades al stock de {$producto['nombre']}. Total: C$" . number_format($total, 2),
        ];
    } catch (\PDOException $e) {
        $connection->rollBack();
        return ["success" => false, "message" => "Error al registrar la compra: " . $e->getMessage()];
    }
}
