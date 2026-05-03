<?php
// * Stored function or procedure has been executed

$error = null;

$id = $_SERVER["REQUEST_METHOD"] === "POST"
    ? (int) ($_POST["id_categoria"] ?? 0)
    : (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["flash_error"] = "Categoría no válida.";
    header("Location: categorias.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre === "") {
        $error = "El nombre de la categoría es obligatorio.";
    } elseif (mb_strlen($nombre) > 80) {
        $error = "El nombre de la categoría no debe superar los 80 caracteres.";
    } else {
        try {
            $stmtUpd = $connection->prepare("
                SELECT filas_afectadas
                FROM actualizar_categoria(:id, :nombre)
            ");

            $stmtUpd->execute([
                ":id" => $id,
                ":nombre" => $nombre
            ]);

            $resultado = $stmtUpd->fetch(PDO::FETCH_ASSOC);
            $filasAfectadas = (int) ($resultado["filas_afectadas"] ?? 0);

            $_SESSION["flash_success"] = $filasAfectadas > 0
                ? "Categoría actualizada correctamente."
                : "No se realizaron cambios.";

            header("Location: categorias.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe otra categoría con ese nombre.";
            } else {
                error_log("editar categoria error: " . $e->getMessage());
                $error = "Error al actualizar la categoría.";
            }
        }
    }
}

$stmtCat = $connection->prepare("
    SELECT
        id_categoria,
        nombre
    FROM obtener_categoria_por_id(:id)
");

$stmtCat->execute([
    ":id" => $id
]);

$categoria = $stmtCat->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    $_SESSION["flash_error"] = "La categoría especificada no existe.";
    header("Location: categorias.php");
    exit();
}
