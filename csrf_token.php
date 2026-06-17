<?php
session_start();
require_once __DIR__ . "/helpers/csrf.php";
header("Content-Type: text/plain");
echo csrfToken();
