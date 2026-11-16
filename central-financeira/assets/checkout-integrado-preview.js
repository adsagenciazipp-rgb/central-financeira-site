'use strict';
(() => {
  const plans = Object.freeze({
    familiar: {
      label: 'PARA SUA VIDA PESSOAL',
      name: 'Plano Mensal Familiar',
      description: 'Controle os gastos da casa, os cartões e as contas dos próximos meses em uma plataforma simples.',
      price: 'R$ 19,90',
      benefits: ['Receitas, despesas e contas a pagar', 'Cartões, bancos e caixa', 'Metas, relatórios e previsão financeira', 'Curso e videoaulas como bônus']
    },
    empresarial: {
      label: 'PARA SUA CASA E SEU NEGÓCIO',
      name: 'Familiar + Empresarial',
      description: 'Organize o dinheiro da família e acompanhe receitas, despesas e recebíveis da empresa em espaços separados.',
      price: 'R$ 34,90',
      benefits: ['Tudo do plano Familiar', 'Receitas e despesas empresariais', 'Previsibilidade do caixa e mensalidades', 'Curso e videoaulas como bônus']
    }
  });
  const buttons = [...document.querySelectorAll('[data-plan]')];
  const get = id => document.getElementById(id);
  const result = get('demo-result');
  for (const b of buttons) b.addEventListener('click', () => {
    const key = b.dataset.plan;
    if (!Object.hasOwn(plans, key)) return;
    const p = plans[key];
    get('product-label').textContent = p.label;
    get('product-name').textContent = p.name;
    get('product-description').textContent = p.description;
    get('product-price').textContent = p.price;
    get('product-benefits').replaceChildren(...p.benefits.map(value => {
      const li = document.createElement('li'); li.textContent = value; return li;
    }));
    buttons.forEach(el => {el.classList.toggle('selected', el === b);el.setAttribute('aria-pressed', String(el === b));});
    result.hidden = true;
  });
  get('demo-finish')?.addEventListener('click', () => {
    result.hidden = false;
    result.scrollIntoView({ block: 'nearest', behavior: 'auto' });
  });
})();