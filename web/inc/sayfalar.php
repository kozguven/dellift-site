<?php
// DELLIFT — sayfa gövdeleri.
declare(strict_types=1);

function sayfa_bas(string $baslik, string $alt, array $kirinti, string $gorsel = '/assets/img/forklift-nigmer-800.webp'): string {
    $k = ['<a href="/">Ana Sayfa</a>'];
    foreach ($kirinti as [$a, $h]) { $k[] = $h ? '<a href="' . e($h) . '">' . e($a) . '</a>' : e($a); }
    return '<section class="sayfa-bas" style="--bas-gorsel:url(\'' . e($gorsel) . '\')"><div class="kap">
<div class="kirinti">' . implode(' <span>/</span> ', $k) . '</div>
<h1>' . e($baslik) . '</h1>' . ($alt !== '' ? '<p>' . e($alt) . '</p>' : '') . '
</div></section>';
}

function yan_kutu(): string {
    return '<div class="yan-kutu"><h3>Hızlı teklif alın</h3>
<p>İhtiyacınızı yazın, aynı gün size uygun makine ve fiyatla dönüş yapalım.</p>
<a class="btn btn-k" href="/iletisim#teklif">Teklif Formu ' . I['ok'] . '</a>
<a class="btn btn-c" href="' . e(wa('Merhaba, teklif almak istiyorum.')) . '" target="_blank" rel="noopener">' . I['wa'] . ' WhatsApp</a>
<a class="btn btn-c" href="tel:' . e(F('tel_uri')) . '">' . I['tel'] . ' ' . e(F('tel')) . '</a></div>';
}

function hizmet_yan(string $aktif): string {
    $l = ['forklift-satis' => 'Forklift Satış', 'forklift-kiralama' => 'Forklift Kiralama', 'servis' => 'Servis ve Yedek Parça',
          'finansal-cozumler' => 'Finansal Çözümler', 'sss' => 'Sıkça Sorulan Sorular'];
    $o = '';
    foreach ($l as $s => $a) { $o .= '<a href="/' . $s . '"' . ($s === $aktif ? ' aria-current=page' : '') . '>' . $a . '<span>›</span></a>'; }
    return '<nav class="yan-liste" aria-label="Hizmetler">' . $o . '</nav>';
}

function form_html(string $baslik = 'Teklif / bilgi talebi'): string {
    $opts = '';
    foreach (['Forklift satın almak istiyorum', 'Forklift kiralamak istiyorum', 'Servis / yedek parça', 'Finansman / leasing', 'Diğer'] as $s) { $opts .= "<option>$s</option>"; }
    return '<form class="form" id="teklif" method="post" action="/gonder.php">
<h3>' . e($baslik) . '</h3>
<div id="form-uyari"></div>
<div class="alanlar">
<div class="alan"><label for="ad">Ad Soyad *</label><input id="ad" name="ad" required maxlength="80" autocomplete="name"></div>
<div class="alan"><label for="firma">Firma</label><input id="firma" name="firma" maxlength="120" autocomplete="organization"></div>
<div class="alan"><label for="tel">Telefon *</label><input id="tel" name="tel" type="tel" required maxlength="25" autocomplete="tel"></div>
<div class="alan"><label for="eposta">E-posta</label><input id="eposta" name="eposta" type="email" maxlength="120" autocomplete="email"></div>
<div class="alan tam"><label for="konu">Konu</label><select id="konu" name="konu">' . $opts . '</select></div>
<div class="alan tam"><label for="mesaj">Mesajınız *</label><textarea id="mesaj" name="mesaj" required maxlength="3000" placeholder="Örn. 3 ton dizel forklift, 4,5 m kaldırma, açık saha"></textarea></div>
<div class="gizli-alan" aria-hidden="true"><label>Web sitesi<input name="web" tabindex="-1" autocomplete="off"></label></div>
<label class="onay"><input type="checkbox" name="kvkk" required> <span><a href="/kvkk" target="_blank">KVKK Aydınlatma Metni</a>\'ni okudum; talebimin değerlendirilmesi için benimle iletişime geçilmesini kabul ediyorum.</span></label>
<div class="alan tam"><button class="btn btn-k" type="submit">Gönder ' . I['ok'] . '</button></div>
</div></form>';
}

function iletisim_blok(bool $gri = true): string {
    $q = rawurlencode(F('harita_q'));
    $tu = e(F('tel_uri'));
    return '<section class="bolum' . ($gri ? ' gri' : '') . '" id="iletisim"><div class="kap">
<div class="iletisim-grid">
<div>
<span class="etiket">İletişim</span><h2>Size en yakın çözüm bir telefon uzağınızda</h2>
<ul class="bilgi">
<li><span class="ik">' . I['tel'] . '</span><div><small>Telefon / WhatsApp</small><a href="tel:' . $tu . '">' . e(F('tel')) . '</a></div></li>
<li><span class="ik">' . I['posta'] . '</span><div><small>E-posta</small><a href="mailto:' . e(F('eposta')) . '">' . e(F('eposta')) . '</a></div></li>
<li><span class="ik">' . I['konum'] . '</span><div><small>Adres</small><span>' . e(F('adres')) . '</span></div></li>
</ul>
<div class="harita"><iframe title="DELLIFT konum haritası" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=' . $q . '&amp;z=16&amp;output=embed"></iframe></div>
</div>
' . form_html() . '
</div></div></section>';
}

