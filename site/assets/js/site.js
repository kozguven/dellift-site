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
})();
