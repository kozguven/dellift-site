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


  // satır editörü (özellikler, öne çıkanlar, modeller, teknik tablo)
  function numarala(ed){
    var ad=ed.getAttribute('data-ad');
    [].forEach.call(ed.querySelectorAll(':scope > table > tbody > tr, :scope > .tablo-kaydir > table > tbody > tr'),function(tr,r){
      [].forEach.call(tr.querySelectorAll('input'),function(inp){
        var c=inp.getAttribute('data-c');
        inp.name=ed.hasAttribute('data-liste')?ad+'['+r+']':ad+'['+r+']['+c+']';
      });
    });
  }
  function satirEkle(ed,sablon){
    var t=ed.querySelector(sablon||'template'),tb=ed.querySelector('tbody');
    var div=document.createElement('tbody');div.innerHTML=t.innerHTML.trim();
    var tr=div.firstElementChild;tb.appendChild(tr);
    if(ed.classList.contains('teknik-ed'))teknikSutun(ed);
    numarala(ed);var i=tr.querySelector('input:not([type=hidden])');i&&i.focus();
  }
  document.querySelectorAll('.satir-ed').forEach(function(ed){
    ed.addEventListener('click',function(e){
      var b=e.target.closest('button');if(!b)return;
      var tr=b.closest('tr'),tb=ed.querySelector('tbody');
      if(b.classList.contains('ed-ekle')){satirEkle(ed,ed.classList.contains('teknik-ed')?'template.t-veri':'template');}
      else if(b.classList.contains('ed-ekle-bas')){satirEkle(ed,'template.t-bas');}
      else if(b.classList.contains('ed-sil')&&tr){tr.remove();numarala(ed);}
      else if(b.classList.contains('ed-yukari')&&tr&&tr.previousElementSibling){tb.insertBefore(tr,tr.previousElementSibling);numarala(ed);}
      else if(b.classList.contains('ed-asagi')&&tr&&tr.nextElementSibling){tb.insertBefore(tr.nextElementSibling,tr);numarala(ed);}
      else return;
      var f=ed.closest('form');f&&f.dispatchEvent(new Event('input',{bubbles:true}));
    });
    // son satırda Enter → yeni satır
    ed.addEventListener('keydown',function(e){
      if(e.key!=='Enter'||e.target.tagName!=='INPUT')return;e.preventDefault();
      var tr=e.target.closest('tr');
      if(!tr.nextElementSibling)satirEkle(ed,ed.classList.contains('teknik-ed')?'template.t-veri':'template');
      else{var n=tr.nextElementSibling.querySelector('input:not([type=hidden])');n&&n.focus();}
    });
  });

  // teknik tablo: model sütunları "Model adları" listesine göre
  var modelEd=document.querySelector('.satir-ed[data-ad="f_modeller"]');
  function modeller(){if(!modelEd)return[];return [].map.call(modelEd.querySelectorAll('tbody input'),function(i){return i.value.trim();}).filter(Boolean);}
  function teknikSutun(ed){
    var m=modeller(),n=Math.max(1,m.length),ad=ed.getAttribute('data-ad');
    var thr=ed.querySelector('thead tr'),ths=thr.querySelectorAll('.model-th');
    for(var k=ths.length;k<n;k++){var th=document.createElement('th');th.className='model-th';thr.insertBefore(th,thr.lastElementChild);}
    for(var k2=ths.length-1;k2>=n;k2--){ths[k2].remove();}
    [].forEach.call(thr.querySelectorAll('.model-th'),function(th,k){th.textContent=m[k]||(k+1)+'. model';});
    [].forEach.call(ed.querySelectorAll('tbody tr, template.t-veri'),function(tr){
      var kok=tr.tagName==='TEMPLATE'?tr.content.firstElementChild:tr;
      if(!kok||kok.classList.contains('ara-bas')){if(kok){var td=kok.querySelector('td');td&&td.setAttribute('colspan',2+n);}return;}
      var tds=kok.querySelectorAll('.model-td');
      for(var k=tds.length;k<n;k++){var td=document.createElement('td');td.className='model-td';td.innerHTML='<input data-c="'+(2+k)+'">';kok.insertBefore(td,kok.lastElementChild);}
      for(var k3=tds.length-1;k3>=n;k3--){tds[k3].remove();}
    });
    [].forEach.call(ed.querySelectorAll('template.t-bas'),function(t){var td=t.content.querySelector('td');td&&td.setAttribute('colspan',2+n);});
    numarala(ed);
  }
  var tek=document.querySelector('.teknik-ed');
  if(tek&&modelEd){
    var zam;modelEd.addEventListener('input',function(){clearTimeout(zam);zam=setTimeout(function(){teknikSutun(tek);},400);});
    modelEd.addEventListener('click',function(e){if(e.target.closest('button'))setTimeout(function(){teknikSutun(tek);},0);});
  }

  // kaydedilmemiş değişiklik uyarısı
  var deg=false;document.querySelectorAll('form.duzenle').forEach(function(f){f.addEventListener('input',function(){deg=true;});f.addEventListener('submit',function(){deg=false;});});
  window.addEventListener('beforeunload',function(e){if(deg){e.preventDefault();e.returnValue='';}});
})();
