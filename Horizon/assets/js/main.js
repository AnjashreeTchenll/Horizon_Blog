// Barre de progression de lecture
const barre = document.getElementById('progress');
if (barre) {
  const maj = () => {
    const haut = document.documentElement;
    const total = haut.scrollHeight - haut.clientHeight;
    barre.style.width = (total > 0 ? (haut.scrollTop / total) * 100 : 0) + '%';
  };
  window.addEventListener('scroll', maj, { passive: true });
  maj();
}

// Bouton « Copier le lien »
const bouton = document.getElementById('copier');
if (bouton) {
  bouton.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(window.location.href);
      bouton.textContent = 'Lien copié ✓';
    } catch (e) {
      bouton.textContent = 'Copie impossible';
    }
    setTimeout(() => (bouton.textContent = 'Copier le lien'), 2000);
  });
}

// Apparition en douceur au scroll
const cibles = document.querySelectorAll('.carte, .article, .apropos .container, .actus h2');
if ('IntersectionObserver' in window) {
  const obs = new IntersectionObserver((entrees) => {
    entrees.forEach((e) => {
      if (!e.isIntersecting) return;
      e.target.classList.add('visible');
      obs.unobserve(e.target);
      // On retire les classes ensuite pour ne pas gêner l'effet au survol
      setTimeout(() => e.target.classList.remove('reveal', 'visible'), 900);
    });
  }, { threshold: 0.15 });

  cibles.forEach((el, i) => {
    el.classList.add('reveal');
    el.style.transitionDelay = (i % 3) * 120 + 'ms';
    obs.observe(el);
  });
}