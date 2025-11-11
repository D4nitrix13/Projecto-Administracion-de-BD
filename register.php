<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}
?>

<?php

$error = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["theme"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        !str_contains($email, "@") ||
        strlen($password) <= 7
    ) {
        $error = "Complete todos los campos";
    } else {
        $connection = require './sql/db.php';

        $statement = $connection->prepare("SELECT email FROM Usuario WHERE email = :email LIMIT 1");
        $statement->bindParam(":email", $_POST["email"]);
        $statement->execute();

        // No tiene que haber coincidencias, ya que estamos registrando un nuevo usuario.
        if ($statement->rowCount() > 0) {
            $error = "Este correo electrónico ya está en uso.";
        } else {
            $connection->prepare(
                "INSERT INTO Usuario (name, email, password) VALUES (:name, :email, :password)"
            )->execute([
                ":name" => $_POST["name"],
                ":email" => $_POST["email"],
                ":password" => password_hash($_POST["password"], PASSWORD_BCRYPT)
            ]);

            session_start();
            unset($user["password"]);
            $_SESSION["user"] = $user;
            header("Location: dashboard.php");
        }
    }
}
