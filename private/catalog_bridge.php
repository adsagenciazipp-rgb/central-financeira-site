<?php
declare(strict_types=1);
/**
 * A comunicação fica exclusivamente no servidor e não entrega segredo ao navegador.
 * Uma falha de conexão não autoriza valores estáticos nem uma cobrança.
 */
function site_catalog_fetch(array $settings): array {
    $url=(string)($settings['catalog_url']??'');
    $secret=(string)($settings['catalog_secret']??'');
    $parts=parse_url($url);
    if(!is_array($parts)||($parts['scheme']??'')!=='https'
        ||($parts['host']??'')!=='central.naiaracora.com.br'
        ||($parts['path']??'')!=='/commerce_site_catalog.php'
        ||isset($parts['port'])||isset($parts['user'])||isset($parts['pass'])
        ||isset($parts['query'])||isset($parts['fragment'])
        ||strlen($secret)<32) {
        return ['ok'=>false,'plans'=>[]];
    }
    if(!function_exists('curl_init')) return ['ok'=>false,'plans'=>[]];
    $ch=curl_init($url);
    if($ch===false)return ['ok'=>false,'plans'=>[]];
    try {
        curl_setopt_array($ch,[
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_CONNECTTIMEOUT=>3,
            CURLOPT_TIMEOUT=>6,
            CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_HTTPHEADER=>['X-CF-Catalog-Secret: '.$secret,'Accept: application/json'],
        ]);
        $body=curl_exec($ch);
        $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    } finally {curl_close($ch);}
    if($status!==200 || !is_string($body)||strlen($body)>50000)return ['ok'=>false,'plans'=>[]];
    try{$data=json_decode($body,true,16,JSON_THROW_ON_ERROR);}catch(JsonException $e){return ['ok'=>false,'plans'=>[]];}
    if(!is_array($data)||($data['mode']??'')!=='sandbox_catalog_only'||!is_array($data['plans']??null))return ['ok'=>false,'plans'=>[]];
    $plans=[];
    foreach(array_slice($data['plans'],0,20) as $p){
        if(!is_array($p)||!is_int($p['id']??null)||$p['id']<1
            ||!is_string($p['name']??null)||trim($p['name'])===''
            ||!is_int($p['card_cents']??null)||$p['card_cents']<1
            ||!is_int($p['pix_cents']??null)||$p['pix_cents']<1
            ||!is_bool($p['public']??null))continue;
        $plans[]=$p;
    }
    return ['ok'=>true,'plans'=>$plans];
}
