<?php
declare(strict_types=1);
/**
 * Capturas fornecidas pelo titular, em seis miniaturas compactadas em um único
 * WebP. A verificação de integridade impede publicar imagens incompletas.
 * Código do site sem upload/armazenamento de dados financeiros privados.
 */
$parts=['base64-1.txt','base64-2.txt','base64-3.txt','base64-4.txt','base64-5.txt'];
$encoded='';
foreach($parts as $file){
    $raw=@file_get_contents(__DIR__.'/sprites/'.$file);
    if(!is_string($raw)){http_response_code(503);exit;}
    $encoded.=$raw;
}
$binary=base64_decode($encoded,true);
if(!is_string($binary)||hash('sha256',$binary)!=='09d855677055c6deefcfaefb1e62f3e9fe447da646ebe029acb0a7e4035d90c0'){
    http_response_code(503);exit;
}
header('Content-Type: image/webp');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=86400');
echo $binary;
