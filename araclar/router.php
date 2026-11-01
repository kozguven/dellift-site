<?php
// Yerel deneme: php -S 127.0.0.1:8090 -t web araclar/router.php
$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$kok = __DIR__ . '/../web';
if (preg_match('~^/(veri|inc)(/|$)~', $p)) { http_response_code(403); return true; }
if ($p !== '/' && is_file($kok . $p)) return false;
if (is_dir($kok . $p) && is_file($kok . rtrim($p, '/') . '/index.php')) { $_SERVER['SCRIPT_NAME'] = rtrim($p, '/') . '/index.php'; chdir($kok . rtrim($p, '/')); require $kok . rtrim($p, '/') . '/index.php'; return true; }
require $kok . '/index.php';
