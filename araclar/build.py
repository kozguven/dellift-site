#!/usr/bin/env python3
"""DELLIFT statik site üretici. Çalıştır: python3 araclar/build.py  → site/ altına HTML yazar."""
import html, os, urllib.parse
from datetime import date
from veri import FIRMA as F, MARKALAR, KATEGORILER, URUNLER, SSS

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "site")
SURUM = "4"
e = html.escape

# ---- ikonlar ----
I = {
 "tel": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>',
 "posta": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>',
 "konum": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
 "saat": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
 "ig": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/></svg>',
 "wa": '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8zm8.4-18.2A11.8 11.8 0 0 0 1.7 17.8L0 24l6.3-1.7A11.8 11.8 0 0 0 23.8 12c0-3.2-1.2-6.1-3.4-8.4z"/></svg>',
 "ok": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
 "sol": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>',
 "sag": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>',
 "anahtar": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z"/></svg>',
 "kalkan": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
 "kisi": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>',
 "tl": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3v15a3 3 0 0 0 3 3 8 8 0 0 0 8-8M5 11l9-4M5 15l9-4"/></svg>',
 "kutu": '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.7z"/><path d="M3.3 7 12 12l8.7-5M12 22V12"/></svg>',
}
# basit forklift çizimi (kategori kartlarının köşesinde)
CIZIM = '<svg class="cizim" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" stroke-linecap="round"><path d="M10 8v42M10 50h20M14 12v34"/><path d="M18 46V26h14l8 10h10v14H36"/><path d="M24 26V16h10l4 10"/><circle cx="26" cy="52" r="5"/><circle cx="46" cy="52" r="5"/></svg>'


def wa_link(metin):
    return f"https://wa.me/{F['wa']}?text=" + urllib.parse.quote(metin)


def kat(slug):
    return next(k for k in KATEGORILER if k["slug"] == slug)


# ---- iskelet ----
def menu(aktif):
    def a(href, ad, key=None):
        cur = ' aria-current="page"' if key and key == aktif else ""
        return f'<a href="{href}"{cur}>{ad}</a>'
    urun_kat = "".join(f'<li><a href="/{k["slug"]}">{e(k["ad"])}</a></li>' for k in KATEGORILER)
    urun_marka = "".join(f'<li><a href="/marka/{m["slug"]}">{e(m["ad"])}</a></li>' for m in MARKALAR)
    return f'''<ul class="menu">
<li>{a("/", "Ana Sayfa", "ana")}</li>
<li>{a("/hakkimizda", "Kurumsal", "kurumsal")}<ul class="alt"><li><a href="/hakkimizda">Hakkımızda</a></li><li><a href="/kvkk">KVKK Aydınlatma Metni</a></li></ul></li>
<li>{a("/forklift-satis", "Hizmetlerimiz", "hizmet")}<ul class="alt"><li><a href="/forklift-satis">Forklift Satış</a></li><li><a href="/forklift-kiralama">Forklift Kiralama</a></li><li><a href="/servis">Servis ve Yedek Parça</a></li><li><a href="/finansal-cozumler">Finansal Çözümler</a></li><li><a href="/sss">Sıkça Sorulan Sorular</a></li></ul></li>
<li>{a("/urunler", "Ürünler", "urun")}<ul class="alt genis"><li><div class="ara">Kategoriler</div><ul style="list-style:none;padding:0">{urun_kat}</ul></li><li><div class="ara">Markalar</div><ul style="list-style:none;padding:0">{urun_marka}</ul></li></ul></li>
<li>{a("/iletisim", "İletişim", "iletisim")}</li>
</ul>'''


def sayfa(yol, baslik, aciklama, govde, aktif="", ek_bas=""):
    tam_baslik = baslik if "DELLIFT" in baslik else f"{baslik} | DELLIFT Forklift"
    kanonik = F["site"] + ("/" if yol == "index" else "/" + yol)
    yil = date.today().year
    sema = ('<script type="application/ld+json">{"@context":"https://schema.org","@type":"LocalBusiness",'
            f'"name":"{F["unvan"]}","url":"{F["site"]}","telephone":"{F["tel_uri"]}","email":"{F["eposta"]}",'
            f'"image":"{F["site"]}/assets/img/forklift-nigmer.webp","logo":"{F["site"]}/assets/img/logo.png",'
            '"address":{"@type":"PostalAddress","streetAddress":"Battalgazi Mah. Egemen Sk. No:44","addressLocality":"Sultanbeyli",'
            '"addressRegion":"İstanbul","addressCountry":"TR"},"sameAs":["' + F["instagram"] + '"]}</script>') if yol == "index" else ""
    return f'''<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{e(tam_baslik)}</title>
<meta name="description" content="{e(aciklama)}">
<link rel="canonical" href="{kanonik}">
<meta property="og:type" content="website"><meta property="og:locale" content="tr_TR">
<meta property="og:title" content="{e(tam_baslik)}"><meta property="og:description" content="{e(aciklama)}">
<meta property="og:url" content="{kanonik}"><meta property="og:image" content="{F["site"]}/assets/img/og.jpg">
<meta name="theme-color" content="#e30613">
<link rel="icon" href="/favicon.svg" type="image/svg+xml"><link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/site.css?v={SURUM}">
{ek_bas}{sema}
</head>
<body>
<div class="serit"><div class="kap">
<div class="serit-sol"><a href="tel:{F["tel_uri"]}">{I["tel"]}{F["tel"]}</a><a class="e-posta" href="mailto:{F["eposta"]}">{I["posta"]}{F["eposta"]}</a></div>
<div class="serit-orta">{e(F["kampanya"])}</div>
<div class="serit-sag"><a href="{F["instagram"]}" target="_blank" rel="noopener" aria-label="Instagram">{I["ig"]}Instagram</a></div>
</div></div>
<header class="ust"><div class="kap">
<a class="logo" href="/" aria-label="DELLIFT ana sayfa"><img src="/assets/img/logo.png" alt="DELLIFT Forklift ve İstif Makinaları" width="480" height="94"></a>
<nav class="gezinti" id="gezinti" aria-label="Ana menü">{menu(aktif)}</nav>
<a class="btn btn-k" href="/iletisim#teklif">Teklif Al {I["ok"]}</a>
<button class="hamburger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="gezinti"><span></span><span></span><span></span></button>
</div></header>
<main>
{govde}
</main>
<footer class="alt-bilgi"><div class="kap">
<div class="ab-grid">
<div><a class="logo" href="/"><img src="/assets/img/logo-beyaz.png" alt="DELLIFT" width="480" height="94" loading="lazy"></a>
<p>Forklift ve istif makinalarında satış, kiralama, servis ve yedek parça. Sultanbeyli'den İstanbul ve çevresine hızlı hizmet.</p>
<div class="sosyal"><a href="{F["instagram"]}" target="_blank" rel="noopener" aria-label="Instagram">{I["ig"]}</a><a href="{wa_link("Merhaba, bilgi almak istiyorum.")}" target="_blank" rel="noopener" aria-label="WhatsApp">{I["wa"]}</a></div></div>
<div><h4>Hizmetler</h4><ul><li><a href="/forklift-satis">Forklift Satış</a></li><li><a href="/forklift-kiralama">Forklift Kiralama</a></li><li><a href="/servis">Servis ve Yedek Parça</a></li><li><a href="/finansal-cozumler">Finansal Çözümler</a></li><li><a href="/sss">Sıkça Sorulan Sorular</a></li></ul></div>
<div><h4>Ürünler</h4><ul>{"".join(f'<li><a href="/{k["slug"]}">{e(k["ad"])}</a></li>' for k in KATEGORILER)}</ul></div>
<div><h4>İletişim</h4><ul><li><a href="tel:{F["tel_uri"]}">{F["tel"]}</a></li><li><a href="mailto:{F["eposta"]}">{F["eposta"]}</a></li><li>{e(F["adres"])}</li><li><a href="/kvkk">KVKK Aydınlatma Metni</a></li></ul></div>
</div>
<div class="telif"><span>© {yil} {e(F["unvan"])}. Tüm hakları saklıdır.</span><span><a href="/kvkk">KVKK</a> · <a href="/sitemap.xml">Site haritası</a></span></div>
</div></footer>
<a class="wa-sabit" href="{wa_link("Merhaba, forklift hakkında bilgi almak istiyorum.")}" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">{I["wa"]}</a>
<a class="ara-sabit" href="tel:{F["tel_uri"]}" aria-label="Hemen arayın">{I["tel"]}</a>
<script src="/assets/js/site.js?v={SURUM}" defer></script>
</body>
</html>
'''


