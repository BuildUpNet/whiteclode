const BASE = '../';

/* ── Nav ── */
fetch(BASE + 'components/nav.html')
  .then(r => r.text())
  .then(html => {
    document.getElementById('nav').innerHTML = html;
    const logo = document.querySelector('.nav-logo img');
    if (logo) logo.src = BASE + 'assets/images/white-cloud.png';
    document.querySelectorAll('.nav-items a, .contact-btn, .top-bar-link').forEach(a => {
      const href = a.getAttribute('href');
      if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('../') && !href.startsWith('tel:')) {
        a.setAttribute('href', BASE + href);
      }
    });
    
    // Tours Mobile Dropdown Toggle
    const dropdownToggle = document.querySelector('.nav-item-dropdown .dropdown-toggle');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                const dropdownItem = this.closest('.nav-item-dropdown');
                if (dropdownItem) dropdownItem.classList.toggle('open');
            }
        });
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            const dropdownItem = document.querySelector('.nav-item-dropdown');
            if (dropdownItem) dropdownItem.classList.remove('open');
        }
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
   BLOG PAGE LOGIC
   ══════════════════════════════════════════════════════ */

/* ── Month-wise place filter ── */
const monthPills = document.querySelectorAll('.month-pill');
const placeCards = document.querySelectorAll('.place-card');

monthPills.forEach(pill => {
  pill.addEventListener('click', () => {
    monthPills.forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    const selected = pill.getAttribute('data-month');
    placeCards.forEach(card => {
      const month = card.getAttribute('data-month');
      card.classList.toggle('hide-card', selected !== 'all' && month !== selected);
    });
  });
});

/* ── Blog category filter ── */
const filterPills = document.querySelectorAll('.filter-pill');
const blogCards  = document.querySelectorAll('.grid-blog-card');

if (filterPills.length && blogCards.length) {
  filterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      filterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      const selected = pill.getAttribute('data-category');
      blogCards.forEach(card => {
        const cats = (card.getAttribute('data-category') || '').split(' ');
        card.style.display = (selected === 'all' || cats.includes(selected)) ? 'flex' : 'none';
      });
    });
  });
}

/* ── Generic infinite slider factory ── */
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

makeSlider('itinTrack', 'itinPrevBtn', 'itinNextBtn', 20);
makeSlider('visaTrack',  'visaPrevBtn',  'visaNextBtn',  16);
