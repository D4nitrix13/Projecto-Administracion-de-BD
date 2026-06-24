<?php
// * Stored function or procedure has been executed

$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$canManageCategories) {
        $error = "Su rol no tiene permisos para crear categorías.";
    } else {
        $nombre = trim($_POST["nombre"] ?? "");

        if ($nombre === "") {
            $error = "El nombre de la categoría es obligatorio.";
        } elseif (mb_strlen($nombre) > 80) {
            $error = "El nombre de la categoría no debe superar los 80 caracteres.";
        } else {
            try {
                $stmtIns = $connection->prepare("
                    SELECT
                        id_categoria,
                        nombre
                    FROM registrar_categoria(:nombre)
                ");

                $stmtIns->execute([
                    ":nombre" => $nombre
                ]);

                $success = "Categoría registrada correctamente.";
            } catch (PDOException $e) {
                if ($e->getCode() === "23505") {
                    $error = "Ya existe una categoría con ese nombre.";
                } else {
                    error_log("crear categoria error: " . $e->getMessage());
                    $error = "Error al registrar la categoría.";
                }
            }
        }
    }
}

$ordenesPermitidosCat = [
    'nombre', 'mas_vendidos_mes', 'menos_vendidos_mes',
    'mas_vendidos_semana', 'menos_vendidos_semana',
    'mas_vendidos_anio', 'menos_vendidos_anio',
    'total_ventas', 'mas_productos', 'menos_productos', 'stock_total'
];
$filtroOrdenCat = in_array($filtroOrdenCat, $ordenesPermitidosCat) ? $filtroOrdenCat : 'nombre';

$stmtCount = $connection->prepare("
    SELECT COUNT(*) FROM Categoria c
    WHERE COALESCE(TRIM(:busqueda), '') = ''
       OR c.nombre ILIKE '%' || TRIM(:busqueda) || '%'
");

$stmtCount->execute([":busqueda" => $busqueda]);

$totalRegistros = (int) $stmtCount->fetchColumn();
$paginacion = calcularPaginacion($totalRegistros, $paginaActual, 15);

$offset = $paginacion["offset"];
$limit = $paginacion["porPagina"];

$stmtCat = $connection->prepare("
    SELECT
        id_categoria,
        nombre,
        cantidad_productos,
        stock_total,
        total_vendido
    FROM buscar_categorias(:busqueda, :limit, :offset, :orden)
");

$stmtCat->execute([
    ":busqueda" => $busqueda,
    ":limit"    => $limit,
    ":offset"   => $offset,
    ":orden"    => $filtroOrdenCat,
]);

$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$filtrosGET = array_filter([
    "q" => $busqueda ?: null,
    "orden" => $filtroOrdenCat !== 'nombre' ? $filtroOrdenCat : null,
], fn($v) => $v !== null);
