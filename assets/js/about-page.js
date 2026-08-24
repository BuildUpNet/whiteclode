const BASE = '../';

/* ── Nav ── */
fetch(BASE + 'components/nav.html')
  .then(r => r.text())
  .then(html => {
    document.getElementById('nav').innerHTML = html;
    // Fix logo path
    const logo = document.querySelector('.nav-logo img');
    if (logo) logo.src = BASE + 'assets/images/white-cloud.png';
    // Fix all nav hrefs — prepend BASE so links work from pages/ subfolder
    document.querySelectorAll('.nav-items a, .contact-btn').forEach(a => {
      const href = a.getAttribute('href');
      if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('../')) {
        a.setAttribute('href', BASE + href);
      }
    });
  })
  .catch(e => console.log('Nav error:', e));

/* ── Footer ── */
fetch(BASE + 'components/footer.html')
  .then(r => r.text())
  .then(html => {
    document.getElementById('footer').innerHTML = html;
    const logo = document.querySelector('.footer__logo-img');
    if (logo) logo.src = BASE + 'assets/images/white-cloud.png';
  })
  .catch(e => console.log('Footer error:', e));

/* ── Animated counter for stats ── */
function animateCounter(el) {
  const target = parseInt(el.getAttribute('data-target'), 10);
  const duration = 1800;
  const step = target / (duration / 16);
  let current = 0;

  const timer = setInterval(() => {
    current += step;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }
    // Format: add + suffix for thousands
    el.textContent = target >= 1000
      ? Math.floor(current / 1000) + 'K+'
      : Math.floor(current) + (el.closest('.ab-stats__item').querySelector('span').textContent.startsWith('%') ? '' : '+');
  }, 16);
}

/* Trigger counters when stats section enters viewport */
const statsSection = document.querySelector('.ab-stats');
if (statsSection) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        document.querySelectorAll('.ab-stats__num').forEach(animateCounter);
        observer.disconnect();
      }
    });
  }, { threshold: 0.4 });
  observer.observe(statsSection);
}
