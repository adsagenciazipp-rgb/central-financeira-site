# Checkout integrado — homologação visual V1

## Objetivo
Uma única tela para escolher plano, definir acesso (nome, CPF, e-mail, senha),
autorizar a cobrança mensal do cartão via Mercado Pago e receber o link de acesso por e-mail.

## Entrega nesta branch
- Rota: /central-financeira/checkout-integrado-preview.php
- Layout responsivo, comparação entre Familiar e Familiar + Empresarial, valor recorrente, dados de acesso e bloco reservado a cartão.
- Prévia **sem inputs de dados reais**, sem solicitações de rede, sem armazenamento, sem SDK e sem pagamento. Apenas alternância de planos e visualização do resultado simulado.
- Não substituir CTAs da landing ou a loja real pela prévia.
- A aplicação usa noindex, CSP restritiva, no-store e frame-ancestors none.

## Arquitetura de produção (a implementar após aprovação visual)
1. Central Financeira mantém autoridade de preço, plano, status e conta recebedora; nunca confiar em valores do browser.
2. Portal cria o titular com e-mail único, senha com hash e aceite legal auditável; e-mail existente não é revelado por consulta pública e exige login.
3. SDK MercadoPago.js v2 com CardForm/Brick cria card_token_id de uso único; o browser nunca envia PAN, validade ou CVV ao Portal/Central.
4. Endpoint de cobrança HMAC, com ação específica, valida ref e titular, conta recebedora, preço, gateway, token e idempotência; POST /preapproval com card_token_id, status authorized e auto_recurring.frequency_type=months.
5. O registro remoto authorized **não libera acesso**. Buscar o pagamento da primeira fatura (/authorized_payments e /v1/payments/{id}), validar valor, moeda, vendedor, estorno e associação; atualizar Central/Portal somente depois de pagamento confirmado.
6. E-mail de boas-vindas enviado uma única vez após confirmação e acesso ativo. Nunca incluir senha no e-mail; apenas link HTTPS do Portal.
7. Se o pagamento for recusado ou ocorrer timeout, manter cadastro pendente e permitir nova tentativa após reconciliação, sem repetir POST de cobrança cegamente.
8. Guardar logs mínimos, nunca tokens, CPF ou payloads sensíveis; implantar cabeçalhos de segurança e revisão antifraude.
9. Testar sandbox com vendedor e comprador fictícios autorizados, repetição de webhooks, renovação, estorno, suspensão 5 dias, e-mail, login existente e falhas de integração.
10. Só habilitar cobrança real com aprovação explícita de produção, credenciais conferidas e teste completo.

## Limitação do estado atual
A API atual da Central mp_live_checkout_start usa status=pending e init_point para checkout hospedado. O Portal registra o usuário depois da loja. A prévia não modifica esse comportamento, nem faz uma cobrança silenciosa. O checkout integrado requer a rota nova de token, uma tela de cadastro combinada e conciliação de pagamento comprovado antes do lançamento.
