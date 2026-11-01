<?php
// Tek kullanımlık bağlantıyla şifre belirleme: /yonetim/?s=sifre&j=<jeton>  (48 saat geçerli)
declare(strict_types=1);
if (!function_exists('kullanici')) { http_response_code(404); exit; }

$j = (string)($_GET['j'] ?? '');
$k = kullanici();
$gecerli = $j !== '' && !empty($k['jeton_hash']) && hash_equals($k['jeton_hash'], hash('sha256', $j)) && time() < (int)($k['jeton_son'] ?? 0);
$hata = ''; $tamam = false;

if ($gecerli && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_kontrol();
    $y = (string)($_POST['yeni'] ?? '');
    if (mb_strlen($y) < 10) { $hata = 'Şifre en az 10 karakter olmalı.'; }
    elseif ($y !== (string)($_POST['yeni2'] ?? '')) { $hata = 'Şifreler birbiriyle aynı değil.'; }
    else {
        $k['hash'] = password_hash($y, PASSWORD_DEFAULT);
        $k['oturum_surum'] = bin2hex(random_bytes(8));
        unset($k['jeton_hash'], $k['jeton_son']);
        $k['sifre_tarihi'] = date('c');
        kullanici_yaz($k);
        $tamam = true;
    }
}

echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<meta name="referrer" content="no-referrer"><title>Şifre belirle · DELLIFT Yönetim</title><link rel="icon" href="/favicon.svg"><link rel="stylesheet" href="/yonetim/yonetim.css?v=' . SURUM . '"></head><body class="giris">
<form method="post" class="giris-kutu"><img src="/assets/img/logo.png" alt="DELLIFT"><h1>Şifrenizi belirleyin</h1>';
if ($tamam) {
    echo '<div class="uyari ok">Şifreniz kaydedildi.</div><p>Kullanıcı adınız: <b>' . e($k['kullanici']) . '</b></p><a class="btn" href="/yonetim/">Giriş yap</a>';
} elseif (!$gecerli) {
    echo '<div class="uyari hata">Bu bağlantı geçersiz, kullanılmış ya da süresi dolmuş.</div><p><a href="/yonetim/?unuttum=1">Yeni bağlantı iste</a></p>';
} else {
    echo csrf_alan();
    if ($hata) echo '<div class="uyari hata">' . e($hata) . '</div>';
    echo '<p>Kullanıcı adınız: <b>' . e($k['kullanici']) . '</b></p>
<label class="alan"><span>Yeni şifre</span><input type="password" name="yeni" autocomplete="new-password" minlength="10" required autofocus><small>En az 10 karakter.</small></label>
<label class="alan"><span>Yeni şifre (tekrar)</span><input type="password" name="yeni2" autocomplete="new-password" minlength="10" required></label>
<button class="btn">Şifreyi kaydet</button>';
}
echo '</form></body></html>';
