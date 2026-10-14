(function(){
var nav=document.querySelector('.nav'),bg=document.querySelector('.burger');
bg.addEventListener('click',function(){var o=nav.classList.toggle('open');bg.setAttribute('aria-expanded',o)});
nav.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){nav.classList.remove('open');bg.setAttribute('aria-expanded',false)})});
// menu aktif sesuai section
var links={};nav.querySelectorAll('ul a[href^="#"]').forEach(function(a){links[a.getAttribute('href').slice(1)]=a});
document.addEventListener('keydown',function(e){if(e.key==='Escape'&&nav.classList.contains('open')){nav.classList.remove('open');bg.setAttribute('aria-expanded',false);bg.focus()}});
// tracking klik CTA ke dataLayer (GTM/GA) bila ada
document.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('a[href]');if(!a)return;var h=a.getAttribute('href'),t=h.indexOf('wa.me')>-1?'whatsapp_click':h.indexOf('ai-checkup')>-1?'ai_checkup_click':null;if(t){(window.dataLayer=window.dataLayer||[]).push({event:t,cta_text:a.textContent.trim(),cta_url:h})}});
document.querySelectorAll('.lang a').forEach(function(l){l.addEventListener('click',function(e){var on=nav.querySelector('ul a.on');if(on&&on.getAttribute('href')){e.preventDefault();location.href=l.getAttribute('href')+on.getAttribute('href')}})});
var pm=document.getElementById('pm');if(pm){document.querySelectorAll('.tc').forEach(function(c){c.addEventListener('click',function(){document.getElementById('pmi').src=c.dataset.img;document.getElementById('pmn').textContent=c.dataset.name;document.getElementById('pmr').textContent=c.dataset.role;var sb=document.getElementById('pms'),st={};try{st=JSON.parse(c.dataset.stat||'{}')}catch(e){}sb.innerHTML='';var du=[];if(st.tahun)du.push(st.tahun+' '+sb.dataset.y);if(st.bulan)du.push(st.bulan+' '+sb.dataset.m);[[sb.dataset.e,du.join(' ')],[sb.dataset.b,st.batch?st.batch+' '+sb.dataset.bt:'']].forEach(function(p){if(!p[1])return;var d=document.createElement('div');d.className='s';var b=document.createElement('b');b.textContent=p[1];var sp=document.createElement('span');sp.textContent=p[0];d.appendChild(b);d.appendChild(sp);sb.appendChild(d)});var x=document.getElementById('pme'),ex=[];try{ex=JSON.parse(c.dataset.exp)}catch(e){}x.innerHTML='';if(ex.length){var l=document.createElement('b');l.textContent=x.dataset.l;x.appendChild(l);var u=document.createElement('ul');ex.forEach(function(t){var li=document.createElement('li');li.textContent=t;u.appendChild(li)});x.appendChild(u)}pm.showModal()})});pm.querySelector('.pmx').addEventListener('click',function(){pm.close()});pm.addEventListener('click',function(e){if(e.target===pm)pm.close()})}
if(!('IntersectionObserver' in window)){return}
var so=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){Object.values(links).forEach(function(l){l.classList.remove('on')});if(links[e.target.id])links[e.target.id].classList.add('on')}})},{rootMargin:'-45% 0px -50% 0px'});
Object.keys(links).forEach(function(id){var el=document.getElementById(id);if(el)so.observe(el)});
// muncul saat scroll
var rm=matchMedia('(prefers-reduced-motion:reduce)').matches;
function count(el){var t=el.textContent,n=parseInt(t.replace(/\D/g,''),10),suf=t.replace(/^[\d.,]+/,'');if(!n||rm)return;var s=null;function f(ts){s=s||ts;var p=Math.min((ts-s)/1400,1),v=Math.round(n*(1-Math.pow(1-p,3)));el.textContent=v.toLocaleString(document.documentElement.lang==='en'?'en-US':'id-ID')+suf;if(p<1)requestAnimationFrame(f)}requestAnimationFrame(f)}
var ro=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');if(e.target.matches('.nums li,.stats li'))count(e.target.querySelector('b'));ro.unobserve(e.target)}})},{threshold:.15});
document.querySelectorAll('.head,.pc,.chk li,.tc,.nums li,.stats li,.cb,.box,.vc,.gi').forEach(function(el){el.classList.add('rv');ro.observe(el)});
})();
// trainer: carousel geser (tombol, swipe, drag mouse) + nomor profil
(function(){var tr=document.querySelector('.tr');if(!tr)return;var w=tr.parentNode,pv=w.querySelector('.prev'),nx=w.querySelector('.next');
function step(){var c=tr.querySelector('.tc');return c?c.offsetWidth+parseFloat(getComputedStyle(tr).columnGap||16):240}
function upd(){pv.disabled=tr.scrollLeft<4;nx.disabled=tr.scrollLeft+tr.clientWidth>=tr.scrollWidth-4}
pv.addEventListener('click',function(){tr.scrollBy({left:-step()*2,behavior:'smooth'})});nx.addEventListener('click',function(){tr.scrollBy({left:step()*2,behavior:'smooth'})});
tr.addEventListener('scroll',upd,{passive:true});window.addEventListener('resize',upd);upd();
var d=false,sx=0,sl=0,moved=false;
tr.addEventListener('pointerdown',function(e){if(e.pointerType!=='mouse')return;d=true;moved=false;sx=e.clientX;sl=tr.scrollLeft});
window.addEventListener('pointermove',function(e){if(!d)return;var dx=e.clientX-sx;if(Math.abs(dx)>5){moved=true;tr.classList.add('drag')}tr.scrollLeft=sl-dx});
window.addEventListener('pointerup',function(){if(!d)return;d=false;tr.classList.remove('drag')});
tr.addEventListener('click',function(e){if(moved){e.stopPropagation();e.preventDefault();moved=false}},true);
tr.addEventListener('keydown',function(e){if(e.key==='ArrowRight')tr.scrollBy({left:step(),behavior:'smooth'});if(e.key==='ArrowLeft')tr.scrollBy({left:-step(),behavior:'smooth'})});
var cs=[].slice.call(tr.querySelectorAll('.tc')),pc=document.getElementById('pmc');
cs.forEach(function(c,i){c.addEventListener('click',function(){if(pc)pc.textContent=(i+1)+' / '+cs.length})});
})();

