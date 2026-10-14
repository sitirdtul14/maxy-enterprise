<?php
session_start();
$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'id'); if (!in_array($lang, ['id','en'], true)) $lang = 'id';
if (isset($_GET['lang'])) @setcookie('lang', $lang, time()+31536000, '/'); // ingat pilihan bahasa selama setahun
$D = require __DIR__.($lang==='en' ? '/data.en.php' : '/data.php'); $S = $D['site']; $U = $D['ui'];
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
function cut($t,$n){ return function_exists('mb_substr') ? mb_substr($t,0,$n) : substr($t,0,$n); }
function csv_safe($v){ return preg_match('/^[=+\-@\t\r]/', $v) ? "'".$v : $v; } // cegah formula injection di Excel/Sheets
function save_lead($row, $S){
  foreach ([$S['storage_dir'], __DIR__.'/storage'] as $dir) { // utama: di luar web root; cadangan: ./storage
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) continue;
    $fh = @fopen($dir.'/leads.csv', 'a'); if (!$fh) continue;
    flock($fh, LOCK_EX); fputcsv($fh, array_map('csv_safe', $row), ',', '"', '\\'); flock($fh, LOCK_UN); fclose($fh);
    return true;
  }
  error_log('MAXY: gagal menyimpan lead ke leads.csv');
  return false;
}
function client_logo($f){ // logo asli (png/webp/jpg/svg) di assets/clients/ didahulukan; kalau belum ada, pakai placeholder
  foreach (['png','webp','jpg','jpeg','svg'] as $x) if (is_file(__DIR__."/assets/clients/$f.$x")) return ['src'=>"assets/clients/$f.$x",'ph'=>false];
  return ['src'=>"assets/clients/_placeholder/$f.svg",'ph'=>true];
}
function hero_photo($base){ // foto hero: assets/hero.(jpg|jpeg|png|webp); kalau tidak ada, pakai ilustrasi SVG
  foreach (['jpg','jpeg','png','webp'] as $x) if (is_file(__DIR__."/$base.$x")) return "$base.$x";
  return null;
}
function newest($base, $exts=['jpg','jpeg','png','webp']){ // pilih file terbaru dengan nama dasar sama (jadi foto baru tinggal diunggah, tanpa menghapus yang lama)
  $best=null; $bt=-1; foreach($exts as $x){ $p=__DIR__."/$base.$x"; if(is_file($p)){ $t=@filemtime($p); if($t>$bt){$bt=$t;$best="$base.$x";} } } return $best; }
function person_img($slug){ return newest("assets/people/$slug") ?: "assets/people/$slug.svg"; }
function prog_img($k){ return newest("assets/prog/$k") ?: (is_file(__DIR__."/assets/prog/$k-scene.svg") ? "assets/prog/$k-scene.svg" : "assets/prog/$k.svg"); } // foto program: taruh assets/prog/<nama>.jpg (ceo, finance, hr, marketing, ops, agent, talent)
function inline_img($path){ // gambar kecil disematkan langsung ke HTML (data URI) supaya tidak bergantung pada request file terpisah di hosting
  $p=__DIR__.'/'.$path; if(!is_file($p)||filesize($p)>250000) return $path; $m=['png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','webp'=>'image/webp','svg'=>'image/svg+xml'][strtolower(pathinfo($p,PATHINFO_EXTENSION))]??'application/octet-stream';
  return 'data:'.$m.';base64,'.base64_encode((string)file_get_contents($p)); }
