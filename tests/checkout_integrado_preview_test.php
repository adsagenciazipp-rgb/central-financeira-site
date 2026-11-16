<?php
declare(strict_types=1);
$root=dirname(__DIR__);
ob_start();
require $root.'/central-financeira/checkout-integrado-preview.php';
$html=(string)ob_get_clean();
function previewCheck(bool $result,string $note):void {if(!$result)throw new RuntimeException('PREVIEW CHECKOUT: '.$note);}
foreach(['noindex,nofollow','sem cobrança','Plano Mensal Familiar','R$ 19,90','data-plan="empresarial"','Crie sua senha','Cartão de crédito','Mercado Pago','não há cadastro','demo-finish'] as $needle)
 previewCheck(str_contains($html,$needle),'falta '.$needle);
$phpSource=(string)file_get_contents($root.'/central-financeira/checkout-integrado-preview.php');
previewCheck(str_contains($phpSource,"form-action 'none'") && str_contains($phpSource,"frame-ancestors 'none'"),'CSP segura no servidor');
$js=(string)file_get_contents($root.'/central-financeira/assets/checkout-integrado-preview.js');
$css=(string)file_get_contents($root.'/central-financeira/assets/checkout-integrado-preview.css');
previewCheck(str_contains($js,'R$ 34,90') && str_contains($js,'R$ 19,90'),'dois preços no preview');
previewCheck(str_contains($js,'textContent') && str_contains($js,'replaceChildren'),'troca dos benefícios sem HTML dinâmico');
foreach(['fetch(', 'XMLHttpRequest', 'sendBeacon', 'localStorage','sessionStorage','navigator.credentials','api.mercadopago.com'] as $danger)
 previewCheck(!str_contains($js,$danger),'preview não pode transmitir dados: '.$danger);
previewCheck(!preg_match('/<input\b|<form\b/i',$html),'preview não deve aceitar CPF, senha ou cartão real');
previewCheck(str_contains($css,'@media(max-width:760px)') && str_contains($css,'prefers-reduced-motion'),'responsive e animação reduzida');
echo "OK: prévia de checkout segura, isolada e responsiva.\n";