# Homologação de assinaturas Mercado Pago — instruções de teste

## Estado atual
- A Central é a dona do plano, preço e Access Token criptografado.
- O site envia somente dados de teste à Central via HTTPS autenticado.
- A criação do plano e da assinatura pode ser testada com `commerce_mp_sandbox_checkout_enabled=true` em ambos os servidores **somente após conferir as credenciais e o ambiente**.
- **Acesso ao Portal, confirmação de pagamento e cobranças reais continuam desabilitados**. O retorno do checkout não é prova de aprovação.
- O `config.php` e os segredos não fazem parte do GitHub.

## Comprador de teste: usuário x e-mail
A conta de teste apresenta o campo **Usuário** (formato `TESTUSER...`) para login; isso **não é um e-mail**. A API de Assinaturas exige `payer_email` com formato de e-mail.

Na referência oficial do POST `/preapproval`, o exemplo é `test@testuser.com`. Use e-mail somente de teste, sem supor que o username gerado seja um e-mail. Se o provedor rejeitar, consulte as exigências específicas do produto Assinaturas antes de mudar a credencial de integração. Nunca use conta real ou cartão real.

Documentação oficial:
- https://www.mercadopago.com.br/developers/pt/docs/checkout-pro-preferences/test-accounts
- https://www.mercadopago.com.br/developers/pt/reference/online-payments/subscriptions/create-preapproval/post
- https://www.mercadopago.com.br/developers/pt/docs/subscriptions/integration-configuration/subscription-associated-plan

## Procedimento manual
1. Em **Contas de teste** do Mercado Pago, renovar a senha da conta compradora que tiver sido exposta em imagens ou compartilhamentos. Não compartilhar senha e código de verificação.
2. Conferir Central > Loja e cobranças > provedor **Mercado Pago**, ambiente **Sandbox**, API cadastrada. Não marcar produção.
3. Conferir que o Plano Mensal Familiar e os preços aparecem na seção "Preços consultados na Central" do checkout.
4. Usar dados fictícios, inclusive e-mail de teste válido, selecionar cartão recorrente, confirmar o termo e clicar no botão de autorização Mercado Pago.
5. Se houver erro, anotar somente o código (exemplo `MP_PLAN_HTTP_400` ou `MP_SUBSCRIPTION_HTTP_400`). Não enviar credenciais, endereço de redirecionamento completo ou dados de pagamento.
6. Se for aberta a autorização, conferir preço e ambiente; usar login da conta compradora de teste em janela anônima e apenas cartões de teste.
7. Na Central, consultar "Tentativas Mercado Pago — Sandbox". `pending_authorization` registra que o **link foi gerado**, e NÃO que houve pagamento.
8. Se houver retorno para `checkout_retorno.php`, não liberar acesso até implementar e homologar API de consulta, webhook, conferência de valor/assinatura e conciliação.

## Diagnóstico seguro
| Código | Interpretação |
|---|---|
| `MP_PLAN_HTTP_400` | API rejeitou criação de plano (conferir payload e credenciais) |
| `MP_SUBSCRIPTION_HTTP_400` | API rejeitou criar assinatura (conferir payer_email e modalidade de teste) |
| `MP_PLAN_HTTP_401` / `MP_SUBSCRIPTION_HTTP_401` | Autenticação do token remoto recusada |
| `MP_PLAN_HTTP_403` / `MP_SUBSCRIPTION_HTTP_403` | Credencial sem autorização para a operação |
| `MP_PLAN_FAILED` / `MP_SUBSCRIPTION_FAILED` | Erro sem resposta HTTP classificada |
| `CENTRAL_HTTP_503` | Requisição falhou antes da resposta remota classificada |
Nunca alterar para credencial de produção apenas para evitar esses códigos. Exigir investigação conforme o serviço efetivamente contratado.

## Requisitos restantes para produção
Verificação independente do status remoto, webhook autenticado, conciliação, proteção contra duplicação de eventos, testes de pagamento aprovado/recusado/cancelado, suspensão segura e sincronização Central→Portal. Esses passos ainda não foram homologados.
