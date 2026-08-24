const BASE = '../';

/* ── Nav ── */
fetch(BASE + 'components/nav.html')
  .then(r => r.text())
  .then(html => {
    document.getElementById('nav').innerHTML = html;
    const logo = document.querySelector('.nav-logo img');
    if (logo) logo.src = BASE + 'assets/images/white-cloud.png';
    document.querySelectorAll('.nav-items a, .contact-btn').forEach(a => {
      const href = a.getAttribute('href');
      if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('../')) {
        a.setAttribute('href', BASE + href);
      }
    });
    // Customize navbar CTA text specifically for Tours page
    document.querySelectorAll('.contact-btn').forEach(btn => {
      btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Customize Tour';
    });
  })
  .catch(e => console.log('Nav load error:', e));

/* ── Footer ── */
fetch(BASE + 'components/footer.html')
  .then(r => r.text())
  .then(html => {
    document.getElementById('footer').innerHTML = html;
    const logo = document.querySelector('.footer__logo-img');
    if (logo) logo.src = BASE + 'assets/images/white-cloud.png';
  })
  .catch(e => console.log('Footer load error:', e));

/* ══════════════════════════════════════════════════════
   TOURS PAGE LOGIC
   ══════════════════════════════════════════════════════ */
/* ── Infinite carousel slider factory ── */
function makeSlider(trackId, prevId, nextId, gap) {
  const track   = document.getElementById(trackId);
  const prevBtn = document.getElementById(prevId);
  const nextBtn = document.getElementById(nextId);
  if (!track || !prevBtn || !nextBtn) return;

  let busy = false;

  function stepWidth() {
    return track.firstElementChild.offsetWidth + gap;
  }

  nextBtn.addEventListener('click', () => {
    if (busy) return; busy = true;
    track.style.transition = 'transform 0.4s ease-in-out';
    track.style.transform  = `translateX(-${stepWidth()}px)`;
    track.addEventListener('transitionend', function onEnd() {
      track.removeEventListener('transitionend', onEnd);
      track.appendChild(track.firstElementChild);
      track.style.transition = 'none';
      track.style.transform  = 'translateX(0)';
      busy = false;
    });
  });

  prevBtn.addEventListener('click', () => {
    if (busy) return; busy = true;
    track.insertBefore(track.lastElementChild, track.firstElementChild);
    track.style.transition = 'none';
    track.style.transform  = `translateX(-${stepWidth()}px)`;
    void track.offsetHeight;
    track.style.transition = 'transform 0.4s ease-in-out';
    track.style.transform  = 'translateX(0)';
    track.addEventListener('transitionend', function onEnd() {
      track.removeEventListener('transitionend', onEnd);
      busy = false;
    });
  });
}

makeSlider('cardsTrack',  'prevBtn',      'nextBtn',      20);
makeSlider('testiTrack',  'testiPrevBtn', 'testiNextBtn', 20);

/* ── Balloon scroll animation ── */
let isScrolling = false;
window.addEventListener('scroll', () => {
  if (!isScrolling) {
    isScrolling = true;
    requestAnimationFrame(() => {
      const section = document.getElementById('resortsSection');
      const balloon = document.getElementById('balloon');
      if (section && balloon) {
        const rect       = section.getBoundingClientRect();
        const winH       = window.innerHeight;
        const total      = section.offsetHeight + winH;
        const progress   = Math.min(Math.max((winH - rect.top) / total, 0), 1);
        const maxX       = section.offsetWidth  - balloon.offsetWidth  - 40;
        const maxY       = section.offsetHeight - balloon.offsetHeight - 40;

        // GPU-accelerated translate3d for smooth animations
        balloon.style.transform = `translate3d(${progress * maxX}px, ${progress * maxY}px, 0)`;
      }
      isScrolling = false;
    });
  }
});
