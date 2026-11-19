<?php
declare(strict_types=1);
$root=dirname(__DIR__);
ob_start();
require $root.'/central-financeira/index.php';
$html=(string)ob_get_clean();
function site_ux_check(bool $good,string $label):void {
 if(!$good)throw new RuntimeException('LANDING UX: '.$label);
}
site_ux_check(!str_contains($html,'class="site-header"'),'sem menu superior');
site_ux_check(substr_count($html,'Quero organizar minhas contas')>=5,'CTA repetido após seções');
site_ux_check(substr_count($html,'class="cta-repeat"')===4,'CTA entre cada duas seções');
site_ux_check(substr_count($html,'class="preview-image-link"')===6,'6 telas reais clicáveis');
site_ux_check(substr_count($html,'class="feature-preview has-screenshot"')===6,'6 telas completas');
site_ux_check(!str_contains($html,'class="preview-screenshot"')&&!str_contains($html,'class="preview-window"'),
 'não usar minisprite de baixa resolução nem barra falsa sobre o print');
site_ux_check(!str_contains($html,'Em breve: tela real do sistema'),'sem placeholders não implementados');
$sourceSizes=[[1651,951],[1594,803],[1619,923],[1585,712],[1584,945],[1946,955]];
$hashes=[
 '73a5c431d93ec6dde6492f81c14c0d243a0dd592aabbe8f60ad88684c7370ea9',
 '7838c6a0bcbbbf5db8ced13d9db1ad90b4bead6eee1c35a61fe715826b34998b',
 'd0f563c47acba236acfd81ad493805e0095ad4dab1cdca8ab8e364d36dbe7f92',
 'b1450d17dd9255a5a0b21cc846007cd896b8ee1b1e72d373b15413e2348157fb',
 '081a4a40b5bf38b5a161648eb0156d8b6152fe59369c262345531618b7a988b3',
 '6b7e7f3189413b63eb614dd464b52b252d13aeee9a5be1f39ab754c52facd6c8'
];
for($i=1;$i<=6;$i++){
 $path=$root.'/central-financeira/assets/screens/screen-'.$i.'.webp';
 site_ux_check(is_file($path),'arquivo do screenshot '.$i);
 $dimensions=@getimagesize($path);
 site_ux_check(is_array($dimensions) &&
   $dimensions[0]===$sourceSizes[$i-1][0] &&
   $dimensions[1]===$sourceSizes[$i-1][1],'resolução original do screenshot '.$i);
 site_ux_check(hash_file('sha256',$path)===$hashes[$i-1],
   'imagem original do screenshot '.$i.' sem alteração de pixels');
 $url='/central-financeira/assets/screens/screen-'.$i.'.webp';
 site_ux_check(substr_count($html,'href="'.$url.'"')===1 &&
   substr_count($html,'src="'.$url.'"')===1,'abrir e mostrar o mesmo screenshot original '.$i);
}
$css=file_get_contents($root.'/central-financeira/assets/landing.css');
site_ux_check(str_contains($css,'.feature-showcase{grid-template-columns:minmax(0,1fr)'),
 'prints em largura total');
site_ux_check(str_contains($css,'height:auto!important') &&
 str_contains($css,'aspect-ratio:auto!important') &&
 str_contains($css,'filter:none!important'),'prints sem corte, distorção ou desfoque');
site_ux_check(!str_contains($css,'background-size:200% 300%') &&
 !str_contains($css,'portal-screens.php'),'sprite pequeno desativado');
site_ux_check(str_contains($css,'prefers-reduced-motion'),'animação acessível');
$motion=file_get_contents($root.'/central-financeira/assets/landing-motion.js');
site_ux_check(str_contains($motion,'IntersectionObserver'),'efeitos leves ao navegar');
echo "OK: landing com seis capturas nítidas na resolução original e ampliação por clique.\n";
