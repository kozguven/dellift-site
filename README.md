# DELLIFT — dellift.com.tr

Forklift ve istif makinaları firması DELLIFT'in statik web sitesi (Ekrem'in getirdiği iş, 27.10.2026).

- İçerik/ürün verisi: `araclar/veri.py` → `python3 araclar/build.py` → `site/`
- Canlı (Cenuta hosting): `site/` içeriği public_html'e yüklenir (`.htaccess` temiz URL + HTTPS, `gonder.php` form → info@dellift.com.tr)
- Önizleme (GitHub Pages): `python3 araclar/onizleme.py` → `docs/` → https://kozguven.github.io/dellift-site/
