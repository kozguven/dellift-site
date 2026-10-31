#!/usr/bin/env python3
"""veri.KATALOGLAR'daki üretici broşürlerini siteye hazırlar: PDF'i Ghostscript ile sıkıştırır (assets/dosya/<marka>/<slug>.pdf),
ilk sayfadan kapak görseli çıkarır (assets/img/katalog/<marka>/<slug>.webp). Çalıştır: python3 araclar/katalog.py"""
import os, subprocess, tempfile
from PIL import Image
from veri import KATALOGLAR

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")

for marka, (klasor, _dil, liste) in KATALOGLAR.items():
    pd, gd = os.path.join(KOK, "site/assets/dosya", marka), os.path.join(KOK, "site/assets/img/katalog", marka)
    os.makedirs(pd, exist_ok=True); os.makedirs(gd, exist_ok=True)
    for dosya, slug, *_ in liste:
        kaynak, pdf, kapak = os.path.join(KOK, klasor, dosya), os.path.join(pd, slug + ".pdf"), os.path.join(gd, slug + ".webp")
        if not os.path.exists(pdf):
            subprocess.run(["gs", "-q", "-dNOPAUSE", "-dBATCH", "-dSAFER", "-sDEVICE=pdfwrite", "-dPDFSETTINGS=/ebook",
                            "-dCompatibilityLevel=1.5", f"-sOutputFile={pdf}", kaynak], check=True)
            if os.path.getsize(pdf) > os.path.getsize(kaynak):  # sıkıştırma büyüttüyse aslını koy
                subprocess.run(["cp", kaynak, pdf], check=True)
        if not os.path.exists(kapak):
            with tempfile.TemporaryDirectory() as t:
                subprocess.run(["pdftoppm", "-f", "1", "-l", "1", "-r", "90", "-png", kaynak, os.path.join(t, "k")], check=True)
                im = Image.open(os.path.join(t, os.listdir(t)[0])).convert("RGB")
                im.thumbnail((600, 600)); im.save(kapak, quality=80, method=6)
    print(marka, len(liste), "katalog")