function slayt_baslik(string $s): string {
    // *vurgu* → kırmızı vurgulu kısım
    return preg_replace('~\*(.+?)\*~u', '<span>$1</span>', e($s));
}

function urun_kart(array $u): string {
    $m = marka_ad($u['marka']);
    $satir = '';
    foreach ($u['ozellik'] ?? [] as $r) { $satir .= '<tr><td>' . e($r[0] ?? '') . '</td><td>' . e($r[1] ?? '') . '</td></tr>'; }
    $d = ($u['detay'] ?? '') !== '' && seri($u['detay']) ? '/urun/' . $u['detay'] : '';
    $img = '<img src="' . e($u['gorsel']) . '" alt="' . e($u['ad']) . '" loading="lazy">';
    $bas = $d ? '<a href="' . e($d) . '" style="text-decoration:none">' . e($u['ad']) . '</a>' : e($u['ad']);
    $detay = $d ? '<a class="btn btn-s" href="' . e($d) . '">Teknik Özellikler</a>' : '';
    return '<div class="urun">' . ($d ? '<a href="' . e($d) . '">' . $img . '</a>' : $img) . '<div class="ic">
<span class="etiket">' . e($m) . ' · ' . e($u['model'] ?? '') . '</span><h3>' . $bas . '</h3><p style="color:var(--gri);font-size:.95rem">' . e($u['aciklama'] ?? '') . '</p>
<table>' . $satir . '</table><div class="urun-btn">' . $detay . '<a class="btn btn-k" href="' . e(wa('Merhaba, ' . $u['ad'] . ' için fiyat almak istiyorum.')) . '" target="_blank" rel="noopener">Fiyat Sor ' . I['ok'] . '</a></div></div></div>';
}

function katalog_kart(array $k): string {
    $m = marka_ad($k['marka']);
    $sf = '/katalog/' . $k['marka'] . '/' . $k['slug'];
    $kk = kat($k['kat']);
    $kapak = $k['kapak'] ?: '/assets/img/forklift-nigmer-800.webp';
    return '<div class="kt"><a href="' . e($sf) . '"><img src="' . e($kapak) . '" alt="' . e("$m {$k['model']}") . ' kataloğu" loading="lazy"></a>
<div class="ic"><span class="tur">' . e($kk['kisa'] ?? '') . '</span><b><a href="' . e($sf) . '" style="text-decoration:none">' . e("$m {$k['model']}") . '</a></b><p>' . e($k['aciklama']) . '</p>
<div class="bag"><a href="' . e($sf) . '">Kataloğu incele' . (($k['dil'] ?? '') === 'EN' ? ' (İng.)' : '') . '</a><a class="wa" href="' . e(wa("Merhaba, $m {$k['model']} için fiyat almak istiyorum.")) . '" target="_blank" rel="noopener">Fiyat sor →</a></div></div></div>';
}

/** Katalog listesi: 12'şerli sayfalı; $marka_basligi=true ise sayfa içinde marka başlıklarıyla gruplanır. */
function katalog_blok(array $liste, bool $marka_basligi): string {
    if (!$liste) return '';
    $sp = sayfala($liste, 12, 'sayfa', 'katalog');
    $o = ''; $grup = null; $ic = '';
    foreach ($sp['l'] as $k) {
        if ($marka_basligi && $k['marka'] !== $grup) {
            if ($ic !== '') { $o .= '<div class="katalog">' . $ic . '</div>'; $ic = ''; }
            $o .= '<h3 style="margin:32px 0 0;font-size:1.5rem">' . e(marka_ad($k['marka'])) . '</h3>';
            $grup = $k['marka'];
        }
        $ic .= katalog_kart($k);
    }
    if ($ic !== '') { $o .= '<div class="katalog">' . $ic . '</div>'; }
    return $o . $sp['nav'];
}

/** Ürün kartları: 12'şerli sayfalı (?urun=N). */
function urun_blok(array $us): string {
    $sp = sayfala($us, 12, 'urun', 'modeller');
    return '<div class="urunler">' . implode('', array_map('urun_kart', $sp['l'])) . '</div>' . $sp['nav'];
}

function marka_kartlari(array $ms, string $kat_ad): string {
    $o = '';
    foreach ($ms as $m) {
        $o .= '<div class="mk"><div class="ad">' . e($m['ad']) . '</div><p>' . e($m['ozet']) . '</p>
<div class="bag"><a class="wa" href="' . e(wa("Merhaba, {$m['ad']} " . kucuk($kat_ad) . ' modelleri hakkında bilgi almak istiyorum.')) . '" target="_blank" rel="noopener">WhatsApp\'tan model ve fiyat sor →</a><a href="/marka/' . e($m['slug']) . '">Marka sayfası</a></div></div>';
    }
    return '<div class="marka-kartlar">' . $o . '</div>';
}