def sayfa_bas(baslik, alt, kirinti, gorsel="/assets/img/forklift-nigmer-800.webp"):
    k = ' <span>/</span> '.join([f'<a href="/">Ana Sayfa</a>'] + [f'<a href="{h}">{e(a)}</a>' if h else e(a) for a, h in kirinti])
    return f'''<section class="sayfa-bas" style="--bas-gorsel:url('{gorsel}')"><div class="kap">
<div class="kirinti">{k}</div>
<h1>{baslik}</h1>{f"<p>{alt}</p>" if alt else ""}
</div></section>'''


def yan_kutu():
    return f'''<div class="yan-kutu"><h3>Hızlı teklif alın</h3>
<p>İhtiyacınızı yazın, aynı gün size uygun makine ve fiyatla dönüş yapalım.</p>
<a class="btn btn-k" href="/iletisim#teklif">Teklif Formu {I["ok"]}</a>
<a class="btn btn-c" href="{wa_link("Merhaba, teklif almak istiyorum.")}" target="_blank" rel="noopener">{I["wa"]} WhatsApp</a>
<a class="btn btn-c" href="tel:{F["tel_uri"]}">{I["tel"]} {F["tel"]}</a></div>'''


def hizmet_yan(aktif):
    l = [("forklift-satis", "Forklift Satış"), ("forklift-kiralama", "Forklift Kiralama"), ("servis", "Servis ve Yedek Parça"),
         ("finansal-cozumler", "Finansal Çözümler"), ("sss", "Sıkça Sorulan Sorular")]
    return '<nav class="yan-liste" aria-label="Hizmetler">' + "".join(
        f'<a href="/{s}"{" aria-current=page" if s == aktif else ""}>{a}<span>›</span></a>' for s, a in l) + "</nav>"


def form_html(baslik="Teklif / bilgi talebi", onsec=""):
    secenekler = ["Forklift satın almak istiyorum", "Forklift kiralamak istiyorum", "Servis / yedek parça", "Finansman / leasing", "Diğer"]
    opts = "".join(f'<option{" selected" if s == onsec else ""}>{s}</option>' for s in secenekler)
    return f'''<form class="form" id="teklif" method="post" action="/gonder.php">
<h3>{baslik}</h3>
<div id="form-uyari"></div>
<div class="alanlar">
<div class="alan"><label for="ad">Ad Soyad *</label><input id="ad" name="ad" required maxlength="80" autocomplete="name"></div>
<div class="alan"><label for="firma">Firma</label><input id="firma" name="firma" maxlength="120" autocomplete="organization"></div>
<div class="alan"><label for="tel">Telefon *</label><input id="tel" name="tel" type="tel" required maxlength="25" autocomplete="tel"></div>
<div class="alan"><label for="eposta">E-posta</label><input id="eposta" name="eposta" type="email" maxlength="120" autocomplete="email"></div>
<div class="alan tam"><label for="konu">Konu</label><select id="konu" name="konu">{opts}</select></div>
<div class="alan tam"><label for="mesaj">Mesajınız *</label><textarea id="mesaj" name="mesaj" required maxlength="3000" placeholder="Örn. 3 ton dizel forklift, 4,5 m kaldırma, açık saha"></textarea></div>
<div class="gizli-alan" aria-hidden="true"><label>Web sitesi<input name="web" tabindex="-1" autocomplete="off"></label></div>
<label class="onay"><input type="checkbox" name="kvkk" required> <span><a href="/kvkk" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum; talebimin değerlendirilmesi için benimle iletişime geçilmesini kabul ediyorum.</span></label>
<div class="alan tam"><button class="btn btn-k" type="submit">Gönder {I["ok"]}</button></div>
</div></form>'''


def iletisim_blok(gri=True):
    q = urllib.parse.quote(F["harita_q"])
    return f'''<section class="bolum{" gri" if gri else ""}" id="iletisim"><div class="kap">
<div class="iletisim-grid">
<div>
<span class="etiket">İletişim</span><h2>Size en yakın çözüm bir telefon uzağınızda</h2>
<ul class="bilgi">
<li><span class="ik">{I["tel"]}</span><div><small>Telefon / WhatsApp</small><a href="tel:{F["tel_uri"]}">{F["tel"]}</a></div></li>
<li><span class="ik">{I["posta"]}</span><div><small>E-posta</small><a href="mailto:{F["eposta"]}">{F["eposta"]}</a></div></li>
<li><span class="ik">{I["konum"]}</span><div><small>Adres</small><span>{e(F["adres"])}</span></div></li>
</ul>
<div class="harita"><iframe title="DELLIFT konum haritası" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q={q}&z=16&output=embed"></iframe></div>
</div>
{form_html()}
</div></div></section>'''


