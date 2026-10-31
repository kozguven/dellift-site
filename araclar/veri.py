# DELLIFT sitesi içerik verisi — metin ve ürün güncellemeleri buradan yapılır, sonra build.py çalıştırılır.

FIRMA = {
    "ad": "DELLIFT",
    "alt": "Forklift ve İstif Makinaları",
    "unvan": "DELLIFT Forklift ve İstif Makinaları",
    "tel": "0534 821 70 50",
    "tel_uri": "+905348217050",
    "wa": "905348217050",
    "eposta": "info@dellift.com.tr",
    "adres": "Battalgazi Mah. Egemen Sk. No:44, Sultanbeyli / İstanbul",
    "adres_kisa": "Egemen Sk. No:44, Sultanbeyli / İstanbul",
    "harita_q": "Egemen Sokak No:44 Battalgazi Sultanbeyli İstanbul",
    "instagram": "https://www.instagram.com/dellift.com.tr/",
    "site": "https://dellift.com.tr",
    "kampanya": "6 ay ödemesiz, 60 aya varan ödeme seçenekleriyle hemen forklift sahibi olun",
}

MARKALAR = [
    {"slug": "nigmer", "ad": "Niğmer", "kat": ["dizel-forklift", "akulu-forklift"],
     "ozet": "Sahada sık gördüğünüz turkuaz gövdeli Niğmer forkliftler; dizel ve akülü seçenekleriyle depo, üretim ve dış saha işleri için."},
    {"slug": "ep", "ad": "EP", "kat": ["dizel-forklift", "akulu-forklift"],
     "ozet": "EP'nin dizel ve akülü forklift modelleri; yoğun vardiyalı depo ve üretim hatlarında verimli çalışma için."},
    {"slug": "litef", "ad": "Litef", "kat": ["dizel-forklift", "akulu-forklift", "istif-makinalari"],
     "ozet": "Litef forklift ve istif makinaları; dar koridorlu depolardan açık sahaya kadar farklı ihtiyaçlara uygun seçenekler."},
    {"slug": "dolphin", "ad": "Dolphin", "kat": ["dizel-forklift", "akulu-forklift", "istif-makinalari"],
     "ozet": "Dolphin dizel, akülü forklift ve istif makinaları; günlük operasyonda pratik ve ekonomik çözümler."},
    {"slug": "jac", "ad": "JAC", "kat": ["dizel-forklift", "akulu-forklift", "istif-makinalari"],
     "ozet": "JAC forklift ve istif makinaları; dizel, lityum akülü ve istif modelleriyle geniş kapasite aralığı."},
    {"slug": "lonking", "ad": "Lonking", "kat": ["dizel-forklift", "akulu-forklift", "istif-makinalari"],
     "ozet": "Lonking forklift ve istif makinaları; ağır yük ve dış saha işlerinde dayanıklı performans."},
]

KATEGORILER = [
    {"slug": "dizel-forklift", "ad": "Dizel Forkliftler", "kisa": "Dizel", "ust": "forklift",
     "ozet": "Açık saha, şantiye ve ağır yük işleri için güçlü, uzun çalışma süreli dizel forkliftler.",
     "metin": "Dizel forkliftler, yakıt ikmaliyle kesintisiz çalışabilmeleri ve yüksek çekiş güçleri sayesinde açık sahada, "
              "inşaat malzemesi ve ağır yük taşımacılığında ilk tercihtir. Havalı veya dolgu lastik, farklı direk yükseklikleri ve "
              "ataşman seçenekleriyle işinize uygun makineyi birlikte belirleriz."},
    {"slug": "akulu-forklift", "ad": "Akülü Forkliftler", "kisa": "Akülü", "ust": "forklift",
     "ozet": "Kapalı alanda sessiz, egzozsuz ve düşük işletme maliyetli elektrikli forkliftler.",
     "metin": "Akülü (elektrikli) forkliftler egzoz gazı çıkarmadığı ve sessiz çalıştığı için gıda, ilaç, tekstil ve kapalı depo "
              "işletmelerinde öne çıkar. Lityum veya jel akü, 3 ve 4 teker seçenekleriyle vardiya düzeninize uygun modeli öneririz."},
    {"slug": "ikinci-el-forklift", "ad": "2. El Forkliftler", "kisa": "2. El", "ust": "forklift",
     "ozet": "Bakımı yapılmış, kontrolden geçmiş, bütçe dostu ikinci el forkliftler.",
     "metin": "İkinci el forkliftlerimiz teslimden önce servis ekibimizin kontrolünden geçer; motor, hidrolik, direk ve fren "
              "sistemleri incelenir. Güncel stok, çalışma saati ve fiyat bilgisi için bizi arayın ya da WhatsApp'tan yazın."},
    {"slug": "istif-makinalari", "ad": "İstif Makinaları", "kisa": "İstif", "ust": None,
     "ozet": "Raf sistemlerinde paletleri güvenle yükseğe istiflemek için akülü istif makinaları.",
     "metin": "İstif makinaları, dar koridorlu depolarda paletleri raflara yerleştirmek için idealdir. Yaya kumandalı ve "
              "platformlu modellerde farklı kaldırma yükseklikleri ve kapasiteler sunuyoruz."},
    {"slug": "transpaletler", "ad": "Transpaletler", "kisa": "Akülü transpalet", "ust": None,
     "ozet": "Yükleme rampası ve depo içi taşıma için akülü transpaletler.",
     "metin": "Akülü transpaletler, kamyon yükleme-boşaltma ve depo içi palet taşımada operatörün yükünü hafifletir, işi "
              "hızlandırır. Lityum akülü, kompakt modellerle dar alanlarda rahat manevra sağlar."},
    {"slug": "manuel-transpaletler", "ad": "Manuel Transpaletler", "kisa": "Manuel transpalet", "ust": None,
     "ozet": "Her işletmenin ihtiyacı olan dayanıklı, bakım gerektirmeyen el transpaletleri.",
     "metin": "Manuel transpaletler, kısa mesafeli palet taşımada en pratik ve ekonomik çözümdür. Farklı çatal uzunlukları "
              "ve taşıma kapasiteleriyle stoktan teslim edilir."},
]

