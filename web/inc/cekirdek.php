<?php
// DELLIFT — ortak çekirdek: veri okuma/yazma, yardımcılar, ikonlar.
declare(strict_types=1);

// sunucu PHP 7.4: PHP 8 yardımcıları
if (!function_exists('str_starts_with')) { function str_starts_with(string $h, string $n): bool { return $n === '' || strncmp($h, $n, strlen($n)) === 0; } }
if (!function_exists('str_contains')) { function str_contains(string $h, string $n): bool { return $n === '' || strpos($h, $n) !== false; } }

const SURUM = '8';
define('KOK', dirname(__DIR__));
define('VERI', KOK . '/veri');
const PDFJS = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/';

function veri(): array {
    static $v = null;
    if ($v === null) {
        $v = json_decode((string)file_get_contents(VERI . '/icerik.json'), true) ?: [];
    }
    return $v;
}

/** İçeriği atomik yazar ve önceki sürümü yedekler (son 40 yedek tutulur). */
function veri_yaz(array $v): void {
    $dosya = VERI . '/icerik.json';
    $yd = VERI . '/yedek';
    if (!is_dir($yd)) { @mkdir($yd, 0700, true); }
    if (is_file($dosya)) { @copy($dosya, $yd . '/icerik-' . date('Ymd-His') . '-' . substr(bin2hex(random_bytes(3)), 0, 4) . '.json'); }
    $l = glob($yd . '/icerik-*.json') ?: [];
    sort($l);
    while (count($l) > 40) { @unlink(array_shift($l)); }
    $gecici = $dosya . '.tmp';
    file_put_contents($gecici, json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    rename($gecici, $dosya);
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function js($s): string { return json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
function kucuk(string $s): string { return mb_strtolower(str_replace(['I', 'İ'], ['ı', 'i'], $s), 'UTF-8'); }

function wa(string $metin): string { return 'https://wa.me/' . F('wa') . '?text=' . rawurlencode($metin); }
function F(string $k): string { return (string)(veri()['firma'][$k] ?? ''); }

/** Panelde girilen bağlantı: "wa:mesaj" → WhatsApp, "tel" → telefon, diğerleri olduğu gibi. */
function baglanti(string $l): string {
    if (str_starts_with($l, 'wa:')) { return wa(trim(substr($l, 3))); }
    if ($l === 'tel') { return 'tel:' . F('tel_uri'); }
    return $l;
}
function dis_mi(string $href): bool { return (bool)preg_match('~^https?://~', $href); }

function kategoriler(): array { return veri()['kategoriler'] ?? []; }
function markalar(): array { return veri()['markalar'] ?? []; }
function kat(string $slug): ?array { foreach (kategoriler() as $k) { if ($k['slug'] === $slug) return $k; } return null; }
function marka(string $slug): ?array { foreach (markalar() as $m) { if ($m['slug'] === $slug) return $m; } return null; }
function marka_ad(string $slug): string { return marka($slug)['ad'] ?? ''; }
function urunler(): array { return array_values(array_filter(veri()['urunler'] ?? [], fn($u) => !empty($u['aktif']))); }
function seriler(): array { return array_values(array_filter(veri()['seriler'] ?? [], fn($s) => !empty($s['aktif']))); }
function seri(string $slug): ?array { foreach (seriler() as $s) { if ($s['slug'] === $slug) return $s; } return null; }
function kataloglar(?string $marka = null, ?string $kat = null): array {
    return array_values(array_filter(veri()['kataloglar'] ?? [], fn($k) => !empty($k['aktif'])
        && ($marka === null || $k['marka'] === $marka) && ($kat === null || $k['kat'] === $kat)));
}
function katalog(string $marka, string $slug): ?array {
    foreach (kataloglar($marka) as $k) { if ($k['slug'] === $slug) return $k; } return null;
}

function slugla(string $s): string {
    $s = strtr(kucuk($s), ['ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u', 'â' => 'a', 'î' => 'i', 'û' => 'u']);
    $s = preg_replace('~[^a-z0-9]+~', '-', $s);
    return trim((string)$s, '-') ?: 'kayit';
}

require __DIR__ . '/ikonlar.php';
