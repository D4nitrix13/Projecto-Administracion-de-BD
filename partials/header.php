<?php
// partials/header.php
// $pageTitle debe ser definido en cada página antes de incluir el header
if (!isset($pageTitle)) {
    $pageTitle = "Sistema - Panda Estampados / Kitsune";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="/css/styles.css">
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

</head>