function media($base,$exts){ return newest($base,$exts); }
function ico($n){ static $I=[
 'building'=>'<path d="M4 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16M14 9h5a1 1 0 0 1 1 1v11M2 21h20M8 8h2M8 12h2M8 16h2M17 13h1M17 17h1"/>',
 'users'=>'<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.8a3.5 3.5 0 0 1 0 6.4M18 14.4c2.2.7 3.5 2.6 3.5 5.6"/>',
 'layers'=>'<path d="M12 3 2 8l10 5 10-5-10-5zM2 13l10 5 10-5M2 17.5l10 5 10-5"/>',
 'hand'=>'<path d="M9 11V5.5a1.5 1.5 0 0 1 3 0V10m0-2.5a1.5 1.5 0 0 1 3 0V11m0-2a1.5 1.5 0 0 1 3 0v6a6 6 0 0 1-6 6h-1.4a6 6 0 0 1-4.7-2.3L4 14.7a1.6 1.6 0 0 1 2.4-2L9 15"/>',
 'sliders'=>'<path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/>',
 'target'=>'<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2"/>',
 'play'=>'<path d="M7 4.5v15l13-7.5z" fill="currentColor"/>',
 'camera'=>'<path d="M4 8h3l1.6-2.4h6.8L17 8h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13.5" r="3.8"/>',
 'scan'=>'<path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3M4 12h16"/>',
 'chart'=>'<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
 'bolt'=>'<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
 'doc'=>'<path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM14 3v5h5M9 13h6M9 17h6"/>',
 ]; return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($I[$n]??'').'</svg>'; }
function soc($n){ static $S=[
 'Instagram'=>'<path fill="currentColor" d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 4 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.1-2.4.1-3.5 1.2-3.6 3.6C3.6 8.9 3.6 9.3 3.6 12s0 3.1.1 4.3c.1 2.4 1.2 3.5 3.6 3.6 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c2.4-.1 3.5-1.2 3.6-3.6.1-1.2.1-1.6.1-4.3s0-3.1-.1-4.3c-.1-2.4-1.2-3.5-3.6-3.6C15.500 4 15.100 4 12 4zm0 2.900a5.100 5.100 0 1 1 0 10.200 5.100 5.100 0 0 1 0-10.200zm0 1.800a3.300 3.300 0 1 0 0 6.600 3.300 3.300 0 0 0 0-6.600zm5.300-3.100a1.200 1.200 0 1 1 0 2.400 1.200 1.200 0 0 1 0-2.400z"/>',
 'LinkedIn'=>'<path fill="currentColor" d="M4.900 3.500a2.200 2.200 0 1 1 0 4.400 2.200 2.200 0 0 1 0-4.400zM3 9.600h3.800V21H3zm6.200 0h3.600v1.600h.1c.5-1 1.800-1.900 3.600-1.900 3.800 0 4.500 2.500 4.500 5.700V21h-3.800v-5.200c0-1.300 0-2.900-1.800-2.900s-2.100 1.400-2.100 2.800V21H9.200z"/>',
 'Facebook'=>'<path fill="currentColor" d="M13.500 21v-8h2.700l.4-3.200h-3.100V7.800c0-.9.300-1.500 1.600-1.500h1.700V3.400c-.3 0-1.300-.1-2.400-.1-2.400 0-4.100 1.500-4.100 4.200v2.300H7.500V13h2.800v8z"/>',
 'TikTok'=>'<path fill="currentColor" d="M16.600 2h-3.100v12.600a2.700 2.700 0 1 1-2.700-2.700c.3 0 .5 0 .8.100V8.800a5.900 5.900 0 1 0 5 5.800V8.300a7.200 7.200 0 0 0 4.200 1.300V6.500a4.200 4.200 0 0 1-4.200-4.500z"/>',
 ]; return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($S[$n]??'').'</svg>'; }
