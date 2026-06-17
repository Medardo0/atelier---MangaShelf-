/* MangaShelf - main.js */
(function () {
  'use strict';

  /* Hero slider */
  const slider = document.querySelector('.hero-slider');
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll('.hero-slide'));
  const dots   = Array.from(slider.querySelectorAll('.hero-dot'));
  const total  = slides.length;
  let current  = 0;
  let timer;
  let animating = false;

  function goTo(n) {
    if (animating) return;
    const next = ((n % total) + total) % total;
    if (next === current) return;

    animating = true;
    const prev = current;
    current = next;

    /* Outgoing slide */
    slides[prev].classList.remove('active');
    slides[prev].classList.add('exit');

    /* Incoming slide */
    slides[current].classList.remove('exit');
    slides[current].classList.add('active');

    /* Cleanup after transition */
    setTimeout(() => {
      slides[prev].classList.remove('exit');
      animating = false;
    }, 580);

    /* Dots */
    dots.forEach((dot, i) => {
      const active = i === current;
      dot.classList.toggle('active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  }

  function startAuto() {
    clearInterval(timer);
    timer = setInterval(() => goTo(current + 1), 5000);
  }

  slider.querySelector('.hero-prev').addEventListener('click', () => { goTo(current - 1); startAuto(); });
  slider.querySelector('.hero-next').addEventListener('click', () => { goTo(current + 1); startAuto(); });
  dots.forEach((d, i) => d.addEventListener('click', () => { goTo(i); startAuto(); }));

  /* Pause au survol */
  slider.addEventListener('mouseenter', () => clearInterval(timer));
  slider.addEventListener('mouseleave', startAuto);

  /* Touch swipe */
  let touchX = 0;
  slider.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
  slider.addEventListener('touchend', e => {
    const diff = touchX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) { goTo(current + (diff > 0 ? 1 : -1)); startAuto(); }
  });

  startAuto();
}());
