#!/usr/bin/env php
<?php
/**
 * Script CLI: Genera notificaciones de inventario
 * Ejecutar: php scripts/notificaciones_inventario.php
 * Cron: every 30 minutes via crontab
 */

require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../helpers/notificaciones.php";
require_once __DIR__ . "/../src/Service/NotificacionService.php";
require_once __DIR__ . "/../src/Repository/NotificacionRepository.php";

use App\Service\NotificacionService;

$connection = require __DIR__ . "/../sql/db.php";

$service = new NotificacionService();

// 1. Low stock products
$stmt = $connection->query("SELECT * FROM obtener_productos_stock_bajo()");
$bajoStock = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($bajoStock as $producto) {
    $existe = $service->existeNotificacionHoy("stock_bajo", (int)$producto["id_producto"]);
    if (!$existe) {
        notificar(
            tipo: "stock_bajo",
            titulo: "Stock bajo: " . $producto["producto"],
            mensaje: sprintf(
                "El producto %s (%s) tiene solo %d unidades en stock. Categoría: %s.",
                $producto["producto"],
                $producto["codigo"],
                $producto["stock_actual"],
                $producto["nombre_categoria"] ?? "Sin categoría"
            ),
            opts: ["id_usuario_destino" => 0, "metadata" => ["id_producto" => (int)$producto["id_producto"]]]
        );
    }
}

// 2. Least sold products with stock (rotation alert)
$stmt = $connection->query("SELECT * FROM obtener_productos_menos_vendidos_mes()");
$menosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($menosVendidos as $producto) {
    if ((int)$producto["stock_actual"] > 5 && (int)$producto["cantidad_vendida"] === 0) {
        $existe = $service->existeNotificacionHoy("producto_sin_rotacion", (int)$producto["id_producto"]);
        if (!$existe) {
            notificar(
                tipo: "producto_sin_rotacion",
                titulo: "Sin rotación: " . $producto["producto"],
                mensaje: sprintf(
                    "El producto %s (%s) tiene %d unidades en stock pero 0 ventas este mes.",
                    $producto["producto"],
                    $producto["codigo"],
                    $producto["stock_actual"]
                ),
                opts: ["id_usuario_destino" => 0, "metadata" => ["id_producto" => (int)$producto["id_producto"]]]
            );
        }
    }
}

// 3. Categories with few products
$stmt = $connection->query("SELECT * FROM obtener_categorias_menos_productos()");
$categoriasDebiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($categoriasDebiles as $cat) {
    if ((int)$cat["cantidad_productos"] <= 3) {
        $existe = $service->existeNotificacionHoyCategoria("categoria_debil", (int)$cat["id_categoria"]);
        if (!$existe) {
            notificar(
                tipo: "categoria_debil",
                titulo: "Categoría débil: " . $cat["categoria"],
                mensaje: sprintf(
                    "La categoría %s tiene solo %d productos y %d unidades en stock total.",
                    $cat["categoria"],
                    $cat["cantidad_productos"],
                    $cat["stock_total"]
                ),
                opts: ["id_usuario_destino" => 0, "metadata" => ["id_categoria" => (int)$cat["id_categoria"]]]
            );
        }
    }
}

echo "Notificaciones de inventario procesadas: " . date("Y-m-d H:i:s") . "\n";