function e($t){ return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function wa($m){ global $S; return 'https://wa.me/'.$S['wa'].'?text='.rawurlencode($m); }
$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!empty($_POST['website'])) { exit; } // honeypot anti-spam
  $f = []; foreach (['nama'=>100,'email'=>120,'perusahaan'=>120,'pesan'=>600] as $k=>$max) $f[$k] = cut(trim(strip_tags((string)($_POST[$k] ?? ''))), $max);
  if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) { $err = $U['err_csrf']; }
  elseif (time() - ($_SESSION['last_submit'] ?? 0) < 5) { $err = $U['err_wait']; }
  elseif ($f['nama']==='' || $f['perusahaan']==='' || !filter_var($f['email'], FILTER_VALIDATE_EMAIL)) { $err = $U['err_fields']; }
  else {
    $_SESSION['last_submit'] = time();
    save_lead([date('Y-m-d H:i:s'), $f['nama'], $f['email'], $f['perusahaan'], $f['pesan']], $S); // gagal simpan tidak menghalangi user ke WhatsApp
    header('Location: '.wa("{$U['wa_hi']}\n{$U['wa_name']}: {$f['nama']}\n{$U['wa_email']}: {$f['email']}\n{$U['wa_company']}: {$f['perusahaan']}\n{$U['wa_need']}: ".($f['pesan']?:'-')));
    exit;
  }
}
function garet_face($w, $names){ // pakai file Garet pertama yang ada di assets/fonts/, tanpa 404
  foreach ($names as $n) foreach (['woff2'=>'woff2','woff'=>'woff','otf'=>'opentype','ttf'=>'truetype'] as $ext=>$fmt)
    if (is_file(__DIR__."/assets/fonts/$n.$ext")) return ['css'=>"@font-face{font-family:'Garet';font-weight:$w;font-display:swap;src:url('assets/fonts/$n.$ext') format('$fmt')}", 'url'=>"assets/fonts/$n.$ext", 'ext'=>$ext];
  return null;
}
$garet = array_filter([garet_face(400,['Garet-Book','Garet-Regular','Garet']), garet_face(800,['Garet-Heavy','Garet-ExtraBold','Garet-Bold'])]);
$H=$D['hero']; $SO=$D['solution']; $T=$D['testimonial']; $W=$D['why']; $C=$D['checkup']; $FI=$D['final'];
?><!doctype html>
<html lang="<?=e($lang)?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?=e($S['title'])?></title><meta name="description" content="<?=e($S['desc'])?>"><meta name="theme-color" content="#192e6d"><meta property="og:type" content="website"><meta property="og:title" content="<?=e($S['title'])?>"><meta property="og:description" content="<?=e($S['desc'])?>"><meta property="og:image" content="<?=e($S['logo'])?>"><meta name="twitter:card" content="summary"><link rel="icon" href="<?=e($S['logo'])?>">
<?php foreach($garet as $g): ?><link rel="preload" href="<?=e($g['url'])?>" as="font" type="font/<?=e($g['ext'])?>" crossorigin>
<?php endforeach ?><?php if($garet): ?><style><?=implode('',array_column($garet,'css'))?></style>
<?php endif ?><link rel="preload" href="assets/fonts/Montserrat-800.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="assets/style.css?v=<?=@filemtime(__DIR__.'/assets/style.css')?>"></head><body>
<div class="prog" aria-hidden="true"><i></i></div>
<header class="nav"><div class="w"><a class="logo" href="#hero" aria-label="Maxy"><img src="assets/logo-maxy-white.png" alt="Maxy Academy" width="102" height="40"></a>
<nav aria-label="<?=e($U['aria_nav'])?>"><ul><?php foreach($D['nav'] as $id=>$l): ?><li><a href="#<?=e($id)?>"><?=e($l)?></a></li><?php endforeach ?><li class="mcta"><a class="btn y wa" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"><?=e($H['cta1'])?></a></li></ul></nav>
<div class="ncta"><div class="lang" role="group" aria-label="<?=e($U['aria_lang'])?>"><a href="?lang=id" lang="id" hreflang="id" title="Bahasa Indonesia"<?=$lang==='id'?' class="on" aria-current="true"':''?>><svg class="flag" viewBox="0 0 20 14" aria-hidden="true"><rect width="20" height="7" fill="#e70011"/><rect y="7" width="20" height="7" fill="#fff"/></svg>ID</a><a href="?lang=en" lang="en" hreflang="en" title="English"<?=$lang==='en'?' class="on" aria-current="true"':''?>><svg class="flag" viewBox="0 0 60 40" aria-hidden="true"><rect width="60" height="40" fill="#012169"/><path d="M0 0L60 40M60 0L0 40" stroke="#fff" stroke-width="8"/><path d="M0 0L60 40M60 0L0 40" stroke="#c8102e" stroke-width="3"/><path d="M30 0V40M0 20H60" stroke="#fff" stroke-width="12"/><path d="M30 0V40M0 20H60" stroke="#c8102e" stroke-width="7"/></svg>EN</a></div>
<a class="btn y wa" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"><?=e($H['cta1'])?></a></div>
<button class="burger" type="button" aria-label="<?=e($U['aria_menu'])?>" aria-expanded="false"><span></span></button></div></header>
<main>
<section class="hero" id="hero"><div class="w"><div class="hcopy"><h1><?=e($H['h1'])?> <span class="mk"><?=e($H['mark'])?></span></h1><p class="lead"><?=e($H['lead'])?></p>
<div class="cta"><a class="btn y wa" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"><?=e($H['cta1'])?></a><a class="btn o" href="#solution"><?=e($H['cta2'])?></a></div></div></div>
<?php $ph = hero_photo($H['photo']); $hv = media('assets/video/hero',['mp4','webm']); if($ph): ?><div class="hphoto"><img src="<?=e($ph)?>" alt="<?=e($H['photo_alt'])?>" fetchpriority="high"><?php if($hv): ?><video class="hvid" src="<?=e($hv)?>" poster="<?=e($ph)?>" autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video><?php endif ?></div><?php endif ?><span class="hfx" aria-hidden="true"></span></section>
<div class="stats"><div class="w"><ul><?php foreach($H['stats'] as $st): ?><li><b><?=e($st[0])?></b><span><?=e($st[1])?></span></li><?php endforeach ?></ul></div></div>

