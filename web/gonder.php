<?php
// DELLIFT iletişim/teklif formu → e-posta + panelde "Gelen talepler"
declare(strict_types=1);
require __DIR__ . '/inc/cekirdek.php';
$alici = F('eposta') ?: 'info@dellift.com.tr';
$geri = '/iletisim';

function temiz($s, int $uz): string { return mb_substr(trim(str_replace(["\r", "\n"], ' ', (string)$s)), 0, $uz); }

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { header('Location: ' . $geri); exit; }

$ref = $_SERVER['HTTP_REFERER'] ?? '';
$don = $geri;
if ($ref && parse_url($ref, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? '')) {
    $don = strtok(parse_url($ref, PHP_URL_PATH) ?: $geri, '?');
}

// bot tuzağı: gizli alan doluysa sessizce başarılı dön
if (!empty($_POST['web'])) { header('Location: ' . $don . '?durum=ok#teklif'); exit; }

$ad     = temiz($_POST['ad'] ?? '', 80);
$firma  = temiz($_POST['firma'] ?? '', 120);
$tel    = temiz($_POST['tel'] ?? '', 25);
$eposta = temiz($_POST['eposta'] ?? '', 120);
$konu   = temiz($_POST['konu'] ?? '', 80);
$mesaj  = mb_substr(trim((string)($_POST['mesaj'] ?? '')), 0, 3000);
$kvkk   = !empty($_POST['kvkk']);

if ($ad === '' || $tel === '' || $mesaj === '' || !$kvkk || ($eposta !== '' && !filter_var($eposta, FILTER_VALIDATE_EMAIL))) {
    header('Location: ' . $don . '?durum=hata#teklif'); exit;
}

// basit hız sınırı: aynı IP'den 60 sn'de bir
$kilit = sys_get_temp_dir() . '/dellift_form_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
if (is_file($kilit) && time() - filemtime($kilit) < 60) { header('Location: ' . $don . '?durum=ok#teklif'); exit; }
@touch($kilit);

// önce panele kaydet: e-posta gitmese de talep kaybolmasın
$dosya = VERI . '/mesajlar.json';
$fp = fopen($dosya, 'c+');
if ($fp && flock($fp, LOCK_EX)) {
    $l = json_decode((string)stream_get_contents($fp), true) ?: [];
    array_unshift($l, ['id' => bin2hex(random_bytes(6)), 'tarih' => date('c'), 'ad' => $ad, 'firma' => $firma, 'tel' => $tel,
        'eposta' => $eposta, 'konu' => $konu, 'mesaj' => $mesaj, 'sayfa' => $don, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'okundu' => false]);
    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode(array_slice($l, 0, 2000), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    flock($fp, LOCK_UN); fclose($fp);
}

$govde = "Web sitesinden yeni talep\n\n"
       . "Ad Soyad : $ad\nFirma    : $firma\nTelefon  : $tel\nE-posta  : $eposta\nKonu     : $konu\n"
       . "Sayfa    : $don\nTarih    : " . date('d.m.Y H:i') . "\n\n"
       . "Mesaj:\n$mesaj\n\nTüm talepler: " . F('site') . "/yonetim/?s=mesajlar\n";

$basliklar = [
    'From: DELLIFT Web <noreply@' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'dellift.com.tr') . '>',
    'Content-Type: text/plain; charset=UTF-8',
    'MIME-Version: 1.0',
];
if ($eposta !== '') { $basliklar[] = 'Reply-To: ' . $eposta; }
@mail($alici, '=?UTF-8?B?' . base64_encode("Web talebi: $konu – $ad") . '?=', $govde, implode("\r\n", $basliklar));

header('Location: ' . $don . '?durum=ok#teklif');
exit;