# ---- sayfalar ----
def ana_sayfa():
    slaytlar = [
        ("assets/img/hero-satis.webp", "h1", "Forklift ve istif makinalarında <span>doğru adres</span>",
         "Sıfır ve ikinci el forklift satışı, kısa ve uzun dönem kiralama, 24 saatte servis ve yedek parça. Niğmer, EP, Litef, Dolphin, JAC ve Lonking.",
         ("/forklift-satis", "Satış"), ("/iletisim#teklif", "Teklif Al")),
        ("assets/img/hero-kiralama.webp", "h2", "İhtiyacınız kadar <span>kiralayın</span>",
         "Günlük, aylık ve uzun dönem forklift kiralama. Bakım ve servis bizden, siz işinize odaklanın.",
         ("/forklift-kiralama", "Kiralama"), ("/iletisim#teklif", "Fiyat Sor")),
        ("assets/img/hero-servis.webp", "h2", "24 saatte <span>servis ve yedek parça</span>",
         "Arızada hızlı müdahale, periyodik bakım ve orijinal yedek parça. Makineniz durursa işiniz durmasın.",
         ("/servis", "Servis"), (wa_link("Merhaba, servis talebim var."), "WhatsApp'tan Yaz")),
    ]
    s_html = ""
    for i, (img, h, bas, alt, b1, b2) in enumerate(slaytlar):
        yukle = 'fetchpriority="high"' if i == 0 else 'loading="lazy"'
        hedef = ' target="_blank" rel="noopener"' if b2[0].startswith("http") else ""
        s_html += f'''<div class="slayt{" aktif" if i == 0 else ""}{" sag" if i == 0 else ""}" aria-roledescription="slayt">
<img src="/{img}" alt="" {yukle}>
<div class="kap"><div class="icerik"><span class="etiket" style="color:#fff">DELLIFT · Satış · Kiralama · Servis</span>
<{h}>{bas}</{h}><p>{alt}</p>
<div class="dugmeler"><a class="btn btn-k" href="{b2[0]}"{hedef}>{b2[1]} {I["ok"]}</a><a class="btn btn-c" href="{b1[0]}">{b1[1]}</a></div></div></div></div>'''
    hero = f'''<section class="hero" aria-label="Öne çıkanlar">
{s_html}
<button class="hero-ok sol" aria-label="Önceki">{I["sol"]}</button><button class="hero-ok sag" aria-label="Sonraki">{I["sag"]}</button>
<div class="noktalar">{"".join(f'<button aria-label="Slayt {i+1}"{" class=aktif" if i==0 else ""}></button>' for i in range(len(slaytlar)))}</div>
<div class="hero-alt"><div class="kap">
<div class="oz">{I["saat"]}<div><b>24 saatte servis</b><small>Servis ve yedek parça garantisi</small></div></div>
<div class="oz">{I["tl"]}<div><b>6 ay ödemesiz</b><small>60 aya varan leasing seçenekleri</small></div></div>
<div class="oz">{I["kalkan"]}<div><b>Sıfır ve 2. el</b><small>Kontrolden geçmiş, teslime hazır</small></div></div>
</div></div></section>'''

    giris = f'''<section class="bolum"><div class="kap ikili">
<div class="gorsel"><img src="/assets/img/forklift-nigmer.webp" alt="DELLIFT deposunda turkuaz Niğmer dizel forklift" width="1536" height="1152" loading="lazy"></div>
<div><span class="etiket">Neden DELLIFT</span>
<h2>Doğru ekipman, güvenilir çözüm</h2>
<p>DELLIFT; forklift, istif makinası ve transpalet ihtiyacınızı tek noktadan karşılar. İşinizi dinler, sahanıza uygun makineyi önerir, teslimden sonra da yanınızda oluruz.</p>
<ul class="ozellikler">
<li><span class="ik">{I["kisi"]}</span><div><b>Uzman kadro</b><span>Forklift ve iş makinesi alanında deneyimli satış ve servis ekibi.</span></div></li>
<li><span class="ik">{I["kalkan"]}</span><div><b>Güvenilir, kaliteli makineler</b><span>Bilinen markalar, kontrol edilmiş ikinci el makineler, şeffaf teknik bilgi.</span></div></li>
<li><span class="ik">{I["anahtar"]}</span><div><b>Satış sonrası destek</b><span>24 saatte servis ve yedek parça; periyodik bakım ile makineniz hep çalışır.</span></div></li>
</ul></div></div></section>'''

    hizmet_l = [("forklift-satis", "Forklift Satış", "Sıfır ve ikinci el dizel, akülü forklift; istif makinası ve transpalet."),
                ("forklift-kiralama", "Forklift Kiralama", "Günlük, aylık, uzun dönem. Bakım ve servis kiraya dahil."),
                ("servis", "Servis ve Yedek Parça", "24 saatte servis ve yedek parça garantisi, periyodik bakım."),
                ("finansal-cozumler", "Finansal Çözümler", "Peşin alımda avantajlı fiyat, 60 aya varan leasing.")]
    hizmetler = f'''<section class="bolum gri"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Hizmetlerimiz</span><h2>Satıştan servise tek muhatap</h2></div>
<p>Makineyi satın alın, kiralayın ya da mevcut filonuzun bakımını bize bırakın. Her durumda tek telefonla ulaşacağınız bir ekip.</p></div>
<div class="hizmetler">{"".join(f'<a class="hizmet" href="/{s}"><span class="no">0{i+1}</span><h3>{a}</h3><p>{d}</p><span class="ok">Detaylı bilgi →</span></a>' for i, (s, a, d) in enumerate(hizmet_l))}</div>
</div></section>'''

    kat_gorsel = {"dizel-forklift": "/assets/img/forklift-nigmer-800.webp", "akulu-forklift": "/assets/img/hero-kiralama-800.webp",
                  "ikinci-el-forklift": "/assets/img/hero-servis-800.webp"}
    kartlar = ""
    for k in KATEGORILER:
        g = kat_gorsel.get(k["slug"])
        img = f'<img src="{g}" alt="" loading="lazy">' if g else ""
        kartlar += f'<a class="kat" href="/{k["slug"]}">{img}{CIZIM}<span class="tur">{"Forklift" if k["ust"] else "Depo ekipmanı"}</span><h3>{e(k["ad"])}</h3><p>{e(k["ozet"])}</p></a>'
    urunler = f'''<section class="bolum"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Ürünler</span><h2>Her iş için doğru makine</h2></div>
<p>Dizel ve akülü forkliftlerden istif makinalarına, akülü ve manuel transpaletlere kadar geniş ürün yelpazesi.</p></div>
<div class="kategoriler">{kartlar}</div>
</div></section>'''

    band = f'''<section class="band"><div class="kap">
<div><h2>24 saatte servis ve yedek parça</h2><p>Makineniz arızalandığında 24 saat içinde servis ve yedek parça desteği sağlıyoruz. Bakım planınızı da birlikte çıkaralım.</p></div>
<a class="btn" href="/servis">Servis Talebi {I["ok"]}</a></div></section>'''

    video = f'''<section class="bolum koyu"><div class="kap video-ikili">
<div class="video-kutu"><video src="/assets/video/dellift-tanitim.mp4" poster="/assets/img/video-poster.jpg" muted loop playsinline autoplay preload="none" aria-label="DELLIFT tanıtım videosu"></video></div>
<div><span class="etiket">DELLIFT</span><h2>Satış, kiralama ve servis aynı çatı altında</h2>
<p style="color:#c9ccd0">Sultanbeyli'deki merkezimizden İstanbul ve çevresindeki işletmelere hizmet veriyoruz. Makineyi seçerken, teslim alırken ve yıllarca kullanırken muhatabınız aynı ekip.</p>
<div class="sayilar">
<div><b>24<em>s</em></b><span>içinde servis ve yedek parça</span></div>
<div><b>60<em>ay</em></b><span>vadeye kadar leasing seçeneği</span></div>
<div><b>6</b><span>marka: Niğmer, EP, Litef, Dolphin, JAC, Lonking</span></div>
<div><b>6<em>ay</em></b><span>ödemesiz finansman imkânı</span></div>
</div>
<div style="margin-top:30px;display:flex;gap:12px;flex-wrap:wrap"><a class="btn btn-k" href="/hakkimizda">Hakkımızda {I["ok"]}</a><a class="btn btn-c" href="{F["instagram"]}" target="_blank" rel="noopener">{I["ig"]} Instagram</a></div>
</div></div></section>'''

    markalar = f'''<section class="bolum"><div class="kap">
<div class="bolum-bas"><div><span class="etiket">Markalar</span><h2>Çalıştığımız markalar</h2></div><p>Markaya tıklayın, o markanın forklift ve istif makinası seçeneklerini görün.</p></div>
<div class="markalar">{"".join(f'<a class="marka" href="/marka/{m["slug"]}">{e(m["ad"])}</a>' for m in MARKALAR)}</div>
</div></section>'''

    govde = hero + giris + hizmetler + urunler + band + video + markalar + iletisim_blok()
    return sayfa("index", "DELLIFT | Forklift Satış, Kiralama ve Servis – İstanbul",
                 "DELLIFT: sıfır ve ikinci el forklift satışı, forklift kiralama, 24 saatte servis ve yedek parça. Niğmer, EP, Litef, Dolphin, JAC, Lonking. Sultanbeyli / İstanbul.",
                 govde, "ana", '<link rel="preload" as="image" href="/assets/img/hero-satis.webp">\n')