<section id="solution"><div class="w"><div class="head"><h2><?=e($SO['title'])?></h2><p><?=e($SO['intro'])?></p></div>
<?php $ri=0; $ci=0; $rn=[1,3,4]; $ing=false; foreach($SO['services'] as $v): $cell=$v['kind']==='cell';
 if($cell && !$ing){ $ing=true; echo '<div class="fgbox"><div class="fgh"><span class="num">2</span><h3>'.e($SO['group']).'</h3><p>'.e($SO['group_note']).'</p></div><div class="cgrid">'; }
 if(!$cell && $ing){ $ing=false; echo '</div></div>'; } ?>
<?php $pi=prog_img($v['img']); $isph=substr($pi,-10)==='-scene.svg'||substr($pi,-4)==='.svg'; if($isph && !$S['placeholders']) $pi=null; ?><article class="pc <?=$cell?'cell':'row'.(($ri%2)?' rev':'')?><?=$pi?'':' nophoto'?>"><?php if($pi): ?><div class="pimg"><img src="<?=e($pi)?>" alt="" loading="lazy"><?php $pv=media("assets/video/prog-".$v['img'],['mp4','webm']); if($pv): ?><video class="pvid" src="<?=e($pv)?>" poster="<?=e($pi)?>" autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video><?php endif ?><?php if($cell): ?><span class="tagn"><?=chr(97+$ci)?></span><?php else: ?><span class="num"><?=$rn[$ri]?></span><?php endif ?></div><?php endif ?><div class="pb"><?php if(!$pi): ?><?php if($cell): ?><span class="tagn"><?=chr(97+$ci)?></span><?php else: ?><span class="num"><?=$rn[$ri]?></span><?php endif ?><?php endif ?><?php if($cell) $ci++; else $ri++; ?><h3><?=e($v['name'])?></h3><p><?=e($v['desc'])?></p>
<ul class="chk2"><?php foreach($v['chips'] as $c): ?><li><?=e($c)?></li><?php endforeach ?></ul>
<a class="more" target="_blank" rel="noopener" href="<?=e(wa($U['wa_hi'].' '.$U['wa_interest'].' '.$v['name']))?>"><?=e($U['more'])?></a></div></article>
<?php endforeach; if($ing) echo '</div></div>'; ?>
</div></section>

<section class="cl" id="clients"><div class="w"><div class="head"><h2><?=e($D['clients']['title'])?></h2></div></div>
<div class="marq"><ul class="track"><?php $L=$D['clients']['logos']; foreach($L as $n=>$f): $lg=client_logo($f); ?><li class="<?=$lg['ph']?'ph':''?>"><img src="<?=e(inline_img($lg['src']))?>" alt="<?=e($n)?>" height="40"></li><?php endforeach ?></ul></div><noscript><style>.track{animation:none!important;flex-wrap:wrap;justify-content:center;width:auto!important}</style></noscript></section>

