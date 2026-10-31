#!/usr/bin/env python3
"""Ekrem Bey'in gönderdiği ürün fotoğraflarını siteye hazırlar: bayi logosu (alt şerit) ve ilan no (üst) kırpılır,
1600 + 800 px webp yazılır. Çalıştır: python3 araclar/urun_gorsel.py"""
import os
from PIL import Image

KOK = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
SETLER = {
    # hedef klasör: (kaynak klasör, seçilen kareler sırasıyla, kırpma oranı üst/alt — bayi logosu ve ilan no için)
    "nigmer-nmr": ("kaynak/nigmer-dizel-2026-10-31/gorsel",
                   ["15", "04", "02", "10", "03", "06", "13", "20", "05", "21", "07", "08", "11", "12"], (.025, .925)),
    "ep-cpcd": ("kaynak/ep-2026-10-31/gorsel",
                ["0914_AC032043FE4E816BB0F0A82BADFDB371", "0914_AC54215086CBAF7387BE9399EF2C9B0B",
                 "0914_ACB2CB975A78012BB0E18FC922CC1470", "0914_ACF4D73230FD91B3307978CA48851595"], (0, 1)),
    "ep-x5": ("kaynak/ep-2026-10-31/gorsel",
              ["0920_ACB1BE0EE14989E012BA25E06337F278", "0919_AC8B477A4BE938A67600CADBE35ECA15",
               "0919_ACB3DFCFA0E1EDB1CBF78E7C4CB88333", "0920_ACBCA21403DE761C47FBA96C95CA2343",
               "0919_AC2516BA229B2F6507D22E6D9B099589", "0920_AC04F4CB9D82971CE70FE171D4DB4BE4"], (0, 1)),
}

for hedef, (kaynak, kareler, (ust, alt)) in SETLER.items():
    o = os.path.join(KOK, "site/assets/img/urun", hedef)
    os.makedirs(o, exist_ok=True)
    for i, k in enumerate(kareler, 1):
        im = Image.open(os.path.join(KOK, kaynak, k + ".jpg")).convert("RGB")
        w, h = im.size
        im = im.crop((0, int(h * ust), w, int(h * alt)))
        im.save(os.path.join(o, f"{i:02d}.webp"), quality=80, method=6)
        im.thumbnail((800, 800))
        im.save(os.path.join(o, f"{i:02d}-800.webp"), quality=78, method=6)
    print(hedef, len(kareler), "görsel")