# Ürün kartları: Ekrem Bey görsel + teknik bilgileri gönderdikçe buraya eklenir.
# Örnek: {"kat": "dizel-forklift", "marka": "jac", "ad": "JAC 3 Ton Dizel Forklift – Havalı Lastik",
#         "model": "CPCD30", "gorsel": "assets/img/urun/jac-cpcd30.webp",
#         "ozellik": [("Kapasite", "3.000 kg"), ("Motor", "Xinchai Euro 5"), ("Asansör", "4,50 m")],
#         "aciklama": "Dış saha, depo ve ağır yük taşıma operasyonları için ..."}
URUNLER = [
    {"kat": "dizel-forklift", "marka": "nigmer", "ad": "Niğmer 3 Ton Dizel Forklift", "model": "NMR 30D",
     "gorsel": "assets/img/urun/nigmer-nmr/01-800.webp", "detay": "urun/nigmer-nmr-dizel-forklift",
     "ozellik": [("Kapasite", "3.000 kg"), ("Kaldırma", "4.700 mm"), ("Motor", "Xinchai Euro V · 44,8 kW"), ("Garanti", "2 yıl / 2.000 saat")],
     "aciklama": "Yerli üretim, Euro V dizel motorlu, havalı lastikli 3 ton forklift; depo, saha ve yükleme işleri için."},
    {"kat": "dizel-forklift", "marka": "nigmer", "ad": "Niğmer 3,5 Ton Dizel Forklift", "model": "NMR 35D",
     "gorsel": "assets/img/urun/nigmer-nmr/02-800.webp", "detay": "urun/nigmer-nmr-dizel-forklift",
     "ozellik": [("Kapasite", "3.500 kg"), ("Kaldırma", "4.700 mm"), ("Motor", "Xinchai Euro V · 44,8 kW"), ("Garanti", "2 yıl / 2.000 saat")],
     "aciklama": "Aynı gövdede 3,5 ton kapasite; daha ağır paletler ve yoğun vardiyalar için güçlü dizel forklift."},
]

