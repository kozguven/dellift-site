<?php
// DELLIFT — sayfa şablonları (eski build.py'nin PHP karşılığı; içerik veri/icerik.json'dan, panelden düzenlenir).
declare(strict_types=1);

function menu(string $aktif): string {
    $a = function (string $href, string $ad, string $key) use ($aktif): string {
        return '<a href="' . $href . '"' . ($key === $aktif ? ' aria-current="page"' : '') . '>' . $ad . '</a>';
    };
    $uk = ''; foreach (kategoriler() as $k) { $uk .= '<li><a href="/' . e($k['slug']) . '">' . e($k['ad']) . '</a></li>'; }
    $um = ''; foreach (markalar() as $m) { $um .= '<li><a href="/marka/' . e($m['slug']) . '">' . e($m['ad']) . '</a></li>'; }
    return '<ul class="menu">
<li>' . $a('/', 'Ana Sayfa', 'ana') . '</li>
<li>' . $a('/hakkimizda', 'Kurumsal', 'kurumsal') . '<ul class="alt"><li><a href="/hakkimizda">Hakkımızda</a></li><li><a href="/kvkk">KVKK Aydınlatma Metni</a></li></ul></li>
<li>' . $a('/forklift-satis', 'Hizmetlerimiz', 'hizmet') . '<ul class="alt"><li><a href="/forklift-satis">Forklift Satış</a></li><li><a href="/forklift-kiralama">Forklift Kiralama</a></li><li><a href="/servis">Servis ve Yedek Parça</a></li><li><a href="/finansal-cozumler">Finansal Çözümler</a></li><li><a href="/sss">Sıkça Sorulan Sorular</a></li></ul></li>
<li>' . $a('/urunler', 'Ürünler', 'urun') . '<ul class="alt genis"><li><div class="ara">Kategoriler</div><ul style="list-style:none;padding:0">' . $uk . '</ul></li><li><div class="ara">Markalar</div><ul style="list-style:none;padding:0">' . $um . '</ul></li></ul></li>
<li>' . $a('/iletisim', 'İletişim', 'iletisim') . '</li>
</ul>';
}