<section class="why" id="why"><div class="w"><div class="panel"><div class="head"><h2><?=e($W['title'])?></h2><p><?=e($W['sub'])?></p></div>
<ul class="nums"><?php foreach($W['nums'] as $k=>$n): ?><li><i class="ni"><?=ico($k?'users':'building')?></i><small><?=e($n[0])?></small><b><?=e($n[1])?></b><span><?=e($n[2])?></span></li><?php endforeach ?></ul>
<ul class="chk"><?php $wi=['layers','hand','sliders','target']; foreach($W['points'] as $k=>$p): ?><li><i class="wi"><?=ico($wi[$k]??'bolt')?></i><b><?=e($p[0])?></b><span><?=e($p[1])?></span></li><?php endforeach ?></ul></div></div></section>

<section id="testimonial"><div class="w"><div class="head"><h2><?=e($T['title'])?></h2><p><?=e($T['sub'])?></p></div>
<div class="tcar" tabindex="0" aria-roledescription="carousel"><?php $TL=['anteraja','assa','anteraja','assa','mito','daai-tv','adira','adira','anteraja']; /* logo perusahaan per testimoni, urutan sama dengan daftar */ foreach($T['list'] as $ti=>$t): ?><article class="tm"><i class="av" aria-hidden="true"><?=ico('building')?></i><span class="qm" aria-hidden="true">&ldquo;</span><q><?=e($t['q'])?></q><?php if(!empty($T['dummy'])): ?><div class="who ind"><span><?=e($T['ind_label'])?></span><b><?=e($t['ind'])?></b></div><?php else: ?><div class="who"><b><?=e($t['name'])?></b><span><?=e($t['role'])?></span></div><?php endif ?><?php if(!empty($TL[$ti])): $lg=client_logo($TL[$ti]); ?><div class="tlogo"><img src="<?=e(inline_img($lg['src']))?>" alt="<?=e($TL[$ti])?>" height="34" loading="lazy"></div><?php endif ?></article><?php endforeach ?></div>
<div class="tnav"><button type="button" class="tprev" aria-label="Prev">&#8592;</button><div class="tdots"></div><button type="button" class="tnext" aria-label="Next">&#8594;</button></div>
<?php $vids=[]; foreach([1,2,3] as $n){ $f=media("assets/video/feedback-$n",['mp4','webm']); if($f) $vids[$n]=$f; } if($vids||$S['placeholders']): ?>
<div class="vfb"><div class="vh"><h3><?=e($T['vtitle'])?></h3><p><?=e($T['vsub'])?></p></div><div class="vgrid">
<?php foreach([1,2,3] as $n): $f=$vids[$n]??null; if(!$f && $vids) continue; $pst=newest("assets/video/feedback-$n"); ?>
<figure class="vc<?=$f?'':' ph'?>"><?php if($f): ?><video src="<?=e($f)?>"<?=$pst?' poster="'.e($pst).'"':''?> playsinline preload="metadata"></video><button type="button" class="vplay" aria-label="<?=e($U['play'])?>"><?=ico('play')?></button><?php else: ?><div class="vph"><span class="vplay"><?=ico('play')?></span><small><?=e($T['vph'])?></small></div><?php endif ?></figure>
<?php endforeach ?></div></div><?php endif ?></div></section>

