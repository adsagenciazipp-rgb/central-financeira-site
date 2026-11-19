<?php
declare(strict_types=1);
$root=dirname(__DIR__);
ob_start();
require $root.'/central-financeira/index.php';
$html=ob_get_clean();
function site_ux_check(bool $good,string $label):void {
 if(!$good)throw new RuntimeException('LANDING UX: '.$label);
}
site_ux_check(!str_contains($html,'class="site-header"'),'sem menu superior');
site_ux_check(substr_count($html,'Quero organizar minhas contas')>=5,'CTA repetido após seções');
site_ux_check(substr_count($html,'class="cta-repeat"')===4,'CTA entre cada duas seções');
site_ux_check(substr_count($html,'class="preview-screenshot"')===6,'capturas presentes em todos os seis recursos');
site_ux_check(!str_contains($html,'Em breve: tela real do sistema'),'sem placeholders não implementados');
$asset=file_get_contents($root.'/central-financeira/assets/portal-screens.php');
site_ux_check(str_contains($asset,'sha256') && str_contains($asset,'base64_decode'),'asset com integridade verificada');
ob_start();
require $root.'/central-financeira/assets/portal-screens.php';
$bytes=ob_get_clean();
site_ux_check(hash('sha256',$bytes)==='09d855677055c6deefcfaefb1e62f3e9fe447da646ebe029acb0a7e4035d90c0','miniaturas reais sem corrupção');
$size=getimagesizefromstring($bytes);
site_ux_check(is_array($size)&&$size[0]===660&&$size[1]===480,'sprite com seis capturas');
$css=file_get_contents($root.'/central-financeira/assets/landing.css');
site_ux_check(str_contains($css,"portal-screens.php")&&str_contains($css,'prefers-reduced-motion'),'miniaturas e animações acessíveis');
$motion=file_get_contents($root.'/central-financeira/assets/landing-motion.js');
site_ux_check(str_contains($motion,'IntersectionObserver')&&str_contains($motion,'prefers-reduced-motion'),'animação sutil de navegação');
echo "OK: landing sem menu, CTAs distribuídos, seis capturas verdadeiras e efeitos suaves.\n";
