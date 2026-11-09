<?php
declare(strict_types=1);
require dirname(__DIR__).'/private/catalog_bridge.php';
function ensure_catalog(bool $value,string $msg):void {if(!$value)throw new RuntimeException($msg);}
$cases=[
 ['catalog_url'=>'http://central.naiaracora.com.br/commerce_site_catalog.php','catalog_secret'=>str_repeat('a',32)],
 ['catalog_url'=>'https://outro.site/commerce_site_catalog.php','catalog_secret'=>str_repeat('a',32)],
 ['catalog_url'=>'https://central.naiaracora.com.br/commerce_site_catalog.php?token=abc','catalog_secret'=>str_repeat('a',32)],
 ['catalog_url'=>'https://central.naiaracora.com.br/commerce_site_catalog.php','catalog_secret'=>'fraca'],
 [],
];
foreach($cases as $case){$result=site_catalog_fetch($case);ensure_catalog($result['ok']===false && $result['plans']===[], 'Conexão inválida deve falhar fechada');}
echo "OK: URL HTTPS restrita, credenciais não compartilhadas e falha fechada.\n";