<section id="trainer"><div class="w"><div class="head"><h2><?=e($D['trainer']['title'])?></h2><p><?=e($D['trainer']['sub'])?></p></div>
<div class="trw"><button type="button" class="trn prev" aria-label="Prev">&#8249;</button><div class="tr" tabindex="0"><?php foreach($D['trainer']['list'] as $t): ?><button type="button" class="tc" data-name="<?=e($t[0])?>" data-role="<?=e($t[1])?>" data-img="<?=e(person_img($t[2]))?>" data-exp="<?=e(json_encode($t[3]))?>" data-stat="<?=e(json_encode($t[4]??[]))?>"><img src="<?=e(person_img($t[2]))?>" alt="" loading="eager" draggable="false"><span><h3><?=e($t[0])?></h3><p><?=e($t[1])?></p></span></button><?php endforeach ?></div><button type="button" class="trn next" aria-label="Next">&#8250;</button></div></div>
<dialog id="pm" class="pm" aria-labelledby="pmn"><button type="button" class="pmx" aria-label="<?=e($U['close'])?>">&times;</button><div class="pmimg"><img id="pmi" src="" alt=""></div><div class="pmb"><span class="pmk" id="pmc"></span><h3 id="pmn"></h3><p id="pmr"></p><div id="pms" data-e="<?=e($D['trainer']['l_exp'])?>" data-b="<?=e($D['trainer']['l_batch'])?>" data-y="<?=e($D['trainer']['u_y'])?>" data-m="<?=e($D['trainer']['u_m'])?>" data-bt="<?=e($D['trainer']['u_b'])?>"></div><div id="pme" data-l="<?=e($D['trainer']['exp_label'])?>"></div><a class="btn y wa" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"><?=e($U['talk'])?></a></div></dialog></section>

<section class="ck" id="assessment"><div class="w"><div class="cb ai"><span class="cgrid-bg" aria-hidden="true"></span><span class="scan" aria-hidden="true"></span><div class="cl2"><span class="ctag"><i></i><?=e($C['tag'])?></span><h2><?=e($C['title'])?></h2><p><?=e($C['text'])?></p>
<ul class="ckl"><?php $bi=['chart','target','doc']; foreach($C['bullets'] as $k=>$b): ?><li><i><?=ico($bi[$k]??'bolt')?></i><?=e($b)?></li><?php endforeach ?></ul>
<div class="cta"><a class="btn" href="<?=e($S['checkup_url'])?>" target="_blank" rel="noopener"><?=e($C['cta1'])?></a><a class="btn o wa" target="_blank" rel="noopener" href="<?=e(wa($U['wa_hi'].' '.$U['wa_checkup']))?>"><?=e($C['cta2'])?></a></div></div>
<div class="hud" role="img" aria-label="<?=e($U['aria_gauge'])?>"><div class="hudtop"><span><?=e($C['sample'])?></span><em></em></div>
<div class="gwrap"><svg class="gauge" viewBox="0 0 220 220" aria-hidden="true"><defs><linearGradient id="gg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7aa2ff"/><stop offset="1" stop-color="#ffbf00"/></linearGradient></defs>
<g class="ring r1"><circle cx="110" cy="110" r="104" fill="none" stroke="#7aa2ff" stroke-opacity=".5" stroke-width="1.5" stroke-dasharray="3 9"/></g>
<g class="ring r2"><circle cx="110" cy="110" r="94" fill="none" stroke="#fff" stroke-opacity=".28" stroke-width="2" stroke-dasharray="60 24 14 24"/></g>
<circle cx="110" cy="110" r="80" fill="none" stroke="#fff" stroke-opacity=".12" stroke-width="14"/>
<circle class="garc" cx="110" cy="110" r="80" fill="none" stroke="url(#gg)" stroke-width="14" stroke-linecap="round" pathLength="100" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 110 110)"/>
<circle cx="110" cy="110" r="62" fill="rgba(8,18,52,.6)" stroke="#7aa2ff" stroke-opacity=".35"/></svg>
<div class="gnum"><b data-to="78">0</b><small><?=e($C['score_label'])?></small></div></div>
<ul class="abars"><?php foreach($C['areas'] as $ar): ?><li><span><?=e($ar[0])?></span><div class="bar"><i style="--w:<?=(int)$ar[1]?>"></i></div><em><?=(int)$ar[1]?></em></li><?php endforeach ?></ul></div></div></div></section>

