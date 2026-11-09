<?php
declare(strict_types=1);
require dirname(__DIR__).'/private/mp_checkout_bridge.php';
function site_check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function site_reject(callable $f,string $message):void {
    try{$f();}catch(RuntimeException $e){return;}
    throw new RuntimeException($message);
}
site_reject(fn()=>site_mp_sandbox_start([],[]),'Flag padrão não impede requisições externas');
site_reject(fn()=>site_mp_sandbox_start(['mp_sandbox_checkout_enabled'=>true],[]),'Credenciais ausentes');
site_reject(fn()=>site_mp_sandbox_start(['mp_sandbox_checkout_enabled'=>true,'mp_sandbox_checkout_url'=>'https://central.naiaracora.com.br/commerce_mp_sandbox_checkout.php','catalog_secret'=>str_repeat('a',48)],[]),'Chave de catálogo não pode autorizar pagamento');
site_check(site_mp_checkout_url('https://www.mercadopago.com.br/subscriptions/checkout?preapproval_id=abc')!=='','Link Mercado Pago válido');
site_reject(fn()=>site_mp_checkout_url('https://mercadopago.com.br.evil.org'), 'Host malicioso');
site_reject(fn()=>site_mp_checkout_url('http://www.mercadopago.com.br'), 'HTTP não permitido');

$bridgeSource=file_get_contents(dirname(__DIR__).'/private/mp_checkout_bridge.php');
$checkoutSource=file_get_contents(dirname(__DIR__).'/checkout.php');
if(!is_string($bridgeSource)||!str_contains($bridgeSource,'mp_sandbox_checkout_secret')||!str_contains($bridgeSource,'X-CF-Checkout-Secret')||!str_contains($bridgeSource,'MP_(PLAN|SUBSCRIPTION)')||!str_contains($bridgeSource,'Diagnóstico:'))throw new RuntimeException('Código de diagnóstico MP seguro ausente');
if(!is_string($checkoutSource)||!str_contains($checkoutSource,'TESTUSER...')||!str_contains($checkoutSource,'payer_email'))throw new RuntimeException('Orientação sobre login e email ausente');
if(!str_contains($bridgeSource,'_REASON_'))throw new RuntimeException('Bridge deve reconhecer código técnico de validação remoto');
if(!str_contains($bridgeSource,'CENTRAL_RATE_LIMITED')
  || !str_contains($bridgeSource,'CENTRAL_DB_ERROR')
  || !str_contains($bridgeSource,'três tentativas por hora'))
    throw new RuntimeException('Checkout deve distinguir HTTP 429 de erro 503 do servidor');
if(!str_contains($bridgeSource,'MP_TEST_SELLER_')||!str_contains($bridgeSource,'TOKEN_TYPE')||!str_contains($bridgeSource,'NOT_TEST_USER'))
    throw new RuntimeException('Erros de credencial da conta vendedora de teste não estão protegidos e reconhecidos');
if(!str_contains($checkoutSource,'Iniciar assinatura pendente no Mercado Pago (teste)'))throw new RuntimeException('Checkout deve informar status pendente');
if(!str_contains($checkoutSource,'Apenas simular (sem Mercado Pago)'))throw new RuntimeException('Simulação não distinguida da autorização');
if(str_contains($checkoutSource,'?>"" type="submit" name="checkout_action" value="simulate"'))throw new RuntimeException('Aspas extras no botão de simulação');
echo "OK: opt-in, endpoint restrito e redirecionamento Mercado Pago validado.\n";
