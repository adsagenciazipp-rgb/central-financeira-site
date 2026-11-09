<?php
declare(strict_types=1);

/**
 * Fluxo demonstrativo de checkout. NÃO cria pagamento, assinatura ou usuário.
 * Valores são referências de homologação e NÃO substituem preços da Central.
 */
function checkout_test_catalog(): array {
    return [
        'familiar' => [
            'name' => 'Plano Mensal Familiar',
            'description' => 'Financeiro Pessoal · até 2 integrantes · 1 espaço pessoal',
            'card' => 1990, 'pix' => 2590,
        ],
    ];
}
function checkout_test_normalize(array $post): array {
    $plans = checkout_test_catalog();
    $key = (string)($post['plan'] ?? '');
    $method = (string)($post['method'] ?? '');
    if (!array_key_exists($key, $plans)) {
        throw new InvalidArgumentException('Selecione um plano válido.');
    }
    if (!in_array($method, ['card','pix'], true)) {
        throw new InvalidArgumentException('Forma de pagamento inválida.');
    }
    $name = trim((string)($post['name'] ?? ''));
    $email = strtolower(trim((string)($post['email'] ?? '')));
    if (mb_strlen($name) < 3 || mb_strlen($name) > 120) {
        throw new InvalidArgumentException('Digite um nome de teste válido.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        throw new InvalidArgumentException('Digite um e-mail de teste válido.');
    }
    if (($post['ack'] ?? '') !== '1') {
        throw new InvalidArgumentException('Confirme que compreendeu a simulação.');
    }
    return [
        'plan' => $key, 'method' => $method,
        'name' => $name, 'email' => $email,
        'amount_cents' => $plans[$key][$method],
    ];
}
/** Identificador de login TESTUSER não é e-mail; aceitar somente e-mails de teste. */
function checkout_mp_test_email_valid(string $email): bool {
    $email=strtolower(trim($email));
    return filter_var($email, FILTER_VALIDATE_EMAIL)!==false
        && preg_match('/^[a-z0-9._%+-]+@testuser\.com$/D', $email)===1;
}

function checkout_test_money(int $cents): string {
    return 'R$ '.number_format($cents / 100, 2, ',', '.');
}
function checkout_test_escape(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
