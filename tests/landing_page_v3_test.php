<?php
declare(strict_types=1);
$root=dirname(__DIR__);
ob_start(); require $root.'/central-financeira/index.php'; $html=(string)ob_get_clean();
$slots=['receitas-despesas','previsao','cartoes-caixa','metas-relatorios','familia','empresarial'];
foreach($slots as $slot){
  if(substr_count($html,'data-screenshot-slot="'.$slot.'"') !== 1) throw new RuntimeException('V3: slot '.$slot.' ausente ou duplicado');
}
if(substr_count($html,'class="feature-preview has-screenshot"') !== 6) throw new RuntimeException('V3: seis capturas reais de resolução original esperadas');
if(!str_contains($html,'naiara-retrato-original.jpg') || str_contains($html,'src="assets/naiara.svg"')) throw new RuntimeException('V3: foto antiga não substituída');
$photo=(string)file_get_contents($root.'/central-financeira/assets/naiara-retrato-original.jpg');
if(strlen($photo)<15000 || !str_starts_with($photo,hex2bin('ffd8')) || !str_ends_with($photo,hex2bin('ffd9'))) throw new RuntimeException('V3: foto inválida');
$style=(string)file_get_contents($root.'/central-financeira/assets/features.css');
if(!str_contains($style,'.feature-showcase') || !str_contains($style,'.feature-accent .feature-card-copy p')) throw new RuntimeException('V3: estilos de cards/contraste ausentes');
$responsive=(string)file_get_contents($root.'/central-financeira/assets/responsive.css');
if(!str_contains($responsive,'.section-features .feature-showcase')) throw new RuntimeException('V3: estilo mobile não encontrado');
echo "OK: foto real da Naiara, slots para seis prints, contraste e mobile.\n";
