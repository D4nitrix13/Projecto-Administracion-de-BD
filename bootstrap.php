<?php

declare(strict_types=1);

if (!defined('BOOTSTRAP_LOADED')) {
    define('BOOTSTRAP_LOADED', true);
}

require_once __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();

require_once __DIR__ . "/helpers/csrf.php";
