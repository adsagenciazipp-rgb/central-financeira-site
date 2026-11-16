<?php
declare(strict_types=1);
$root=dirname(__DIR__);
ob_start(); require $root.'/central-financeira/index.php'; $html=(string)ob_get_clean();
$checks=['As contas não fecham?','para onde seu dinheiro está indo','O dinheiro acaba antes do mês?','Central Financeira:', 'R$ 19,90','R$ 34,90','videoaulas','Cliente de material anterior','Nomes não divulgados'];
foreach($checks as $check){if(!str_contains($html,$check)){throw new RuntimeException('V2: ausente: '.$check);}}
if(str_contains($html,'class="person-card"')){throw new RuntimeException('V2: foto pequena não removida');}
if(!str_contains($html,'https://central.naiaracora.com.br/loja.php#planos'))throw new RuntimeException('CTA loja ausente');
$css=file_get_contents($root.'/central-financeira/assets/landing.css').file_get_contents($root.'/central-financeira/assets/features.css');
if(!str_contains($css,'prefers-reduced-motion')||!str_contains($css,'.audience-list p'))throw new RuntimeException('V2: animação acessível ou contraste ausente');
echo "OK: V2 comercial, contrastes, CTA e animações acessíveis.\n";
