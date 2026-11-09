<?php
declare(strict_types=1);
require_once __DIR__.'/catalog_bridge.php';

/** Somente servidores trocam o segredo; o token do Mercado Pago nunca vem para este site. */
function site_mp_sandbox_start(array $settings,array $payload): array {
    if(($settings['mp_sandbox_checkout_enabled']??false)!==true)
        throw new RuntimeException('O teste de cartão ainda não está habilitado.');
    $secret=(string)($settings['mp_sandbox_checkout_secret']??'');
    $url=(string)($settings['mp_sandbox_checkout_url']??'');
    if($url!=='https://central.naiaracora.com.br/commerce_mp_sandbox_checkout.php'||strlen($secret)<32)
        throw new RuntimeException('Comunicação com a Central não está configurada.');
    if(!function_exists('curl_init'))
        throw new RuntimeException('Extensão cURL indisponível.');
    $ch=curl_init($url);
    if($ch===false)throw new RuntimeException('Não foi possível iniciar a comunicação.');
    try {
        curl_setopt_array($ch,[
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>json_encode($payload,JSON_THROW_ON_ERROR),
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_CONNECTTIMEOUT=>4,
            CURLOPT_TIMEOUT=>22,
            CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json',
                'X-CF-Checkout-Secret: '.$secret],
        ]);
        $body=curl_exec($ch);
        $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    } finally {curl_close($ch);}
    if($status!==200||!is_string($body)||strlen($body)>4096) {
        // Apenas códigos previamente previstos no backend são exibidos.
        // Nunca mostrar resposta bruta, cabeçalhos, credenciais ou e-mail.
        $code='CENTRAL_HTTP_'.$status;
        if(is_string($body)&&strlen($body)<=4096) {
            $remote=json_decode($body,true);
            if(is_array($remote)&&is_string($remote['code']??null)
               &&preg_match('/^MP_(PLAN|SUBSCRIPTION)_(HTTP_[1-5][0-9]{2}|FAILED)(_REASON_[a-z0-9_-]{2,40})?$/D',$remote['code'])) {
                $code=$remote['code'];
            }
        }
        throw new RuntimeException('Não foi possível iniciar a autorização Sandbox. Diagnóstico: '.$code);
    }
    try{$response=json_decode($body,true,16,JSON_THROW_ON_ERROR);}
    catch(JsonException $e){throw new RuntimeException('Resposta inválida da Central.');}
    if(!is_array($response)||!isset($response['checkout_url'])||!is_string($response['checkout_url']))
        throw new RuntimeException('Link de autorização não fornecido.');
    return $response;
}
function site_mp_checkout_url(string $url): string {
    $parts=parse_url($url);
    if(!is_array($parts)||($parts['scheme']??'')!=='https'
      ||!in_array(strtolower((string)($parts['host']??'')),
        ['mercadopago.com.br','www.mercadopago.com.br','www.mercadopago.com'],true)
      ||isset($parts['user'])||isset($parts['pass'])||isset($parts['port'])
      ||strlen($url)>1024)throw new RuntimeException('Destino de checkout não autorizado.');
    return $url;
}
