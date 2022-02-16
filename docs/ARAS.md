**ARAS KARGO SEVKİYAT ENTEGRASYONU**

**WEB SERVİS DÖKÜMANI**

**Tanım Bilgisi:**

` `Aras Kargo Kurumsal Müşterilerinin online olarak kargo bilgilerini gönderilebildiği web servistir. Servis geri dönüşü olarak onay ya da hata mesajı dönüşü sağlar.

**Servis Metot İsmi: SetOrder**

**Önemli Not :**

SetOrder metodu entegrasyonu aracılığı ile gönderilen veriler ile Aras Kargo şubesi tarafından irsaliye kaydı oluşturulduğu anda Kargo Takip Numarası oluşmaktadır.

Kargo takip numaralarının alınabildiği sorgulama servisi farklıdır. Eğer sorgulama servisi dokumanı elinizde bulunmuyorsa aşağıdaki adımları izleyerek dokumanı indirebilirsiniz.

<https://esasweb.araskargo.com.tr/>  adresine giriş yapınız.
Tanımlamalar-Entegrasyonlar sekmesine tıklayınız.

Açılan sayfada karşınıza gelen seçeneklerden “[XML Servisleri](javascript:__doPostBack\('ctl00$ContentPlaceHolder1$lblXmlServices',''\))”  linkine tıklayınız.
Açılan yeni sayfa ile servis dokümanını indirebilir ve servise üye olabilirsiniz.

**Sevkiyat Entegrasyonu için Servis Linkleri:**

**Test Link:**

[**https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx**](https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx)

**Test Ortamı İçin;**

**UserName**: neodyum

**Password**: nd2580

Yukarıdaki kullanıcı bilgileri ile sadece aşağıdaki metotlardan yararlanabilirsiniz.

