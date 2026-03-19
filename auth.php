<?php
session_start();

// limpiar mensajes anteriores
unset($_SESSION["error"], $_SESSION["success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1) Leer y normalizar datos
    $email    = trim($_POST["email"]    ?? '');
    $password = $_POST["password"]      ?? '';

    // 2) Validación básica de formulario
    if (
        $email === '' ||
        $password === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6
    ) {
        $_SESSION["error"] = "Credenciales incorrectas. Complete todos los campos.";
        header("Location: login.php");
        exit;
    }

    // 3) Conexión a BD
    /** @var PDO $connection */
    $connection = require "./sql/db.php";

    // 4) Buscar usuario por email
    $statement = $connection->prepare("
        SELECT 
            u.id_usuario,
            u.nombre,
            u.email,
            u.password,
            u.id_rol,
            u.id_seccion,
            r.nombre AS rol
        FROM Usuario AS u
        INNER JOIN Rol AS r ON r.id_rol = u.id_rol
        WHERE u.email = :email
        LIMIT 1
    ");
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $loginValido  = false;
    $rehashNeeded = false;

    if ($user) {
        $hashBD = $user["password"];

        // 1) Caso normal: hash bcrypt
        if (password_verify($password, $hashBD)) {
            $loginValido = true;

            // opcional: rehash si cambiaste el coste
            if (password_needs_rehash($hashBD, PASSWORD_DEFAULT)) {
                $rehashNeeded = true;
            }

            // 2) Caso legacy: contraseña guardada en texto plano
        } elseif (hash_equals($hashBD, $password)) {
            $loginValido  = true;
            $rehashNeeded = true; // queremos pasarla a hash
        }
    }

    if (!$loginValido) {
        $_SESSION["error"] = "Credenciales no válidas.";
        header("Location: login.php");
        exit;
    }

    // 5) Si hace falta rehashear, actualizamos la BD
    if ($rehashNeeded) {
        $nuevoHash = password_hash($password, PASSWORD_DEFAULT);

        $upd = $connection->prepare("
            UPDATE Usuario
            SET password = :password
            WHERE id_usuario = :id
        ");
        $upd->execute([
            ":password" => $nuevoHash,
            ":id"       => $user["id_usuario"],
        ]);
    }

    // 6) Guardar usuario en sesión sin el hash
    unset($user["password"]);
    $_SESSION["user"] = $user;
    $_SESSION["success"] = "Inicio de sesión exitoso.";

    header("Location: dashboard.php");
    exit;
}

// si alguien entra por GET a auth.php, lo mandamos al login
header("Location: login.php");
exit;
