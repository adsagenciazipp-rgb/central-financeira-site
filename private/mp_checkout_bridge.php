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
               &&preg_match('/^(?:MP_(PLAN|SUBSCRIPTION)_(HTTP_[1-5][0-9]{2}|FAILED)(_REASON_[a-z0-9_-]{2,40})?|MP_TEST_SELLER_(?:TOKEN_TYPE|ID_REQUIRED|ID_MISMATCH|NOT_TEST_USER|COUNTRY_MISMATCH|TOKEN_REJECTED|IDENTITY_UNAVAILABLE)|CENTRAL_(?:RATE_LIMITED|SANDBOX_DISABLED|GATEWAY_NOT_SANDBOX|TOKEN_MISSING|PLAN_UNAVAILABLE|REFERENCE_REUSED|TOKEN_INVALID|TRANSPORT_ERROR|DB_ERROR|INTERNAL_ERROR))$/D',$remote['code'])) {
                $code=$remote['code'];
            }
        }
        $instructions=[
            'MP_TEST_SELLER_TOKEN_TYPE'=>'O token atual não é do tipo exigido pelo produto Assinaturas. Use exclusivamente o Access Token de produção da CONTA VENDEDORA DE TESTE.',
            'MP_TEST_SELLER_ID_REQUIRED'=>'Falta informar commerce_mp_test_seller_user_id no config.php privado da Central.',
            'MP_TEST_SELLER_ID_MISMATCH'=>'O Access Token não pertence ao usuário vendedor de teste configurado.',
            'MP_TEST_SELLER_NOT_TEST_USER'=>'A conta associada ao token não foi identificada como conta de teste. Operação bloqueada.',
            'MP_TEST_SELLER_COUNTRY_MISMATCH'=>'A conta de teste do vendedor não pertence ao Brasil (MLB).',
            'MP_TEST_SELLER_TOKEN_REJECTED'=>'A verificação da identidade do token foi recusada pelo provedor.',
            'MP_TEST_SELLER_IDENTITY_UNAVAILABLE'=>'Não foi possível confirmar a identidade de teste do vendedor. Nenhuma assinatura foi iniciada.',
            'CENTRAL_RATE_LIMITED'=>'A Central permite no máximo três tentativas por hora, incluindo as recusadas pelo Mercado Pago. Aguarde até completar uma hora desde a primeira tentativa recente antes de testar novamente.',
            'CENTRAL_SANDBOX_DISABLED'=>'O modo de autorização Sandbox está desabilitado na Central. Não habilite cobranças reais.',
            'CENTRAL_GATEWAY_NOT_SANDBOX'=>'Confira se Mercado Pago está selecionado, ambiente Sandbox e cobrança online desativada na Central.',
            'CENTRAL_TOKEN_MISSING'=>'Não há Access Token cadastrado na Central.',
            'CENTRAL_PLAN_UNAVAILABLE'=>'O plano mensal com preço de cartão está indisponível na Central.',
            'CENTRAL_REFERENCE_REUSED'=>'A referência de checkout já foi usada. Recarregue o formulário.',
            'CENTRAL_TOKEN_INVALID'=>'O token armazenado não passou na validação de configuração.',
            'CENTRAL_TRANSPORT_ERROR'=>'O servidor da Central não conseguiu inicializar o transporte HTTP.',
            'CENTRAL_DB_ERROR'=>'A Central apresentou uma falha de banco de dados durante o teste; confira o log privado da Hostinger.',
            'CENTRAL_INTERNAL_ERROR'=>'A Central encontrou uma falha interna não identificada. Confira o log privado da Hostinger; não altere as credenciais automaticamente.',
        ];
        $help=$instructions[$code]??'';
        throw new RuntimeException('Não foi possível iniciar a autorização Sandbox. Diagnóstico: '.$code.($help!==''?' — '.$help:''));
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
