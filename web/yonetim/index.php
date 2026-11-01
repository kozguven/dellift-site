<?php
// DELLIFT yönetim paneli — /yonetim
declare(strict_types=1);
require __DIR__ . '/cekirdek.php';

$s = preg_replace('~[^a-z]~', '', (string)($_GET['s'] ?? 'pano'));

// ---------------- giriş / çıkış / şifre ----------------
if ($s === 'cikis') { $_SESSION = []; session_destroy(); header('Location: /yonetim/'); exit; }
if ($s === 'sifre') { require __DIR__ . '/sifre.php'; exit; }

if (!giris_var()) {
    $hata = '';
    $k = kullanici();
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['islem'] ?? '') === 'giris') {
        csrf_kontrol();
        if (deneme_engelli()) {
            $hata = 'Çok fazla hatalı deneme. 15 dakika sonra tekrar deneyin.';
        } elseif (!empty($k['hash']) && hash_equals(kucuk((string)($k['kullanici'] ?? '')), kucuk(trim((string)($_POST['kullanici'] ?? ''))))
                  && password_verify((string)($_POST['sifre'] ?? ''), $k['hash'])) {
            session_regenerate_id(true);
            $_SESSION['giris'] = true; $_SESSION['surum'] = $k['oturum_surum'] ?? ''; $_SESSION['son'] = time();
            header('Location: /yonetim/'); exit;
        } else {
            deneme_ekle(); usleep(400000);
            $hata = 'Kullanıcı adı veya şifre hatalı.';
        }
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['islem'] ?? '') === 'unuttum') {
        csrf_kontrol();
        if (!deneme_engelli() && !empty($k['eposta'])) {
            deneme_ekle();
            $j = jeton_uret();
            $url = F('site') . '/yonetim/?s=sifre&j=' . $j;
            @mail($k['eposta'], '=?UTF-8?B?' . base64_encode('DELLIFT yönetim paneli: şifre yenileme') . '?=',
                "Yönetim paneli şifrenizi yenilemek için aşağıdaki bağlantıyı açın (48 saat geçerli, tek kullanımlık):\n\n$url\n\nBu isteği siz yapmadıysanız bu e-postayı yok sayın.",
                implode("\r\n", ['From: DELLIFT Web <noreply@dellift.com.tr>', 'Content-Type: text/plain; charset=UTF-8', 'MIME-Version: 1.0']));
        }
        $hata = 'Kayıtlı e-posta adresine şifre yenileme bağlantısı gönderildi.';
    }
    giris_sayfasi($hata);
    exit;
}
$_SESSION['son'] = time();

// ---------------- koleksiyon tanımları ----------------
function secenek_marka(): array { $o = []; foreach (markalar() as $m) { $o[$m['slug']] = $m['ad']; } return $o; }
function secenek_kat(): array { $o = []; foreach (kategoriler() as $k) { $o[$k['slug']] = $k['ad']; } return $o; }
function secenek_seri(): array { $o = ['' => '— detay sayfası yok —']; foreach (veri()['seriler'] ?? [] as $x) { $o[$x['slug']] = $x['ad']; } return $o; }

