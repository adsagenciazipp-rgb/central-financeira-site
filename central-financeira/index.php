<?php
declare(strict_types=1);
/** Página comercial isolada de checkout, token, banco e assinatura. */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self' data:; base-uri 'none'; form-action 'none'; frame-ancestors 'self'");
$storeFamiliar='https://central.naiaracora.com.br/loja.php?perfil=familiar#planos';
$storeCompleto='https://central.naiaracora.com.br/loja.php?perfil=empresarial#planos';
$portal='https://financeiro.naiaracora.com.br/index.php?page=login';
function lp_escape(string $value):string {return htmlspecialchars($value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
?><!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#103c33">
<meta name="description" content="Central Financeira: gestão familiar e empresarial em uma plataforma simples. Assinaturas mensais a partir de R$ 19,90, com curso e videoaulas de gestão financeira.">
<meta property="og:title" content="Central Financeira | As contas não fecham? Organize o seu dinheiro">
<meta property="og:description" content="Veja para onde seu dinheiro está indo, acompanhe as contas e organize as finanças da família e do pequeno negócio.">
<meta property="og:type" content="website"><meta property="og:locale" content="pt_BR">
<link rel="canonical" href="https://naiaracora.com.br/central-financeira/">
<link rel="stylesheet" href="/central-financeira/assets/landing.css?v=4">
<title>Central Financeira — Gestão Familiar e Empresarial | Naiara Corá</title>
</head>
<body>
<a class="skip" href="#conteudo">Pular para o conteúdo</a>
<header class="site-header"><div class="container nav-shell">
  <a class="brand" href="#inicio" aria-label="Central Financeira — início">
    <span class="brand-symbol" aria-hidden="true">◈</span><span class="brand-copy"><strong>central financeira</strong><small>com Naiara Corá</small></span>
  </a>
  <nav class="nav-links" aria-label="Seções"><a href="#beneficios">Funcionalidades</a><a href="#depoimentos">Depoimentos</a><a href="#planos">Planos</a><a href="#faq">Dúvidas</a></nav>
  <a class="nav-login" href="<?=lp_escape($portal)?>" target="_blank" rel="noopener noreferrer">Já sou cliente ↗</a>
</div></header>
<main id="conteudo">
<?php require __DIR__.'/sections/hero.php'; ?>
<?php require __DIR__.'/sections/features.php'; ?>
<?php require __DIR__.'/sections/offer.php'; ?>
</main>
<footer class="site-footer"><div class="container footer-grid">
  <div><a class="brand" href="#inicio"><span class="brand-symbol">◈</span><span class="brand-copy"><strong>central financeira</strong><small>com Naiara Corá</small></span></a><p>Mais clareza para cuidar do que é importante.</p></div>
  <div class="footer-links"><a href="#beneficios">Funcionalidades</a><a href="#depoimentos">Depoimentos</a><a href="#planos">Planos</a><a href="#faq">Dúvidas</a></div>
  <div class="footer-links"><a href="<?=lp_escape($portal)?>" target="_blank" rel="noopener noreferrer">Entrar no sistema</a><a href="https://central.naiaracora.com.br/terms.php" target="_blank" rel="noopener noreferrer">Termos de uso</a><a href="https://central.naiaracora.com.br/privacy_policy.php" target="_blank" rel="noopener noreferrer">Privacidade</a></div>
</div><div class="container footer-bottom">© <?=date('Y')?> Central Financeira · Naiara Corá <span>Organize hoje. Viva melhor amanhã.</span></div></footer>
</body></html>