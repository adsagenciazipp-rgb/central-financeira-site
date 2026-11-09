# Central Financeira — Site comercial

Site público e checkout da plataforma Central Financeira.

## Ambientes
- `main`: versão aprovada para o domínio principal, após homologação.
- `homologacao`: desenvolvimento e testes, **sem publicação automática no domínio principal**.

## Segurança do deploy
**NÃO conectar este repositório ao deploy da Hostinger até verificar a pasta de destino e a política de exclusão/sincronização.**

O domínio principal pode compartilhar a árvore de arquivos com:
- Central Administrativa: `central.naiaracora.com.br`
- Portal Financeiro: `financeiro.naiaracora.com.br`

As pastas físicas reais devem ser confirmadas na Hostinger. Não executar `rm -rf`, `rsync --delete`, limpeza do diretório raiz, nem deploy que sobrescreva pastas de subdomínios.

O site comercial **não** armazena Access Token, segredo HMAC, credenciais bancárias ou dados privados do Portal. A Central Administrativa permanece responsável por assinaturas e pagamentos.

Próxima etapa: definir uma pasta de publicação exclusiva para o site comercial, preservando os diretórios de Central e Portal, e construir uma base em PHP 8.3 com identidade visual provisória.
