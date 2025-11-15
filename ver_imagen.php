<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("ID de producto no válido.");
}

$stmt = $connection->prepare("
    SELECT nombre, descripcion, imagen
    FROM Producto
    WHERE id_producto = :id
");
$stmt->execute([":id" => $id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prod) {
    die("Producto no encontrado.");
}

?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<style>
    .imagen-container {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .imagen-container img {
        max-width: 100%;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .prod-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .prod-desc {
        font-size: 1rem;
        color: #555;
        margin-bottom: 25px;
    }

    .back-btn {
        display: inline-block;
        padding: 10px 20px;
        background: #2563eb;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
    }

    .back-btn:hover {
        background: #1e40af;
    }
</style>

<body class="page-bg">

    <?php include "partials/navbar.php"; ?>

    <div class="imagen-container">
        <h2 class="prod-title"><?= htmlspecialchars($prod["nombre"]) ?></h2>

        <img src="uploads/productos/<?= htmlspecialchars($prod["imagen"]) ?>" alt="Producto">

        <p class="prod-desc">
            <?= nl2br(htmlspecialchars($prod["descripcion"] ?: "Sin descripción")) ?>
        </p>

        <a href="productos.php" class="back-btn">← Volver al listado</a>
    </div>

</body>

</html>