function kat_kart(array $k, string $gorsel = ''): string {
    $img = $gorsel ? '<img src="' . e($gorsel) . '" alt="" loading="lazy">' : '';
    return '<a class="kat" href="/' . e($k['slug']) . '">' . $img . CIZIM . '<span class="tur">' . (!empty($k['ust']) ? 'Forklift' : 'Depo ekipmanı') . '</span><h3>' . e($k['ad']) . '</h3><p>' . e($k['ozet']) . '</p></a>';
}

function markalar_serit(): string {
    $o = ''; foreach (markalar() as $m) { $o .= '<a class="marka" href="/marka/' . e($m['slug']) . '">' . e($m['ad']) . '</a>'; }
    return '<div class="markalar">' . $o . '</div>';
}

// ---------------- sayfalar ----------------
function ana_sayfa(): string {
    $v = veri(); $I = I;
    $sl = $v['slaytlar'] ?? [];
    $s_html = ''; $nok = '';
    foreach ($sl as $i => $s) {
        $yukle = $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"';
        $b1 = baglanti($s['b1_link'] ?? ''); $b2 = baglanti($s['b2_link'] ?? '');
        $dug = '';
        if (($s['b1_yazi'] ?? '') !== '') { $dug .= '<a class="btn btn-k" href="' . e($b1) . '"' . (dis_mi($b1) ? ' target="_blank" rel="noopener"' : '') . '>' . e($s['b1_yazi']) . ' ' . $I['ok'] . '</a>'; }
        if (($s['b2_yazi'] ?? '') !== '') { $dug .= '<a class="btn btn-c" href="' . e($b2) . '"' . (dis_mi($b2) ? ' target="_blank" rel="noopener"' : '') . '>' . e($s['b2_yazi']) . '</a>'; }
        $h = $i === 0 ? 'h1' : 'h2';
        $s_html .= '<div class="slayt' . ($i === 0 ? ' aktif sag' : '') . '" aria-roledescription="slayt">
<img src="' . e($s['gorsel']) . '" alt="" ' . $yukle . '>
<div class="kap"><div class="icerik"><span class="etiket" style="color:#fff">DELLIFT · Satış · Kiralama · Servis</span>
<' . $h . '>' . slayt_baslik($s['baslik']) . '</' . $h . '><p>' . e($s['metin']) . '</p>
<div class="dugmeler">' . $dug . '</div></div></div></div>';
        $nok .= '<button aria-label="Slayt ' . ($i + 1) . '"' . ($i === 0 ? ' class=aktif' : '') . '></button>';
    }
    $hero = '<section class="hero" aria-label="Öne çıkanlar">' . $s_html .
        (count($sl) > 1 ? '<button class="hero-ok sol" aria-label="Önceki">' . $I['sol'] . '</button><button class="hero-ok sag" aria-label="Sonraki">' . $I['sag'] . '</button><div class="noktalar">' . $nok . '</div>' : '') . '
<div class="hero-alt"><div class="kap">
<div class="oz">' . $I['saat'] . '<div><b>24 saatte servis</b><small>Servis ve yedek parça garantisi</small></div></div>
<div class="oz">' . $I['tl'] . '<div><b>6 ay ödemesiz</b><small>60 aya varan leasing seçenekleri</small></div></div>
<div class="oz">' . $I['kalkan'] . '<div><b>Sıfır ve 2. el</b><small>Kontrolden geçmiş, teslime hazır</small></div></div>
</div></div></section>';

    $giris = '<section class="bolum"><div class="kap ikili">
<div class="gorsel"><img src="/assets/img/forklift-nigmer.webp" alt="DELLIFT deposunda turkuaz Niğmer dizel forklift" width="1536" height="1152" loading="lazy"></div>
<div><span class="etiket">Neden DELLIFT</span>
<h2>Doğru ekipman, güvenilir çözüm</h2>
<p>DELLIFT; forklift, istif makinası ve transpalet ihtiyacınızı tek noktadan karşılar. İşinizi dinler, sahanıza uygun makineyi önerir, teslimden sonra da yanınızda oluruz.</p>
<ul class="ozellikler">
<li><span class="ik">' . $I['kisi'] . '</span><div><b>Uzman kadro</b><span>Forklift ve iş makinesi alanında deneyimli satış ve servis ekibi.</span></div></li>
<li><span class="ik">' . $I['kalkan'] . '</span><div><b>Güvenilir, kaliteli makineler</b><span>Bilinen markalar, kontrol edilmiş ikinci el makineler, şeffaf teknik bilgi.</span></div></li>
<li><span class="ik">' . $I['anahtar'] . '</span><div><b>Satış sonrası destek</b><span>24 saatte servis ve yedek parça; periyodik bakım ile makineniz hep çalışır.</span></div></li>
</ul></div></div></section>';

    $hl = [['forklift-satis', 'Forklift Satış', 'Sıfır ve ikinci el dizel, akülü forklift; istif makinası ve transpalet.'],
           ['forklift-kiralama', 'Forklift Kiralama', 'Günlük, aylık, uzun dönem. Bakım ve servis kiraya dahil.'],
           ['servis', 'Servis ve Yedek Parça', '24 saatte servis ve yedek parça garantisi, periyodik bakım.'],
           ['finansal-cozumler', 'Finansal Çözümler', 'Peşin alımda avantajlı fiyat, 60 aya varan leasing.']];
    $hk = '';
    foreach ($hl as $i => [$s, $a, $d]) { $hk .= '<a class="hizmet" href="/' . $s . '"><span class="no">0' . ($i + 1) . '</span><h3>' . $a . '</h3><p>' . $d . '</p><span class="ok">Detaylı bilgi →</span></a>'; }
    $hizmetler = '<section class="bolum gri"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Hizmetlerimiz</span><h2>Satıştan servise tek muhatap</h2></div>
<p>Makineyi satın alın, kiralayın ya da mevcut filonuzun bakımını bize bırakın. Her durumda tek telefonla ulaşacağınız bir ekip.</p></div>
<div class="hizmetler">' . $hk . '</div></div></section>';

    $kg = ['dizel-forklift' => '/assets/img/forklift-nigmer-800.webp', 'akulu-forklift' => '/assets/img/hero-kiralama-800.webp', 'ikinci-el-forklift' => '/assets/img/hero-servis-800.webp'];
    $kartlar = ''; foreach (kategoriler() as $k) { $kartlar .= kat_kart($k, $k['gorsel'] ?? ($kg[$k['slug']] ?? '')); }
    $one = array_filter(urunler(), fn($u) => !empty($u['anasayfa']));
    $one_html = $one ? '<h3 style="margin:48px 0 18px;font-size:1.7rem">Öne çıkan modeller</h3><div class="urunler">' . implode('', array_map('urun_kart', $one)) . '</div>' : '';
    $urunler = '<section class="bolum"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Ürünler</span><h2>Her iş için doğru makine</h2></div>
<p>Dizel ve akülü forkliftlerden istif makinalarına, akülü ve manuel transpaletlere kadar geniş ürün yelpazesi.</p></div>
<div class="kategoriler">' . $kartlar . '</div>' . $one_html . '</div></section>';

    $band = '<section class="band"><div class="kap">
<div><h2>24 saatte servis ve yedek parça</h2><p>Makineniz arızalandığında 24 saat içinde servis ve yedek parça desteği sağlıyoruz. Bakım planınızı da birlikte çıkaralım.</p></div>
<a class="btn" href="/servis">Servis Talebi ' . $I['ok'] . '</a></div></section>';

    $video = '<section class="bolum koyu"><div class="kap video-ikili">
<div class="video-kutu"><video src="/assets/video/dellift-tanitim.mp4" poster="/assets/img/video-poster.jpg" muted loop playsinline autoplay preload="none" aria-label="DELLIFT tanıtım videosu"></video></div>
<div><span class="etiket">DELLIFT</span><h2>Satış, kiralama ve servis aynı çatı altında</h2>
<p style="color:#c9ccd0">Sultanbeyli\'deki merkezimizden İstanbul ve çevresindeki işletmelere hizmet veriyoruz. Makineyi seçerken, teslim alırken ve yıllarca kullanırken muhatabınız aynı ekip.</p>
<div class="sayilar">
<div><b>24<em>s</em></b><span>içinde servis ve yedek parça</span></div>
<div><b>60<em>ay</em></b><span>vadeye kadar leasing seçeneği</span></div>
<div><b>' . count(markalar()) . '</b><span>marka: ' . e(implode(', ', array_column(markalar(), 'ad'))) . '</span></div>
<div><b>6<em>ay</em></b><span>ödemesiz finansman imkânı</span></div>
</div>
<div style="margin-top:30px;display:flex;gap:12px;flex-wrap:wrap"><a class="btn btn-k" href="/hakkimizda">Hakkımızda ' . $I['ok'] . '</a><a class="btn btn-c" href="' . e(F('instagram')) . '" target="_blank" rel="noopener">' . $I['ig'] . ' Instagram</a></div>
</div></div></section>';

    $mk = '<section class="bolum"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Markalar</span><h2>Çalıştığımız markalar</h2></div><p>Markaya tıklayın, o markanın forklift ve istif makinası seçeneklerini görün.</p></div>
' . markalar_serit() . '</div></section>';

    $ilk = $sl[0]['gorsel'] ?? '';
    return sayfa('index', 'DELLIFT | Forklift Satış, Kiralama ve Servis – İstanbul',
        'DELLIFT: sıfır ve ikinci el forklift satışı, forklift kiralama, 24 saatte servis ve yedek parça. ' . implode(', ', array_column(markalar(), 'ad')) . '. Sultanbeyli / İstanbul.',
        $hero . $giris . $hizmetler . $urunler . $band . $video . $mk . iletisim_blok(), 'ana',
        $ilk ? '<link rel="preload" as="image" href="' . e($ilk) . '">' . "\n" : '');
}

