from PIL import Image, ImageOps
import sys
K='/Users/keremozguven/Projeler/ekrem-forklift-site/kaynak/pptx/ppt/media/'
O='/Users/keremozguven/Projeler/ekrem-forklift-site/site/assets/img/'
im=Image.open(K+'image3.png').convert('RGBA')
# beyaz zemini şeffaf yap (yumuşak kenar)
px=im.load(); w,h=im.size
for y in range(h):
    for x in range(w):
        r,g,b,a=px[x,y]
        m=min(r,g,b)
        if m>235: px[x,y]=(r,g,b,0)
        elif m>200: px[x,y]=(r,g,b,int(255*(235-m)/35))
bb=im.getbbox(); im=im.crop(bb)
im.thumbnail((900,900)); im.save(O+'logo.png',optimize=True)
# koyu zemin için: siyah kısımları beyaz yap
wv=im.copy(); p=wv.load()
for y in range(wv.size[1]):
    for x in range(wv.size[0]):
        r,g,b,a=p[x,y]
        if a and r<90 and g<90 and b<90: p[x,y]=(255,255,255,a)
wv.save(O+'logo-beyaz.png',optimize=True)
fav=im.crop((0,0,int(im.size[1]*1.15),im.size[1])) if False else None
