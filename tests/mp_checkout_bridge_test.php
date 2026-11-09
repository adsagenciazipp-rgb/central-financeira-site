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
site_check(site_mp_checkout_url('https://www.mercadopago.com.br/subscriptions/checkout?preapproval_id=abc')!=='','Link Mercado Pago válido');
site_reject(fn()=>site_mp_checkout_url('https://mercadopago.com.br.evil.org'), 'Host malicioso');
site_reject(fn()=>site_mp_checkout_url('http://www.mercadopago.com.br'), 'HTTP não permitido');
echo "OK: opt-in, endpoint restrito e redirecionamento Mercado Pago validado.\n";