def metin_sayfasi(yol, baslik, aciklama, h1, alt, kirinti, icerik, yan, aktif, gorsel=None):
    govde = sayfa_bas(h1, alt, kirinti, gorsel or "/assets/img/forklift-nigmer-800.webp") + f'''<section class="bolum"><div class="kap yan-duzen">
<article class="metin">{icerik}</article>
<aside class="yan">{yan}</aside></div></section>'''
    return sayfa(yol, baslik, aciklama, govde, aktif)


def hakkimizda():
    ic = f'''<span class="etiket">Hakkımızda</span>
<h2 style="margin-top:0">İşinizi taşıyan makineler, yanınızda duran bir ekip</h2>
<p>DELLIFT, forklift ve istif makinaları alanında satış, kiralama, servis ve yedek parça hizmeti veren bir İstanbul firmasıdır. Sultanbeyli'deki merkezimizden depo, lojistik, üretim, inşaat ve perakende sektöründeki işletmelere hizmet veriyoruz.</p>
<p>Bizim için forklift satmak, işin yalnızca başlangıcı. Doğru makine; taşınan yüke, kaldırma yüksekliğine, zemine ve vardiya düzenine göre seçilir. Bu yüzden önce işinizi dinliyor, gerekirse sahanızı yerinde görüyor, ardından size en uygun modeli ve en uygun ödeme yolunu birlikte belirliyoruz.</p>
<div class="vurgu"><b>Makineniz durursa, işiniz durmasın.</b>Satış ve kiralamanın arkasında 24 saatte servis ve yedek parça sözümüz var.</div>
<h2>Neler yapıyoruz?</h2>
<ul>
<li><b>Satış:</b> Niğmer, EP, Litef, Dolphin, JAC ve Lonking markalarında sıfır dizel ve akülü forklift, istif makinası ve transpalet; kontrolden geçmiş ikinci el forkliftler.</li>
<li><b>Kiralama:</b> Günlük, haftalık, aylık ve uzun dönem forklift kiralama; bakım ve servis kiraya dahil.</li>
<li><b>Servis ve yedek parça:</b> Arıza müdahalesi, periyodik bakım ve yedek parça tedariki.</li>
<li><b>Finansal çözümler:</b> Peşin alımda avantajlı fiyat; leasing ile 6 ay ödemesiz, 60 aya varan vade.</li>
</ul>
<h2>Nasıl çalışıyoruz?</h2>
<ol class="adimlar">
<li><div><b>İhtiyacı dinliyoruz.</b> Yük, yükseklik, zemin ve çalışma süresini öğreniyoruz.</div></li>
<li><div><b>Doğru makineyi öneriyoruz.</b> Satın alma, kiralama ya da ikinci el seçeneklerini karşılaştırmalı sunuyoruz.</div></li>
<li><div><b>Hızlı teslim ediyoruz.</b> Makine, kontrolleri yapılmış ve kullanıma hazır şekilde sahanıza gelir.</div></li>
<li><div><b>Yanınızda kalıyoruz.</b> Bakım takvimi, servis ve yedek parçada muhatabınız yine biziz.</div></li>
</ol>'''
    return metin_sayfasi("hakkimizda", "Hakkımızda", "DELLIFT; forklift ve istif makinalarında satış, kiralama, servis ve yedek parça hizmeti veren İstanbul Sultanbeyli merkezli firma.",
                         "Hakkımızda", "Forklift ve istif makinalarında satış, kiralama, servis ve yedek parça.", [("Kurumsal", "/hakkimizda"), ("Hakkımızda", None)],
                         ic, yan_kutu(), "kurumsal")


