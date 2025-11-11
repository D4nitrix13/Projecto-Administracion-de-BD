<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acceso restringido</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="page-bg">

    <div style="
        max-width: 520px;
        margin: 80px auto;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
    ">
        <div style="
            width: 70px;
            height: 70px;
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #b91c1c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            border-radius: 50%;
            margin: 0 auto 16px auto;
        ">
            !
        </div>

        <h2 style="color:#111827; font-size:22px; margin-bottom:10px;">
            Acceso restringido
        </h2>

        <p style="color:#6b7280; font-size:15px; margin-bottom:22px;">
            Esta sección está disponible únicamente para usuarios con rol de <strong>Administrador</strong>.
        </p>

        <a href="dashboard.php" class="btn-primary-inline" style="padding:10px 20px;">
            ← Volver al inicio
        </a>
    </div>

</body>

</html>