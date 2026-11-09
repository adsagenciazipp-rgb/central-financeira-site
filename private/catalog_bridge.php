<?php
declare(strict_types=1);
/**
 * A comunicação fica exclusivamente no servidor e não entrega segredo ao navegador.
 * Uma falha de conexão não autoriza valores estáticos nem uma cobrança.
 */
function site_catalog_fetch(array $settings): array {
    $fail=static function(string $code): array {
        error_log('Central catalog bridge diagnostic: '.$code);
        return ['ok'=>false,'plans'=>[],'diagnostic'=>$code];
    };
    $url=(string)($settings['catalog_url']??'');
    $secret=(string)($settings['catalog_secret']??'');
    $parts=parse_url($url);
    if(!is_array($parts)||($parts['scheme']??'')!=='https'
        ||($parts['host']??'')!=='central.naiaracora.com.br'
        ||($parts['path']??'')!=='/commerce_site_catalog.php'
        ||isset($parts['port'])||isset($parts['user'])||isset($parts['pass'])
        ||isset($parts['query'])||isset($parts['fragment'])
        ||strlen($secret)<32) {
        return $fail(strlen($secret)<32 ? 'CONFIG_SECRET_MISSING' : 'CONFIG_URL_INVALID');
    }
    if(!function_exists('curl_init')) return $fail('CURL_UNAVAILABLE');
    $ch=curl_init($url);
    if($ch===false)return $fail('CURL_INIT_FAILED');
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
        $curlError=$body===false?(int)curl_errno($ch):0;
    } finally {curl_close($ch);}
    if($curlError!==0)return $fail('CURL_ERROR_'.$curlError);
    if($status===403) {
        // Identifica apenas a origem do bloqueio, sem expor segredo ou corpo HTTP.
        $response=is_string($body)&&strlen($body)<2048?json_decode($body,true):null;
        if(is_array($response)&&($response['error']??null)==='Não autorizado')
            return $fail('AUTH_REJECTED_BY_CENTRAL');
        return $fail('HTTP_403_BEFORE_CENTRAL_OR_UNEXPECTED');
    }
    if($status!==200)return $fail('HTTP_'.$status);
    if(!is_string($body)||strlen($body)>50000)return $fail('RESPONSE_INVALID_SIZE');
    try{$data=json_decode($body,true,16,JSON_THROW_ON_ERROR);}catch(JsonException $e){return $fail('INVALID_JSON');}
    if(!is_array($data)||($data['mode']??'')!=='sandbox_catalog_only'||!is_array($data['plans']??null))return $fail('INVALID_RESPONSE_SCHEMA');
    $plans=[];
    foreach(array_slice($data['plans'],0,20) as $p){
        if(!is_array($p)||!is_int($p['id']??null)||$p['id']<1
            ||!is_string($p['name']??null)||trim($p['name'])===''
            ||!is_int($p['card_cents']??null)||$p['card_cents']<1
            ||!is_int($p['pix_cents']??null)||$p['pix_cents']<1
            ||!is_bool($p['public']??null))continue;
        $plans[]=$p;
    }
    $remoteDiagnostic=(string)($data['diagnostic']??'');
    $allowed=['OK','PRICES_NOT_SAVED','NO_ACTIVE_MONTHLY_PLANS','NO_ACTIVE_PLANS'];
    $diagnostic=count($plans)>0?'OK':(
        count($data['plans'])===0 && in_array($remoteDiagnostic,$allowed,true)
        ? $remoteDiagnostic
        : (count($data['plans'])===0?'EMPTY_CATALOG':'INVALID_PLAN_DATA')
    );
    return ['ok'=>true,'plans'=>$plans,'diagnostic'=>$diagnostic];
}
