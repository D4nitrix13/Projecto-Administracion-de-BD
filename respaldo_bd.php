<?php
session_start();
$pageTitle = "Respaldos BD - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION["user"];


// Validación de que el usuario sea Administrador
// Administrador -- id_rol = 1
// Supervisor    -- id_rol = 2
// Facturador    -- id_rol = 3
if (!isset($user["id_rol"]) || (int)$user["id_rol"] !== 1) {
    header("Location: acceso_restringido.php");
    exit();
}


$connection = require "./sql/db.php";
require __DIR__ . "/lib/backup.php";

$BACKUP_DIR = __DIR__ . "/backups";

$error = null;
$success = null;

// Manejo de acciones
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "backup") {

        // NUEVO: leer nombre y mensaje personalizados
        $nombrePersonalizado  = trim($_POST["nombre_archivo"] ?? "");
        $mensajePersonalizado = trim($_POST["mensaje"] ?? "");

        if ($nombrePersonalizado === "") {
            $nombrePersonalizado = null;
        }
        if ($mensajePersonalizado === "") {
            $mensajePersonalizado = null;
        }

        // id del usuario (ajusta la clave según cómo la guardes en sesión)
        $idUsuario = $user["id_usuario"] ?? null;

        list($ok, $msg) = hacerRespaldo(
            $connection,
            'manual',
            $idUsuario,
            $nombrePersonalizado,
            $mensajePersonalizado
        );

        if ($ok) {
            $success = $msg;
        } else {
            $error = $msg;
        }
    } elseif ($action === "restore") {

        $archivo = basename($_POST["archivo"] ?? "");
        $filepath = $BACKUP_DIR . "/" . $archivo;

        if (!is_file($filepath)) {
            $error = "Archivo de respaldo no encontrado.";
        } else {
            // ⚠️ RESTAURAR PUEDE BORRAR TODOS LOS DATOS. HAZLO SOLO EN DESARROLLO.
            $DB_HOST   = '172.17.0.3';
            $DB_PORT   = '5432';
            $DB_USER   = 'postgres';
            $DB_PASS   = "root";
            $DB_NAME   = 'pandas_estampados_y_kitsune';

            // 1) Limpiar por completo el esquema public (para que quede como en el backup)
            $cmdDrop = sprintf(
                'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" 2>&1',
                escapeshellarg($DB_PASS),
                escapeshellarg($DB_HOST),
                escapeshellarg($DB_PORT),
                escapeshellarg($DB_USER),
                escapeshellarg($DB_NAME)
            );

            $outputDrop   = [];
            $returnDrop   = 0;
            exec($cmdDrop, $outputDrop, $returnDrop);

            if ($returnDrop !== 0) {
                $error = "Error al limpiar la base de datos antes de restaurar:\n" . implode("\n", $outputDrop);
            } else {

                // 2) Ejecutar el archivo de respaldo (.sql)
                $cmdRestore = sprintf(
                    'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -f %s 2>&1',
                    escapeshellarg($DB_PASS),
                    escapeshellarg($DB_HOST),
                    escapeshellarg($DB_PORT),
                    escapeshellarg($DB_USER),
                    escapeshellarg($DB_NAME),
                    escapeshellarg($filepath)
                );

                $outputRestore = [];
                $returnRestore = 0;
                exec($cmdRestore, $outputRestore, $returnRestore);

                if ($returnRestore === 0) {
                    $success = "Base de datos restaurada desde {$archivo}.";
                } else {
                    $error = "Error al restaurar la base de datos:\n" . implode("\n", $outputRestore);
                }
            }
        }
    }
}


// Listar archivos de respaldo
$archivos = [];
if (is_dir($BACKUP_DIR)) {
    foreach (glob($BACKUP_DIR . "/*.sql") as $f) {
        $archivos[] = [
            "nombre" => basename($f),
            "fecha"  => date("Y-m-d H:i:s", filemtime($f)),
            "tamanio" => filesize($f),
        ];
    }
    // Ordenar por fecha desc
    usort($archivos, fn($a, $b) => strcmp($b["fecha"], $a["fecha"]));
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Sistema</p>
            <h1 class="dashboard-title">Respaldos de base de datos</h1>
            <p class="dashboard-muted">
                Genere respaldos, restaure desde un archivo existente y revise el historial.
            </p>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= nl2br(htmlspecialchars($error)) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= nl2br(htmlspecialchars($success)) ?></div>
            <?php endif; ?>

            <!-- Exportar -->
            <!-- Exportar -->
            <h2 style="margin-bottom:12px;">Generar respaldo (exportar)</h2>

            <form method="POST" style="margin-bottom:24px;">
                <input type="hidden" name="action" value="backup">

                <div class="form-group">
                    <label class="label">Nombre del archivo (opcional)</label>
                    <input
                        type="text"
                        name="nombre_archivo"
                        class="input"
                        placeholder="Ej. respaldo_cierre_mes">
                    <p class="dashboard-muted" style="margin-top:4px; font-size:13px;">
                        No incluyas la extensión <strong>.sql</strong>. Se agregará automáticamente.
                        Si dejas esto vacío, se usará el nombre por defecto con el nombre de la base de datos.
                    </p>
                </div>

                <div class="form-group">
                    <label class="label">Mensaje o descripción (opcional)</label>
                    <input
                        type="text"
                        name="mensaje"
                        class="input"
                        maxlength="255"
                        placeholder="Ej. Respaldo antes de importar catálogo nuevo">
                </div>

                <button type="submit" class="btn-primary">
                    Generar respaldo ahora
                </button>
            </form>


            <!-- Importar -->
            <h2 style="margin-bottom:12px;">Restaurar desde respaldo (importar)</h2>
            <form method="POST" style="margin-bottom:24px;">
                <input type="hidden" name="action" value="restore">

                <div class="form-group">
                    <label class="label">Seleccione un archivo de respaldo (.sql)</label>
                    <select name="archivo" class="input" required>
                        <option value="">-- Seleccione archivo --</option>
                        <?php foreach ($archivos as $a): ?>
                            <option value="<?= htmlspecialchars($a["nombre"]) ?>">
                                <?= htmlspecialchars($a["nombre"]) ?> (<?= $a["fecha"] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <p class="dashboard-muted" style="margin-bottom:12px;">
                    ⚠ Esta acción puede sobrescribir datos existentes. Úsala con cuidado.
                </p>

                <button type="submit" class="btn-danger">
                    Restaurar base de datos
                </button>
            </form>

            <!-- Historial -->
            <h2 style="margin-bottom:12px;">Archivos de respaldo disponibles</h2>

            <?php if (empty($archivos)): ?>
                <p class="dashboard-muted">No hay respaldos generados todavía.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>Archivo</th>
                                <th>Fecha</th>
                                <th>Tamaño (KB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($archivos as $a): ?>
                                <tr>
                                    <td><?= htmlspecialchars($a["nombre"]) ?></td>
                                    <td><?= htmlspecialchars($a["fecha"]) ?></td>
                                    <td><?= number_format($a["tamanio"] / 1024, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </section>

    </main>
</body>

</html>