const METIN_SAYFALARI = [
    'hakkimizda' => ['kurumsal', [['Kurumsal', '/hakkimizda'], ['Hakkımızda', null]]],
    'kvkk' => ['kurumsal', [['Kurumsal', '/hakkimizda'], ['KVKK', null]]],
    'forklift-satis' => ['hizmet', [['Hizmetlerimiz', '/forklift-satis'], ['Forklift Satış', null]]],
    'forklift-kiralama' => ['hizmet', [['Hizmetlerimiz', '/forklift-satis'], ['Forklift Kiralama', null]]],
    'servis' => ['hizmet', [['Hizmetlerimiz', '/forklift-satis'], ['Servis', null]]],
    'finansal-cozumler' => ['hizmet', [['Hizmetlerimiz', '/forklift-satis'], ['Finansal Çözümler', null]]],
];

function metin_sayfasi(string $yol): string {
    $p = veri()['sayfalar'][$yol];
    [$aktif, $kir] = METIN_SAYFALARI[$yol];
    if ($yol === 'servis') {
        $yan = hizmet_yan('servis') . '<div class="yan-kutu"><h3>Servis talebi</h3><p>Arızayı ve makine bilgisini yazın, hemen dönüş yapalım.</p>
<a class="btn btn-k" href="' . e(wa('Merhaba, servis talebim var. Makine marka/model: ')) . '" target="_blank" rel="noopener">' . I['wa'] . ' WhatsApp\'tan Yaz</a>
<a class="btn btn-c" href="tel:' . e(F('tel_uri')) . '">' . I['tel'] . ' ' . e(F('tel')) . '</a></div>';
    } else {
        $yan = ($aktif === 'hizmet' ? hizmet_yan($yol) : '') . yan_kutu();
    }
    $govde = sayfa_bas($p['h1'], $p['alt'] ?? '', $kir, $p['gorsel'] ?: '/assets/img/forklift-nigmer-800.webp') . '<section class="bolum"><div class="kap yan-duzen">
<article class="metin">' . $p['icerik'] . '</article>
<aside class="yan">' . $yan . '</aside></div></section>';
    return sayfa($yol, $p['baslik'], $p['aciklama'], $govde, $aktif);
}