def kvkk():
    U = e(F["unvan"])
    ic = f'''<h2 style="margin-top:0">Kişisel Verilerin Korunması ve Aydınlatma Metni</h2>
<p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında kişisel verilerinizin korunmasına önem veriyoruz. İşbu Aydınlatma Metni, {U} ("DELLIFT" veya "Şirket") tarafından internet sitemiz üzerinden sunulan iletişim ve teklif formları aracılığıyla elde edilen kişisel verilerin işlenmesine ilişkin olarak hazırlanmıştır.</p>
<h3>1. Veri Sorumlusu</h3>
<p>KVKK kapsamında kişisel verileriniz, veri sorumlusu sıfatıyla aşağıdaki şirket tarafından işlenmektedir:</p>
<ul><li><b>Şirket:</b> {U}</li><li><b>Adres:</b> {e(F["adres"])}</li><li><b>Telefon:</b> {F["tel"]}</li><li><b>E-posta:</b> {F["eposta"]}</li></ul>
<h3>2. İşlenen Kişisel Veriler</h3>
<p>İnternet sitemizdeki iletişim ve teklif formları aracılığıyla aşağıdaki kişisel verileriniz işlenebilmektedir:</p>
<ul><li>Ad ve soyad,</li><li>Telefon numarası,</li><li>E-posta adresi,</li><li>Firma adı,</li><li>Form aracılığıyla ilettiğiniz mesaj, talep ve teklif bilgileri.</li></ul>
<p>Formlarda yalnızca talebinizin değerlendirilmesi ve sizinle iletişim kurulması için gerekli bilgiler alınmaktadır.</p>
<h3>3. Kişisel Verilerin İşlenme Amaçları</h3>
<p>Toplanan kişisel verileriniz;</p>
<ul><li>İletişim taleplerinizin alınması ve cevaplandırılması,</li><li>Teklif taleplerinizin değerlendirilmesi,</li><li>Talebinize uygun ürün veya hizmetlerin belirlenmesi,</li><li>Forklift ve iş makinası satış, kiralama, servis, yedek parça ve finansal çözüm talepleriniz hakkında sizinle iletişim kurulması,</li><li>Talep ve müşteri ilişkileri süreçlerinin yürütülmesi,</li><li>Müşteri memnuniyetinin sağlanması,</li><li>Gerekli hâllerde hukuki yükümlülüklerin yerine getirilmesi</li></ul>
<p>amaçlarıyla sınırlı olarak işlenmektedir. Kişisel verileriniz, toplandıkları amaçlarla bağlantılı, sınırlı ve ölçülü şekilde işlenir.</p>
<h3>4. Kişisel Verilerin Toplanma Yöntemi ve Hukuki Sebebi</h3>
<p>Kişisel verileriniz, internet sitemizdeki iletişim ve teklif formlarını doldurmanız suretiyle elektronik ortamda elde edilmektedir. Bu veriler, KVKK'nın 5. maddesinde belirtilen "bir sözleşmenin kurulması veya ifasıyla doğrudan doğruya ilgili olması" ve "ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla veri sorumlusunun meşru menfaatleri için veri işlenmesinin zorunlu olması" hukuki sebeplerine dayanılarak işlenmektedir.</p>
<h3>5. Kişisel Verilerin Aktarılması</h3>
<p>İletişim ve teklif formları aracılığıyla elde edilen kişisel verileriniz satış veya pazarlama amacıyla üçüncü kişilerle paylaşılmaz. Kişisel verileriniz yalnızca DELLIFT bünyesinde, talebinizin değerlendirilmesi ve size geri dönüş yapılması için yetkili kişiler tarafından erişilebilir şekilde işlenir. Finansman (leasing) talep etmeniz hâlinde, talebinizin değerlendirilebilmesi için gerekli bilgiler yalnızca sizin onayınızla ilgili finans kuruluşuna iletilir. Kanunen yetkili kamu kurum ve kuruluşlarının usulüne uygun talepleri veya Şirketimizin hukuki yükümlülüklerini yerine getirmesi gereken durumlarda, mevzuatın izin verdiği ölçüde ilgili mercilerle paylaşım yapılabilir.</p>
<h3>6. Kişisel Verilerin Saklanması</h3>
<p>Kişisel verileriniz, işlenme amaçlarının gerektirdiği süre boyunca ve ilgili mevzuatta öngörülen yasal saklama süreleri çerçevesinde muhafaza edilir. İşleme amacının ortadan kalkması ve verilerin saklanmasını gerektiren başka bir hukuki yükümlülüğün bulunmaması hâlinde kişisel verileriniz mevzuata uygun şekilde silinir, yok edilir veya anonim hâle getirilir.</p>
<h3>7. Kişisel Verilerin Güvenliği</h3>
<p>DELLIFT, kişisel verilerin hukuka aykırı olarak işlenmesini ve erişilmesini önlemek, kişisel verilerin güvenli şekilde muhafaza edilmesini sağlamak amacıyla gerekli teknik ve idari tedbirleri almaktadır. Kişisel verilere yalnızca görevleri gereği erişmesi gereken yetkili kişiler erişebilir.</p>
<h3>8. KVKK Kapsamındaki Haklarınız</h3>
<p>KVKK'nın 11. maddesi kapsamında veri sahibi olarak;</p>
<ul><li>Kişisel verilerinizin işlenip işlenmediğini öğrenme,</li><li>İşlenmişse buna ilişkin bilgi talep etme,</li><li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme,</li><li>Kişisel verilerinizin aktarıldığı üçüncü kişileri öğrenme,</li><li>Eksik veya yanlış işlenmiş kişisel verilerin düzeltilmesini isteme,</li><li>Kanuni şartların oluşması hâlinde kişisel verilerin silinmesini veya yok edilmesini isteme,</li><li>Yapılan düzeltme, silme veya yok etme işlemlerinin verilerin aktarıldığı üçüncü kişilere bildirilmesini isteme,</li><li>Kanunda belirtilen diğer haklarınızı kullanma</li></ul>
<p>hakkına sahipsiniz.</p>
<h3>9. Başvuru</h3>
<p>KVKK kapsamındaki taleplerinizi <b>{F["eposta"]}</b> e-posta adresine veya <b>{e(F["adres"])}</b> adresine yazılı olarak iletebilirsiniz. Başvurularınız, talebin niteliğine göre en geç 30 gün içinde 6698 sayılı Kanun ve ilgili mevzuat kapsamında değerlendirilerek sonuçlandırılacaktır.</p>'''
    return metin_sayfasi("kvkk", "KVKK Aydınlatma Metni", "DELLIFT internet sitesi iletişim ve teklif formları için 6698 sayılı KVKK kapsamında aydınlatma metni.",
                         "KVKK Aydınlatma Metni", None, [("Kurumsal", "/hakkimizda"), ("KVKK", None)], ic, yan_kutu(), "kurumsal")


