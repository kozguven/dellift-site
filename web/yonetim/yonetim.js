(function(){
  // silme onayı
  document.addEventListener('click',function(e){var b=e.target.closest('[data-onay]');if(b&&!confirm(b.getAttribute('data-onay')))e.preventDefault();});

  // galeri: kaldır, öne al, sürükle-bırak sıralama
  document.querySelectorAll('.galeri').forEach(function(g){
    g.addEventListener('click',function(e){var li=e.target.closest('li');if(!li)return;
      if(e.target.classList.contains('g-sil'))li.remove();
      if(e.target.classList.contains('g-sol')&&li.previousElementSibling)g.insertBefore(li,li.previousElementSibling);});
    var tas=null;
    g.addEventListener('dragstart',function(e){tas=e.target.closest('li');});
    g.addEventListener('dragover',function(e){e.preventDefault();var li=e.target.closest('li');if(tas&&li&&li!==tas){var r=li.getBoundingClientRect();g.insertBefore(tas,(e.clientX-r.left)>r.width/2?li.nextSibling:li);}});
  });

  // sayfa metni düzenleyici
  document.querySelectorAll('.editor').forEach(function(ed){
    var alan=ed.querySelector('.alan-yaz'),ta=ed.parentNode.querySelector('textarea[name="'+ed.getAttribute('data-hedef')+'"]'),html=false;
    ed.querySelector('.arac').addEventListener('click',function(e){var b=e.target.closest('button');if(!b)return;e.preventDefault();
      var k=b.getAttribute('data-k');
      if(k==='html'){html=!html;if(html){ta.value=alan.innerHTML;ta.classList.remove('gizli');alan.style.display='none';}else{alan.innerHTML=ta.value;ta.classList.add('gizli');alan.style.display='';}return;}
      if(html)return;
      alan.focus();
      if(k==='createLink'){var u=prompt('Bağlantı adresi (ör. /iletisim veya https://…)');if(u)document.execCommand('createLink',false,u);return;}
      document.execCommand(k,false,b.getAttribute('data-d')||null);});
    ed.closest('form').addEventListener('submit',function(){if(!html)ta.value=alan.innerHTML;});
  });

  // katalog: PDF seçilince ilk sayfadan kapak görseli üret (pdf.js)
  var pdfIn=document.querySelector('input[type=file][data-kapak]');
  if(pdfIn){
    var gizli=pdfIn.parentNode.querySelector('input[name=kapak_veri]'),cdn='https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/';
    var not=document.createElement('small');pdfIn.parentNode.appendChild(not);
    var yukle=function(cb){if(window.pdfjsLib)return cb();var s=document.createElement('script');s.src=cdn+'build/pdf.min.js';s.onload=cb;s.onerror=function(){not.textContent='Kapak otomatik oluşturulamadı; isterseniz kapak görselini elle seçin.';};document.head.appendChild(s);};
    pdfIn.addEventListener('change',function(){
      gizli.value='';var f=pdfIn.files[0];if(!f)return;not.textContent='Kapak hazırlanıyor…';
      yukle(function(){pdfjsLib.GlobalWorkerOptions.workerSrc=cdn+'build/pdf.worker.min.js';
        f.arrayBuffer().then(function(b){return pdfjsLib.getDocument({data:b}).promise;}).then(function(d){return d.getPage(1);}).then(function(p){
          var v0=p.getViewport({scale:1}),v=p.getViewport({scale:900/v0.width}),c=document.createElement('canvas');c.width=v.width;c.height=v.height;
          return p.render({canvasContext:c.getContext('2d'),viewport:v}).promise.then(function(){gizli.value=c.toDataURL('image/jpeg',.85);
            not.innerHTML='';var im=new Image();im.src=gizli.value;im.style.cssText='max-width:180px;border:1px solid #e3e5e8;border-radius:6px;margin-top:8px;display:block';not.appendChild(document.createTextNode('Otomatik kapak:'));not.appendChild(im);});
        }).catch(function(){not.textContent='Kapak otomatik oluşturulamadı; isterseniz kapak görselini elle seçin.';});});
    });
  }

  // kaydedilmemiş değişiklik uyarısı
  var deg=false;document.querySelectorAll('form.duzenle').forEach(function(f){f.addEventListener('input',function(){deg=true;});f.addEventListener('submit',function(){deg=false;});});
  window.addEventListener('beforeunload',function(e){if(deg){e.preventDefault();e.returnValue='';}});
})();
