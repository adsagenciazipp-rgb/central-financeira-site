'use strict';
(() => {
  const plans = Object.freeze({
    familiar: {
      id: 3,
      label: 'PARA SUA VIDA PESSOAL',
      name: 'Plano Mensal Familiar',
      description: 'Controle os gastos da casa, os cartões e as contas dos próximos meses em uma plataforma simples.',
      price: 'R$ 19,90',
      benefits: ['Receitas, despesas e contas a pagar', 'Cartões, bancos e caixa', 'Metas, relatórios e previsão financeira', 'Curso e videoaulas como bônus']
    },
    empresarial: {
      id: 4,
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
  const finish = get('demo-finish');
  const choosePlan = (key) => {
    const b = buttons.find(button => button.dataset.plan === key);
    if (!b) return;
    if (!Object.hasOwn(plans, key)) return;
    const p = plans[key];
    get('product-label').textContent = p.label;
    get('product-name').textContent = p.name;
    get('product-description').textContent = p.description;
    get('product-price').textContent = p.price;
    get('product-id').textContent = 'Plano #' + p.id;
    if (finish) finish.textContent = 'Simular conclusão do plano #' + p.id + ' — sem cobrança';
    get('product-benefits').replaceChildren(...p.benefits.map(value => {
      const li = document.createElement('li'); li.textContent = value; return li;
    }));
    buttons.forEach(el => {el.classList.toggle('selected', el === b);el.setAttribute('aria-pressed', String(el === b));});
    result.hidden = true;
  };
  for (const b of buttons) b.addEventListener('click', () => choosePlan(b.dataset.plan));
  const requested = new URLSearchParams(window.location.search).get('plano');
  choosePlan(requested === '4' ? 'empresarial' : 'familiar');
  get('demo-finish')?.addEventListener('click', () => {
    result.hidden = false;
    result.scrollIntoView({ block: 'nearest', behavior: 'auto' });
  });
})();