function sss_sayfasi(): string {
    $ic = '<span class="etiket">Sıkça Sorulan Sorular</span><h2 style="margin-top:0">Forklift hakkında merak edilenler</h2><div class="sss">';
    $q = [];
    foreach (veri()['sss'] ?? [] as $i => $s) {
        $ic .= '<details' . ($i === 0 ? ' open' : '') . '><summary>' . e($s['soru']) . '</summary><p>' . nl2br(e($s['cevap'])) . '</p></details>';
        $q[] = ['@type' => 'Question', 'name' => $s['soru'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $s['cevap']]];
    }
    $ic .= '</div>';
    $sema = '<script type="application/ld+json">' . js(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $q]) . '</script>';
    $govde = sayfa_bas('Sıkça Sorulan Sorular', 'Forklift alırken, kiralarken ve kullanırken en çok sorulanlar.', [['Hizmetlerimiz', '/forklift-satis'], ['SSS', null]]) .
        '<section class="bolum"><div class="kap yan-duzen"><article class="metin">' . $ic . '</article><aside class="yan">' . hizmet_yan('sss') . yan_kutu() . '</aside></div></section>';
    return sayfa('sss', 'Sıkça Sorulan Sorular', 'Forklift alırken nelere dikkat edilmeli, dizel mi akülü mü, fiyatlar nasıl belirlenir, ehliyet gerekir mi? DELLIFT SSS.', $govde, 'hizmet', $sema);
}