<section class="fin" id="contact"><div class="w">
<?php $gal=[]; foreach([1,2,3,4,5] as $n){ $f=newest("assets/gallery/g$n"); if($f) $gal[$n]=$f; } if($gal||$S['placeholders']): ?>
<div class="gal"><div class="head"><h2><?=e($FI['gtitle'])?></h2><p><?=e($FI['gsub'])?></p></div><div class="gmos<?=($gal&&count($gal)<5)?' few':''?>">
<?php foreach([1,2,3,4,5] as $n): $f=$gal[$n]??null; if(!$f && $gal) continue; ?>
<figure class="gi g<?=$n?><?=$f?'':' ph'?>"><?php if($f): ?><button type="button" class="gz" data-src="<?=e($f)?>" data-cap="<?=e($FI['gcap'][$n-1]??'')?>" aria-label="<?=e($U['zoom'])?>"><img src="<?=e($f)?>" alt="<?=e($FI['gcap'][$n-1]??'')?>" loading="lazy"></button><?php else: ?><div class="gph"><?=ico('camera')?></div><?php endif ?><figcaption><?=e($FI['gcap'][$n-1]??'')?></figcaption></figure>
<?php endforeach ?></div></div><?php endif ?>
<dialog id="gd" class="gd"><button type="button" class="pmx" aria-label="<?=e($U['close'])?>">&times;</button><img id="gdi" src="" alt=""><p id="gdc"></p></dialog>
<div class="box"><div><h2><?=e($FI['title'])?></h2><p><?=e($FI['text'])?></p></div>
<form class="form" method="post" action="#contact"><?php if($err): ?><p class="err"><?=e($err)?></p><?php endif ?>
<input type="hidden" name="csrf" value="<?=e($_SESSION['csrf'])?>">
<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
<label><?=e($U['name'])?><input name="nama" required autocomplete="name" value="<?=e($_POST['nama']??'')?>"></label><label><?=e($U['email'])?><input name="email" type="email" required autocomplete="email" value="<?=e($_POST['email']??'')?>"></label><label><?=e($U['company'])?><input name="perusahaan" required autocomplete="organization" value="<?=e($_POST['perusahaan']??'')?>"></label><label><?=e($U['need'])?><textarea name="pesan" rows="3"><?=e($_POST['pesan']??'')?></textarea></label><button class="btn y" type="submit"><?=e($U['send'])?></button></form></div></div></section>
</main>
<footer><div class="w"><div class="fgrid"><div><a class="flogo" href="#hero" aria-label="Maxy Academy"><img src="<?=e($S['logo_icon'])?>" alt="Maxy Academy" width="52" height="52"></a><p><a class="fmail" href="mailto:<?=e($S['email'])?>"><?=e($S['email'])?></a></p><p><a class="fmail" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"><?=e($S['phone'])?></a></p>
<h4 class="fsoc"><?=e($S['social_title'])?></h4><ul class="soc"><?php foreach($S['socials'] as $n=>$u): ?><li><a class="s-<?=strtolower(e($n))?>" href="<?=e($u)?>" target="_blank" rel="noopener" aria-label="<?=e($n)?>" title="<?=e($n)?>"><?=soc($n)?></a></li><?php endforeach ?></ul></div>
<?php foreach($S['addresses'] as $n=>$a): ?><div><h4><?=e($n)?></h4><p><?=e($a)?></p></div><?php endforeach ?></div><p class="copy">&copy; <?=date('Y')?> Maxy Academy. All rights reserved.</p></div></footer>
<a class="wafab" target="_blank" rel="noopener" href="<?=e(wa($S['wa_default']))?>"aria-label="<?=e($U['aria_wa'])?>" title="<?=e($U['aria_wa'])?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.200zm4.500-6.100c-.2-.1-1.500-.7-1.700-.8s-.4-.1-.6.100-.7.800-.8 1-.3.200-.5.100a6.700 6.700 0 0 1-3.300-2.900c-.3-.4.300-.4.800-1.400.1-.2 0-.3 0-.5l-.8-1.800c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.300 3 3 0 0 0-.9 2.200 5.200 5.200 0 0 0 1.100 2.800 11.900 11.900 0 0 0 4.600 4.000c1.700.700 2.400.800 3.200.700.500-.1 1.500-.6 1.700-1.200s.2-1.100.2-1.200-.2-.2-.4-.3z"/></svg><span class="t"><?=e($U['aria_wa'])?></span></a>
<script src="assets/app.js?v=<?=@filemtime(__DIR__.'/assets/app.js')?>"></script>
</body></html>
