<?php
// eliminar_factura.php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    $_SESSION["flash_error"] = "Factura no válida.";
    header("Location: facturas.php");
    exit();
}

try {
    $connection->beginTransaction();

    // 1) Obtener detalles para devolver stock
    $stmtDet = $connection->prepare("
        SELECT id_producto, cantidad
        FROM DetalleFactura
        WHERE id_factura = :id
    ");
    $stmtDet->execute([":id" => $id]);
    $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

    // 2) Devolver stock de cada producto
    foreach ($detalles as $det) {
        $stmtUpd = $connection->prepare("
            UPDATE Producto
            SET stock = stock + :cantidad
            WHERE id_producto = :id_prod
        ");
        $stmtUpd->execute([
            ":cantidad" => (int)$det["cantidad"],
            ":id_prod"  => (int)$det["id_producto"],
        ]);
    }

    // 3) Borrar detalles de la factura
    $stmtDelDet = $connection->prepare("DELETE FROM DetalleFactura WHERE id_factura = :id");
    $stmtDelDet->execute([":id" => $id]);

    // 4) Borrar la factura
    $stmtDelFac = $connection->prepare("DELETE FROM Factura WHERE id_factura = :id");
    $stmtDelFac->execute([":id" => $id]);

    $connection->commit();

    $_SESSION["flash_success"] = "Factura eliminada correctamente y stock ajustado.";
} catch (PDOException $e) {
    $connection->rollBack();
    $_SESSION["flash_error"] = "No se pudo eliminar la factura: " . $e->getMessage();
}

header("Location: facturas.php");
exit();
