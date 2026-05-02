<?php

function obtenerDatosConfigurarCuenta(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $error = null;
    $success = null;

    $dbUser = obtenerUsuarioCuentaActual($connection, (int)$user["id_usuario"]);

    if (!$dbUser) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $nombre = $dbUser["nombre"];
    $email = $dbUser["email"];
    $seccionTexto = obtenerTextoSeccionCuenta($dbUser);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = actualizarCuentaDesdePost($connection, $dbUser, $_POST);

        if ($resultado["success"]) {
            $success = "Datos de cuenta actualizados correctamente.";

            $nombre = $resultado["nombre"];
            $email = $resultado["email"];

            $_SESSION["user"]["nombre"] = $nombre;
            $_SESSION["user"]["email"] = $email;
        } else {
            $error = $resultado["message"];

            $nombre = trim($_POST["nombre"] ?? $nombre);
            $email = trim($_POST["email"] ?? $email);
        }
    }

    return [
        "user" => $_SESSION["user"],
        "error" => $error,
        "success" => $success,
        "nombre" => $nombre,
        "email" => $email,
        "seccionTexto" => $seccionTexto,
    ];
}

function obtenerUsuarioCuentaActual(PDO $connection, int $idUsuario): ?array
{
    $statement = $connection->prepare("
        SELECT 
            u.id_usuario,
            u.nombre,
            u.email,
            u.password,
            u.id_rol,
            u.id_seccion,
            r.nombre AS rol_nombre,
            s.nombre AS seccion_nombre
        FROM Usuario u
        INNER JOIN Rol r ON r.id_rol = u.id_rol
        LEFT JOIN Seccion s ON s.id_seccion = u.id_seccion
        WHERE u.id_usuario = :id_usuario
    ");

    $statement->execute([
        ":id_usuario" => $idUsuario,
    ]);

    $dbUser = $statement->fetch(PDO::FETCH_ASSOC);

    return $dbUser ?: null;
}

function obtenerTextoSeccionCuenta(array $dbUser): string
{
    if ($dbUser["id_seccion"] === null) {
        return "Administrador general de todas las secciones";
    }

    return $dbUser["seccion_nombre"] ?: "Sección asignada";
}

function actualizarCuentaDesdePost(
    PDO $connection,
    array $dbUser,
    array $post
): array {
    $nombre = trim($post["nombre"] ?? "");
    $email = trim($post["email"] ?? "");

    $passwordActual = $post["password_actual"] ?? "";
    $passwordNueva = $post["password_nueva"] ?? "";
    $passwordConfirm = $post["password_confirm"] ?? "";

    $error = validarDatosCuenta(
        $nombre,
        $email,
        $passwordActual,
        $passwordNueva,
        $passwordConfirm,
        $dbUser["password"]
    );

    if ($error !== null) {
        return [
            "success" => false,
            "message" => $error,
            "nombre" => $nombre,
            "email" => $email,
        ];
    }

    $passwordHashFinal = $dbUser["password"];

    $quiereCambiarPassword = (
        $passwordActual !== "" ||
        $passwordNueva !== "" ||
        $passwordConfirm !== ""
    );

    if ($quiereCambiarPassword) {
        $passwordHashFinal = password_hash(
            $passwordNueva,
            PASSWORD_BCRYPT,
            ["cost" => 12]
        );
    }

    try {
        $statement = $connection->prepare("
            UPDATE Usuario
            SET 
                nombre = :nombre,
                email = :email,
                password = :password
            WHERE id_usuario = :id_usuario
        ");

        $statement->execute([
            ":nombre" => $nombre,
            ":email" => $email,
            ":password" => $passwordHashFinal,
            ":id_usuario" => (int)$dbUser["id_usuario"],
        ]);

        return [
            "success" => true,
            "message" => null,
            "nombre" => $nombre,
            "email" => $email,
        ];
    } catch (PDOException $exception) {
        if ($exception->getCode() === "23505") {
            return [
                "success" => false,
                "message" => "Ya existe un usuario registrado con ese correo electrónico.",
                "nombre" => $nombre,
                "email" => $email,
            ];
        }

        return [
            "success" => false,
            "message" => "Error al actualizar la cuenta: " . $exception->getMessage(),
            "nombre" => $nombre,
            "email" => $email,
        ];
    }
}

function validarDatosCuenta(
    string $nombre,
    string $email,
    string $passwordActual,
    string $passwordNueva,
    string $passwordConfirm,
    string $passwordHashActual
): ?string {
    if ($nombre === "") {
        return "El nombre no puede estar vacío.";
    }

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Debe ingresar un correo electrónico válido.";
    }

    $quiereCambiarPassword = (
        $passwordActual !== "" ||
        $passwordNueva !== "" ||
        $passwordConfirm !== ""
    );

    if (!$quiereCambiarPassword) {
        return null;
    }

    if ($passwordActual === "" || $passwordNueva === "" || $passwordConfirm === "") {
        return "Para cambiar la contraseña debe completar todos los campos de contraseña.";
    }

    if (!password_verify($passwordActual, $passwordHashActual)) {
        return "La contraseña actual no es correcta.";
    }

    if (strlen($passwordNueva) < 8) {
        return "La nueva contraseña debe tener al menos 8 caracteres.";
    }

    if ($passwordNueva !== $passwordConfirm) {
        return "La confirmación de contraseña no coincide.";
    }

    return null;
}
