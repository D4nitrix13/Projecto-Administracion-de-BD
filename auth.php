<?php
// * Stored function or procedure has been executed

session_start();

// limpiar mensajes anteriores
unset($_SESSION["error"], $_SESSION["success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1) Leer y normalizar datos
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // 2) Validación básica de formulario
    if (
        $email === "" ||
        $password === "" ||
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

    // 4) Buscar usuario por email usando función almacenada
    $statement = $connection->prepare("
        SELECT
            id_usuario,
            nombre,
            email,
            password,
            id_rol,
            id_seccion,
            rol
        FROM obtener_usuario_login(:email)
    ");

    $statement->bindValue(":email", $email, PDO::PARAM_STR);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $loginValido  = false;
    $rehashNeeded = false;

    if ($user) {
        $hashBD = $user["password"];

        // 1) Caso normal: hash bcrypt
        if (password_verify($password, $hashBD)) {
            $loginValido = true;

            // Rehash si cambia el algoritmo o el coste de PASSWORD_DEFAULT
            if (password_needs_rehash($hashBD, PASSWORD_DEFAULT)) {
                $rehashNeeded = true;
            }

            // 2) Caso legacy: contraseña guardada en texto plano
        } elseif (hash_equals($hashBD, $password)) {
            $loginValido  = true;
            $rehashNeeded = true;
        }
    }

    if (!$loginValido) {
        $_SESSION["error"] = "Credenciales no válidas.";
        header("Location: login.php");
        exit;
    }

    // 5) Si hace falta rehashear, actualizar la contraseña con función almacenada
    if ($rehashNeeded) {
        $nuevoHash = password_hash($password, PASSWORD_DEFAULT);

        $upd = $connection->prepare("
            SELECT actualizar_password_usuario_login(
                :id_usuario,
                :password_hash
            ) AS actualizado
        ");

        $upd->execute([
            ":id_usuario"    => $user["id_usuario"],
            ":password_hash" => $nuevoHash,
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
