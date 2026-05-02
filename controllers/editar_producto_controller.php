<?php

require_once __DIR__ . "/../repositories/ProductoRepository.php";
require_once __DIR__ . "/../repositories/CategoriaRepository.php";
require_once __DIR__ . "/../repositories/ProveedorRepository.php";
require_once __DIR__ . "/../services/ProductoImageService.php";

function obtenerDatosEditarProducto(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $productoRepository = new ProductoRepository($connection);
    $categoriaRepository = new CategoriaRepository($connection);
    $proveedorRepository = new ProveedorRepository($connection);

    $imageService = new ProductoImageService(
        __DIR__ . "/../uploads/productos"
    );

    $error = null;

    $id = obtenerIdProductoDesdeRequest();

    if ($id <= 0) {
        $_SESSION["flash_error"] = "Producto no válido.";
        header("Location: productos.php");
        exit();
    }

    $producto = $productoRepository->obtenerProductoPorId($id);

    if (!$producto) {
        $_SESSION["flash_error"] = "El producto especificado no existe.";
        header("Location: productos.php");
        exit();
    }

    $categorias = $categoriaRepository->obtenerCategoriasOrdenadas();
    $proveedores = $proveedorRepository->obtenerProveedoresOrdenados();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $datos = obtenerDatosProductoDesdePost();

        $error = validarDatosProducto($datos);

        $nombreImagenBD = $producto["imagen"] ?? null;

        if ($error === null) {
            $resultadoImagen = $imageService->procesarImagenOpcional(
                $_FILES["imagen"] ?? null,
                $nombreImagenBD
            );

            if (!$resultadoImagen["success"]) {
                $error = $resultadoImagen["message"];
            } else {
                $nombreImagenBD = $resultadoImagen["imagen"];
            }
        }

        if ($error === null) {
            try {
                $datos["imagen"] = $nombreImagenBD;

                $productoRepository->actualizarProducto($id, $datos);

                $_SESSION["flash_success"] = "Producto actualizado correctamente.";

                header("Location: productos.php");
                exit();
            } catch (PDOException $exception) {
                if ($exception->getCode() === "23505") {
                    $error = "Ya existe otro producto con ese código.";
                } else {
                    $error = "Error al actualizar el producto: " . $exception->getMessage();
                }
            }
        }

        /*
            Si hubo error, mantenemos en pantalla los datos que el usuario intentó guardar.
        */
        $producto = array_merge($producto, [
            "codigo" => $datos["codigo"],
            "nombre" => $datos["nombre"],
            "descripcion" => $datos["descripcion"],
            "id_categoria" => $datos["id_categoria"],
            "id_proveedor" => $datos["id_proveedor"],
            "precio_compra" => $datos["precio_compra"],
            "precio_venta" => $datos["precio_venta"],
            "stock" => $datos["stock"],
            "imagen" => $nombreImagenBD,
        ]);
    }

    return [
        "user" => $user,
        "error" => $error,
        "producto" => $producto,
        "categorias" => $categorias,
        "proveedores" => $proveedores,
    ];
}

function obtenerIdProductoDesdeRequest(): int
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        return (int)($_POST["id_producto"] ?? 0);
    }

    return (int)($_GET["id"] ?? 0);
}

function obtenerDatosProductoDesdePost(): array
{
    return [
        "codigo" => trim($_POST["codigo"] ?? ""),
        "nombre" => trim($_POST["nombre"] ?? ""),
        "descripcion" => trim($_POST["descripcion"] ?? ""),
        "id_categoria" => ($_POST["id_categoria"] ?? "") !== "" ? (int)$_POST["id_categoria"] : null,
        "id_proveedor" => ($_POST["id_proveedor"] ?? "") !== "" ? (int)$_POST["id_proveedor"] : null,
        "precio_compra" => trim($_POST["precio_compra"] ?? ""),
        "precio_venta" => trim($_POST["precio_venta"] ?? ""),
        "stock" => trim($_POST["stock"] ?? ""),
    ];
}

function validarDatosProducto(array $datos): ?string
{
    if (
        $datos["codigo"] === "" ||
        $datos["nombre"] === "" ||
        $datos["precio_compra"] === "" ||
        $datos["precio_venta"] === ""
    ) {
        return "Complete los campos obligatorios marcados con (*).";
    }

    if (!is_numeric($datos["precio_compra"]) || !is_numeric($datos["precio_venta"])) {
        return "Los precios deben ser valores numéricos.";
    }

    if ((float)$datos["precio_compra"] < 0 || (float)$datos["precio_venta"] < 0) {
        return "Los precios no pueden ser negativos.";
    }

    if ($datos["stock"] === "" || !ctype_digit((string)$datos["stock"])) {
        return "El stock debe ser un número entero mayor o igual a 0.";
    }

    return null;
}