$KOL = [
    'slaytlar' => ['ad' => 'Ana sayfa banner', 'tekil' => 'banner', 'goster' => fn($x) => strip_tags(str_replace('*', '', $x['baslik'])), 'resim' => fn($x) => $x['gorsel'] ?? '',
        'aciklama' => 'Ana sayfanın en üstünde dönen büyük görseller. Sırayı oklarla değiştirebilirsiniz.',
        'alanlar' => [
            ['gorsel', 'Görsel', 'gorsel', 'gen' => 1920, 'klasor' => 'banner', 'zorunlu' => true, 'ipucu' => 'Yatay, en az 1600 px genişlikte bir fotoğraf seçin. Yazı solda durur; görselin sağ tarafı daha görünür kalır.'],
            ['baslik', 'Başlık', 'metin', 'zorunlu' => true, 'ipucu' => 'Kırmızı görünmesini istediğiniz kısmı *yıldız* içine alın. Örnek: İhtiyacınız kadar *kiralayın*'],
            ['metin', 'Alt yazı', 'yazi'],
            ['b1_yazi', '1. düğme (kırmızı) yazısı', 'metin'], ['b1_link', '1. düğme bağlantısı', 'metin', 'ipucu' => 'Site içi sayfa: /iletisim#teklif · WhatsApp: wa:Merhaba, bilgi almak istiyorum. · Telefon: tel'],
            ['b2_yazi', '2. düğme yazısı', 'metin'], ['b2_link', '2. düğme bağlantısı', 'metin'],
        ]],
    'urunler' => ['ad' => 'Ürünler', 'tekil' => 'ürün', 'goster' => fn($x) => $x['ad'], 'alt' => fn($x) => marka_ad($x['marka']) . ' · ' . (kat($x['kat'])['ad'] ?? '') . (!empty($x['anasayfa']) ? ' · ana sayfada' : '') . (empty($x['aktif']) ? ' · YAYINDA DEĞİL' : ''), 'resim' => fn($x) => $x['gorsel'] ?? '',
        'aciklama' => 'Kategori, marka ve ana sayfada görünen ürün kartları. Kartın "Teknik Özellikler" düğmesi seçtiğiniz detay sayfasına gider.',
        'alanlar' => [
            ['ad', 'Ürün adı', 'metin', 'zorunlu' => true, 'ipucu' => 'Örnek: Niğmer 3 Ton Dizel Forklift'],
            ['model', 'Model', 'metin', 'ipucu' => 'Örnek: NMR 30D'],
            ['marka', 'Marka', 'secim', 'secenek' => 'secenek_marka'],
            ['kat', 'Kategori', 'secim', 'secenek' => 'secenek_kat'],
            ['gorsel', 'Ürün görseli', 'gorsel', 'gen' => 1000, 'klasor' => 'urun', 'zorunlu' => true],
            ['aciklama', 'Kısa açıklama', 'yazi'],
            ['ozellik', 'Kart üzerindeki özellikler', 'satirlar', 'ipucu' => 'Her satıra bir özellik: Özellik | Değer. Örnek: Kapasite | 3.000 kg'],
            ['detay', 'Detay sayfası', 'secim', 'secenek' => 'secenek_seri', 'ipucu' => 'Galeri ve teknik tablolu detay sayfası "Ürün detay sayfaları" bölümünden eklenir.'],
            ['anasayfa', 'Ana sayfada "Öne çıkan modeller" içinde göster', 'onay'],
            ['aktif', 'Sitede yayında', 'onay', 'varsayilan' => true],
        ]],
    'seriler' => ['ad' => 'Ürün detay sayfaları', 'tekil' => 'detay sayfası', 'goster' => fn($x) => $x['ad'], 'alt' => fn($x) => '/urun/' . $x['slug'] . (empty($x['aktif']) ? ' · YAYINDA DEĞİL' : ''), 'resim' => fn($x) => !empty($x['gorseller'][0]) ? $x['gorseller'][0] . '-800.webp' : '', 'url' => fn($x) => '/urun/' . $x['slug'],
        'aciklama' => 'Galeri, öne çıkan özellikler ve teknik tablo içeren ürün sayfaları (/urun/…). Bir sayfada birden fazla model (ör. 3 ve 3,5 ton) yan yana gösterilebilir.',
        'alanlar' => [
            ['ad', 'Sayfa başlığı', 'metin', 'zorunlu' => true, 'ipucu' => 'Örnek: Niğmer NMR Serisi Dizel Forklift'],
            ['slug', 'Sayfa adresi', 'slug', 'kaynak' => 'ad', 'onek' => '/urun/'],
            ['kisa', 'Kısa tanım (başlığın altında)', 'metin', 'ipucu' => 'Örnek: 3 ve 3,5 ton · Euro V dizel · yerli üretim'],
            ['marka', 'Marka', 'secim', 'secenek' => 'secenek_marka'],
            ['kat', 'Kategori', 'secim', 'secenek' => 'secenek_kat'],
            ['giris', 'Tanıtım metni', 'yazi', 'satir' => 6],
            ['gorseller', 'Galeri görselleri', 'galeri', 'klasor' => 'urun', 'ipucu' => 'İlk görsel büyük gösterilir. Birden fazla görseli aynı anda seçebilirsiniz.'],
            ['modeller', 'Modeller', 'liste', 'ipucu' => 'Her satıra bir model. Teknik tablodaki değer sütunları bu sırayla eşleşir.'],
            ['one_baslik', 'Öne çıkanlar başlığı', 'metin', 'varsayilan' => 'Öne çıkan özellikler'],
            ['one_cikan', 'Öne çıkan özellikler', 'satirlar', 'satir' => 8, 'ipucu' => 'Her satıra: Başlık | Açıklama'],
            ['teknik', 'Teknik özellikler tablosu', 'teknik', 'satir' => 16, 'ipucu' => 'Her satıra: Özellik | Birim | 1. model değeri | 2. model değeri … Ara başlık için satırı ## ile başlatın (## Ölçüler). Kapasite özetinin görünmesi için bir satırın adı "Yük kapasitesi" olmalı.'],
            ['katalog', 'Katalog PDF', 'pdf', 'klasor' => 'katalog'],
            ['not', 'Alt not', 'metin', 'ipucu' => 'Örnek: 2 yıl / 2.000 saat üretici garantisi'],
            ['aktif', 'Sitede yayında', 'onay', 'varsayilan' => true],
        ]],
    'kataloglar' => ['ad' => 'Kataloglar', 'tekil' => 'katalog', 'goster' => fn($x) => marka_ad($x['marka']) . ' ' . $x['model'], 'alt' => fn($x) => (kat($x['kat'])['ad'] ?? '') . ' · ' . $x['aciklama'] . (empty($x['aktif']) ? ' · YAYINDA DEĞİL' : ''), 'resim' => fn($x) => $x['kapak'] ?? '', 'url' => fn($x) => "/katalog/{$x['marka']}/{$x['slug']}", 'filtre' => 'marka',
        'aciklama' => 'Üretici broşürleri. Her katalog kendi sayfasında (PDF sayfa içinde) gösterilir ve ilgili kategori ile marka sayfalarında listelenir.',
        'alanlar' => [
            ['marka', 'Marka', 'secim', 'secenek' => 'secenek_marka'],
            ['kat', 'Kategori', 'secim', 'secenek' => 'secenek_kat'],
            ['model', 'Model', 'metin', 'zorunlu' => true, 'ipucu' => 'Örnek: CPD15/20L1'],
            ['slug', 'Sayfa adresi', 'slug', 'kaynak' => 'model', 'onek' => '/katalog/<marka>/'],
            ['aciklama', 'Kısa açıklama', 'metin', 'zorunlu' => true, 'ipucu' => 'Örnek: 1,5–2 ton lityum akülü forklift'],
            ['dil', 'Katalog dili', 'secim', 'secenek' => fn() => ['TR' => 'Türkçe', 'EN' => 'İngilizce']],
            ['pdf', 'Katalog PDF', 'pdf', 'klasor' => 'katalog', 'zorunlu' => true, 'kapak' => true],
            ['kapak', 'Kapak görseli', 'gorsel', 'gen' => 700, 'klasor' => 'katalog', 'ipucu' => 'Boş bırakırsanız PDF\'in ilk sayfası otomatik kapak yapılır.'],
            ['aktif', 'Sitede yayında', 'onay', 'varsayilan' => true],
        ]],
    'markalar' => ['ad' => 'Markalar', 'tekil' => 'marka', 'goster' => fn($x) => $x['ad'], 'alt' => fn($x) => '/marka/' . $x['slug'], 'url' => fn($x) => '/marka/' . $x['slug'],
        'aciklama' => 'Menüde, ana sayfada ve kategori sayfalarında görünen markalar.',
        'alanlar' => [
            ['ad', 'Marka adı', 'metin', 'zorunlu' => true],
            ['slug', 'Sayfa adresi', 'slug', 'kaynak' => 'ad', 'onek' => '/marka/'],
            ['ozet', 'Kısa tanıtım', 'yazi'],
            ['kat', 'Bulunduğu kategoriler', 'coklu', 'secenek' => 'secenek_kat'],
        ]],
    'kategoriler' => ['ad' => 'Kategoriler', 'tekil' => 'kategori', 'goster' => fn($x) => $x['ad'], 'alt' => fn($x) => '/' . $x['slug'], 'url' => fn($x) => '/' . $x['slug'], 'resim' => fn($x) => $x['gorsel'] ?? '',
        'aciklama' => 'Ürün kategorileri. Menüde ve ürünler sayfasında bu sırayla görünür.',
        'alanlar' => [
            ['ad', 'Kategori adı', 'metin', 'zorunlu' => true],
            ['slug', 'Sayfa adresi', 'slug', 'kaynak' => 'ad', 'onek' => '/'],
            ['kisa', 'Kısa ad', 'metin', 'ipucu' => 'Kartlardaki küçük etiket. Örnek: Dizel'],
            ['ozet', 'Özet (tek cümle)', 'yazi'],
            ['metin', 'Kategori sayfası tanıtım metni', 'yazi', 'satir' => 5],
            ['ust', 'Forklift grubunda (değilse "Depo ekipmanı" sayılır)', 'onay'],
            ['gorsel', 'Ana sayfa kart görseli (isteğe bağlı)', 'gorsel', 'gen' => 900, 'klasor' => 'kategori'],
        ]],
    'sss' => ['ad' => 'Sıkça sorulan sorular', 'tekil' => 'soru', 'goster' => fn($x) => $x['soru'], 'url' => fn($x) => '/sss',
        'aciklama' => '/sss sayfasındaki sorular.',
        'alanlar' => [['soru', 'Soru', 'metin', 'zorunlu' => true], ['cevap', 'Cevap', 'yazi', 'satir' => 6, 'zorunlu' => true]]],
];