function kategori_sayfasi(array $k): string {
    $ms = array_values(array_filter(markalar(), fn($m) => in_array($k['slug'], $m['kat'] ?? [], true)));
    $us = array_values(array_filter(urunler(), fn($u) => $u['kat'] === $k['slug']));
    $cip = '<div class="cipler">';
    foreach (kategoriler() as $x) { $cip .= '<a href="/' . e($x['slug']) . '"' . ($x['slug'] === $k['slug'] ? ' aria-current=page' : '') . '>' . e($x['ad']) . '</a>'; }
    $cip .= '</div>';
    $blok = '';
    if ($us) { $blok .= '<h2 style="margin-top:48px" id="modeller">Öne çıkan modeller</h2>' . urun_blok($us); }
    $kl = [];
    foreach (markalar() as $m) { foreach (kataloglar($m['slug'], $k['slug']) as $x) { $kl[] = $x; } }
    if ($kl) { $blok .= '<h2 style="margin-top:48px" id="katalog">Model kataloğu</h2><p style="color:var(--gri)">Üretici broşürleri; modele tıklayarak teknik föyü inceleyip fiyat için bize yazabilirsiniz.</p>' . katalog_blok($kl, true); }
    if ($ms) {
        $blok .= '<h2 style="margin-top:48px">Markalar</h2><p style="color:var(--gri)">Güncel model, stok ve teknik föy için markayı seçip bize yazın.</p>' . marka_kartlari($ms, $k['ad']);
    } else {
        $blok .= '<div class="vurgu" style="margin-top:40px"><b>Güncel stok ve fiyat</b>' . e($k['ad']) . ' için güncel model, kapasite ve fiyat bilgisini almak için bize yazın.
<p style="margin:14px 0 0"><a class="btn btn-k" href="' . e(wa('Merhaba, ' . kucuk($k['ad']) . ' hakkında bilgi almak istiyorum.')) . '" target="_blank" rel="noopener">' . I['wa'] . ' WhatsApp\'tan Sor</a></p></div>';
    }
    $kir = [['Ürünler', '/urunler']];
    if (!empty($k['ust'])) { $kir[] = ['Forklift', '/urunler#forklift']; }
    $kir[] = [$k['ad'], null];
    $govde = sayfa_bas($k['ad'], $k['ozet'], $kir) . '<section class="bolum"><div class="kap">
<div class="metin"><p style="font-size:1.12rem">' . e($k['metin']) . '</p></div>' . $cip . $blok . '</div></section>' . iletisim_blok();
    return sayfa($k['slug'], $k['ad'], "{$k['ad']}: {$k['ozet']} Satış, kiralama ve servis — DELLIFT İstanbul.", $govde, 'urun');
}

function urunler_sayfasi(): string {
    $f = ''; $d = '';
    foreach (kategoriler() as $k) { if (!empty($k['ust'])) $f .= kat_kart($k); else $d .= kat_kart($k); }
    $govde = sayfa_bas('Ürünler', 'Forklift, istif makinası ve transpaletlerde geniş ürün yelpazesi.', [['Ürünler', null]]) . '
<section class="bolum" id="forklift"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Forklift</span><h2>Dizel, akülü ve 2. el</h2></div></div><div class="kategoriler">' . $f . '</div></div></section>
<section class="bolum gri"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Depo ekipmanları</span><h2>İstif ve transpalet</h2></div></div><div class="kategoriler">' . $d . '</div></div></section>
<section class="bolum"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Markalar</span><h2>Çalıştığımız markalar</h2></div></div>' . markalar_serit() . '</div></section>';
    return sayfa('urunler', 'Ürünler – Forklift, İstif Makinası, Transpalet', 'Dizel ve akülü forklift, 2. el forklift, istif makinası, akülü ve manuel transpalet. ' . implode(', ', array_column(markalar(), 'ad')) . '.', $govde, 'urun');
}

function marka_sayfasi(array $m): string {
    $liste = '';
    foreach ($m['kat'] ?? [] as $s) {
        if (!($k = kat($s))) continue;
        $liste .= '<div class="mk"><div class="ad" style="font-size:1.5rem">' . e($k['ad']) . '</div><p>' . e($k['ozet']) . '</p>
<div class="bag"><a class="wa" href="' . e(wa("Merhaba, {$m['ad']} " . kucuk($k['ad']) . ' modelleri hakkında bilgi almak istiyorum.')) . '" target="_blank" rel="noopener">WhatsApp\'tan sor →</a><a href="/' . e($k['slug']) . '">Kategoriye git</a></div></div>';
    }
    $us = array_values(array_filter(urunler(), fn($u) => $u['marka'] === $m['slug']));
    $blok = $us ? '<h2 style="margin-top:40px" id="modeller">Modeller</h2>' . urun_blok($us) : '';
    $cip = '<div class="cipler">';
    foreach (markalar() as $x) { $cip .= '<a href="/marka/' . e($x['slug']) . '"' . ($x['slug'] === $m['slug'] ? ' aria-current=page' : '') . '>' . e($x['ad']) . '</a>'; }
    $cip .= '</div>';
    $kl = kataloglar($m['slug']);
    $ktb = $kl ? '<h2 style="margin-top:48px" id="katalog">' . e($m['ad']) . ' model kataloğu</h2><p style="color:var(--gri)">Üretici broşürleri; modele tıklayarak teknik föyü inceleyebilirsiniz.</p>' . katalog_blok($kl, false) : '';
    $govde = sayfa_bas($m['ad'], $m['ozet'], [['Ürünler', '/urunler'], [$m['ad'], null]]) . '<section class="bolum"><div class="kap">
<div class="metin"><p style="font-size:1.12rem">' . e($m['ad']) . ' markalı forklift ve depo ekipmanlarında satış, kiralama ve servis hizmeti veriyoruz. Güncel model listesi, teknik föy ve fiyat için bize yazın; ihtiyacınıza en uygun modeli birlikte belirleyelim.</p></div>
' . $cip . $blok . $ktb . '<h2 style="margin-top:40px">' . e($m['ad']) . ' ürün grupları</h2><div class="marka-kartlar">' . $liste . '</div></div></section>' . iletisim_blok();
    return sayfa('marka/' . $m['slug'], $m['ad'] . ' Forklift', "{$m['ad']} dizel ve akülü forklift, istif makinası satış, kiralama ve servis. DELLIFT İstanbul.", $govde, 'urun');
}

