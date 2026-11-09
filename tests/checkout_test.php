<?php
declare(strict_types=1);
require dirname(__DIR__).'/private/checkout_test.php';
function ensure(bool $ok,string $message): void { if(!$ok)throw new RuntimeException($message); }
function fails(array $values,string $message): void {
 try { checkout_test_normalize($values); }catch(InvalidArgumentException $e){return;}
 throw new RuntimeException($message);
}
$valid=['plan'=>'familiar','method'=>'card','name'=>'Cliente de Teste','email'=>'cliente@example.com','ack'=>'1'];
$a=checkout_test_normalize($valid);
ensure($a['amount_cents']===1990,'Cartão deve usar R$ 19,90');
ensure(checkout_test_normalize([...$valid,'method'=>'pix'])['amount_cents']===2590,'Pix deve usar R$ 25,90');
ensure(count(checkout_test_catalog())===1,'Não inventar planos ainda não cadastrados');
ensure(checkout_test_money(1990)==='R$ 19,90','Formatação BRL');
ensure(checkout_test_escape('<script>')==='&lt;script&gt;','Escape HTML');
fails([...$valid,'plan'=>'empresarial'],'Não aceitar plano desconhecido');
fails([...$valid,'method'=>'external'],'Não aceitar método desconhecido');
fails([...$valid,'name'=>'a'],'Validar nome');
fails([...$valid,'email'=>'ruim'],'Validar email');
fails([...$valid,'ack'=>'0'],'Exigir confirmação de simulação');
echo "Checkout de homologação: validações e valores aprovados; nenhum pagamento executado.\n";
