<?php
declare(strict_types=1);
require __DIR__.'/private/checkout_test.php';
require __DIR__.'/private/catalog_bridge.php';
require __DIR__.'/private/mp_checkout_bridge.php';
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, private');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Content-Security-Policy: default-src \'none\'; style-src \'self\'; img-src \'self\'; form-action \'self\'; base-uri \'none\'; frame-ancestors \'none\'');
session_start(['cookie_httponly'=>true, 'cookie_samesite'=>'Lax','use_strict_mode'=>true]);
if (empty($_SESSION['checkout_test_csrf'])) $_SESSION['checkout_test_csrf']=bin2hex(random_bytes(24));
$catalog=checkout_test_catalog();
$centralCatalog=['ok'=>false,'plans'=>[]];
$catalogDiag='CONFIG_FILE_MISSING';
if(is_file(__DIR__.'/config.php')) {
    $siteConfig=require __DIR__.'/config.php';
    if(is_array($siteConfig)) {
        $centralCatalog=site_catalog_fetch($siteConfig);
        $catalogDiag=(string)($centralCatalog['diagnostic']??'UNKNOWN');
    } else {$catalogDiag='CONFIG_NOT_ARRAY';}
}
$planKey=(string)($_POST['plan']??$_GET['plano']??'familiar');
if(!isset($catalog[$planKey]))$planKey='familiar';
$method=(string)($_POST['method']??'card');
if(!in_array($method,['card','pix'],true))$method='card';
$postedName=(string)($_POST['name']??'');
$postedEmail=(string)($_POST['email']??'');
$error='';$result=null;
$canMp=isset($siteConfig) && is_array($siteConfig) && ($siteConfig['mp_sandbox_checkout_enabled']??false)===true && $centralCatalog['ok'] && count($centralCatalog['plans'])>0;
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
    try{
        if (!hash_equals((string)$_SESSION['checkout_test_csrf'],(string)($_POST['csrf']??''))) {
            throw new RuntimeException('Sessão expirada. Atualize a página e tente novamente.');
        }
        $result=checkout_test_normalize($_POST);
        if(($_POST['checkout_action']??'')==='mp'){
            if(!$canMp || $result['method']!=='card')throw new RuntimeException('Cartão Sandbox não habilitado ou catálogo indisponível.');
            $selectedId=(int)($_POST['mp_plan_id']??0);
            $selected=null;
            foreach($centralCatalog['plans'] as $remotePlan){
                if($remotePlan['id']===$selectedId){$selected=$remotePlan;break;}
            }
            if(!$selected)throw new RuntimeException('Plano não encontrado na Central.');
            $reference=bin2hex(random_bytes(20));
            $response=site_mp_sandbox_start($siteConfig,[
                'plan_id'=>$selectedId,'method'=>'card','name'=>$result['name'],
                'email'=>$result['email'],'terms'=>'1','reference'=>$reference,
                'visitor'=>hash_hmac('sha256',(string)($_SERVER['REMOTE_ADDR']??'unknown'),(string)$siteConfig['catalog_secret']),
            ]);
            $_SESSION['checkout_test_csrf']=bin2hex(random_bytes(24));
            header('Location: '.site_mp_checkout_url((string)$response['checkout_url']),true,303);
            exit;
        }
        $_SESSION['checkout_test_csrf']=bin2hex(random_bytes(24));
    } catch (InvalidArgumentException|RuntimeException $e) {
        $error=$e->getMessage();
    }
}
?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive">
<title>Checkout de homologação | Central Financeira</title>
<link rel="stylesheet" href="assets/checkout.css">
</head><body>
<header class="header"><div class="shell header-inner"><a class="brand" href="index.php"><span class="brand-icon">C</span> Central Financeira</a><span class="environment">AMBIENTE DE HOMOLOGAÇÃO</span></div></header>
<main class="shell main">
<div class="notice" role="status"><strong>Simulação, sem cobrança.</strong> A simulação não cobra nem solicita dados do cartão. Se o modo de teste do Mercado Pago for habilitado, o checkout poderá redirecionar para autorização Sandbox; nenhuma assinatura do Portal será ativada automaticamente. Utilize apenas dados fictícios ou de teste.</div>
<?php if($result!==null):?>
<div class="success"><div class="check">✓</div><div><span class="step">TESTE CONCLUÍDO</span><h1>Fluxo demonstrativo validado</h1>
<p>Recebemos os dados apenas durante esta requisição. Nenhuma assinatura foi criada e nenhum pagamento foi realizado.</p>
<dl><div><dt>Plano</dt><dd><?=checkout_test_escape($catalog[$result['plan']]['name'])?></dd></div><div><dt>Modalidade</dt><dd><?=$result['method']==='card'?'Cartão recorrente':'Pix mensal manual'?></dd></div><div><dt>Valor ilustrativo</dt><dd><?=checkout_test_money($result['amount_cents'])?> / mês</dd></div></dl>
<a class="btn" href="checkout.php?plano=familiar">Testar novamente</a></div></div>
<?php else:?>
<div class="intro"><span class="step">ETAPA 1 DE 1 · FLUXO DE TESTE</span><h1>Teste do checkout</h1><p>Confira a estrutura de contratação antes de conectar o processamento seguro de pagamentos.</p></div>
<div class="layout">
<section class="panel"><h2>Dados para simulação</h2>
<?php if($error!==''):?><div class="error" role="alert"><?=checkout_test_escape($error)?></div><?php endif;?>
<form method="post" action="checkout.php" autocomplete="off">
<input type="hidden" name="csrf" value="<?=checkout_test_escape((string)$_SESSION['checkout_test_csrf'])?>">
<label class="field">Plano<select name="plan" required>
<?php foreach($catalog as $id=>$plan):?><option value="<?=checkout_test_escape($id)?>" <?=$planKey===$id?'selected':''?>><?=checkout_test_escape($plan['name'])?></option><?php endforeach;?>
</select></label>
<label class="field">Forma de pagamento<select name="method" required><option value="card" <?=$method==='card'?'selected':''?>>Cartão recorrente — referência R$ 19,90/mês</option><option value="pix" <?=$method==='pix'?'selected':''?>>Pix mensal — referência R$ 25,90/mês</option></select></label>
<label class="field">Nome fictício de teste<input name="name" type="text" required minlength="3" maxlength="120" value="<?=checkout_test_escape($postedName)?>" placeholder="Ex.: Cliente Teste"></label>
<label class="field">E-mail de teste<input name="email" type="email" required maxlength="190" value="<?=checkout_test_escape($postedEmail)?>" placeholder="teste@exemplo.com"></label>
<label class="agreement"><input name="ack" type="checkbox" value="1" required><span>Entendo que este é um teste de interface, sem pagamento, contrato, cobrança ou ativação de acesso.</span></label>
<button class="btn" type="submit" name="checkout_action" value="simulate">Simular continuidade <span aria-hidden="true">→</span></button>
<?php if($canMp):?>
<label class="field">Plano cadastrado na Central para o teste com Mercado Pago
<select name="mp_plan_id" required>
<?php foreach($centralCatalog['plans'] as $remotePlan):?><option value="<?=(int)$remotePlan['id']?>"><?=checkout_test_escape($remotePlan['name'])?> — <?=checkout_test_money($remotePlan['card_cents'])?>/mês</option><?php endforeach;?>
</select></label>
<button class="btn" type="submit" name="checkout_action" value="mp">Iniciar autorização de cartão no Mercado Pago Sandbox</button>
<p class="fineprint">Atenção: este botão abre o Mercado Pago e pode criar uma assinatura pendente no ambiente de testes. Não utilize cartão ou conta reais. Nenhum acesso é ativado pelo retorno do checkout.</p>
<?php endif;?>
</form>
<p class="fineprint">Na simulação, nenhum dado é armazenado. Ao iniciar a autorização Sandbox, nome e e-mail de teste são enviados à Central e o e-mail de teste ao Mercado Pago. O preço definitivo será consultado na Central quando a integração estiver homologada.</p>
</section>
<aside class="panel summary"><div class="summary-tag">Resumo de referência</div><h2>Plano Mensal Familiar</h2><p>Financeiro pessoal e familiar, para até dois integrantes e um espaço pessoal.</p><div class="divider"></div><div class="method"><span>Cartão recorrente</span><strong>R$ 19,90 / mês</strong></div><div class="method"><span>Pix mensal manual</span><strong>R$ 25,90 / mês</strong></div><div class="divider"></div><?php if($centralCatalog['ok']):?><p class="fineprint"><strong>Preços consultados na Central (somente leitura):</strong></p><?php if(count($centralCatalog['plans'])===0):?><p class="fineprint">A comunicação está funcionando, mas nenhum plano válido foi retornado. Confira se os preços foram salvos na Central, e não apenas preenchidos na tela. Se o diagnóstico for NO_ACTIVE_MONTHLY_PLANS, confira o ciclo de cobrança e se o plano está ativo.</p><?php if(($_GET['diagnostico']??'')==='1'):?><p class="fineprint"><strong>Diagnóstico:</strong> <?=checkout_test_escape($catalogDiag)?></p><?php endif;?><?php endif;?><?php foreach($centralCatalog['plans'] as $cp):?><div class="method"><span><?=checkout_test_escape($cp['name'])?></span><strong>Cartão <?=checkout_test_money($cp['card_cents'])?> · Pix <?=checkout_test_money($cp['pix_cents'])?></strong></div><?php endforeach;?><?php else:?><p class="fineprint">A conexão com os planos da Central ainda não foi configurada ou está indisponível.</p><?php if(($_GET['diagnostico']??'')==='1'):?><p class="fineprint" role="status"><strong>Diagnóstico de comunicação:</strong> <?=checkout_test_escape($catalogDiag)?></p><?php endif;?><?php endif;?><p class="fineprint">Valores do formulário demonstrativo são provisórios. A autorização Sandbox, quando habilitada, utiliza o preço validado pela Central e não ativa a assinatura no Portal.</p></aside>
</div>
<?php endif;?>
</main><footer class="shell footer">Central Financeira · Ambiente de testes · <a href="index.php">Voltar à página inicial</a></footer>
</body></html>
