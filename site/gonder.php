<?php
// DELLIFT iletişim/teklif formu → info@dellift.com.tr
$alici = 'info@dellift.com.tr';
$geri = '/iletisim';

function temiz($s, $uz) { return mb_substr(trim(str_replace(["\r", "\n"], ' ', (string)$s)), 0, $uz); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $geri); exit; }

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

$govde = "Web sitesinden yeni talep\n\n"
       . "Ad Soyad : $ad\nFirma    : $firma\nTelefon  : $tel\nE-posta  : $eposta\nKonu     : $konu\n"
       . "Sayfa    : $don\nTarih    : " . date('d.m.Y H:i') . "\nIP       : " . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n\n"
       . "Mesaj:\n$mesaj\n";

$basliklar = [
    'From: DELLIFT Web <noreply@' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'dellift.com.tr') . '>',
    'Content-Type: text/plain; charset=UTF-8',
    'MIME-Version: 1.0',
];
if ($eposta !== '') { $basliklar[] = 'Reply-To: ' . $eposta; }

$ok = @mail($alici, '=?UTF-8?B?' . base64_encode("Web talebi: $konu – $ad") . '?=', $govde, implode("\r\n", $basliklar));

// e-posta gitmese de talep kaybolmasın: web dışı klasöre kayıt
$kayitDizin = dirname(__DIR__) . '/dellift-talepler';
if (!is_dir($kayitDizin)) { @mkdir($kayitDizin, 0700, true); }
@file_put_contents($kayitDizin . '/talepler.log', str_repeat('=', 40) . "\n" . $govde . "\n", FILE_APPEND | LOCK_EX);

header('Location: ' . $don . '?durum=ok#teklif');
exit;