- SetOrder
- [GetOrder](http://customerws.araskargo.com.tr/arascargoservice.asmx?op=GetOrder)
- [GetOrderWithIntegrationCode](http://customerws.araskargo.com.tr/arascargoservice.asmx?op=GetOrderWithIntegrationCode)
- [CancelDispatch](http://customerws.araskargo.com.tr/arascargoservice.asmx?op=CancelDispatch)

**Canlı Ortam Link:**

[**https://customerws.araskargo.com.tr/arascargoservice.asmx**](https://customerws.araskargo.com.tr/arascargoservice.asmx)

- Test ortamında Aras Kargo ile karşılıklı testler yapıldıktan sonra canlı ortam bilgileri paylaşılmaktadır.
- Canlı ortam bilgileri Aras Kargo kurumsal müşterisinin yetkili kişisi ile paylaşılmaktadır.
- Eğer daha önceden entegrasyon sağlanmış herhangi bir entegratör bir firmadan hizmet alınıyorsa, test ortamında test yapılmadan canlı ortam bilgileri paylaşılmaktadır.


1. **SetOrder**


**Servis Kullanım Amacı :**

Kargonun alıcısına ait bilgilerinin gönderildiği metottur.

Kargoya ait **Varış Merkezi Belirleme** İşleminin yapılabilmesi ve Aras Kargo şubesi tarafından **irsaliye kaydı** oluşturma işleminin gerçekleşmesi için SetOrder metodu entegrasyonu kullanılmaktadır.

**Servis Parametreleri:**

Servise ait giriş parametreleri aşağıdaki tabloda belirtilmiştir.


|İSİM|TİPİ|AÇIKLAMA|ZORUNLU|
| :-: | :-: | :-: | :-: |
|UserName|String|Web Servis Kullanıcı Adınız|Evet|
|Password|String|Web Servis Kullanıcı Şifreniz|Evet|
|TradingWaybillNumber|String(16)|Sevk İrsaliye No.|Evet|
|InvoiceNumber|String(20)|Fatura No|Hayır|
|IntegrationCode|String(32)|Sipariş Kodu /Entegrasyon Kodu (mök )|Evet|
|ReceiverName|String(100)|Alıcı Adı|Evet|
|ReceiverAddress|String(250)|Alıcı Adresi (String şeklinde toplu adres bilgisi)|Evet|
|ReceiverPhone1|String(10)|Telefon-1|Evet|
|ReceiverPhone2|String(10)|Telefon-2|Hayır|
|ReceiverPhone3|String(10)|Gsm No|Hayır|
|ReceiverCityName|String(40)|İl-Şehir Adı|Evet|
|ReceiverTownName|String(16)|İlçe Adı|Evet|
|VolumetricWeight|Double(9, 3)|Ürün desi|Hayır|
|Weight|Double(9, 3)|Ürün kg|Hayır|
|PieceCount|Integer(2)|Sevkedilen Kargoya ait paket /koli Sayısı|<p>Hayır</p><p>\*Detay iletiliyorsa  Zorunlu</p>|
|SpecialField1|String(200)|Özel Alan - 1|Hayır|
|SpecialField2|String(100)|Özel Alan - 2|Hayır|
|SpecialField3|String(100)|Özel Alan - 3|Hayır|
|IsCod|String(1)|'Tahsilatlı Kargo' gönderisi (0=Hayır, 1=Evet)|Hayır<br>\*Tahsilatlı Kargo ise Zorunlu|
|CodAmount|Double(18, 2)|Tahsilatlı Teslimat ürünü tutar bilgisi|Hayır|
|CodCollectionType|String(1)|<p>Tahsilatlı teslimat ürünü ödeme tipi</p><p>(0=Nakit,1=Kredi Kartı)</p><p>Bu alana sadece 0 veya 1 gelmesi gerekmektedir.</p>|Hayır|
|CodBillingType|String(1)|<p>Tahsilatlı teslimat ürünü hizmet bedeli gönderi içerisinde mi? Ayrı mı faturalandırılacak? </p><p>Bu alanda  sabit "0" değeri yollanmalıdır.</p>|Hayır|
|Description|String(255)|Açıklama|Hayır|
|TaxNumber|String(15)|Vergi No|Hayır|
|TaxOffice|Long(8)|Vergi dairesi |Hayır|
|||||
|PrivilegeOrder|String(20)|Varış merkezi belirleme öncelik sırası|Hayır|
|CityCode|String(32)|İl Kodu|Hayır|
|TownCode|String(32)|İlçe Kodu|Hayır|
|ReceiverDistrictName|String(64)|Semt|Hayır|
|ReceiverQuarterName|String(64)|Mahalle|Hayır|
|ReceiverAvenueName|String(64)|Cadde|Hayır|
|ReceiverStreetName|String(64)|Sokak|Hayır|
|PayorTypeCode|Integer (1)|<p>Gönderinin ödemesini kimin yapacağını belirler. (1=Gönderici Öder, 2=Alıcı Öder)</p><p>Bu alana sadece 1 veya 2 gelmesi gerekmektedir.</p><p>Boş gelmesi durumunda sistem kendisi 1 değerini atamaktadır.</p>|Evet|
|IsWorldWide|Integer (1)|Yurtdışı gönderisi mi (0=Yurtiçi, 1=Yurtdışı)|Evet|
|PieceDetails|List|<p>Kargoya ait paket/koli detay bilgilerini içerir</p><p>PieceDetail nesnelerinden oluşan bir listedir.</p>|Hayır|
**PieceDetail**

Koli detaylarının gönderilmesi için kullanılacak nesnedir.

Aras Kargo şubesi kargoları sevk ederken BarcodeNumber alanında yer alan değer ile işlem yapmaktadır.

**PieceDetail** nesnesinin parametreleri aşağıdaki tabloda belirtilmiştir.

|VolumetricWeight|String(6)|Kargonun ilgili parçasının hacim bilgisidir|Hayır|
| :- | :- | :- | :- |
|Weight|String(6)|Kargonun ilgili parçasının kg bilgisidir|Hayır|
|BarcodeNumber|String(64)|<p>Kargonun ilgili parçasının barkod numarasıdır</p><p>Aras Kargo şubesinde bu koda göre işlem yapılır. Her parça barkod numarası barkodu ayrı ayrı okutulur.</p>|Evet|
|ProductNumber|String(32)|İlgili Parçanın ürün kodudur|Hayır|
|Description|String(64)|Kargoya ait açıklama bilgisidir|Hayır|





Servise ait geri dönüş kodları ve açıklamaları aşağıdaki tabloda belirtilmiştir.


|0|Başarılı|
| :- | :- |
|935|ReceiverPhone1 (Telefon 1) alanı sadece sayılardan oluşmalıdır.|
|936|Güncelleme hatası|
|937|Sipariş Numarasını Girmeniz Gerekmektedir|
|938|Alıcı Adresini Girmeniz Gerekmektedir|
|939|Alıcı Adını Girmeniz Gerekmektedir|
|940|Şehir Adını Girmeniz Gerekmektedir|
|941|İlçe Adını Girmeniz Gerekmektedir|
|942|Alıcı Ödemeli Tahsilatlı Kargo Gönderisi Yapılamamaktadır|
|1000|Kullanıcı Adı ve Şifreniz Yanlıştır|
|1001|Entegrasyon bilgileriniz güncellenirken bir hata oluşmuştur. Müşteri Temsilcinizle görüşünüz|
|1002|Aras Şube Bilginiz Tanımlı Değildir. Müşteri Temsilcinizle görüşünüz|
|60020|…….Sipariş Numaralı gönderinin irsaliyesi kesildiği için bilgilerini güncelleyemezsiniz|
|60021|En az bir adet sipariş bilgisi göndermeniz gerekmektedir|
|70018|ReceiverAddress alanı en fazla 250 karakter olabilir|
|70019|InvoiceKey alanı en fazla 20 karakter olabilir|
|70020|Toplam parça sayısı ile gönderilen parça sayısı eşit değil|
|70021|Toplam parça sayısını göndermeniz gerekmektedir|
|70022|Sipariş Numaralı gönderinin parça bilgilerinde barcode bilgisi eksik|
|70023|Sipariş Numaralı gönderinin volume bilgisi eksik|
|70024|Sipariş Numaralı gönderinin Kg bilgisi eksik|
|70025|Bir dosya gönderisi bir parçadan oluşmalıdır|
|70026|Volume değeri decimal bir alan olmalıdır|
|70027|Bu barkod daha önce gönderilmiş|
|70028|Kg değeri decimal bir alan olmalıdır|
|70029|irsaliyesi kesilmiş gönderinin bilgilerini güncelleyemezsiniz!|
|70030|Parçaların barkod numaraları aynı olamaz|
|70031|…….  numaralı kargo işleme tabii tutulmuştur!|
|70032|Ödeme Tipi (PayorTypeCode) sadece  1 veya 2  olabilir. Farklı bir değer göndermemeniz gerekmektedir.|
|70033|Tahsilat Tipi (CodCollectionType)   sadece 0 veya 1 olabilir.  Farklı bir değer göndermemeniz gerekmektedir.|
|70034|(IsCod) sadece 0 veya 1 olabilir.  Farklı bir değer göndermemeniz gerekmektedir.|
|70035|Bir siparişe ait bilgileri en fazla 20 kez gönderebilirsiniz. Bu siparişe ait yeni bir bilgi gönderemezsiniz.|

**Örnek Kod;**



`       `Service arasCargoService = new Service();

`            `Order[] orderInfos = new Order[1];

`            `Order orderInfo = new Order();

`            `orderInfo.UserName = “neodyum”;

`            `orderInfo.Password = “nd2580”;

`            `orderInfo.IntegrationCode = “665544333245676”;

`            `orderInfo.TradingWaybillNumber = “C164436”;

`            `orderInfo.InvoiceNumber = “FC164436”;

`            `orderInfo.ReceiverName = "Test";

`            `orderInfo.ReceiverAddress = "Rüzgarlıbahçe Mahallesi Yavuzsultanselim Caddesi No:2 Aras Plaza Kavacık/İstanbul";

`            `orderInfo.ReceiverPhone1 = "02165385562";

`            `orderInfo.ReceiverCityName = "İSTANBUL";

`            `orderInfo.ReceiverTownName = "BEYKOZ";

`            `orderInfo.PieceCount = "2";

`            `orderInfos[0] = orderInfo;

`            `orderInfo.PieceDetails = new PieceDetail[2];

`            `PieceDetail pieceDetail = new PieceDetail();

`            `pieceDetail.BarcodeNumber = “34567890”;

`            `pieceDetail.ProductNumber =””;

`            `pieceDetail.Description = "Test";

`            `pieceDetail.Weight = “1”

`            `pieceDetail.VolumetricWeight = “1”

`            `orderInfo.PieceDetails[0] = pieceDetail;

`            `PieceDetail pieceDetail2= new PieceDetail();

`            `pieceDetail2.BarcodeNumber = “234567887654323456”;

`            `pieceDetail2.ProductNumber = “”;

`            `pieceDetail2.Description = "Test";

`            `pieceDetail2.Weight = “1” ();

`            `pieceDetail2.VolumetricWeight = “1”;

`            `orderInfo.PieceDetails[1] = pieceDetail2;

`            `arasCargoService.Timeout = 999999999;

`            `OrderResultInfo[] dispatchResultInfoArray = arasCargoService.SetOrder(orderInfos, orderInfo.UserName, orderInfo.Password);












**Örnek XML;**


<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">

`   `<soap:Header/>

`   `<soap:Body>

`      `<SetOrder xmlns="http://tempuri.org/">

`         `<orderInfo>

`            `<Order>

`               `<UserName>neodyum</UserName>

`               `<Password>nd2580</Password>

`               `<TradingWaybillNumber>9423012</TradingWaybillNumber>

`               `<InvoiceNumber>6902001888</InvoiceNumber>

`               `<ReceiverName>NEDİM DEMİRCİ</ReceiverName>

`               `<ReceiverAddress>xxxxx CAD. yyyyy SOK. NO:7</ReceiverAddress>

`               `<ReceiverPhone1>02121111111</ReceiverPhone1>

`               `<ReceiverCityName>İSTANBUL</ReceiverCityName>

`               `<ReceiverTownName>GAZİOSMANPAŞA</ReceiverTownName>

`               `<VolumetricWeight>1</VolumetricWeight>

`               `<PieceCount>1</PieceCount>

`               `<IntegrationCode>6154197713</IntegrationCode>

`               `<PayorTypeCode>1</PayorTypeCode>

`               `<PieceDetails>

`                  `<PieceDetail>

`                     `<VolumetricWeight>3</VolumetricWeight>

`                     `<Weight>2</Weight>

`                     `<BarcodeNumber>79792027121</BarcodeNumber>

`                     `<ProductNumber />

`                     `<Description />

`                  `</PieceDetail>

`               `</PieceDetails>

`               `<SenderAccountAddressId />

`            `</Order>

`         `</orderInfo>

`         `<userName>neodyum</userName>

`         `<password>nd2580</password>

`      `</SetOrder>

`   `</soap:Body>

</soap:Envelope>

**Service-GetOrder**

**Tanım Bilgisi**

SetOrder metodu ile gönderilen dataların kontrolü için GetOrder metodu kullanılmaktadır.

Tarih ve Entegrasyon kodu ile sorgulama yapılabilmektedir.

**Test Ortamı İçin;**

**Tarih Bazlı Sorgulama;**

**Metot İsmi: GetOrder**

<https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx?op=GetOrder>

Tarih Formatı Örneği: 01.02.2015

**Entegrasyon Kodu İle Sorgulama;**

**Metot İsmi:** **GetOrderWithIntegrationCode**

<https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx?op=GetOrderWithIntegrationCode>

**Canlı Ortamı İçin;**

**Metot İsmi: GetOrder**

Tarih Bazlı Sorgulama;

<https://customerws.araskargo.com.tr/arascargoservice.asmx?op=GetOrder>

Tarih Formatı Örneği: 01.02.2015

**Entegrasyon Kodu İle Sorgulama;**

**Metot İsmi:** **GetOrderWithIntegrationCode**

<https://customerws.araskargo.com.tr/arascargoservice.asmx?op=GetOrderWithIntegrationCode>

**Service-CancelDispatch**

**Tanım Bilgisi**

CancelDispatch Metodu, SetOrder Metodu ile gönderilen dataların silinmesi için kullanılır.

Test Linki:

<https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx?op=CancelDispatch>

Live Link:

<https://customerws.araskargo.com.tr/arascargoservice.asmx?op=CancelDispatch>

Metoda ait geri dönüş kodları ve açıklamaları aşağıdaki tabloda belirtilmiştir.


|999|İrsaliyesi Kesilmiş Sipariş İptal Edilemez.|
| :- | :- |
|936|Hata oluştu|
|0|Başarılı|
|1|orderCode + " Başarılı bir şekilde silindi."|
|-1|Kayıt bulunamadı|
|-2|Kullanıcı adı ve Şifreniz hatalıdır.|