const SAYFA_ADLARI = ['hakkimizda' => 'Hakkımızda', 'forklift-satis' => 'Forklift Satış', 'forklift-kiralama' => 'Forklift Kiralama',
    'servis' => 'Servis ve Yedek Parça', 'finansal-cozumler' => 'Finansal Çözümler', 'kvkk' => 'KVKK Aydınlatma Metni'];

const FIRMA_ALANLARI = [
    ['unvan', 'Firma ünvanı'], ['tel', 'Telefon (görünen)', 'Örnek: 0534 821 70 50'], ['tel_uri', 'Telefon (arama için)', '+90 ile, boşluksuz: +905348217050'],
    ['wa', 'WhatsApp numarası', 'Başında + olmadan: 905348217050'], ['eposta', 'E-posta', 'Form talepleri bu adrese de gider.'],
    ['adres', 'Adres'], ['harita_q', 'Harita arama metni', 'Google Haritalar\'da konumu bulduran adres metni.'],
    ['instagram', 'Instagram bağlantısı'], ['kampanya', 'Üst şerit kampanya yazısı'], ['alt_bilgi_metni', 'Alt bilgi tanıtım yazısı'],
];

// ---------------- alan işleme ----------------
function satir_coz(string $t, int $en_az = 2): array {
    $o = [];
    foreach (preg_split('~\R~u', $t) as $l) {
        if (trim($l) === '') continue;
        $p = array_map('trim', explode('|', $l));
        $o[] = array_pad($p, $en_az, '');
    }
    return $o;
}
function teknik_coz(string $t): array {
    $o = [];
    foreach (preg_split('~\R~u', $t) as $l) {
        $l = trim($l);
        if ($l === '') continue;
        if (str_starts_with($l, '##')) { $o[] = [trim(substr($l, 2)), null]; continue; }
        $o[] = array_pad(array_map('trim', explode('|', $l)), 3, '');
    }
    return $o;
}
function teknik_yaz(array $r): string {
    return implode("\n", array_map(fn($x) => (count($x) < 2 || $x[1] === null) ? '## ' . $x[0] : implode(' | ', $x), $r));
}

/** Formdan gelen kaydı oluşturur. $eski: düzenlenen kayıt. Hata varsa RuntimeException. */
function kayit_isle(string $kol, array $tanim, array $eski, bool $yeni): array {
    $x = $eski;
    $P = $_POST;
    foreach ($tanim['alanlar'] as $a) {
        [$ad, $etiket, $tur] = $a;
        $alan = 'f_' . $ad;
        switch ($tur) {
            case 'metin': case 'yazi': $x[$ad] = trim((string)($P[$alan] ?? '')); break;
            case 'secim': $x[$ad] = (string)($P[$alan] ?? ''); break;
            case 'onay': $x[$ad] = $kol === 'kategoriler' && $ad === 'ust' ? (!empty($P[$alan]) ? 'forklift' : null) : !empty($P[$alan]); break;
            case 'coklu': $x[$ad] = array_values(array_map('strval', (array)($P[$alan] ?? []))); break;
            case 'satirlar': $x[$ad] = satir_coz((string)($P[$alan] ?? '')); break;
            case 'liste': $x[$ad] = array_values(array_filter(array_map('trim', preg_split('~\R~u', (string)($P[$alan] ?? ''))), 'strlen')); break;
            case 'teknik': $x[$ad] = teknik_coz((string)($P[$alan] ?? '')); break;
            case 'slug':
                if ($yeni) {
                    $g = trim((string)($P[$alan] ?? ''));
                    $x[$ad] = slugla($g !== '' ? $g : (string)($x[$a['kaynak']] ?? ''));
                }
                break;
            case 'gorsel':
                if (!empty($P[$alan . '_sil'])) { $x[$ad] = ''; }
                $f = $_FILES[$alan] ?? null;
                if ($f && ($h = yukleme_hatasi($f))) throw new RuntimeException("$etiket: $h");
                if ($f && $f['error'] === UPLOAD_ERR_OK) {
                    $x[$ad] = gorsel_kaydet($f['tmp_name'], $a['klasor'], (string)($x['slug'] ?? $x['ad'] ?? $x['model'] ?? $a['klasor']), $a['gen'], true);
                }
                break;
            case 'galeri':
                $l = $x[$ad] ?? [];
                $sira = array_values(array_filter((array)($P[$alan . '_sira'] ?? []), fn($v) => in_array($v, $l, true)));
                $l = $sira ?: (isset($P[$alan . '_sira']) ? [] : $l);
                $f = $_FILES[$alan] ?? null;
                if ($f && is_array($f['name'])) {
                    foreach ($f['name'] as $i => $_) {
                        $tek = ['error' => $f['error'][$i], 'tmp_name' => $f['tmp_name'][$i]];
                        if ($h = yukleme_hatasi($tek)) throw new RuntimeException("$etiket: $h");
                        if ($tek['error'] === UPLOAD_ERR_OK) { $l[] = gorsel_kaydet($tek['tmp_name'], $a['klasor'] . '/' . ($x['slug'] ?? 'galeri'), (string)($x['slug'] ?? 'gorsel'), 1600, false); }
                    }
                }
                $x[$ad] = array_values($l);
                break;
            case 'pdf':
                if (!empty($P[$alan . '_sil'])) { $x[$ad] = ''; }
                $f = $_FILES[$alan] ?? null;
                if ($f && ($h = yukleme_hatasi($f))) throw new RuntimeException("$etiket: $h");
                if ($f && $f['error'] === UPLOAD_ERR_OK) {
                    $klasor = $kol === 'kataloglar' ? 'dosya/' . slugla($x['marka'] ?? 'genel') : 'yuklenen/katalog';
                    $yol = '/assets/' . $klasor . '/' . slugla((string)($x['slug'] ?? $x['model'] ?? 'katalog')) . '-' . substr(bin2hex(random_bytes(3)), 0, 5) . '.pdf';
                    pdf_kaydet($f['tmp_name'], $yol);
                    $x[$ad] = $yol;
                    // kapak boşsa: tarayıcının PDF'in ilk sayfasından çizdiği görsel
                    if (!empty($a['kapak']) && empty($_FILES['f_kapak']['size']) && preg_match('~^data:image/(png|jpeg);base64,~', (string)($P['kapak_veri'] ?? ''))) {
                        $tmp = tempnam(sys_get_temp_dir(), 'kpk');
                        file_put_contents($tmp, base64_decode(substr((string)$P['kapak_veri'], strpos((string)$P['kapak_veri'], ',') + 1)));
                        try { $x['kapak'] = gorsel_kaydet($tmp, 'katalog', (string)($x['slug'] ?? 'katalog'), 700, true); } finally { @unlink($tmp); }
                    }
                }
                break;
        }
        if (!empty($a['zorunlu']) && (($x[$ad] ?? '') === '' || ($x[$ad] ?? []) === [])) {
            throw new RuntimeException("\"$etiket\" alanı boş bırakılamaz.");
        }
    }
    return $x;
}

