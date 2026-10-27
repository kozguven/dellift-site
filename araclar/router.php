<?php
$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$kok = __DIR__ . '/../site';
if ($p === '/') { readfile("$kok/index.html"); return true; }
if (is_file($kok . $p)) return false;
if (is_file($kok . rtrim($p, '/') . '.html')) { header('Content-Type: text/html; charset=utf-8'); readfile($kok . rtrim($p, '/') . '.html'); return true; }
http_response_code(404); readfile("$kok/404.html"); return true;