def satis():
    ic = f'''<span class="etiket">Forklift Satış</span>
<h2 style="margin-top:0">İşinize uygun forklifti birlikte seçelim</h2>
<p>Sıfır ve ikinci el forklift satışında amacımız size en pahalı makineyi değil, işinizi en verimli yapacak makineyi satmak. Taşıyacağınız yük, kaldırma yüksekliği, çalışma zemini ve günlük kullanım süresi üzerinden ihtiyacınızı netleştirir, seçenekleri karşılaştırmalı olarak sunarız.</p>
<h2>Satışını yaptığımız ürünler</h2>
<ul>
<li><a href="/dizel-forklift"><b>Dizel forkliftler</b></a> — açık saha, şantiye ve ağır yük işleri için.</li>
<li><a href="/akulu-forklift"><b>Akülü (elektrikli) forkliftler</b></a> — kapalı alan, gıda ve depo işletmeleri için; lityum ve jel akü seçenekleriyle.</li>
<li><a href="/ikinci-el-forklift"><b>İkinci el forkliftler</b></a> — bakımı yapılmış, kontrolden geçmiş, bütçe dostu makineler.</li>
<li><a href="/istif-makinalari"><b>İstif makinaları</b></a>, <a href="/transpaletler"><b>akülü transpaletler</b></a> ve <a href="/manuel-transpaletler"><b>manuel transpaletler</b></a>.</li>
</ul>
<p>Niğmer, EP, Litef, Dolphin, JAC ve Lonking markalarında farklı kapasite, direk yüksekliği ve lastik seçenekleri sunuyoruz.</p>
<div class="vurgu"><b>Peşin alımda avantajlı fiyat</b>Peşin ödemede size özel fiyat çalışıyoruz. Finansman isterseniz leasing ile 6 ay ödemesiz, 60 aya varan vade de mümkün. <a href="/finansal-cozumler">Finansal çözümler →</a></div>
<h2>Satın alma süreci</h2>
<ol class="adimlar">
<li><div><b>İhtiyaç analizi:</b> Yük, yükseklik, zemin ve kullanım süresini konuşuyoruz; gerekirse sahanızı yerinde görüyoruz.</div></li>
<li><div><b>Teklif:</b> Uygun modelleri teknik özellikleri ve fiyatlarıyla karşılaştırmalı olarak sunuyoruz.</div></li>
<li><div><b>Teslimat:</b> Makine kontrolleri yapılmış, kullanıma hazır şekilde adresinize teslim edilir; operatöre temel kullanım bilgisi verilir.</div></li>
<li><div><b>Satış sonrası:</b> Periyodik bakım, 24 saatte servis ve yedek parça desteğiyle yanınızdayız.</div></li>
</ol>'''
    return metin_sayfasi("forklift-satis", "Forklift Satış – Sıfır ve 2. El", "Sıfır ve ikinci el dizel, akülü forklift, istif makinası ve transpalet satışı. Peşin alımda avantajlı fiyat, 60 aya varan leasing. DELLIFT İstanbul.",
                         "Forklift Satış", "Sıfır ve ikinci el forklift, istif makinası ve transpalet.", [("Hizmetlerimiz", "/forklift-satis"), ("Forklift Satış", None)],
                         ic, hizmet_yan("forklift-satis") + yan_kutu(), "hizmet", "/assets/img/hero-satis-800.webp")


def kiralama():
    ic = f'''<span class="etiket">Forklift Kiralama</span>
<h2 style="margin-top:0">Makine almadan, ihtiyacınız kadar kullanın</h2>
<p>Sezonluk yoğunluk, yeni bir proje, arızalı makinenizin yerine geçici çözüm ya da yatırım yapmadan filonuzu büyütmek... Forklift kiralama, sermayenizi bağlamadan ihtiyacınız olan makineye hemen ulaşmanın en pratik yoludur.</p>
<h2>Kiralama seçenekleri</h2>
<ul>
<li><b>Kısa dönem:</b> Günlük ve haftalık kiralama; acil işler, yükleme-boşaltma ve dönemsel yoğunluklar için.</li>
<li><b>Aylık kiralama:</b> Proje bazlı ve sezonluk işler için esnek süreli kiralama.</li>
<li><b>Uzun dönem kiralama:</b> 12 ay ve üzeri sözleşmelerle sabit aylık maliyet; bakım ve servis dahil.</li>
<li><b>Operatörlü / operatörsüz:</b> İhtiyacınıza göre makineyi operatörüyle birlikte de sağlayabiliriz.</li>
</ul>
<h2>Kiralamanın avantajları</h2>
<ul>
<li>Makine için büyük bir yatırım yapmazsınız, nakit akışınız korunur.</li>
<li>Periyodik bakım ve servis kiraya dahildir; arızada makine değişimi yapılır.</li>
<li>İşin büyüklüğüne göre farklı kapasitede makineye geçebilirsiniz.</li>
<li>Kira bedeli işletme gideri olarak kaydedilebilir (ayrıntı için mali müşavirinize danışın).</li>
</ul>
<div class="vurgu"><b>Dizel, akülü, istif ve transpalet</b>Kiralık filomuzda farklı kapasitelerde dizel ve akülü forkliftler ile istif makinaları bulunur. Güncel uygunluk ve fiyat için bizi arayın.</div>'''
    return metin_sayfasi("forklift-kiralama", "Forklift Kiralama – Günlük, Aylık, Uzun Dönem", "İstanbul'da günlük, aylık ve uzun dönem forklift kiralama. Dizel ve akülü forklift, istif makinası. Bakım ve servis dahil. DELLIFT.",
                         "Forklift Kiralama", "Günlük, aylık ve uzun dönem; bakım ve servis dahil.", [("Hizmetlerimiz", "/forklift-satis"), ("Forklift Kiralama", None)],
                         ic, hizmet_yan("forklift-kiralama") + yan_kutu(), "hizmet", "/assets/img/hero-kiralama-800.webp")


def servis():
    ic = f'''<span class="etiket">Servis ve Yedek Parça</span>
<h2 style="margin-top:0">24 saatte servis ve yedek parça garantisi</h2>
<div class="vurgu"><b>24 saat içinde yanınızdayız</b>Forkliftiniz arızalandığında 24 saat içinde servis ve yedek parça desteği sağlıyoruz. Durduğunuz her saat size maliyet; biz bunu biliyoruz.</div>
<p>Satışını yaptığımız makinelerin yanı sıra farklı marka forklift, istif makinası ve transpaletlere de servis veriyoruz. Mobil servis ekibimiz sahanıza gelir; atölye gerektiren işler merkezimizde yapılır.</p>
<h2>Servis hizmetlerimiz</h2>
<ul>
<li><b>Arıza müdahalesi:</b> Motor, şanzıman, hidrolik, elektrik ve fren sistemlerinde arıza tespiti ve onarım.</li>
<li><b>Periyodik bakım:</b> Üretici tavsiyelerine uygun, çalışma saatine göre planlanmış bakım; yağ, filtre ve sarf malzeme değişimi.</li>
<li><b>Akü bakımı:</b> Akülü forklift ve istif makinalarında akü ve şarj sistemi kontrolü.</li>
<li><b>Bakım sözleşmesi:</b> Filonuz için yıllık bakım planı ve öncelikli servis.</li>
</ul>
<h2>Yedek parça</h2>
<p>Filtre, lastik, zincir, fren, hidrolik ve motor parçalarında geniş stok ile hızlı tedarik sağlıyoruz. Stokta olmayan parçalar için en kısa tedarik süresini birlikte planlıyoruz.</p>
<h2>Servis talebi nasıl oluşturulur?</h2>
<ol class="adimlar">
<li><div>Bizi arayın ya da WhatsApp'tan makinenin marka-modelini ve arızayı yazın; mümkünse bir fotoğraf ya da video ekleyin.</div></li>
<li><div>Ekibimiz ön tespiti yapar, gerekli parçayla birlikte yola çıkar.</div></li>
<li><div>Onarım sonrası makine test edilir, yapılan işlemler size raporlanır.</div></li>
</ol>'''
    yan = hizmet_yan("servis") + f'''<div class="yan-kutu"><h3>Servis talebi</h3><p>Arızayı ve makine bilgisini yazın, hemen dönüş yapalım.</p>
<a class="btn btn-k" href="{wa_link("Merhaba, servis talebim var. Makine marka/model: ")}" target="_blank" rel="noopener">{I["wa"]} WhatsApp'tan Yaz</a>
<a class="btn btn-c" href="tel:{F["tel_uri"]}">{I["tel"]} {F["tel"]}</a></div>'''
    return metin_sayfasi("servis", "Forklift Servis ve Yedek Parça – 24 Saatte", "Forklift servis ve yedek parçada 24 saat garantisi. Arıza müdahalesi, periyodik bakım, akü bakımı ve bakım sözleşmesi. DELLIFT İstanbul.",
                         "Servis ve Yedek Parça", "24 saatte servis ve yedek parça garantisi.", [("Hizmetlerimiz", "/forklift-satis"), ("Servis", None)],
                         ic, yan, "hizmet", "/assets/img/hero-servis-800.webp")


