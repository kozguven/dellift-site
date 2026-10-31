#!/usr/bin/env python3
"""Ekrem Bey'in gönderdiği ürün fotoğraflarını siteye hazırlar: bayi logosu (alt şerit) ve ilan no (üst) kırpılır,
1600 + 800 px webp yazılır. Çalıştır: python3 araclar/urun_gorsel.py"""
import os
from PIL import Image

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
SETLER = {
    # hedef klasör: (kaynak klasör, seçilen kareler sırasıyla)
    "nigmer-nmr": ("kaynak/nigmer-dizel-2026-10-31/gorsel",
                   ["15", "04", "02", "10", "03", "06", "13", "20", "05", "21", "07", "08", "11", "12"]),
}

for hedef, (kaynak, kareler) in SETLER.items():
    o = os.path.join(KOK, "site/assets/img/urun", hedef)
    os.makedirs(o, exist_ok=True)
    for i, k in enumerate(kareler, 1):
        im = Image.open(os.path.join(KOK, kaynak, k + ".jpg")).convert("RGB")
        w, h = im.size
        im = im.crop((0, int(h * .025), w, int(h * .925)))
        im.save(os.path.join(o, f"{i:02d}.webp"), quality=80, method=6)
        im.thumbnail((800, 800))
        im.save(os.path.join(o, f"{i:02d}-800.webp"), quality=78, method=6)
    print(hedef, len(kareler), "görsel")
