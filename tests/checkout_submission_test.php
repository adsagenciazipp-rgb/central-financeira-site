<?php
declare(strict_types=1);
// Teste de integração do formulário, sem rede e sem configurações privadas.
// O checkout só pode mostrar sucesso para a ação SIMULATE.
if(is_file(dirname(__DIR__).'/config.php'))throw new RuntimeException('Não executar teste com configuração real.');
$action=$argv[1]??'';
if(!in_array($action,['simulate','mp','unknown'],true))throw new RuntimeException('Modo de teste inválido.');
session_id('checkoutqa'.bin2hex(random_bytes(6)));
session_start();
$_SESSION['checkout_test_csrf']=str_repeat('a',48);
session_write_close();
$_SERVER['REQUEST_METHOD']='POST';
$_SERVER['REMOTE_ADDR']='192.0.2.20';
$_POST=[
    'csrf'=>str_repeat('a',48),
    'plan'=>'familiar','method'=>'card',
    'name'=>'Cliente Teste','email'=>'testuser123456789@testuser.com',
    'ack'=>'1','checkout_action'=>$action,
];
ob_start();
require dirname(__DIR__).'/checkout.php';
$html=(string)ob_get_clean();
$success=str_contains($html,'Fluxo demonstrativo validado');
if($action==='simulate') {
    if(!$success)throw new RuntimeException('Simulação válida não exibiu sucesso.');
} else {
    if($success)throw new RuntimeException('Ação não simulada exibiu sucesso falso.');
    if($action==='mp'&&!str_contains($html,'Cartão Sandbox não habilitado ou catálogo indisponível.'))
        throw new RuntimeException('Falha de autorização não retornou erro ao formulário.');
    if($action==='unknown'&&!str_contains($html,'Selecione uma ação válida para continuar.'))
        throw new RuntimeException('Ação desconhecida não foi rejeitada.');
}
echo "OK: ".$action." não produz sucesso falso.\n";
