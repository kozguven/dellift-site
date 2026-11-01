<?php
// DELLIFT — ortak çekirdek: veri okuma/yazma, yardımcılar, ikonlar.
declare(strict_types=1);

// sunucu PHP 7.4: PHP 8 yardımcıları
if (!function_exists('str_starts_with')) { function str_starts_with(string $h, string $n): bool { return $n === '' || strncmp($h, $n, strlen($n)) === 0; } }
if (!function_exists('str_contains')) { function str_contains(string $h, string $n): bool { return $n === '' || strpos($h, $n) !== false; } }

const SURUM = '10';
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

/**
 * Listeyi sayfalar. ?$param=N okunur; diğer sorgu parametreleri korunur.
 * Dönen: ['l' => dilim, 'nav' => html, 'no' => sayfa, 'son' => son sayfa, 'toplam' => adet]
 */
function sayfala(array $l, int $adet, string $param = 'sayfa', string $capa = ''): array {
    $toplam = count($l);
    $son = max(1, (int)ceil($toplam / $adet));
    $no = min($son, max(1, (int)($_GET[$param] ?? 1)));
    if ($param === 'sayfa' && $no > 1) { $GLOBALS['SAYFA_NO'] = $no; }
    $nav = '';
    if ($son > 1) {
        $q = $_GET; unset($q[$param]);
        $url = function (int $n) use ($q, $param, $capa): string {
            $x = $q; if ($n > 1) { $x[$param] = $n; }
            $s = http_build_query($x);
            $yol = strtok((string)($_SERVER['REQUEST_URI'] ?? '/'), '?');
            return e(($s !== '' ? '?' . $s : $yol) . ($capa !== '' ? '#' . $capa : ''));
        };
        $nav = '<nav class="sayfalama" aria-label="Sayfalar">';
        $nav .= $no > 1 ? '<a href="' . $url($no - 1) . '" rel="prev" aria-label="Önceki sayfa">‹</a>' : '<span class="pasif">‹</span>';
        $onceki = 0;
        for ($i = 1; $i <= $son; $i++) {
            if ($i !== 1 && $i !== $son && abs($i - $no) > 1) continue;
            if ($onceki && $i - $onceki > 1) { $nav .= '<span class="bosluk">…</span>'; }
            $nav .= $i === $no ? '<span aria-current="page">' . $i . '</span>' : '<a href="' . $url($i) . '">' . $i . '</a>';
            $onceki = $i;
        }
        $nav .= $no < $son ? '<a href="' . $url($no + 1) . '" rel="next" aria-label="Sonraki sayfa">›</a>' : '<span class="pasif">›</span>';
        $nav .= '<small>' . (($no - 1) * $adet + 1) . '–' . min($toplam, $no * $adet) . ' / ' . $toplam . '</small></nav>';
    }
    return ['l' => array_slice($l, ($no - 1) * $adet, $adet), 'nav' => $nav, 'no' => $no, 'son' => $son, 'toplam' => $toplam];
}

require __DIR__ . '/ikonlar.php';
