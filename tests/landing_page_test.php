<?php
declare(strict_types=1);
function expect_landing(bool $value,string $message):void{
  if(!$value)throw new RuntimeException('LANDING: '.$message);
}
$root=dirname(__DIR__);
ob_start();
require $root.'/central-financeira/index.php';
$html=(string)ob_get_clean();
$old=(string)file_get_contents($root.'/index.php');
$backup=(string)@file_get_contents($root.'/backups/em-breve-2026-11-16.php');
expect_landing($old!=='' && $old===$backup,'backup exato da página atual "Em breve"');
expect_landing(str_contains($old,'Em breve'),'não substituir a home do domínio principal');
foreach(['<h1','id="beneficios"','id="depoimentos"','id="planos"','id="faq"',
         'R$ 19,90','R$ 34,90','/mês','Familiar + Empresarial',
         'Gestão Familiar','Naiara Corá','videoaulas','curso de gestão financeira',
         'Perguntas frequentes'] as $piece){
  expect_landing(str_contains($html,$piece),'estrutura exigida: '.$piece);
}
expect_landing(substr_count($html,'https://central.naiaracora.com.br/loja.php#planos')>=2,
 'ambos os planos devem levar à loja já existente da Central, sem ativar checkout');
expect_landing(!str_contains($html,'checkout.php') && !str_contains($html,'preapproval'),
 'não abrir pagamento Sandbox ou ativar pagamento no site público');
expect_landing(str_contains($html,'depoimentos sobre nossos materiais anteriores'),
 'depoimentos da antiga planilha não podem ser apresentados como prova do novo sistema');
expect_landing(str_contains($html,'prévia ilustrativa do sistema'),
 'mockups em CSS devem ser honestamente identificados como ilustração');
expect_landing(str_contains($html,'assets/naiara.svg')
  &&is_file($root.'/central-financeira/assets/naiara.svg'),
 'usar a foto enviada da Naiara na página comercial');
$css=(string)@file_get_contents($root.'/central-financeira/assets/landing.css');
expect_landing(str_contains($css,'@media (max-width: 760px)')
  &&str_contains($css,'.device-laptop')
  &&str_contains($css,'.device-phone')
  &&str_contains($css,'.pricing-card'),
 'mockups responsivos de notebook e celular');
expect_landing(str_contains($html,'<details')&&str_contains($html,'<summary'),
 'FAQ acessível sem JavaScript');
expect_landing(str_contains($html,'https://financeiro.naiaracora.com.br'),
 'entrada e acesso existentes mantidos');
expect_landing(str_contains($html,'rel="noopener noreferrer"'),
 'links externos seguros');
expect_landing(!str_contains($html,'APP_USR-')&&!str_contains($html,'X-CF-Catalog-Secret'),
 'nenhum segredo na página pública');
echo "OK: landing, backup, dois planos mensais, credibilidade e checkout intacto.\n";
