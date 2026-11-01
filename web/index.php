<?php
// DELLIFT — ön denetleyici: tüm temiz URL'ler buradan, içerik veri/icerik.json'dan.
declare(strict_types=1);
require __DIR__ . '/inc/cekirdek.php';
require __DIR__ . '/inc/sablon.php';
require __DIR__ . '/inc/sayfalar.php';

$yol = trim(rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'), '/');
if (preg_match('~^(.*?)(?:\.html|/index)$~', $yol, $m) || $yol === 'index') {
    $h = $yol === 'index' ? '' : $m[1];
    header('Location: /' . ($h === 'index' ? '' : $h), true, 301); exit;
}

function gonder(string $html, int $kod = 200): void {
    http_response_code($kod);
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
}

if ($yol === 'sitemap.xml') { header('Content-Type: application/xml; charset=utf-8'); echo site_haritasi(); exit; }
if ($yol === '') gonder(ana_sayfa());
if (isset(METIN_SAYFALARI[$yol]) && isset(veri()['sayfalar'][$yol])) gonder(metin_sayfasi($yol));
if ($yol === 'sss') gonder(sss_sayfasi());
if ($yol === 'urunler') gonder(urunler_sayfasi());
if ($yol === 'iletisim') gonder(iletisim_sayfasi());
if ($k = kat($yol)) gonder(kategori_sayfasi($k));
$p = explode('/', $yol);
if (count($p) === 2 && $p[0] === 'marka' && ($m = marka($p[1]))) gonder(marka_sayfasi($m));
if (count($p) === 2 && $p[0] === 'urun' && ($s = seri($p[1]))) gonder(seri_sayfasi($s));
if (count($p) === 3 && $p[0] === 'katalog' && ($k = katalog($p[1], $p[2]))) gonder(katalog_sayfasi($k));
gonder(bulunamadi(), 404);
