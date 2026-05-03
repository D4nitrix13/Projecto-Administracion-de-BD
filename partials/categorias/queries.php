<?php
// * Stored function or procedure has been executed

$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$error = null;
$success = null;

$busqueda = trim($_GET["q"] ?? "");

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

$stmtCat = $connection->prepare("
    SELECT
        id_categoria,
        nombre
    FROM buscar_categorias(:busqueda)
");

$stmtCat->execute([
    ":busqueda" => $busqueda
]);

$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