function sayfa(string $yol, string $baslik, string $aciklama, string $govde, string $aktif = '', string $ek_bas = ''): string {
    $tam = str_contains($baslik, 'DELLIFT') ? $baslik : "$baslik | DELLIFT Forklift";
    $site = F('site');
    $kanonik = $site . ($yol === 'index' ? '/' : '/' . $yol);
    if (!empty($GLOBALS['SAYFA_NO'])) {
        $kanonik .= '?sayfa=' . $GLOBALS['SAYFA_NO'];
        $tam = preg_replace('~ \| DELLIFT Forklift$~', '', $tam) . ' – Sayfa ' . $GLOBALS['SAYFA_NO'] . ' | DELLIFT Forklift';
    }
    $sema = '';
    if ($yol === 'index') {
        $sema = '<script type="application/ld+json">' . js(['@context' => 'https://schema.org', '@type' => 'LocalBusiness',
            'name' => F('unvan'), 'url' => $site, 'telephone' => F('tel_uri'), 'email' => F('eposta'),
            'image' => $site . '/assets/img/forklift-nigmer.webp', 'logo' => $site . '/assets/img/logo.png',
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => trim(explode(',', F('adres'))[0]), 'addressLocality' => 'Sultanbeyli',
                'addressRegion' => 'İstanbul', 'addressCountry' => 'TR'], 'sameAs' => [F('instagram')]]) . '</script>';
    }
    $kat_li = ''; foreach (kategoriler() as $k) { $kat_li .= '<li><a href="/' . e($k['slug']) . '">' . e($k['ad']) . '</a></li>'; }
    $tel = e(F('tel')); $telu = e(F('tel_uri')); $ep = e(F('eposta')); $ig = e(F('instagram'));
    $yil = date('Y'); $s = SURUM; $I = I; $e = 'e'; $menu = 'menu'; $wa = 'wa';
    return <<<HTML
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$e($tam)}</title>
<meta name="description" content="{$e($aciklama)}">
<link rel="canonical" href="{$e($kanonik)}">
<meta property="og:type" content="website"><meta property="og:locale" content="tr_TR">
<meta property="og:title" content="{$e($tam)}"><meta property="og:description" content="{$e($aciklama)}">
<meta property="og:url" content="{$e($kanonik)}"><meta property="og:image" content="{$e($site)}/assets/img/og.jpg">
<meta name="theme-color" content="#e30613">
<link rel="icon" href="/favicon.svg" type="image/svg+xml"><link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/site.css?v=$s">
$ek_bas$sema
</head>
<body>
<div class="serit"><div class="kap">
<div class="serit-sol"><a href="tel:$telu">{$I['tel']}$tel</a><a class="e-posta" href="mailto:$ep">{$I['posta']}$ep</a></div>
<div class="serit-orta">{$e(F('kampanya'))}</div>
<div class="serit-sag"><a href="$ig" target="_blank" rel="noopener" aria-label="Instagram">{$I['ig']}Instagram</a></div>
</div></div>
<header class="ust"><div class="kap">
<a class="logo" href="/" aria-label="DELLIFT ana sayfa"><img src="/assets/img/logo.png" alt="DELLIFT Forklift ve İstif Makinaları" width="480" height="94"></a>
<nav class="gezinti" id="gezinti" aria-label="Ana menü">{$menu($aktif)}</nav>
<a class="btn btn-k" href="/iletisim#teklif">Teklif Al {$I['ok']}</a>
<button class="hamburger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="gezinti"><span></span><span></span><span></span></button>
</div></header>
<main>
$govde
</main>
<footer class="alt-bilgi"><div class="kap">
<div class="ab-grid">
<div><a class="logo" href="/"><img src="/assets/img/logo-beyaz.png" alt="DELLIFT" width="480" height="94" loading="lazy"></a>
<p>{$e(F('alt_bilgi_metni'))}</p>
<div class="sosyal"><a href="$ig" target="_blank" rel="noopener" aria-label="Instagram">{$I['ig']}</a><a href="{$e($wa('Merhaba, bilgi almak istiyorum.'))}" target="_blank" rel="noopener" aria-label="WhatsApp">{$I['wa']}</a></div></div>
<div><h4>Hizmetler</h4><ul><li><a href="/forklift-satis">Forklift Satış</a></li><li><a href="/forklift-kiralama">Forklift Kiralama</a></li><li><a href="/servis">Servis ve Yedek Parça</a></li><li><a href="/finansal-cozumler">Finansal Çözümler</a></li><li><a href="/sss">Sıkça Sorulan Sorular</a></li></ul></div>
<div><h4>Ürünler</h4><ul>$kat_li</ul></div>
<div><h4>İletişim</h4><ul><li><a href="tel:$telu">$tel</a></li><li><a href="mailto:$ep">$ep</a></li><li>{$e(F('adres'))}</li><li><a href="/kvkk">KVKK Aydınlatma Metni</a></li></ul></div>
</div>
<div class="telif"><span>© $yil {$e(F('unvan'))}. Tüm hakları saklıdır.</span><span><a href="/kvkk">KVKK</a> · <a href="/sitemap.xml">Site haritası</a> · <a href="https://webizmo.tr" target="_blank" rel="noopener">yazılım firması istanbul</a></span></div>
</div></footer>
<a class="wa-sabit" href="{$e($wa('Merhaba, forklift hakkında bilgi almak istiyorum.'))}" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">{$I['wa']}</a>
<a class="ara-sabit" href="tel:$telu" aria-label="Hemen arayın">{$I['tel']}</a>
<script src="/assets/js/site.js?v=$s" defer></script>
</body>
</html>
HTML;
}