// ---------------- işlemler (POST) ----------------
$mesaj = $_SESSION['bildirim'] ?? ''; unset($_SESSION['bildirim']);
$hata = '';
function bildir(string $m, string $git): void { $_SESSION['bildirim'] = $m; header('Location: ' . $git); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_kontrol();
    $v = veri();
    $islem = (string)($_POST['islem'] ?? '');
    try {
        if (isset($KOL[$s])) {
            $t = $KOL[$s];
            $l = $v[$s] ?? [];
            $i = isset($_POST['i']) && $_POST['i'] !== '' ? (int)$_POST['i'] : null;
            if ($islem === 'kaydet') {
                $yeni = $i === null || !isset($l[$i]);
                $eski = $yeni ? [] : $l[$i];
                if ($yeni) { foreach ($t['alanlar'] as $a) { if (isset($a['varsayilan'])) $eski[$a[0]] = $a['varsayilan']; } }
                $x = kayit_isle($s, $t, $eski, $yeni);
                if ($s === 'urunler' && empty($x['id'])) { $x['id'] = 'u' . bin2hex(random_bytes(4)); }
                if (isset($x['slug'])) {
                    foreach ($l as $j => $y) {
                        if ($j !== $i && ($y['slug'] ?? null) === $x['slug'] && ($s !== 'kataloglar' || $y['marka'] === $x['marka'])) {
                            throw new RuntimeException('Bu sayfa adresi zaten kullanılıyor: ' . $x['slug']);
                        }
                    }
                    if ($s === 'kategoriler' && in_array($x['slug'], ['urunler', 'iletisim', 'sss', 'marka', 'urun', 'katalog', 'yonetim', 'assets', 'hakkimizda', 'kvkk', 'servis'], true)) {
                        throw new RuntimeException('Bu sayfa adresi sistemde ayrılmış, başka bir ad seçin.');
                    }
                }
                if ($yeni) { $l[] = $x; $i = count($l) - 1; } else { $l[$i] = $x; }
                $v[$s] = array_values($l);
                veri_yaz($v);
                bildir(ucfirst($t['tekil']) . ' kaydedildi.', "?s=$s&i=$i");
            }
            if ($islem === 'sil' && $i !== null && isset($l[$i])) {
                $x = $l[$i];
                if ($s === 'markalar' || $s === 'kategoriler') {
                    $alan = $s === 'markalar' ? 'marka' : 'kat';
                    $kullanan = count(array_filter(array_merge($v['urunler'] ?? [], $v['seriler'] ?? [], $v['kataloglar'] ?? []), fn($y) => ($y[$alan] ?? '') === $x['slug']));
                    if ($kullanan) throw new RuntimeException("Bu {$t['tekil']} $kullanan ürün/katalog kaydında kullanılıyor. Önce o kayıtlarda başka bir {$t['tekil']} seçin veya onları silin.");
                }
                if ($s === 'seriler') { foreach ($v['urunler'] as &$u) { if (($u['detay'] ?? '') === $x['slug']) $u['detay'] = ''; } unset($u); }
                array_splice($l, $i, 1);
                $v[$s] = $l;
                veri_yaz($v);
                bildir(ucfirst($t['tekil']) . ' silindi.', "?s=$s");
            }
            if ($islem === 'tasi' && $i !== null && isset($l[$i])) {
                $j = $i + ((($_POST['yon'] ?? '') === 'yukari') ? -1 : 1);
                if (isset($l[$j])) { [$l[$i], $l[$j]] = [$l[$j], $l[$i]]; $v[$s] = $l; veri_yaz($v); }
                $q = $_GET; unset($q['i'], $q['yeni']);
                header('Location: ?' . http_build_query($q) . '#k' . $j); exit;
            }
        }
        if ($s === 'sayfalar' && $islem === 'kaydet') {
            $y = (string)($_POST['yol'] ?? '');
            if (!isset($v['sayfalar'][$y])) throw new RuntimeException('Sayfa bulunamadı.');
            foreach (['baslik', 'aciklama', 'h1', 'alt'] as $k) { $v['sayfalar'][$y][$k] = trim((string)($_POST[$k] ?? '')); }
            $v['sayfalar'][$y]['icerik'] = (string)($_POST['icerik'] ?? '');
            $f = $_FILES['gorsel'] ?? null;
            if ($f && ($h = yukleme_hatasi($f))) throw new RuntimeException($h);
            if ($f && $f['error'] === UPLOAD_ERR_OK) { $v['sayfalar'][$y]['gorsel'] = gorsel_kaydet($f['tmp_name'], 'sayfa', $y, 1200, true); }
            veri_yaz($v);
            bildir('Sayfa kaydedildi.', "?s=sayfalar&y=$y");
        }
        if ($s === 'firma' && $islem === 'kaydet') {
            foreach (FIRMA_ALANLARI as $a) { $v['firma'][$a[0]] = trim((string)($_POST[$a[0]] ?? '')); }
            veri_yaz($v);
            bildir('Firma bilgileri kaydedildi.', '?s=firma');
        }
        if ($s === 'mesajlar') {
            $d = VERI . '/mesajlar.json';
            $fp = fopen($d, 'c+'); flock($fp, LOCK_EX);
            $l = json_decode((string)stream_get_contents($fp), true) ?: [];
            $id = (string)($_POST['id'] ?? '');
            foreach ($l as $k => &$m) {
                if ($m['id'] !== $id) continue;
                if ($islem === 'okundu') $m['okundu'] = !$m['okundu'];
                if ($islem === 'sil') unset($l[$k]);
            }
            unset($m);
            ftruncate($fp, 0); rewind($fp); fwrite($fp, json_encode(array_values($l), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            flock($fp, LOCK_UN); fclose($fp);
            header('Location: ?s=mesajlar' . (!empty($_GET['p']) ? '&p=' . (int)$_GET['p'] : '')); exit;
        }
        if ($s === 'hesap' && $islem === 'sifre') {
            $k = kullanici();
            if (!password_verify((string)($_POST['eski'] ?? ''), $k['hash'] ?? '')) throw new RuntimeException('Mevcut şifre hatalı.');
            $y = (string)($_POST['yeni'] ?? '');
            if (mb_strlen($y) < 10) throw new RuntimeException('Yeni şifre en az 10 karakter olmalı.');
            if ($y !== (string)($_POST['yeni2'] ?? '')) throw new RuntimeException('Yeni şifreler birbiriyle aynı değil.');
            $k['hash'] = password_hash($y, PASSWORD_DEFAULT);
            $k['oturum_surum'] = bin2hex(random_bytes(8));
            kullanici_yaz($k);
            $_SESSION['surum'] = $k['oturum_surum'];
            bildir('Şifreniz değiştirildi. Diğer cihazlardaki oturumlar kapatıldı.', '?s=hesap');
        }
    } catch (RuntimeException $ex) {
        $hata = $ex->getMessage();
    }
}

// ---------------- görünüm ----------------
function ust(string $baslik, string $aktif): void {
    global $KOL;
    $ok = 0;
    foreach (json_decode((string)@file_get_contents(VERI . '/mesajlar.json'), true) ?: [] as $m) { if (empty($m['okundu'])) $ok++; }
    $menu = ['pano' => 'Genel bakış', 'mesajlar' => 'Gelen talepler' . ($ok ? " <b class=\"rozet\">$ok</b>" : ''), 'slaytlar' => 'Ana sayfa banner', 'urunler' => 'Ürünler',
        'seriler' => 'Ürün detay sayfaları', 'kataloglar' => 'Kataloglar', 'kategoriler' => 'Kategoriler', 'markalar' => 'Markalar',
        'sayfalar' => 'Sayfa metinleri', 'sss' => 'SSS', 'firma' => 'Firma bilgileri', 'hesap' => 'Hesap'];
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>' . e($baslik) . ' · DELLIFT Yönetim</title><link rel="icon" href="/favicon.svg"><link rel="stylesheet" href="/yonetim/yonetim.css?v=' . SURUM . '"></head><body>
<header class="ust"><button class="menu-ac" aria-label="Menü" onclick="document.body.classList.toggle(\'menu-acik\')">☰</button><a class="logo" href="/yonetim/"><img src="/assets/img/logo.png" alt="DELLIFT"><span>Yönetim</span></a>
<div class="ust-sag"><a href="/" target="_blank" rel="noopener">Siteyi gör ↗</a><a href="?s=cikis">Çıkış</a></div></header>
<div class="duzen"><nav class="yan">';
    foreach ($menu as $k => $a) { echo '<a href="?s=' . $k . '"' . ($k === $aktif ? ' class="aktif"' : '') . '>' . $a . '</a>'; }
    echo '</nav><main class="icerik">';
}
function alt(): void { echo '</main></div><script src="/yonetim/yonetim.js?v=' . SURUM . '"></script></body></html>'; }
function bildirimler(string $mesaj, string $hata): void {
    if ($mesaj) echo '<div class="uyari ok">' . e($mesaj) . '</div>';
    if ($hata) echo '<div class="uyari hata">' . e($hata) . '</div>';
}

function alan_html(array $a, array $x, bool $yeni): string {
    [$ad, $etiket, $tur] = $a;
    $n = 'f_' . $ad;
    $d = $x[$ad] ?? ($a['varsayilan'] ?? '');
    $ip = !empty($a['ipucu']) ? '<small>' . e($a['ipucu']) . '</small>' : '';
    $z = !empty($a['zorunlu']) ? ' <i>*</i>' : '';
    $sat = (int)($a['satir'] ?? 3);
    switch ($tur) {
        case 'metin': return "<label class=\"alan\"><span>" . e($etiket) . "$z</span><input name=\"$n\" value=\"" . e($d) . '"' . (!empty($a['zorunlu']) ? ' required' : '') . ">$ip</label>";
        case 'yazi': return "<label class=\"alan\"><span>" . e($etiket) . "$z</span><textarea name=\"$n\" rows=\"$sat\"" . (!empty($a['zorunlu']) ? ' required' : '') . '>' . e($d) . "</textarea>$ip</label>";
        case 'secim':
            $o = ''; foreach (($a['secenek'])() as $k => $v) { $o .= '<option value="' . e($k) . '"' . ((string)$k === (string)$d ? ' selected' : '') . '>' . e($v) . '</option>'; }
            return "<label class=\"alan\"><span>" . e($etiket) . "</span><select name=\"$n\">$o</select>$ip</label>";
        case 'coklu':
            $o = ''; foreach (($a['secenek'])() as $k => $v) { $o .= '<label class="kutu"><input type="checkbox" name="' . $n . '[]" value="' . e($k) . '"' . (in_array($k, (array)$d, true) ? ' checked' : '') . '> ' . e($v) . '</label>'; }
            return "<div class=\"alan\"><span>" . e($etiket) . "</span><div class=\"kutular\">$o</div>$ip</div>";
        case 'onay': return "<label class=\"kutu tek\"><input type=\"checkbox\" name=\"$n\" value=\"1\"" . (!empty($d) ? ' checked' : '') . '> ' . e($etiket) . "</label>";
        case 'satirlar': return "<label class=\"alan\"><span>" . e($etiket) . "</span><textarea class=\"kod\" name=\"$n\" rows=\"" . max($sat, 4) . '">' . e(implode("\n", array_map(fn($r) => implode(' | ', $r), (array)$d))) . "</textarea>$ip</label>";
        case 'liste': return "<label class=\"alan\"><span>" . e($etiket) . "</span><textarea class=\"kod\" name=\"$n\" rows=\"3\">" . e(implode("\n", (array)$d)) . "</textarea>$ip</label>";
        case 'teknik': return "<label class=\"alan\"><span>" . e($etiket) . "</span><textarea class=\"kod\" name=\"$n\" rows=\"$sat\">" . e(teknik_yaz((array)$d)) . "</textarea>$ip</label>";
        case 'slug':
            if (!$yeni) return '<div class="alan"><span>' . e($etiket) . '</span><code class="adres">' . e(str_replace('<marka>', (string)($x['marka'] ?? ''), $a['onek'])) . e($d) . '</code><small>Sayfa adresi yayından sonra değiştirilmez (Google\'daki bağlantılar kırılmasın diye).</small></div>';
            return "<label class=\"alan\"><span>" . e($etiket) . "</span><input name=\"$n\" placeholder=\"boş bırakırsanız addan otomatik oluşur\" pattern=\"[a-z0-9-]*\"><small>Yalnız küçük harf, rakam ve tire. Örnek: " . e($a['onek']) . "nigmer-nmr-dizel</small></label>";
        case 'gorsel':
            $on = $d ? '<div class="onizleme"><img src="' . e($d) . '" alt=""><label class="kutu"><input type="checkbox" name="' . $n . '_sil" value="1"> Görseli kaldır</label></div>' : '';
            return "<div class=\"alan\"><span>" . e($etiket) . "$z</span>$on<input type=\"file\" name=\"$n\" accept=\"image/jpeg,image/png,image/webp\">" . ($d ? '<small>Yeni dosya seçerseniz mevcut görselin yerine geçer.</small>' : '') . "$ip</div>";
        case 'galeri':
            $o = '';
            foreach ((array)$d as $g) { $o .= '<li draggable="true"><img src="' . e($g) . '-800.webp" alt=""><input type="hidden" name="' . $n . '_sira[]" value="' . e($g) . '"><button type="button" class="g-sol" title="Öne al">◀</button><button type="button" class="g-sil" title="Kaldır">✕</button></li>'; }
            return "<div class=\"alan\"><span>" . e($etiket) . "</span><input type=\"hidden\" name=\"{$n}_sira[]\" value=\"\"><ul class=\"galeri\">$o</ul><input type=\"file\" name=\"{$n}[]\" accept=\"image/jpeg,image/png,image/webp\" multiple>$ip</div>";
        case 'pdf':
            $on = $d ? '<p><a href="' . e($d) . '" target="_blank" rel="noopener">Mevcut PDF\'i aç ↗</a>' . (empty($a['zorunlu']) ? ' <label class="kutu"><input type="checkbox" name="' . $n . '_sil" value="1"> PDF\'i kaldır</label>' : '') . '</p>' : '';
            return "<div class=\"alan\"><span>" . e($etiket) . ($d ? '' : $z) . "</span>$on<input type=\"file\" name=\"$n\" accept=\"application/pdf\"" . (!empty($a['kapak']) ? ' data-kapak="1"' : '') . "><small>En fazla " . e(ini_get('upload_max_filesize')) . '.' . ($d ? ' Yeni dosya seçerseniz mevcut PDF\'in yerine geçer.' : '') . "</small>$ip" . (!empty($a['kapak']) ? '<input type="hidden" name="kapak_veri">' : '') . "</div>";
    }
    return '';
}

function giris_sayfasi(string $hata): void {
    $unuttum = isset($_GET['unuttum']);
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Giriş · DELLIFT Yönetim</title><link rel="icon" href="/favicon.svg"><link rel="stylesheet" href="/yonetim/yonetim.css?v=' . SURUM . '"></head><body class="giris">
<form method="post" class="giris-kutu"><img src="/assets/img/logo.png" alt="DELLIFT"><h1>Yönetim paneli</h1>' . csrf_alan();
    if ($hata) echo '<div class="uyari ' . (str_contains($hata, 'gönderildi') ? 'ok' : 'hata') . '">' . e($hata) . '</div>';
    if ($unuttum) {
        echo '<p>Şifre yenileme bağlantısı kayıtlı e-posta adresine gönderilir.</p><input type="hidden" name="islem" value="unuttum"><button class="btn">Yenileme bağlantısı gönder</button><p><a href="/yonetim/">← Girişe dön</a></p>';
    } else {
        echo '<input type="hidden" name="islem" value="giris"><label class="alan"><span>Kullanıcı adı</span><input name="kullanici" autocomplete="username" required autofocus></label>
<label class="alan"><span>Şifre</span><input type="password" name="sifre" autocomplete="current-password" required></label><button class="btn">Giriş yap</button><p><a href="?unuttum=1">Şifremi unuttum</a></p>';
    }
    echo '</form></body></html>';
}

// ---------------- sayfalar ----------------
if ($s === 'pano') {
    ust('Genel bakış', 'pano');
    $v = veri();
    $ms = json_decode((string)@file_get_contents(VERI . '/mesajlar.json'), true) ?: [];
    $ok = count(array_filter($ms, fn($m) => empty($m['okundu'])));
    echo '<h1>Hoş geldiniz</h1>';
    bildirimler($mesaj, $hata);
    echo '<div class="kartlar">
<a class="kart" href="?s=mesajlar"><b>' . $ok . '</b><span>okunmamış talep</span></a>
<a class="kart" href="?s=urunler"><b>' . count($v['urunler'] ?? []) . '</b><span>ürün kartı</span></a>
<a class="kart" href="?s=seriler"><b>' . count($v['seriler'] ?? []) . '</b><span>ürün detay sayfası</span></a>
<a class="kart" href="?s=kataloglar"><b>' . count($v['kataloglar'] ?? []) . '</b><span>katalog</span></a>
<a class="kart" href="?s=slaytlar"><b>' . count($v['slaytlar'] ?? []) . '</b><span>ana sayfa banner</span></a>
<a class="kart" href="?s=markalar"><b>' . count($v['markalar'] ?? []) . '</b><span>marka</span></a></div>
<div class="kutu-bilgi"><h2>Nasıl kullanılır?</h2><ul>
<li><b>Yeni ürün:</b> Ürünler → “Yeni ürün”. Görsel, marka, kategori ve kart özelliklerini girin; kaydettiğiniz anda sitede görünür.</li>
<li><b>Teknik tablolu ürün sayfası:</b> Ürün detay sayfaları → “Yeni detay sayfası”. Sonra ürün kartında “Detay sayfası” olarak seçin.</li>
<li><b>Katalog (PDF):</b> Kataloglar → “Yeni katalog”. Kapak görseli PDF’in ilk sayfasından otomatik oluşur.</li>
<li><b>Ana sayfa görselleri:</b> Ana sayfa banner bölümünden görsel ve yazıları değiştirin.</li>
<li>Her kayıtta içeriğin önceki hâli otomatik yedeklenir.</li></ul></div>';
    alt(); exit;
}

if ($s === 'mesajlar') {
    ust('Gelen talepler', 'mesajlar');
    echo '<h1>Gelen talepler</h1><p class="aciklama">Sitedeki teklif formundan gelen talepler. Her talep ayrıca ' . e(F('eposta')) . ' adresine e-postayla gönderilir.</p>';
    bildirimler($mesaj, $hata);
    $ms = json_decode((string)@file_get_contents(VERI . '/mesajlar.json'), true) ?: [];
    if (!$ms) echo '<p class="bos">Henüz talep yok.</p>';
    $sp = sayfala($ms, 20, 'p');
    foreach ($sp['l'] as $m) {
        $tel = preg_replace('~\D~', '', $m['tel']);
        $wa = str_starts_with($tel, '0') ? '9' . $tel : (str_starts_with($tel, '90') ? $tel : '90' . $tel);
        echo '<div class="mesaj' . (empty($m['okundu']) ? ' yeni' : '') . '"><div class="m-bas"><b>' . e($m['ad']) . '</b>' . ($m['firma'] ? ' · ' . e($m['firma']) : '') . '<span>' . e(date('d.m.Y H:i', strtotime($m['tarih']))) . '</span></div>
<div class="m-bilgi"><a href="tel:' . e($m['tel']) . '">' . e($m['tel']) . '</a> · <a href="https://wa.me/' . e($wa) . '" target="_blank" rel="noopener">WhatsApp</a>' . ($m['eposta'] ? ' · <a href="mailto:' . e($m['eposta']) . '">' . e($m['eposta']) . '</a>' : '') . ' · ' . e($m['konu']) . ' · <small>' . e($m['sayfa']) . '</small></div>
<p>' . nl2br(e($m['mesaj'])) . '</p>
<form method="post" class="m-islem">' . csrf_alan() . '<input type="hidden" name="id" value="' . e($m['id']) . '"><button name="islem" value="okundu" class="btn ince">' . (empty($m['okundu']) ? 'Okundu işaretle' : 'Okunmadı yap') . '</button><button name="islem" value="sil" class="btn ince sil" data-onay="Bu talep silinsin mi?">Sil</button></form></div>';
    }
    echo $sp['nav'];
    alt(); exit;
}

if (isset($KOL[$s])) {
    $t = $KOL[$s];
    $v = veri();
    $l = $v[$s] ?? [];
    if (isset($_GET['i']) || isset($_GET['yeni']) || ($hata && ($_POST['islem'] ?? '') === 'kaydet')) {
        $i = isset($_GET['i']) ? (int)$_GET['i'] : null;
        $yeni = $i === null || !isset($l[$i]);
        $x = $yeni ? [] : $l[$i];
        if ($hata && ($_POST['islem'] ?? '') === 'kaydet') {
            // hatalı gönderimde girilenleri koru (dosyalar hariç)
            foreach ($t['alanlar'] as $a) {
                $n = 'f_' . $a[0];
                if (!array_key_exists($n, $_POST) && $a[2] !== 'onay') continue;
                $h = is_array($_POST[$n] ?? null) ? '' : (string)($_POST[$n] ?? '');
                switch ($a[2]) {
                    case 'satirlar': $x[$a[0]] = satir_coz($h); break;
                    case 'liste': $x[$a[0]] = preg_split('~\R~u', $h); break;
                    case 'teknik': $x[$a[0]] = teknik_coz($h); break;
                    case 'onay': $x[$a[0]] = !empty($_POST[$n]); break;
                    case 'coklu': $x[$a[0]] = (array)($_POST[$n] ?? []); break;
                    case 'gorsel': case 'galeri': case 'pdf': case 'slug': break;
                    default: $x[$a[0]] = $h;
                }
            }
        }
        ust($t['ad'], $s);
        echo '<p class="geri"><a href="?s=' . $s . '">← ' . e($t['ad']) . '</a></p><h1>' . ($yeni ? 'Yeni ' . e($t['tekil']) : e(($t['goster'])($x))) . '</h1>';
        if (!$yeni && !empty($t['url'])) echo '<p class="aciklama"><a href="' . e(($t['url'])($x)) . '" target="_blank" rel="noopener">Sitede görüntüle ↗</a></p>';
        bildirimler($mesaj, $hata);
        echo '<form method="post" enctype="multipart/form-data" class="duzenle" action="?s=' . $s . ($yeni ? '&yeni=1' : '&i=' . $i) . '">' . csrf_alan() . '<input type="hidden" name="islem" value="kaydet"><input type="hidden" name="i" value="' . ($yeni ? '' : $i) . '">';
        foreach ($t['alanlar'] as $a) { echo alan_html($a, $x, $yeni); }
        echo '<div class="dugmeler"><button class="btn">Kaydet</button><a class="btn ikincil" href="?s=' . $s . '">Vazgeç</a></div></form>';
        if (!$yeni) echo '<form method="post" class="sil-form">' . csrf_alan() . '<input type="hidden" name="i" value="' . $i . '"><button name="islem" value="sil" class="btn ince sil" data-onay="Bu ' . e($t['tekil']) . ' silinsin mi? Bu işlem geri alınamaz.">Sil</button></form>';
        alt(); exit;
    }
    ust($t['ad'], $s);
    echo '<div class="baslik-satir"><h1>' . e($t['ad']) . '</h1><a class="btn" href="?s=' . $s . '&yeni=1">+ Yeni ' . e($t['tekil']) . '</a></div><p class="aciklama">' . e($t['aciklama']) . '</p>';
    bildirimler($mesaj, $hata);
    $f = (string)($_GET['f'] ?? '');
    $ara = trim((string)($_GET['q'] ?? ''));
    if (!empty($t['filtre'])) {
        echo '<div class="filtre"><a href="?s=' . $s . '"' . ($f === '' ? ' class="aktif"' : '') . '>Tümü (' . count($l) . ')</a>';
        foreach (markalar() as $m) {
            $n = count(array_filter($l, fn($y) => $y['marka'] === $m['slug']));
            if ($n) echo '<a href="?s=' . $s . '&f=' . e($m['slug']) . '"' . ($f === $m['slug'] ? ' class="aktif"' : '') . '>' . e($m['ad']) . " ($n)</a>";
        }
        echo '</div>';
    }
    if (count($l) > 10) {
        echo '<form class="ara-kutu" method="get"><input type="hidden" name="s" value="' . e($s) . '">' . ($f !== '' ? '<input type="hidden" name="f" value="' . e($f) . '">' : '') .
            '<input type="search" name="q" value="' . e($ara) . '" placeholder="Ara…"><button class="btn ince">Ara</button>' . ($ara !== '' ? '<a href="?s=' . e($s) . ($f !== '' ? '&f=' . e($f) : '') . '">Temizle</a>' : '') . '</form>';
    }
    $gorunen = [];
    foreach ($l as $i => $x) {
        if ($f !== '' && ($x[$t['filtre']] ?? '') !== $f) continue;
        if ($ara !== '' && mb_stripos(kucuk(($t['goster'])($x) . ' ' . (!empty($t['alt']) ? ($t['alt'])($x) : '')), kucuk($ara)) === false) continue;
        $gorunen[$i] = $x;
    }
    $sp = sayfala(array_keys($gorunen), 25, 'p');
    echo '<ul class="liste">';
    foreach ($sp['l'] as $i) {
        $x = $gorunen[$i];
        $r = !empty($t['resim']) ? ($t['resim'])($x) : '';
        echo '<li id="k' . $i . '">' . ($r ? '<img src="' . e($r) . '" alt="" loading="lazy">' : '') . '<a class="l-ad" href="?s=' . $s . '&i=' . $i . '"><b>' . e(($t['goster'])($x)) . '</b>' . (!empty($t['alt']) ? '<small>' . e(($t['alt'])($x)) . '</small>' : '') . '</a>
<form method="post" class="tasi" action="?' . e(http_build_query(array_diff_key($_GET, ['i' => 1, 'yeni' => 1]))) . '">' . csrf_alan() . '<input type="hidden" name="islem" value="tasi"><input type="hidden" name="i" value="' . $i . '"><button name="yon" value="yukari" title="Yukarı">▲</button><button name="yon" value="asagi" title="Aşağı">▼</button></form>
<a class="btn ince" href="?s=' . $s . '&i=' . $i . '">Düzenle</a></li>';
    }
    echo '</ul>' . $sp['nav'];
    if ($l && !$gorunen) echo '<p class="bos">Aramaya uyan kayıt yok.</p>';
    if (!$l) echo '<p class="bos">Henüz kayıt yok.</p>';
    alt(); exit;
}

if ($s === 'sayfalar') {
    $v = veri();
    $y = (string)($_GET['y'] ?? '');
    ust('Sayfa metinleri', 'sayfalar');
    if (!isset($v['sayfalar'][$y])) {
        echo '<h1>Sayfa metinleri</h1><p class="aciklama">Kurumsal ve hizmet sayfalarının yazıları.</p>';
        bildirimler($mesaj, $hata);
        echo '<ul class="liste">';
        foreach (SAYFA_ADLARI as $k => $a) { if (isset($v['sayfalar'][$k])) echo '<li><a class="l-ad" href="?s=sayfalar&y=' . $k . '"><b>' . e($a) . '</b><small>/' . $k . '</small></a><a class="btn ince" href="?s=sayfalar&y=' . $k . '">Düzenle</a></li>'; }
        echo '</ul>';
        alt(); exit;
    }
    $p = $v['sayfalar'][$y];
    echo '<p class="geri"><a href="?s=sayfalar">← Sayfa metinleri</a></p><h1>' . e(SAYFA_ADLARI[$y] ?? $y) . '</h1><p class="aciklama"><a href="/' . e($y) . '" target="_blank" rel="noopener">Sitede görüntüle ↗</a></p>';
    bildirimler($mesaj, $hata);
    echo '<form method="post" enctype="multipart/form-data" class="duzenle">' . csrf_alan() . '<input type="hidden" name="islem" value="kaydet"><input type="hidden" name="yol" value="' . e($y) . '">
<label class="alan"><span>Sayfa başlığı (H1)</span><input name="h1" value="' . e($p['h1']) . '" required></label>
<label class="alan"><span>Başlık altı yazı</span><input name="alt" value="' . e($p['alt'] ?? '') . '"></label>
<div class="alan"><span>Sayfa içeriği</span><div class="editor" data-hedef="icerik"><div class="arac"><button type="button" data-k="formatBlock" data-d="h2">Başlık</button><button type="button" data-k="formatBlock" data-d="p">Paragraf</button><button type="button" data-k="bold"><b>K</b></button><button type="button" data-k="insertUnorderedList">• Liste</button><button type="button" data-k="insertOrderedList">1. Liste</button><button type="button" data-k="createLink">Bağlantı</button><button type="button" data-k="unlink">Bağlantıyı kaldır</button><button type="button" data-k="html">HTML</button></div><div class="alan-yaz metin" contenteditable="true">' . $p['icerik'] . '</div></div><textarea name="icerik" class="kod gizli" rows="20">' . e($p['icerik']) . '</textarea></div>
<details class="alan"><summary>Google (SEO) ayarları ve üst görsel</summary>
<label class="alan"><span>Tarayıcı / Google başlığı</span><input name="baslik" value="' . e($p['baslik']) . '"></label>
<label class="alan"><span>Google açıklaması</span><textarea name="aciklama" rows="2">' . e($p['aciklama']) . '</textarea><small>150–160 karakter idealdir.</small></label>
<div class="alan"><span>Üst bant görseli</span><div class="onizleme"><img src="' . e($p['gorsel']) . '" alt=""></div><input type="file" name="gorsel" accept="image/jpeg,image/png,image/webp"></div></details>
<div class="dugmeler"><button class="btn">Kaydet</button></div></form>';
    alt(); exit;
}

if ($s === 'firma') {
    ust('Firma bilgileri', 'firma');
    echo '<h1>Firma bilgileri</h1><p class="aciklama">Sitenin her yerinde (üst şerit, alt bilgi, iletişim, WhatsApp düğmeleri) kullanılan bilgiler.</p>';
    bildirimler($mesaj, $hata);
    echo '<form method="post" class="duzenle">' . csrf_alan() . '<input type="hidden" name="islem" value="kaydet">';
    foreach (FIRMA_ALANLARI as $a) { echo '<label class="alan"><span>' . e($a[1]) . '</span><input name="' . $a[0] . '" value="' . e(F($a[0])) . '">' . (!empty($a[2]) ? '<small>' . e($a[2]) . '</small>' : '') . '</label>'; }
    echo '<div class="dugmeler"><button class="btn">Kaydet</button></div></form>';
    alt(); exit;
}

if ($s === 'hesap') {
    ust('Hesap', 'hesap');
    $k = kullanici();
    echo '<h1>Hesap</h1><p class="aciklama">Kullanıcı adı: <b>' . e($k['kullanici'] ?? '') . '</b> · Şifre yenileme e-postası: ' . e($k['eposta'] ?? '') . '</p>';
    bildirimler($mesaj, $hata);
    echo '<form method="post" class="duzenle dar">' . csrf_alan() . '<input type="hidden" name="islem" value="sifre"><h2>Şifre değiştir</h2>
<label class="alan"><span>Mevcut şifre</span><input type="password" name="eski" autocomplete="current-password" required></label>
<label class="alan"><span>Yeni şifre</span><input type="password" name="yeni" autocomplete="new-password" minlength="10" required><small>En az 10 karakter.</small></label>
<label class="alan"><span>Yeni şifre (tekrar)</span><input type="password" name="yeni2" autocomplete="new-password" minlength="10" required></label>
<div class="dugmeler"><button class="btn">Şifreyi değiştir</button></div></form>';
    alt(); exit;
}

header('Location: /yonetim/');
