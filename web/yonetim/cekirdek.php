<?php
// DELLIFT yönetim paneli — oturum, güvenlik, dosya yükleme.
declare(strict_types=1);
require dirname(__DIR__) . '/inc/cekirdek.php';

const KULLANICI_DOSYA = VERI . '/kullanici.json';
const DENEME_DOSYA = VERI . '/giris-deneme.json';

function https_mi(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

session_name('dellift_yonetim');
session_set_cookie_params(['lifetime' => 0, 'path' => '/yonetim', 'secure' => https_mi(), 'httponly' => true, 'samesite' => 'Strict']);
session_start();
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

function kullanici(): array { return json_decode((string)@file_get_contents(KULLANICI_DOSYA), true) ?: []; }
function kullanici_yaz(array $k): void {
    file_put_contents(KULLANICI_DOSYA . '.tmp', json_encode($k, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    rename(KULLANICI_DOSYA . '.tmp', KULLANICI_DOSYA);
}

function giris_var(): bool {
    $k = kullanici();
    return !empty($_SESSION['giris']) && ($_SESSION['surum'] ?? '') === ($k['oturum_surum'] ?? '')
        && (time() - ($_SESSION['son'] ?? 0)) < 4 * 3600;
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function csrf_alan(): string { return '<input type="hidden" name="_csrf" value="' . csrf() . '">'; }
function csrf_kontrol(): void {
    if (!hash_equals(csrf(), (string)($_POST['_csrf'] ?? ''))) { http_response_code(400); exit('Oturum süresi doldu, sayfayı yenileyip tekrar deneyin.'); }
}

// --- kaba kuvvet koruması: IP başına 15 dakikada 6 hatalı deneme ---
function deneme_oku(): array {
    $l = json_decode((string)@file_get_contents(DENEME_DOSYA), true) ?: [];
    return array_filter($l, fn($x) => $x['t'] > time() - 900);
}
function deneme_engelli(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return count(array_filter(deneme_oku(), fn($x) => $x['ip'] === $ip)) >= 6;
}
function deneme_ekle(): void {
    $l = deneme_oku();
    $l[] = ['ip' => $_SERVER['REMOTE_ADDR'] ?? '', 't' => time()];
    file_put_contents(DENEME_DOSYA, json_encode(array_values($l)), LOCK_EX);
}

/** Tek kullanımlık şifre belirleme bağlantısı için jeton üretir (48 saat). */
function jeton_uret(): string {
    $j = bin2hex(random_bytes(24));
    $k = kullanici();
    $k['jeton_hash'] = hash('sha256', $j);
    $k['jeton_son'] = time() + 48 * 3600;
    kullanici_yaz($k);
    return $j;
}

// ---------------- yükleme ----------------
function yukleme_hatasi(array $f): ?string {
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] === UPLOAD_ERR_OK) return null;
    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) return 'Dosya çok büyük (sunucu sınırı ' . ini_get('upload_max_filesize') . ').';
    return 'Dosya yüklenemedi (hata ' . $f['error'] . ').';
}

function _gorsel_ac(string $yol) {
    $bilgi = @getimagesize($yol);
    if (!$bilgi || !in_array($bilgi[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
        throw new RuntimeException('Görsel JPG, PNG veya WEBP olmalı.');
    }
    $im = @imagecreatefromstring((string)file_get_contents($yol));
    if (!$im) throw new RuntimeException('Görsel okunamadı.');
    if ($bilgi[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $o = (int)(@exif_read_data($yol)['Orientation'] ?? 1);
        $aci = [3 => 180, 6 => -90, 8 => 90][$o] ?? 0;
        if ($aci) { $im = imagerotate($im, $aci, 0); }
    }
    imagepalettetotruecolor($im);
    imagealphablending($im, true);
    imagesavealpha($im, true);
    return $im;
}

function _webp_yaz($im, int $gen, string $hedef, int $kalite = 82): void {
    $w = imagesx($im); $h = imagesy($im);
    if ($w > $gen) {
        $yh = (int)round($h * $gen / $w);
        $y = imagecreatetruecolor($gen, $yh);
        imagealphablending($y, false); imagesavealpha($y, true);
        imagecopyresampled($y, $im, 0, 0, 0, 0, $gen, $yh, $w, $h);
        $im = $y;
    }
    if (!is_dir(dirname($hedef))) { mkdir(dirname($hedef), 0755, true); }
    if (!imagewebp($im, $hedef, $kalite)) throw new RuntimeException('Görsel kaydedilemedi.');
}

/**
 * Yüklenen görseli webp'ye çevirir. Dönen değer site yolu ("/assets/yuklenen/x/ad").
 * $tek=true → "/…/ad.webp" (tek dosya); false → ad.webp + ad-800.webp (galeri), uzantısız taban döner.
 */
function gorsel_kaydet(string $tmp, string $klasor, string $ad, int $gen = 1600, bool $tek = true): string {
    if (!function_exists('imagewebp')) throw new RuntimeException('Sunucuda WEBP desteği yok.');
    $im = _gorsel_ac($tmp);
    $ad = slugla($ad) . '-' . substr(bin2hex(random_bytes(3)), 0, 5);
    $taban = '/assets/yuklenen/' . $klasor . '/' . $ad;
    if ($tek) {
        _webp_yaz($im, $gen, KOK . $taban . '.webp');
        return $taban . '.webp';
    }
    _webp_yaz($im, 1600, KOK . $taban . '.webp');
    _webp_yaz($im, 800, KOK . $taban . '-800.webp', 80);
    return $taban;
}

function pdf_kaydet(string $tmp, string $hedef_site_yolu): void {
    $bas = (string)file_get_contents($tmp, false, null, 0, 5);
    if ($bas !== '%PDF-') throw new RuntimeException('Dosya PDF değil.');
    $h = KOK . $hedef_site_yolu;
    if (!is_dir(dirname($h))) { mkdir(dirname($h), 0755, true); }
    if (!move_uploaded_file($tmp, $h)) throw new RuntimeException('PDF kaydedilemedi.');
}