// testimoni: carousel geser (tombol, dots, swipe)
(function(){var c=document.querySelector('.tcar');if(!c)return;var cs=[].slice.call(c.querySelectorAll('.tm')),dw=document.querySelector('.tdots'),pv=document.querySelector('.tprev'),nx=document.querySelector('.tnext'),ds=[];
function step(){return cs[0].offsetWidth+parseFloat(getComputedStyle(c).columnGap||24)}
cs.forEach(function(_,i){var d=document.createElement('button');d.type='button';d.setAttribute('aria-label',(i+1)+'/'+cs.length);d.addEventListener('click',function(){c.scrollTo({left:i*step(),behavior:'smooth'})});dw.appendChild(d);ds.push(d)});
function upd(){var i=Math.round(c.scrollLeft/step());ds.forEach(function(d,k){d.classList.toggle('on',k===i)});pv.disabled=c.scrollLeft<4;nx.disabled=c.scrollLeft+c.clientWidth>=c.scrollWidth-4}
pv.addEventListener('click',function(){c.scrollBy({left:-step(),behavior:'smooth'})});nx.addEventListener('click',function(){c.scrollBy({left:step(),behavior:'smooth'})});
c.addEventListener('scroll',upd,{passive:true});window.addEventListener('resize',upd);upd();
c.addEventListener('keydown',function(e){if(e.key==='ArrowRight')nx.click();if(e.key==='ArrowLeft')pv.click()});
})();

// logo klien: gandakan baris untuk marquee tak berujung (HTML hanya memuat satu salinan)
(function(){var t=document.querySelector('.track');if(!t)return;[].slice.call(t.children).forEach(function(li){var c=li.cloneNode(true);c.classList.add('dup');c.setAttribute('aria-hidden','true');var im=c.querySelector('img');if(im)im.alt='';t.appendChild(c)})})();

// AI Check Up: skor naik dari 0 saat terlihat
(function(){var cb=document.querySelector('.cb.ai');if(!cb)return;var b=cb.querySelector('.gnum b'),to=parseInt(b.dataset.to,10)||0,rm=matchMedia('(prefers-reduced-motion:reduce)').matches;
function run(){cb.classList.add('in');if(rm){b.textContent=to;return}var s=null;requestAnimationFrame(function f(ts){s=s||ts;var p=Math.min((ts-s)/1900,1);b.textContent=Math.round(to*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(f)})}
if(!('IntersectionObserver' in window)){run();return}
var o=new IntersectionObserver(function(es){if(es[0].isIntersecting){run();o.disconnect()}},{threshold:.25});o.observe(cb)})();

// video feedback: klik untuk memutar, kontrol muncul setelah diputar
(function(){document.querySelectorAll('.vc video').forEach(function(v){var f=v.parentNode,bt=f.querySelector('.vplay');if(!bt)return;
function go(){f.classList.add('playing');v.setAttribute('controls','');document.querySelectorAll('.vc video').forEach(function(o){if(o!==v)o.pause()});v.play()}
bt.addEventListener('click',go);v.addEventListener('click',function(){if(!f.classList.contains('playing'))go()});v.addEventListener('ended',function(){f.classList.remove('playing');v.removeAttribute('controls')})})})();

// galeri training: klik untuk memperbesar
(function(){var d=document.getElementById('gd');if(!d)return;var im=document.getElementById('gdi'),cp=document.getElementById('gdc');
document.querySelectorAll('.gz').forEach(function(b){b.addEventListener('click',function(){im.src=b.dataset.src;im.alt=b.dataset.cap||'';cp.textContent=b.dataset.cap||'';d.showModal()})});
d.querySelector('.pmx').addEventListener('click',function(){d.close()});d.addEventListener('click',function(e){if(e.target===d)d.close()})})();

// progress bar scroll di atas halaman
(function(){var b=document.querySelector(".prog i");if(!b)return;function u(){var h=document.documentElement,m=h.scrollHeight-h.clientHeight;b.style.transform="scaleX("+(m>0?h.scrollTop/m:0)+")"}addEventListener("scroll",u,{passive:true});addEventListener("resize",u);u()})();
