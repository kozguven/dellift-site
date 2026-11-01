#!/usr/bin/env python3
"""Tek seferlik geçiş: veri.py + üretilmiş site/*.html metinleri → web/veri/icerik.json.
Bundan sonra içerik yönetim panelinden (/yonetim) düzenlenir; veri.py ve build.py arşivdir."""
import json, os, re, sys
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from veri import FIRMA, MARKALAR, KATEGORILER, URUNLER, SERILER, SSS, KATALOGLAR

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
SITE = os.path.join(KOK, "site")
HEDEF = os.path.join(KOK, "web", "veri", "icerik.json")


def makale(yol):
    h = open(os.path.join(SITE, yol + ".html"), encoding="utf-8").read()
    return re.search(r'<article class="metin">(.*?)</article>', h, re.S).group(1).strip()


SAYFALAR = {
    "hakkimizda": ("Hakkımızda", "DELLIFT; forklift ve istif makinalarında satış, kiralama, servis ve yedek parça hizmeti veren İstanbul Sultanbeyli merkezli firma.",
                   "Hakkımızda", "Forklift ve istif makinalarında satış, kiralama, servis ve yedek parça.", "/assets/img/forklift-nigmer-800.webp"),
    "forklift-satis": ("Forklift Satış – Sıfır ve 2. El", "Sıfır ve ikinci el dizel, akülü forklift, istif makinası ve transpalet satışı. Peşin alımda avantajlı fiyat, 60 aya varan leasing. DELLIFT İstanbul.",
                       "Forklift Satış", "Sıfır ve ikinci el forklift, istif makinası ve transpalet.", "/assets/img/hero-satis-800.webp"),
    "forklift-kiralama": ("Forklift Kiralama – Günlük, Aylık, Uzun Dönem", "İstanbul'da günlük, aylık ve uzun dönem forklift kiralama. Dizel ve akülü forklift, istif makinası. Bakım ve servis dahil. DELLIFT.",
                          "Forklift Kiralama", "Günlük, aylık ve uzun dönem; bakım ve servis dahil.", "/assets/img/hero-kiralama-800.webp"),
    "servis": ("Forklift Servis ve Yedek Parça – 24 Saatte", "Forklift servis ve yedek parçada 24 saat garantisi. Arıza müdahalesi, periyodik bakım, akü bakımı ve bakım sözleşmesi. DELLIFT İstanbul.",
               "Servis ve Yedek Parça", "24 saatte servis ve yedek parça garantisi.", "/assets/img/hero-servis-800.webp"),
    "finansal-cozumler": ("Finansal Çözümler – Peşin ve Leasing", "Forklift alımında peşin ödemede avantajlı fiyat; leasing ile 6 ay ödemesiz, 60 aya varan vade seçenekleri. DELLIFT.",
                          "Finansal Çözümler", "Peşin alımda avantajlı fiyat, leasing ile 60 aya varan vade.", "/assets/img/forklift-nigmer-800.webp"),
    "kvkk": ("KVKK Aydınlatma Metni", "DELLIFT internet sitesi iletişim ve teklif formları için 6698 sayılı KVKK kapsamında aydınlatma metni.",
             "KVKK Aydınlatma Metni", "", "/assets/img/forklift-nigmer-800.webp"),
}

veri = {
    "firma": {**FIRMA, "alt_bilgi_metni": "Forklift ve istif makinalarında satış, kiralama, servis ve yedek parça. Sultanbeyli'den İstanbul ve çevresine hızlı hizmet."},
    "slaytlar": [
        {"gorsel": "/assets/img/hero-satis.webp", "baslik": "Forklift ve istif makinalarında *doğru adres*",
         "metin": "Sıfır ve ikinci el forklift satışı, kısa ve uzun dönem kiralama, 24 saatte servis ve yedek parça. Niğmer, EP, Litef, Dolphin, JAC ve Lonking.",
         "b1_yazi": "Teklif Al", "b1_link": "/iletisim#teklif", "b2_yazi": "Satış", "b2_link": "/forklift-satis"},
        {"gorsel": "/assets/img/hero-kiralama.webp", "baslik": "İhtiyacınız kadar *kiralayın*",
         "metin": "Günlük, aylık ve uzun dönem forklift kiralama. Bakım ve servis bizden, siz işinize odaklanın.",
         "b1_yazi": "Fiyat Sor", "b1_link": "/iletisim#teklif", "b2_yazi": "Kiralama", "b2_link": "/forklift-kiralama"},
        {"gorsel": "/assets/img/hero-servis.webp", "baslik": "24 saatte *servis ve yedek parça*",
         "metin": "Arızada hızlı müdahale, periyodik bakım ve orijinal yedek parça. Makineniz durursa işiniz durmasın.",
         "b1_yazi": "WhatsApp'tan Yaz", "b1_link": "wa:Merhaba, servis talebim var.", "b2_yazi": "Servis", "b2_link": "/servis"},
    ],
    "markalar": MARKALAR,
    "kategoriler": KATEGORILER,
    "urunler": [],
    "seriler": [],
    "kataloglar": [],
    "sss": [{"soru": s, "cevap": c} for s, c in SSS],
    "sayfalar": {},
}

for i, u in enumerate(URUNLER):
    veri["urunler"].append({
        "id": f"u{i + 1}", "kat": u["kat"], "marka": u["marka"], "ad": u["ad"], "model": u.get("model", ""),
        "gorsel": "/" + u["gorsel"], "detay": (u.get("detay") or "").replace("urun/", ""),
        "ozellik": [list(x) for x in u.get("ozellik", [])], "aciklama": u.get("aciklama", ""),
        "anasayfa": i in (0, 2, 4), "aktif": True})

for sr in SERILER:
    gk = sr["gorsel_klasor"]
    veri["seriler"].append({
        "slug": sr["slug"], "marka": sr["marka"], "kat": sr["kat"], "ad": sr["ad"], "kisa": sr["kisa"],
        "giris": sr["giris"], "one_baslik": sr["one_baslik"], "not": sr["not"],
        "one_cikan": [list(x) for x in sr["one_cikan"]], "modeller": sr["modeller"],
        "teknik": [list(r) for r in sr["teknik"]],
        "gorseller": [f"/{gk}/{n:02d}" for n in range(1, sr["gorsel_adet"] + 1)],
        "katalog": "/" + sr["katalog"] if sr.get("katalog") else "", "aktif": True})

for marka, (_k, dil, liste) in KATALOGLAR.items():
    for _d, slug, model, acik, ks in liste:
        assert os.path.exists(os.path.join(SITE, f"assets/dosya/{marka}/{slug}.pdf")), (marka, slug)
        veri["kataloglar"].append({"marka": marka, "slug": slug, "model": model, "aciklama": acik, "kat": ks, "dil": dil,
                                   "pdf": f"/assets/dosya/{marka}/{slug}.pdf", "kapak": f"/assets/img/katalog/{marka}/{slug}.webp",
                                   "aktif": True})

for yol, (baslik, aciklama, h1, alt, gorsel) in SAYFALAR.items():
    veri["sayfalar"][yol] = {"baslik": baslik, "aciklama": aciklama, "h1": h1, "alt": alt, "gorsel": gorsel, "icerik": makale(yol)}

os.makedirs(os.path.dirname(HEDEF), exist_ok=True)
with open(HEDEF, "w", encoding="utf-8") as f:
    json.dump(veri, f, ensure_ascii=False, indent=1)
print("yazıldı:", HEDEF, len(veri["urunler"]), "ürün,", len(veri["seriler"]), "seri,", len(veri["kataloglar"]), "katalog")
