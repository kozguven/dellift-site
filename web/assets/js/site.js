(function(){
  // mobil menü
  var h=document.querySelector('.hamburger'),g=document.getElementById('gezinti');
  if(h&&g){h.addEventListener('click',function(){var a=g.classList.toggle('acik');h.setAttribute('aria-expanded',a);document.body.style.overflow=a?'hidden':'';});}

  // hero slayt
  var sl=[].slice.call(document.querySelectorAll('.slayt')),nk=[].slice.call(document.querySelectorAll('.noktalar button')),i=0,t;
  function git(n){if(!sl.length)return;sl[i].classList.remove('aktif');nk[i]&&nk[i].classList.remove('aktif');i=(n+sl.length)%sl.length;sl[i].classList.add('aktif');nk[i]&&nk[i].classList.add('aktif');bas();}
  function bas(){clearInterval(t);if(!window.matchMedia('(prefers-reduced-motion: reduce)').matches)t=setInterval(function(){git(i+1)},7000);}
  if(sl.length>1){
    var s=document.querySelector('.hero-ok.sol'),r=document.querySelector('.hero-ok.sag');
    s&&s.addEventListener('click',function(){git(i-1)});r&&r.addEventListener('click',function(){git(i+1)});
    nk.forEach(function(b,k){b.addEventListener('click',function(){git(k)})});
    bas();
  }

  // form: ?konu= ön seçimi ve gönderim sonucu
  var q=new URLSearchParams(location.search),u=document.getElementById('form-uyari');
  if(u&&q.get('durum')){
    var ok=q.get('durum')==='ok';
    u.innerHTML='<div class="uyari '+(ok?'ok':'hata')+'">'+(ok?'Talebiniz alındı. En kısa sürede size dönüş yapacağız.':'Mesajınız gönderilemedi. Lütfen zorunlu alanları kontrol edin ya da bizi telefonla arayın.')+'</div>';
    var f=document.getElementById('teklif');f&&f.scrollIntoView({block:'start'});
  }
  var k=q.get('konu'),sel=document.getElementById('konu');
  if(k&&sel){[].forEach.call(sel.options,function(o){if(o.text.toLowerCase().indexOf(k.toLowerCase())>-1)sel.value=o.value;});}

  // video yalnız görünürken oynasın
  var v=document.querySelector('.video-kutu video');
  if(v&&'IntersectionObserver'in window){new IntersectionObserver(function(es){es.forEach(function(e){e.isIntersecting?v.play().catch(function(){}):v.pause();});},{threshold:.3}).observe(v);}

  // ürün galerisi: küçük görsele tıklayınca büyüğü değişsin
  var gb=document.getElementById('galeri-buyuk');
  if(gb){[].forEach.call(document.querySelectorAll('.galeri-kucuk button'),function(b){b.addEventListener('click',function(){
    var src=b.querySelector('img').getAttribute('src').replace('-800.webp','.webp');gb.src=src;gb.parentNode.href=src;
    [].forEach.call(document.querySelectorAll('.galeri-kucuk button'),function(x){x.classList.toggle('aktif',x===b)});});});}

  // katalog sayfası: üretici PDF'ini sayfa içinde çiz (pdf.js); olmazsa indirme bağlantısı göster
  var pg=document.getElementById('pdf-gorunum');
  if(pg){
    var url=pg.querySelector('.pdf-kaynak').href,durum=pg.querySelector('.pdf-durum'),js=pg.getAttribute('data-js');
    var hata=function(){durum.innerHTML='Katalog burada görüntülenemedi. <a href="'+url+'" target="_blank" rel="noopener">PDF olarak açın</a>.';};
    if(!window.pdfjsLib){hata();}else{
      pdfjsLib.GlobalWorkerOptions.workerSrc=js+'build/pdf.worker.min.js';
      pdfjsLib.getDocument({url:url,cMapUrl:js+'cmaps/',cMapPacked:true,standardFontDataUrl:js+'standard_fonts/'}).promise.then(function(doc){
        durum.style.display='none';
        var sira=Promise.resolve(),dpr=Math.min(window.devicePixelRatio||1,2);
        for(var n=1;n<=doc.numPages;n++)(function(n){sira=sira.then(function(){return doc.getPage(n).then(function(p){
          var gen=pg.clientWidth||800,v=p.getViewport({scale:gen/p.getViewport({scale:1}).width*dpr});
          var c=document.createElement('canvas');c.width=Math.floor(v.width);c.height=Math.floor(v.height);
          c.setAttribute('aria-label','Katalog sayfa '+n);pg.appendChild(c);
          return p.render({canvasContext:c.getContext('2d'),viewport:v}).promise;});});})(n);
        return sira;
      }).catch(hata);
    }
  }
})();
