# DELLIFT — dellift.com.tr

Forklift ve istif makinaları firması DELLIFT'in web sitesi + yönetim paneli (Ekrem'in getirdiği iş, 27.10.2026; panel ve yayın 01.11.2026).

## Yapı
- `web/` = canlıdaki `public_html` (Cenuta Limitsiz M, cPanel kullanıcı `delliftc`, sunucu cp68.servername.co, **PHP 8.3**, CloudLinux PHP Selector; upload 64M, post 128M, zip + opcache açık; kod 7.4 uyumlu da kalsın).
- `web/index.php` ön denetleyici; şablonlar `web/inc/` (eski `build.py`'nin PHP karşılığı). İçerik `web/veri/icerik.json`.
- Yönetim paneli: `https://dellift.com.tr/yonetim/` (kullanıcı `dellift`). Ürünler, detay sayfaları (galeri + teknik tablo),
  kataloglar (PDF, kapak otomatik), banner, kategori, marka, SSS, sayfa metinleri, firma bilgileri, gelen talepler.
  Her kayıtta `veri/yedek/` altına yedek (son 40). Şifre: tek kullanımlık bağlantı / "Şifremi unuttum" → info@dellift.com.tr.
- Form (`gonder.php`): e-posta + panelde "Gelen talepler" (`veri/mesajlar.json`).
- Footer'da Webizmo bağlantısı ("yazılım firması istanbul").
- `araclar/build.py` + `veri.py` artık arşiv; `site/` eski statik sürüm. GitHub Pages önizlemesi 01.11.2026 kapatıldı.

## Yerel deneme
`php -S 127.0.0.1:8090 -t web araclar/router.php` → panel için yerelde `web/veri/kullanici.json` gerekir (gitignore'da).

## Yayın (SSH yok)
- **Canlı içerik sunucuda yaşar**: `veri/icerik.json`, `veri/mesajlar.json`, `veri/kullanici.json`, `assets/yuklenen/` ve panelden
  yüklenen PDF'ler (`assets/dosya/<marka>/*-xxxxx.pdf`) asla yerelden ezilmez. Kod güncellemesinde yalnız `index.php`, `gonder.php`,
  `inc/`, `yonetim/`, `assets/css|js` gönderilir.
- Yöntem: cPanel'e Kerem/Ekrem girer (Cenuta'daki "cPanel Giriş" tek tıkla oturum açmaz) → cPanel UAPI
  `Fileman/save_file_content` ile jetonlu geçici yükleyici konur → yerelden parça parça POST + ZipArchive ile açılır → yükleyici çöpe.
- Cenuta WAF'ı curl'ün varsayılan User-Agent'ını 403'ler; isteklerde tarayıcı UA'sı kullan.
