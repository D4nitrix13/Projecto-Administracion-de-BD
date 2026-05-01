<?php

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
                    INSERT INTO Categoria (nombre)
                    VALUES (:nombre)
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

$sqlCat = "
    SELECT 
        id_categoria, 
        nombre
    FROM Categoria
    WHERE 1 = 1
";

$params = [];

if ($busqueda !== "") {
    $sqlCat .= " AND LOWER(nombre) LIKE LOWER(:q)";
    $params[":q"] = "%" . $busqueda . "%";
}

$sqlCat .= " ORDER BY nombre ASC";

$stmtCat = $connection->prepare($sqlCat);
$stmtCat->execute($params);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
