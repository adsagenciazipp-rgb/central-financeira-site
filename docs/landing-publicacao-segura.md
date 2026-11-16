# Landing comercial — /central-financeira/

## Status e escopo
A landing está no diretório `central-financeira/` deste repositório.
A página raiz `index.php` **não foi alterada**; há também backup exato em
`backups/em-breve-2026-11-16.php`.

A nova página é apenas visual; não conecta ao MySQL, não grava leads, não
aciona pagamentos e não possui tokens. Os dois CTAs comerciais vão para
`https://central.naiaracora.com.br/loja.php#planos`, que controla o
catálogo real, a vinculação de titular e a autorização de cobrança.

## Destino pretendido
`https://naiaracora.com.br/central-financeira/`

Antes de configurar deploy da Hostinger:
1. Confirmar a **pasta raiz** associada ao domínio `naiaracora.com.br`.
2. Confirmar os diretórios físicos dos subdomínios `central.` e `financeiro.`
   e garantir que o novo deploy nunca os inclui.
3. Confirmar que a pasta `central-financeira/` não contém conteúdo de
   outro site/WordPress; fazer backup caso já exista.
4. Publicar **somente** o conteúdo desta pasta em
   `<document-root-do-dominio>/central-financeira/`.
5. **Não publicar o repositório inteiro na raiz** nem usar comandos com
   remoção automática (`rm -rf`, `rsync --delete`, limpar/publicar de forma
   destrutiva); preservar `index.php` do domínio e outros sites.
6. Conferir `/central-financeira/`, `/central-financeira/assets/landing.css`,
   `/central-financeira/assets/naiara.svg` e responsividade; testar
   navegação e CTAs sem efetuar uma cobrança.
7. Se o domínio estiver servido por outro site/WordPress, configure uma
   rota/pasta isolada ou regra do servidor antes da publicação.

## Produtos e preços
- Gestão Familiar: **R$ 19,90/mês**.
- Familiar + Empresarial: **R$ 34,90/mês**.
- Curso de gestão financeira, videoaulas e materiais complementares são
  apresentados como bônus nos dois planos.

Os valores exibidos refletem a solicitação comercial, **não** uma leitura
da tabela de preços real da Central. Antes do lançamento, confira se ambos os
planos estão ativos e públicos no catálogo da Central, preços aprovados e
benefícios efetivamente disponíveis. Não habilite cobranças reais para realizar
a revisão visual.

Os depoimentos foram extraídos/reduzidos das referências da antiga página
de planilhas; são identificados na landing como referentes aos materiais
anteriores, não à nova plataforma.

A foto da Naiara foi recortada de uma captura enviada para referência.
É uma versão compactada; para qualidade final profissional, substituir o
arquivo `central-financeira/assets/naiara.svg` por foto original autorizada.

Os mockups foram compostos em HTML/CSS a partir da estrutura e identidade
visual do sistema e explicitamente identificados como **prévia ilustrativa**.
Após validação, trocar por capturas de tela reais higienizadas (sem dados
pessoais ou financeiros).

## Teste automatizado
`php tests/landing_page_test.php`.

O CI valida sintaxe PHP, backup da home, isolamento do checkout,
estrutura da landing e presença das mídias. A publicação só é permitida
após aprovação dos testes e confirmação do destino no hPanel.
