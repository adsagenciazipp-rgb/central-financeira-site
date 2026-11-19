'use strict';
document.addEventListener('DOMContentLoaded',()=>{
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
  const targets=[...document.querySelectorAll('.section-title-center, .feature-card, .problem-list>div, .walkthrough-steps li, .testimonial-card, .pricing-card, .bonus-tile, .cta-repeat-inner, .about-copy')];
  if(!('IntersectionObserver' in window))return;
  const observer=new IntersectionObserver(entries=>{
    for(const entry of entries)if(entry.isIntersecting){
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    }
  },{threshold:.12,rootMargin:'0px 0px -30px 0px'});
  targets.forEach(el=>{el.classList.add('reveal-pending');observer.observe(el);});
});