function seri_sayfasi(array $sr): string {
    $I = I;
    $m = marka($sr['marka']) ?? ['ad' => '', 'slug' => $sr['marka']];
    $k = kat($sr['kat']) ?? ['ad' => '', 'slug' => $sr['kat'], 'kisa' => ''];
    $g = $sr['gorseller'] ?? [];
    $mod = $sr['modeller'] ?? [];
    $ana = $g ? '<a class="galeri-ana" href="' . e($g[0]) . '.webp" target="_blank" rel="noopener"><img id="galeri-buyuk" src="' . e($g[0]) . '.webp" alt="' . e($sr['ad']) . '" width="1600" height="1077"></a>' : '';
    $kc = '';
    foreach ($g as $i => $b) { $kc .= '<button type="button"' . ($i === 0 ? ' class=aktif' : '') . ' aria-label="Görsel ' . ($i + 1) . '"><img src="' . e($b) . '-800.webp" alt="" loading="lazy"></button>'; }
    $tablo = '';
    foreach ($sr['teknik'] ?? [] as $r) {
        if (count($r) < 2 || $r[1] === null) { $tablo .= '<tr class="ara"><th colspan="' . (count($mod) + 2) . '">' . e($r[0]) . '</th></tr>'; continue; }
        $tablo .= '<tr><td>' . e($r[0]) . '</td><td class="birim">' . e($r[1]) . '</td>';
        for ($i = 0; $i < count($mod); $i++) { $tablo .= '<td>' . e($r[2 + $i] ?? '') . '</td>'; }
        $tablo .= '</tr>';
    }
    $ust = implode('', array_map(fn($x) => '<th>' . e($x) . '</th>', $mod));
    $one = '';
    foreach ($sr['one_cikan'] ?? [] as $r) { $one .= '<li><span class="ik">' . $I['kalkan'] . '</span><div><b>' . e($r[0] ?? '') . '</b><span>' . e($r[1] ?? '') . '</span></div></li>'; }
    $mods = implode(' / ', $mod);
    $sor = wa("Merhaba, {$sr['ad']} ($mods) için fiyat almak istiyorum.");
    $kap_satir = null;
    foreach ($sr['teknik'] ?? [] as $r) { if (($r[0] ?? '') === 'Yük kapasitesi') { $kap_satir = $r; break; } }
    $oz = '';
    if ($kap_satir) { foreach ($mod as $i => $x) { $oz .= '<div><b>' . e($x) . '</b><span>' . e($kap_satir[2 + $i] ?? '') . ' kg</span></div>'; } }
    $govde = sayfa_bas($sr['ad'], $sr['kisa'], [['Ürünler', '/urunler'], [$k['ad'], '/' . $k['slug']], [$m['ad'], '/marka/' . $m['slug']], [$mods, null]]) . '
<section class="bolum"><div class="kap urun-detay">
<div class="galeri">' . $ana . '<div class="galeri-kucuk">' . $kc . '</div></div>
<div><span class="etiket">' . e($m['ad']) . ' · ' . e($mods) . '</span><h2>' . e($sr['ad']) . '</h2>
<p style="font-size:1.08rem">' . nl2br(e($sr['giris'])) . '</p>
' . ($oz ? '<div class="model-ozet">' . $oz . '</div>' : '') . '
<div class="urun-btn"><a class="btn btn-k" href="' . e($sor) . '" target="_blank" rel="noopener">' . $I['wa'] . ' Fiyat Sor</a><a class="btn btn-s" href="tel:' . e(F('tel_uri')) . '">' . $I['tel'] . ' ' . e(F('tel')) . '</a>' . (!empty($sr['katalog']) ? '<a class="btn btn-s" href="' . e($sr['katalog']) . '" target="_blank" rel="noopener">Katalog (PDF)</a>' : '') . '</div>
<p style="color:var(--gri);font-size:.95rem;margin-top:16px">' . e($sr['not'] ?? '') . ' · 24 saatte servis ve yedek parça</p>
</div></div></section>' .
        ($one ? '<section class="bolum gri"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Öne çıkanlar</span><h2>' . e($sr['one_baslik'] ?? 'Öne çıkan özellikler') . '</h2></div></div>
<ul class="ozellikler iki-sutun">' . $one . '</ul></div></section>' : '') .
        ($tablo ? '<section class="bolum" id="teknik"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Teknik föy</span><h2>Teknik özellikler</h2></div><p>Değerler üretici kataloğundandır.</p></div>
<div class="tablo-kap"><table class="teknik"><thead><tr><th>Model</th><th class="birim">Birim</th>' . $ust . '</tr></thead><tbody>' . $tablo . '</tbody></table></div></div></section>' : '') . iletisim_blok();
    $urun = [];
    foreach ($mod as $x) {
        $urun[] = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => "{$m['ad']} $x {$k['kisa']} Forklift", 'brand' => ['@type' => 'Brand', 'name' => $m['ad']],
            'model' => $x, 'image' => $g ? F('site') . $g[0] . '.webp' : '', 'description' => $sr['giris']];
    }
    $sema = '<script type="application/ld+json">' . js($urun) . '</script>';
    return sayfa('urun/' . $sr['slug'], "{$sr['ad']} – $mods", "{$sr['ad']} ($mods): {$sr['kisa']}. Teknik özellikler, fiyat, satış ve kiralama — DELLIFT İstanbul.", $govde, 'urun', $sema);
}

function katalog_sayfasi(array $kt): string {
    $I = I;
    $m = marka($kt['marka']) ?? ['ad' => '', 'slug' => $kt['marka']];
    $k = kat($kt['kat']) ?? ['ad' => '', 'slug' => $kt['kat'], 'kisa' => ''];
    $ad = $m['ad'] . ' ' . $kt['model'];
    $pdf = $kt['pdf'];
    $en = ($kt['dil'] ?? '') === 'EN';
    $govde = sayfa_bas($ad, $kt['aciklama'], [['Ürünler', '/urunler'], [$k['ad'], '/' . $k['slug']], [$m['ad'], '/marka/' . $m['slug']], [$kt['model'], null]]) . '
<section class="bolum" style="padding-top:40px"><div class="kap">
<div class="katalog-bas"><div><span class="etiket">' . e($m['ad']) . ' · ' . e($k['kisa']) . '</span><h2>' . e($ad) . ' teknik föyü</h2>
<p style="color:var(--gri);margin:0">Üretici kataloğu' . ($en ? ' (İngilizce)' : '') . '. Stok, fiyat ve size uygun konfigürasyon için bize yazın.</p></div>
<div class="urun-btn"><a class="btn btn-k" href="' . e(wa("Merhaba, $ad için fiyat almak istiyorum.")) . '" target="_blank" rel="noopener">' . $I['wa'] . ' Fiyat Sor</a><a class="btn btn-s" href="tel:' . e(F('tel_uri')) . '">' . $I['tel'] . ' Ara</a><a class="btn btn-s" href="' . e($pdf) . '" download>PDF İndir</a></div></div>
<div class="pdf-gorunum" id="pdf-gorunum" data-js="' . PDFJS . '"><a class="pdf-kaynak" href="' . e($pdf) . '"></a><p class="pdf-durum">Katalog yükleniyor…</p></div>
</div></section>
<script src="' . PDFJS . 'build/pdf.min.js" defer></script>' . iletisim_blok();
    return sayfa("katalog/{$kt['marka']}/{$kt['slug']}", "$ad – {$kt['aciklama']}", "$ad: {$kt['aciklama']}. Teknik föy, fiyat, satış ve kiralama — DELLIFT İstanbul.", $govde, 'urun');
}

function iletisim_sayfasi(): string {
    $govde = sayfa_bas('İletişim', "Bize telefonla, WhatsApp'tan ya da formla ulaşın; aynı gün dönüş yapalım.", [['İletişim', null]]) . iletisim_blok(false);
    return sayfa('iletisim', 'İletişim', 'DELLIFT iletişim: ' . F('tel') . ', ' . F('eposta') . '. ' . F('adres') . '.', $govde, 'iletisim');
}

function bulunamadi(): string {
    $govde = '<section class="bolum"><div class="kap" style="text-align:center;padding:40px 20px">
<span class="etiket">404</span><h1>Sayfa bulunamadı</h1><p style="color:var(--gri)">Aradığınız sayfa taşınmış ya da kaldırılmış olabilir.</p>
<p><a class="btn btn-k" href="/">Ana sayfaya dön ' . I['ok'] . '</a></p></div></section>';
    return sayfa('404', 'Sayfa bulunamadı', 'Aradığınız sayfa bulunamadı.', $govde);
}

function tum_yollar(): array {
    $l = ['', 'hakkimizda', 'kvkk', 'forklift-satis', 'forklift-kiralama', 'servis', 'finansal-cozumler', 'sss', 'urunler', 'iletisim'];
    foreach (kategoriler() as $k) { $l[] = $k['slug']; }
    foreach (markalar() as $m) { $l[] = 'marka/' . $m['slug']; }
    foreach (seriler() as $s) { $l[] = 'urun/' . $s['slug']; }
    foreach (kataloglar() as $k) { $l[] = "katalog/{$k['marka']}/{$k['slug']}"; }
    return $l;
}

function site_haritasi(): string {
    $b = date('Y-m-d', (int)@filemtime(VERI . '/icerik.json') ?: time());
    $o = '';
    foreach (tum_yollar() as $y) { $o .= '<url><loc>' . e(F('site') . '/' . $y) . '</loc><lastmod>' . $b . '</lastmod></url>'; }
    return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $o . '</urlset>';
}
