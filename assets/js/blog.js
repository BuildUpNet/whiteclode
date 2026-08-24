const BASE = '../';

fetch(BASE + "components/nav.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("nav").innerHTML = data;
    const logo = document.querySelector(".nav-logo img");
    if (logo) logo.src = BASE + "assets/images/white-cloud.png";
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
.catch(error => console.log("Error loading navbar:", error));

fetch(BASE + "components/footer.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("footer").innerHTML = data;
    const footerLogo = document.querySelector(".footer__logo-img");
    if (footerLogo) footerLogo.src = BASE + "assets/images/white-cloud.png";
})
.catch(error => console.log("Error loading footer:", error));

// ================= MONTH-WISE FILTERING LOGIC =================
const monthPills = document.querySelectorAll('.month-pill');
const placeCards = document.querySelectorAll('.place-card');

monthPills.forEach((pill) => {
  pill.addEventListener('click', () => {
    // 1. Remove active state from all pills
    monthPills.forEach((p) => p.classList.remove('active'));
    // 2. Add active state to clicked pill
    pill.classList.add('active');

    const selectedMonth = pill.getAttribute('data-month');

    // 3. Filter cards based on data-month attribute
    placeCards.forEach((card) => {
      const cardMonth = card.getAttribute('data-month');

      if (selectedMonth === 'all' || cardMonth === selectedMonth) {
        card.classList.remove('hide-card');
      } else {
        card.classList.add('hide-card');
      }
    });
  });
});


// ================= BLOG CATEGORY FILTER LOGIC =================
const filterPills = document.querySelectorAll('.filter-pill');
const blogCards = document.querySelectorAll('.grid-blog-card');

// Check karte hain ki elements page par present hain ya nahi
if (filterPills.length > 0 && blogCards.length > 0) {
  filterPills.forEach((pill) => {
    pill.addEventListener('click', () => {
      // 1. Sabhi category buttons se 'active' class hatayein
      filterPills.forEach((p) => p.classList.remove('active'));

      // 2. Click kiye gaye button par 'active' class lagayein
      pill.classList.add('active');

      const selectedCategory = pill.getAttribute('data-category');

      // 3. Cards ko category ke hisab se show / hide karein
      blogCards.forEach((card) => {
        const cardCategories = (card.getAttribute('data-category') || '').split(' ');

        if (selectedCategory === 'all' || cardCategories.includes(selectedCategory)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}
// ================= ITINERARY PLANNING SLIDER LOGIC =================
const itinTrack = document.getElementById('itinTrack');
const itinPrevBtn = document.getElementById('itinPrevBtn');
const itinNextBtn = document.getElementById('itinNextBtn');

if (itinTrack && itinPrevBtn && itinNextBtn) {
  const itinGap = 20;
  let isItinAnimating = false;

  function getItinStepWidth() {
    const cardWidth = itinTrack.firstElementChild.offsetWidth;
    return cardWidth + itinGap;
  }

  // Next Slide
  itinNextBtn.addEventListener('click', () => {
    if (isItinAnimating) return;
    isItinAnimating = true;

    const stepWidth = getItinStepWidth();
    itinTrack.style.transition = 'transform 0.4s ease-in-out';
    itinTrack.style.transform = `translateX(-${stepWidth}px)`;

    itinTrack.addEventListener('transitionend', function handleNext() {
      itinTrack.removeEventListener('transitionend', handleNext);
      itinTrack.appendChild(itinTrack.firstElementChild);
      itinTrack.style.transition = 'none';
      itinTrack.style.transform = 'translateX(0)';
      isItinAnimating = false;
    });
  });

  // Prev Slide
  itinPrevBtn.addEventListener('click', () => {
    if (isItinAnimating) return;
    isItinAnimating = true;

    const stepWidth = getItinStepWidth();
    const lastCard = itinTrack.lastElementChild;
    itinTrack.insertBefore(lastCard, itinTrack.firstElementChild);

    itinTrack.style.transition = 'none';
    itinTrack.style.transform = `translateX(-${stepWidth}px)`;
    itinTrack.offsetHeight; // Force reflow

    itinTrack.style.transition = 'transform 0.4s ease-in-out';
    itinTrack.style.transform = 'translateX(0)';

    itinTrack.addEventListener('transitionend', function handlePrev() {
      itinTrack.removeEventListener('transitionend', handlePrev);
      isItinAnimating = false;
    });
  });
}
// ================= VISA GUIDE SLIDER LOGIC =================
const visaTrack = document.getElementById('visaTrack');
const visaPrevBtn = document.getElementById('visaPrevBtn');
const visaNextBtn = document.getElementById('visaNextBtn');

if (visaTrack && visaPrevBtn && visaNextBtn) {
  const visaGap = 16;
  let isVisaAnimating = false;

  function getVisaStepWidth() {
    const cardWidth = visaTrack.firstElementChild.offsetWidth;
    return cardWidth + visaGap;
  }

  visaNextBtn.addEventListener('click', () => {
    if (isVisaAnimating) return;
    isVisaAnimating = true;

    const stepWidth = getVisaStepWidth();
    visaTrack.style.transition = 'transform 0.4s ease-in-out';
    visaTrack.style.transform = `translateX(-${stepWidth}px)`;

    visaTrack.addEventListener('transitionend', function handleNext() {
      visaTrack.removeEventListener('transitionend', handleNext);
      visaTrack.appendChild(visaTrack.firstElementChild);
      visaTrack.style.transition = 'none';
      visaTrack.style.transform = 'translateX(0)';
      isVisaAnimating = false;
    });
  });

  visaPrevBtn.addEventListener('click', () => {
    if (isVisaAnimating) return;
    isVisaAnimating = true;

    const stepWidth = getVisaStepWidth();
    const lastCard = visaTrack.lastElementChild;
    visaTrack.insertBefore(lastCard, visaTrack.firstElementChild);

    visaTrack.style.transition = 'none';
    visaTrack.style.transform = `translateX(-${stepWidth}px)`;
    visaTrack.offsetHeight;

    visaTrack.style.transition = 'transform 0.4s ease-in-out';
    visaTrack.style.transform = 'translateX(0)';

    visaTrack.addEventListener('transitionend', function handlePrev() {
      visaTrack.removeEventListener('transitionend', handlePrev);
      isVisaAnimating = false;
    });
  });
}