# Seri detay sayfaları: /urun/<slug>. Teknik tablo üreticinin kataloğundan birebir (Niğmer NMR katalogu, 31.10.2026).
SERILER = [
    {"slug": "nigmer-nmr-dizel-forklift", "marka": "nigmer", "kat": "dizel-forklift",
     "ad": "Niğmer NMR Serisi Dizel Forklift", "kisa": "3 ve 3,5 ton · Euro V dizel · yerli üretim",
     "gorsel_klasor": "assets/img/urun/nigmer-nmr", "gorsel_adet": 14,
     "giris": "Niğmer NMR serisi, Niğde'de üretilen, Euro V emisyon standardında dizel forkliftlerdir. 3 ton (NMR 30D) ve "
              "3,5 ton (NMR 35D) kapasiteli iki model, 4,70 m kaldırma yüksekliği, havalı lastik ve 60 HP Xinchai dizel "
              "motorla depo, şantiye ve açık saha işlerinde uzun ve kesintisiz çalışma sunar. Üretici garantisi 2 yıl / 2.000 saattir.",
     "one_cikan": [
         ("60 HP Euro V dizel motor", "Xinchai 3E22YG51; 44,8 kW / 2.400 dev/dk, 210 Nm tork."),
         ("Otomatik rejenerasyon", "Partikül filtresi için otomatik rejenerasyon imkânı."),
         ("15 mm ST37 tek parça şase", "Güçlendirilmiş monoblok şase ile uzun ömür."),
         ("Emniyet kemeri ve operatör algılama", "Operatör koltukta değilse ve kemer takılı değilse uyarı."),
         ("Ataşmanlar için 4. yol seçeneği", "Yan kaydırma, çatal pozisyoner gibi ataşmanlara hazır."),
         ("32 cc yağ pompası", "Güçlü hidrolik ile hızlı kaldırma: yüklü 420 mm/sn'ye kadar."),
     ],
     "modeller": ["NMR 30D", "NMR 35D"],
     "teknik": [
         ("Genel", None),
         ("Yük kapasitesi", "kg", "3.000", "3.500"),
         ("Yük merkezi", "mm", "500", "500"),
         ("Azami kaldırma yüksekliği", "mm", "4.700", "4.700"),
         ("Emisyon standardı", "", "EURO V", "EURO V"),
         ("Motor tipi", "", "Dizel", "Dizel"),
         ("Ölçüler", None),
         ("Toplam yükseklik (üst koruma)", "mm", "2.140", "2.140"),
         ("Toplam uzunluk (çatallı / çatalsız)", "mm", "3.814 / 2.744", "3.820 / 2.750"),
         ("Tekerlek tabanı", "mm", "1.750", "1.750"),
         ("Toplam genişlik", "mm", "1.225", "1.225"),
         ("Min. dönüş yarıçapı (dış)", "mm", "2.400", "2.400"),
         ("Min. dönüş yarıçapı (iç)", "mm", "80", "80"),
         ("Direk eğim açısı (ileri / geri)", "°", "6 / 6 – 6 / 12", "6 / 6 – 6 / 12"),
         ("Çatal boyutu", "mm", "1220 × 125 × 45", "1220 × 125 × 45"),
         ("Ağırlık", None),
         ("Toplam ağırlık", "kg", "4.660", "4.900"),
         ("Ağırlık dağılımı, yüklü (ön / arka)", "kg", "7.280 / 820", "8.065 / 870"),
         ("Ağırlık dağılımı, yüksüz (ön / arka)", "kg", "1.790 / 2.810", "1.975 / 3.108"),
         ("Lastikler ve frenler", None),
         ("Lastik tipi (ön / arka)", "", "Havalı", "Havalı"),
         ("Lastik boyutu (ön / arka)", "", "28x9-15-14PR / 6.5-10-12PR", "28x9-15-14PR / 6.5-10-12PR"),
         ("Servis freni", "", "Hidrolik pedal", "Hidrolik pedal"),
         ("El freni", "", "Mekanik el kolu", "Mekanik el kolu"),
         ("Performans", None),
         ("Azami çekme gücü (yüklü / yüksüz)", "kN", "22 / 13,7", "23 / 14"),
         ("Azami tırmanma (yüklü / yüksüz)", "%", "32 / 22", "30 / 21"),
         ("Yürüyüş hızı (yüklü / yüksüz)", "km/sa", "19 / 19", "19 / 19"),
         ("Kaldırma hızı (yüklü / yüksüz)", "mm/sn", "420 / 450", "370 / 390"),
         ("İndirme hızı (yüklü / yüksüz)", "mm/sn", "450 / 460", "450 / 460"),
         ("Motor ve akü", None),
         ("Motor üreticisi / modeli", "", "Xinchai 3E22YG51", "Xinchai 3E22YG51"),
         ("Anma gücü / devri", "kW/dev/dk", "44,8 / 2.400", "44,8 / 2.400"),
         ("Azami tork / devri", "Nm/dev/dk", "210 / 1.600–1.800", "210 / 1.600–1.800"),
         ("Silindir sayısı", "", "3", "3"),
         ("Hacim", "litre", "2,23", "2,23"),
         ("Akü (voltaj / kapasite)", "V/Ah", "12 / 95", "12 / 95"),
     ],
     "katalog": "assets/dosya/nigmer-nmr-dizel-forklift-katalog.pdf"},
]

