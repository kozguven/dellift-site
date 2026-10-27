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
URUNLER = []

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