def finans():
    ic = f'''<span class="etiket">Finansal Çözümler</span>
<h2 style="margin-top:0">Peşin alın, avantajlı fiyattan yararlanın</h2>
<p>Forklift alımında en avantajlı yol peşin alımdır. Peşin ödemelerde size özel fiyat çalışıyor, stoktaki makineleri hızlıca teslim ediyoruz. Ara finansman maliyeti olmadan makineniz doğrudan sizin olur.</p>
<div class="vurgu"><b>Peşin alımda özel fiyat</b>İhtiyacınızı paylaşın, peşin ödeme için net fiyat teklifimizi aynı gün iletelim.</div>
<h2>Leasing ile finansman</h2>
<p>Nakit akışınızı korumak istiyorsanız forklifti leasing (finansal kiralama) yöntemiyle edinebilirsiniz. Anlaşmalı leasing şirketleri aracılığıyla:</p>
<ul>
<li><b>6 ay ödemesiz</b> başlangıç imkânı,</li>
<li><b>60 aya varan</b> vade seçenekleri,</li>
<li>İşletmenizin nakit akışına uygun ödeme planı,</li>
<li>Vade sonunda makinenin mülkiyetinin size geçmesi</li>
</ul>
<p>gibi seçenekler sunulabilir. Leasing başvurusu için gereken belgeleri size iletir, süreci baştan sona takip ederiz. Kesin vade, ödemesiz dönem ve koşullar leasing şirketinin değerlendirmesine göre belirlenir.</p>
<h2>Leasing süreci</h2>
<ol class="adimlar">
<li><div>Makine ve fiyat üzerinde anlaşırız.</div></li>
<li><div>Firmanızın belgeleriyle leasing başvurusu yapılır.</div></li>
<li><div>Onayın ardından sözleşme imzalanır, makine teslim edilir.</div></li>
</ol>'''
    return metin_sayfasi("finansal-cozumler", "Finansal Çözümler – Peşin ve Leasing", "Forklift alımında peşin ödemede avantajlı fiyat; leasing ile 6 ay ödemesiz, 60 aya varan vade seçenekleri. DELLIFT.",
                         "Finansal Çözümler", "Peşin alımda avantajlı fiyat, leasing ile 60 aya varan vade.", [("Hizmetlerimiz", "/forklift-satis"), ("Finansal Çözümler", None)],
                         ic, hizmet_yan("finansal-cozumler") + yan_kutu(), "hizmet")


def sss():
    ic = '<span class="etiket">Sıkça Sorulan Sorular</span><h2 style="margin-top:0">Forklift hakkında merak edilenler</h2><div class="sss">' + \
         "".join(f'<details{" open" if i == 0 else ""}><summary>{e(s)}</summary><p>{e(c)}</p></details>' for i, (s, c) in enumerate(SSS)) + "</div>"
    sema = ('<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage","mainEntity":['
            + ",".join('{"@type":"Question","name":' + _js(s) + ',"acceptedAnswer":{"@type":"Answer","text":' + _js(c) + '}}' for s, c in SSS) + ']}</script>')
    govde = sayfa_bas("Sıkça Sorulan Sorular", "Forklift alırken, kiralarken ve kullanırken en çok sorulanlar.", [("Hizmetlerimiz", "/forklift-satis"), ("SSS", None)]) + \
        f'<section class="bolum"><div class="kap yan-duzen"><article class="metin">{ic}</article><aside class="yan">{hizmet_yan("sss")}{yan_kutu()}</aside></div></section>'
    return sayfa("sss", "Sıkça Sorulan Sorular", "Forklift alırken nelere dikkat edilmeli, dizel mi akülü mü, fiyatlar nasıl belirlenir, ehliyet gerekir mi? DELLIFT SSS.", govde, "hizmet", sema)


def _js(s):
    import json
    return json.dumps(s, ensure_ascii=False)


def urun_kart(u):
    m = next((x["ad"] for x in MARKALAR if x["slug"] == u["marka"]), "")
    satir = "".join(f"<tr><td>{e(a)}</td><td>{e(d)}</td></tr>" for a, d in u.get("ozellik", []))
    return f'''<div class="urun"><img src="/{u["gorsel"]}" alt="{e(u["ad"])}" loading="lazy"><div class="ic">
<span class="etiket">{e(m)} · {e(u.get("model", ""))}</span><h3>{e(u["ad"])}</h3><p style="color:var(--gri);font-size:.95rem">{e(u.get("aciklama", ""))}</p>
<table>{satir}</table><a class="btn btn-k" style="margin-top:auto" href="{wa_link("Merhaba, " + u["ad"] + " için fiyat almak istiyorum.")}" target="_blank" rel="noopener">Fiyat Sor {I["ok"]}</a></div></div>'''


def marka_kartlari(markalar, kat_ad):
    out = ""
    for m in markalar:
        out += f'''<div class="mk"><div class="ad">{e(m["ad"])}</div><p>{e(m["ozet"])}</p>
<div class="bag"><a class="wa" href="{wa_link(f"Merhaba, {m['ad']} {kat_ad.lower()} modelleri hakkında bilgi almak istiyorum.")}" target="_blank" rel="noopener">WhatsApp'tan model ve fiyat sor →</a><a href="/marka/{m["slug"]}">Marka sayfası</a></div></div>'''
    return f'<div class="marka-kartlar">{out}</div>'


def kategori_sayfasi(k):
    markalar = [m for m in MARKALAR if k["slug"] in m["kat"]]
    urunler = [u for u in URUNLER if u["kat"] == k["slug"]]
    cipler = '<div class="cipler">' + "".join(f'<a href="/{x["slug"]}"{" aria-current=page" if x["slug"] == k["slug"] else ""}>{e(x["ad"])}</a>' for x in KATEGORILER) + "</div>"
    blok = ""
    if urunler:
        blok += f'<h2 style="margin-top:48px">Öne çıkan modeller</h2><div class="urunler">{"".join(urun_kart(u) for u in urunler)}</div>'
    if markalar:
        blok += f'<h2 style="margin-top:48px">Markalar</h2><p style="color:var(--gri)">Güncel model, stok ve teknik föy için markayı seçip bize yazın.</p>{marka_kartlari(markalar, k["ad"])}'
    else:
        blok += f'''<div class="vurgu" style="margin-top:40px"><b>Güncel stok ve fiyat</b>{e(k["ad"])} için güncel model, kapasite ve fiyat bilgisini almak için bize yazın.
<p style="margin:14px 0 0"><a class="btn btn-k" href="{wa_link(f"Merhaba, {k['ad'].lower()} hakkında bilgi almak istiyorum.")}" target="_blank" rel="noopener">{I["wa"]} WhatsApp'tan Sor</a></p></div>'''
    kir = [("Ürünler", "/urunler")] + ([("Forklift", "/urunler#forklift")] if k["ust"] else []) + [(k["ad"], None)]
    govde = sayfa_bas(e(k["ad"]), e(k["ozet"]), kir) + f'''<section class="bolum"><div class="kap">
<div class="metin"><p style="font-size:1.12rem">{e(k["metin"])}</p></div>{cipler}{blok}</div></section>''' + iletisim_blok()
    return sayfa(k["slug"], k["ad"], f'{k["ad"]}: {k["ozet"]} Satış, kiralama ve servis — DELLIFT İstanbul.', govde, "urun")


