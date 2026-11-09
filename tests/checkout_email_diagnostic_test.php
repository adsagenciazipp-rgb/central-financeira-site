<?php
declare(strict_types=1);
// GET público somente de diagnóstico local. Não usa banco, tokens ou e-mails reais.
$_GET=['diagnostico'=>'1'];
$_SERVER['REQUEST_METHOD']='GET';
ob_start();
require dirname(__DIR__).'/checkout.php';
$html=(string)ob_get_clean();
if(!str_contains($html,'EMAIL-V2'))throw new RuntimeException('Identificador de versão do validador ausente.');
if(!str_contains($html,'autoteste do formato testuser123456789@testuser.com:'))throw new RuntimeException('Autoteste não foi renderizado.');
if(!preg_match('/autoteste do formato testuser123456789@testuser\.com:\s*<strong>APROVADO<\/strong>/s',$html))
    throw new RuntimeException('O exemplo legítimo deve ser aprovado no servidor.');
$sensitiveKeys=['commerce_'.'master_key','catalog_'.'secret','mp_sandbox_'.'checkout_secret'];
foreach($sensitiveKeys as $sensitiveKey) {
    if(str_contains($html,$sensitiveKey)) throw new RuntimeException('Página de diagnóstico não pode exibir nomes de segredos.');
}
echo "OK: versão do validador e autoteste aprovados, sem dados sigilosos.\n";
