<?php
declare(strict_types=1);
/**
 * Protótipo VISUAL e isolado de integração de assinatura.
 * Nunca recebe nem transmite cartão, CPF, senha, e-mail ou informação pessoal.
 * O fechamento real será integrado no Portal após homologação de API e conciliação.
 */
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, max-age=0');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; script-src 'self'; style-src 'self'; base-uri 'none'; form-action 'none'; frame-ancestors 'none'");
?>
<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#133e34">
<title>Prévia · Checkout Integrado da Central Financeira</title>
<link rel="stylesheet" href="/central-financeira/assets/checkout-integrado-preview.css">
</head><body>
<header class="top"><div class="wrap top-inner"><a href="/central-financeira/" class="brand">◈ <strong>Central Financeira</strong></a><span class="top-status">Prévia de homologação visual · sem cobrança</span></div></header>
<main class="wrap">
<div class="preview-note" role="status">Homologação no site principal: planos reais identificados como #3 (Familiar) e #4 (Familiar + Empresarial). Esta prévia não recebe dados reais, não cadastra contas e não realiza cobrança.</div>
<section class="intro" aria-label="Apresentação da contratação">
<div><span class="overline">ASSINATURA MENSAL · PLATAFORMA DE GESTÃO</span><h1>Seu financeiro organizado <em>começa aqui.</em></h1><p>Escolha o plano, crie sua senha e finalize o pagamento: tudo em uma única experiência.</p></div>
<div class="stepper" aria-label="Etapas da contratação"><div><span>01</span> Escolha seu plano</div><div><span>02</span> Crie seu acesso</div><div><span>03</span> Pague com segurança</div></div>
</section>
<div class="tab-bar" role="group" aria-label="Escolha do plano"><button type="button" class="tab selected" data-plan="familiar" data-plan-id="3" aria-pressed="true">Gestão Familiar</button><button type="button" class="tab" data-plan="empresarial" data-plan-id="4" aria-pressed="false">Familiar + Empresarial</button></div>
<section class="checkout-layout" aria-label="Detalhes do checkout">
<div class="product-side">
<span class="overline" id="product-label">PARA SUA VIDA PESSOAL</span>
<h2 id="product-name">Plano Mensal Familiar</h2><p class="plan-reference">Identificação do produto: <strong id="product-id">Plano #3</strong></p>
<p id="product-description">Controle os gastos da casa, os cartões e as contas dos próximos meses em uma plataforma simples.</p>
<div class="price"><strong id="product-price">R$ 19,90</strong><span>/ mês · cartão recorrente</span></div>
<ul id="product-benefits">
<li>Receitas, despesas e contas a pagar</li><li>Cartões, bancos e caixa</li><li>Metas, relatórios e previsão financeira</li><li>Curso e videoaulas como bônus</li>
</ul>
<div class="trust"><span aria-hidden="true">✓</span> Você define sua senha e utiliza o mesmo e-mail para entrar na plataforma.</div>
</div>
<div class="form-side">
<h2>Finalize sua assinatura</h2><p class="form-sub">Crie seu acesso e informe o cartão em uma única tela.</p>
<div class="section-title">01 · Seus dados e acesso</div>
<div class="fields"><label>Nome completo<div class="sample-field">Seu nome completo</div></label><label>CPF<div class="sample-field">000.000.000-00</div></label><label class="wide">E-mail de acesso<div class="sample-field">voce@exemplo.com</div></label><label class="wide">Crie sua senha<div class="sample-field">•••••••••••• <span>mínimo 10 caracteres</span></div></label></div>
<div class="section-title payment-title">02 · Cartão de crédito <small>Pagamento seguro Mercado Pago</small></div>
<div class="card-panel" aria-label="Exemplo de formulário seguro, desabilitado">
<div class="card-logos"><span>Cartão de crédito</span><span>VISA · MasterCard</span></div>
<div class="sample-card">Número do cartão <strong>•••• •••• •••• ••••</strong></div>
<div class="fields"><div class="sample-card">Validade <strong>MM / AA</strong></div><div class="sample-card">Código de segurança <strong>•••</strong></div><div class="sample-card wide">Nome no cartão <strong>Conforme impresso</strong></div></div>
</div>
<p class="legal"><span class="check-mock" aria-hidden="true">✓</span> Autorizo a cobrança mensal do plano escolhido. O acesso será liberado após a confirmação do pagamento.</p>
<button type="button" id="demo-finish" class="pay-button">Simular conclusão do plano #3 — sem cobrança</button>
<p class="footnote">Na versão final, os campos do cartão serão renderizados pelo Mercado Pago. Não armazenaremos número, validade ou CVV.</p>
<div id="demo-result" class="demo-result" role="status" aria-live="polite" hidden>Fluxo visual aprovado para demonstração. Nenhum pagamento, conta ou e-mail foi criado. A integração com o Mercado Pago será habilitada somente após testes completos.</div>
</div></section>
<footer class="footer"><span>Central Financeira · Checkout integrado em homologação</span><a href="/central-financeira/">Voltar à apresentação da plataforma</a></footer>
</main>
<script src="/central-financeira/assets/checkout-integrado-preview.js" defer></script>
</body></html>