SSS = [
    ("Forklift alırken nelere dikkat etmeliyim?",
     "Önce işin kendisine bakın: taşınacak en ağır yük, yükün ağırlık merkezi, kaldırma yüksekliği, çalışma zemini (beton, "
     "toprak, rampa) ve makinenin kapalı mı açık alanda çalışacağı. Bunlara günlük çalışma saati, servis ve yedek parça "
     "erişimi eklendiğinde doğru model netleşir. Sahanızı görüp size uygun makineyi birlikte seçebiliriz."),
    ("Sıfır forklift mi, ikinci el mi?",
     "Yoğun ve uzun vadeli kullanımda sıfır forklift; garanti, düşük arıza riski ve finansman kolaylığıyla öne çıkar. "
     "Kullanım saati az olan ya da bütçesi sınırlı işletmeler için bakımı yapılmış ikinci el forklift mantıklı bir başlangıçtır. "
     "Kısa süreli ya da dönemsel ihtiyaçlarda kiralama da değerlendirilebilir."),
    ("Elektrikli forklift mi, dizel mi?",
     "Kapalı alanda, gıda ve hijyen gerektiren ortamlarda, düz zeminde elektrikli (akülü) forklift; açık sahada, engebeli "
     "zeminde ve ağır yüklerde dizel forklift daha uygundur. Akülü modellerin enerji ve bakım maliyeti genellikle daha "
     "düşüktür; dizel modeller ise uzun ve kesintisiz çalışmada avantaj sağlar."),
    ("Forklift fiyatları nasıl belirlenir?",
     "Fiyatı; marka ve model, taşıma kapasitesi, direk (asansör) yüksekliği, motor ya da akü tipi, lastik tipi, "
     "ataşmanlar ve döviz kuru belirler. İkinci elde buna çalışma saati ve makinenin genel durumu eklenir. "
     "İhtiyacınızı paylaşın, aynı gün net fiyat teklifi gönderelim."),
    ("Finansman seçenekleri nelerdir?",
     "Peşin alımlarda avantajlı fiyat sunuyoruz. Finansman tercih eden işletmeler için anlaşmalı leasing şirketleri üzerinden "
     "6 ay ödemesiz, 60 aya varan vade seçenekleri sunulabilir. Ayrıntılar Finansal Çözümler sayfamızda."),
    ("Kimlerle çalışıyoruz?",
     "Depo ve lojistik firmaları, üretim tesisleri, inşaat ve yapı malzemesi işletmeleri, gıda ve perakende sektörü başta "
     "olmak üzere forklifte ihtiyaç duyan her ölçekten işletmeye hizmet veriyoruz. Ürün tarafında Niğmer, EP, Litef, "
     "Dolphin, JAC ve Lonking markalarıyla çalışıyoruz."),
    ("Limited ve şahıs şirketi farkı nelerdir?",
     "Forklift alımında iki şirket türü de satın alma ve leasing yapabilir. Fark çoğunlukla finansman başvurusunda istenen "
     "belgelerde ve vergi tarafındadır: şahıs şirketinde işletme sahibinin, limited şirkette şirketin mali tabloları "
     "değerlendirilir. Vergi ve amortisman açısından en doğru yol için mali müşavirinizle görüşmenizi öneririz; leasing "
     "başvurusu için gereken belgeleri biz iletiriz."),
    ("Forklift bakımları ne sıklıkla yapılmalı?",
     "Günlük kontroller (yağ, su, lastik, fren, zincir, akü) operatör tarafından her vardiya başında yapılmalıdır. Periyodik "
     "bakım, üretici tavsiyesine göre genellikle her 250–500 çalışma saatinde bir yapılır. Yoğun kullanımda bu aralık kısalır. "
     "Yasal periyodik kontroller de yılda en az bir kez yaptırılmalıdır."),
    ("Forklift için özel bir ehliyet gerekiyor mu?",
     "Forklift kullanacak kişinin forklift operatörlük belgesine sahip olması gerekir. Forklift trafiğe açık yolda "
     "kullanılacaksa ayrıca uygun sınıf sürücü belgesi aranır. Operatör eğitimi ve belge süreci hakkında da sizi "
     "yönlendirebiliriz."),
    ("Forklift alırken hangi kapasitede seçmeliyim?",
     "Taşıyacağınız en ağır yükü ve kaldırma yüksekliğini esas alın; kapasite yükseldikçe ve direk uzadıkça makinenin "
     "taşıyabileceği yük azalır. Bu yüzden en ağır yükünüzün biraz üzerinde, payı olan bir kapasite seçmek gerekir. "
     "Depo içi işlerde 1,5–2,5 ton, genel kullanımda 3–3,5 ton, ağır işlerde 5 ton ve üzeri modeller tercih edilir."),
]
