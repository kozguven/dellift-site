#!/usr/bin/env python3
"""site/ → docs/ : GitHub Pages önizlemesi (kozguven.github.io/dellift-site/).
Yolları alt klasöre göre düzeltir, arama motorlarına kapatır, formu WhatsApp'a yönlendirir.
Çalıştır: python3 araclar/build.py && python3 araclar/onizleme.py"""
import os, re, shutil

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
KAYNAK, HEDEF, BASE = os.path.join(KOK, "site"), os.path.join(KOK, "docs"), "/dellift-site"

FORM_JS = """<script>
document.addEventListener('submit',function(e){var f=e.target;if(f.id!=='teklif')return;e.preventDefault();
var v=function(n){var x=f.elements[n];return x?x.value.trim():''};
var m='Merhaba, web sitesinden yazıyorum.\\nAd: '+v('ad')+'\\nFirma: '+v('firma')+'\\nTelefon: '+v('tel')+'\\nKonu: '+v('konu')+'\\n\\n'+v('mesaj');
window.open('https://wa.me/905348217050?text='+encodeURIComponent(m),'_blank');});
</script>"""

shutil.rmtree(HEDEF, ignore_errors=True)
shutil.copytree(KAYNAK, HEDEF, ignore=shutil.ignore_patterns(".htaccess", "gonder.php", ".DS_Store"))
for kok, _, dosyalar in os.walk(HEDEF):
    for d in dosyalar:
        if not d.endswith(".html"):
            continue
        p = os.path.join(kok, d)
        s = open(p, encoding="utf-8").read()
        s = re.sub(r'(href|src|action|poster)="/(?!/)', rf'\1="{BASE}/', s)
        s = s.replace("url('/", f"url('{BASE}/")
        s = s.replace('<meta name="theme-color"', '<meta name="robots" content="noindex,nofollow">\n<meta name="theme-color"', 1)
        s = s.replace("</body>", FORM_JS + "\n</body>", 1)
        open(p, "w", encoding="utf-8").write(s)
open(os.path.join(HEDEF, "robots.txt"), "w").write("User-agent: *\nDisallow: /\n")
open(os.path.join(HEDEF, ".nojekyll"), "w").close()
print("docs/ hazır")