def urunler_sayfasi():
    fork = [k for k in KATEGORILER if k["ust"]]
    diger = [k for k in KATEGORILER if not k["ust"]]
    def kartlar(l):
        return '<div class="kategoriler">' + "".join(f'<a class="kat" href="/{k["slug"]}">{CIZIM}<span class="tur">{"Forklift" if k["ust"] else "Depo ekipmanı"}</span><h3>{e(k["ad"])}</h3><p>{e(k["ozet"])}</p></a>' for k in l) + "</div>"
    govde = sayfa_bas("Ürünler", "Forklift, istif makinası ve transpaletlerde geniş ürün yelpazesi.", [("Ürünler", None)]) + f'''
<section class="bolum" id="forklift"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Forklift</span><h2>Dizel, akülü ve 2. el</h2></div></div>{kartlar(fork)}</div></section>
<section class="bolum gri"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Depo ekipmanları</span><h2>İstif ve transpalet</h2></div></div>{kartlar(diger)}</div></section>
<section class="bolum"><div class="kap"><div class="bolum-bas"><div><span class="etiket">Markalar</span><h2>Çalıştığımız markalar</h2></div></div>
<div class="markalar">{"".join(f'<a class="marka" href="/marka/{m["slug"]}">{e(m["ad"])}</a>' for m in MARKALAR)}</div></div></section>'''
    return sayfa("urunler", "Ürünler – Forklift, İstif Makinası, Transpalet", "Dizel ve akülü forklift, 2. el forklift, istif makinası, akülü ve manuel transpalet. Niğmer, EP, Litef, Dolphin, JAC, Lonking.", govde, "urun")


def marka_sayfasi(m):
    kats = [kat(s) for s in m["kat"]]
    urunler = [u for u in URUNLER if u["marka"] == m["slug"]]
    liste = "".join(f'''<div class="mk"><div class="ad" style="font-size:1.5rem">{e(k["ad"])}</div><p>{e(k["ozet"])}</p>
<div class="bag"><a class="wa" href="{wa_link(f"Merhaba, {m['ad']} {k['ad'].lower()} modelleri hakkında bilgi almak istiyorum.")}" target="_blank" rel="noopener">WhatsApp'tan sor →</a><a href="/{k["slug"]}">Kategoriye git</a></div></div>''' for k in kats)
    blok = f'<h2 style="margin-top:40px">Modeller</h2><div class="urunler">{"".join(urun_kart(u) for u in urunler)}</div>' if urunler else ""
    cipler = '<div class="cipler">' + "".join(f'<a href="/marka/{x["slug"]}"{" aria-current=page" if x["slug"] == m["slug"] else ""}>{e(x["ad"])}</a>' for x in MARKALAR) + "</div>"
    govde = sayfa_bas(e(m["ad"]), e(m["ozet"]), [("Ürünler", "/urunler"), (m["ad"], None)]) + f'''<section class="bolum"><div class="kap">
<div class="metin"><p style="font-size:1.12rem">{e(m["ad"])} markalı forklift ve depo ekipmanlarında satış, kiralama ve servis hizmeti veriyoruz. Güncel model listesi, teknik föy ve fiyat için bize yazın; ihtiyacınıza en uygun modeli birlikte belirleyelim.</p></div>
{cipler}{blok}<h2 style="margin-top:40px">{e(m["ad"])} ürün grupları</h2><div class="marka-kartlar">{liste}</div></div></section>''' + iletisim_blok()
    return sayfa(f"marka/{m['slug']}", f"{m['ad']} Forklift", f"{m['ad']} dizel ve akülü forklift, istif makinası satış, kiralama ve servis. DELLIFT İstanbul.", govde, "urun")


def iletisim():
    govde = sayfa_bas("İletişim", "Bize telefonla, WhatsApp'tan ya da formla ulaşın; aynı gün dönüş yapalım.", [("İletişim", None)]) + iletisim_blok(gri=False)
    return sayfa("iletisim", "İletişim", f"DELLIFT iletişim: {F['tel']}, {F['eposta']}. {F['adres']}.", govde, "iletisim")


def bulunamadi():
    govde = f'''<section class="bolum"><div class="kap" style="text-align:center;padding:40px 20px">
<span class="etiket">404</span><h1>Sayfa bulunamadı</h1><p style="color:var(--gri)">Aradığınız sayfa taşınmış ya da kaldırılmış olabilir.</p>
<p><a class="btn btn-k" href="/">Ana sayfaya dön {I["ok"]}</a></p></div></section>'''
    return sayfa("404", "Sayfa bulunamadı", "Aradığınız sayfa bulunamadı.", govde)


def yaz(yol, icerik):
    p = os.path.join(KOK, yol + ".html")
    os.makedirs(os.path.dirname(p), exist_ok=True)
    with open(p, "w", encoding="utf-8") as f:
        f.write(icerik)
    return yol


def main():
    sayfalar = [yaz("index", ana_sayfa()), yaz("hakkimizda", hakkimizda()), yaz("kvkk", kvkk()),
                yaz("forklift-satis", satis()), yaz("forklift-kiralama", kiralama()), yaz("servis", servis()),
                yaz("finansal-cozumler", finans()), yaz("sss", sss()), yaz("urunler", urunler_sayfasi()), yaz("iletisim", iletisim())]
    sayfalar += [yaz(k["slug"], kategori_sayfasi(k)) for k in KATEGORILER]
    sayfalar += [yaz(f"marka/{m['slug']}", marka_sayfasi(m)) for m in MARKALAR]
    yaz("404", bulunamadi())
    bugun = date.today().isoformat()
    urls = "".join(f"<url><loc>{F['site']}{'/' if s == 'index' else '/' + s}</loc><lastmod>{bugun}</lastmod></url>" for s in sayfalar)
    with open(os.path.join(KOK, "sitemap.xml"), "w", encoding="utf-8") as f:
        f.write(f'<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">{urls}</urlset>')
    print(len(sayfalar), "sayfa yazıldı")


if __name__ == "__main__":
    main()
