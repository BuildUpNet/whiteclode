fetch("components/nav.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("nav").innerHTML=data;
    
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
.catch(error=>{
     console.log("Error loading navbar:", error)
});

fetch("components/footer.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("footer").innerHTML=data;
})
.catch(error=>{
     console.log("Error loading footer:", error)
});

// ================= 1. INFINITE CAROUSEL SLIDER LOGIC =================
const track = document.getElementById('cardsTrack');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

const gap = 20;
let isAnimating = false;

function getStepWidth() {
  const cardWidth = track.firstElementChild.offsetWidth;
  return cardWidth + gap;
}

nextBtn.addEventListener('click', () => {
  if (isAnimating) return;
  isAnimating = true;

  const stepWidth = getStepWidth();
  track.style.transition = 'transform 0.4s ease-in-out';
  track.style.transform = `translateX(-${stepWidth}px)`;

  track.addEventListener('transitionend', function handleNext() {
    track.removeEventListener('transitionend', handleNext);
    track.appendChild(track.firstElementChild);
    track.style.transition = 'none';
    track.style.transform = 'translateX(0)';
    isAnimating = false;
  });
});

prevBtn.addEventListener('click', () => {
  if (isAnimating) return;
  isAnimating = true;

  const stepWidth = getStepWidth();
  const lastCard = track.lastElementChild;
  track.insertBefore(lastCard, track.firstElementChild);

  track.style.transition = 'none';
  track.style.transform = `translateX(-${stepWidth}px)`;
  track.offsetHeight; // Force reflow

  track.style.transition = 'transform 0.4s ease-in-out';
  track.style.transform = 'translateX(0)';

  track.addEventListener('transitionend', function handlePrev() {
    track.removeEventListener('transitionend', handlePrev);
    isAnimating = false;
  });
});

// ================= 2. DIAGONAL PARACHUTE SCROLL LOGIC =================
let isScrolling = false;

window.addEventListener('scroll', () => {
  if (!isScrolling) {
    isScrolling = true;
    requestAnimationFrame(() => {
      const section = document.getElementById('resortsSection');
      const balloon = document.getElementById('balloon');

      if (section && balloon) {
        const sectionRect = section.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (sectionRect.top < windowHeight && sectionRect.bottom > 0) {
          const totalDistance = sectionRect.height + windowHeight;
          const progress = (windowHeight - sectionRect.top) / totalDistance;
          const clampedProgress = Math.min(Math.max(progress, 0), 1);

          const maxX = section.offsetWidth - balloon.offsetWidth - 40;
          const maxY = section.offsetHeight - balloon.offsetHeight - 40;

          const moveX = clampedProgress * maxX;
          const moveY = clampedProgress * maxY;

          // GPU-accelerated translate3d for smooth animations
          balloon.style.transform = `translate3d(${moveX}px, ${moveY}px, 0)`;
        }
      }
      isScrolling = false;
    });
  }
});

// ================= TESTIMONIALS SLIDER LOGIC =================
const testiTrack = document.getElementById('testiTrack');
const testiPrevBtn = document.getElementById('testiPrevBtn');
const testiNextBtn = document.getElementById('testiNextBtn');

const testiGap = 20;
let isTestiAnimating = false;

function getTestiStepWidth() {
  const cardWidth = testiTrack.firstElementChild.offsetWidth;
  return cardWidth + testiGap;
}

// Next Slide
testiNextBtn.addEventListener('click', () => {
  if (isTestiAnimating) return;
  isTestiAnimating = true;

  const stepWidth = getTestiStepWidth();
  testiTrack.style.transition = 'transform 0.4s ease-in-out';
  testiTrack.style.transform = `translateX(-${stepWidth}px)`;

  testiTrack.addEventListener('transitionend', function handleNext() {
    testiTrack.removeEventListener('transitionend', handleNext);
    testiTrack.appendChild(testiTrack.firstElementChild);
    testiTrack.style.transition = 'none';
    testiTrack.style.transform = 'translateX(0)';
    isTestiAnimating = false;
  });
});

// Previous Slide
testiPrevBtn.addEventListener('click', () => {
  if (isTestiAnimating) return;
  isTestiAnimating = true;

  const stepWidth = getTestiStepWidth();
  const lastCard = testiTrack.lastElementChild;
  testiTrack.insertBefore(lastCard, testiTrack.firstElementChild);

  testiTrack.style.transition = 'none';
  testiTrack.style.transform = `translateX(-${stepWidth}px)`;
  testiTrack.offsetHeight; // Force reflow

  testiTrack.style.transition = 'transform 0.4s ease-in-out';
  testiTrack.style.transform = 'translateX(0)';

  testiTrack.addEventListener('transitionend', function handlePrev() {
    testiTrack.removeEventListener('transitionend', handlePrev);
    isTestiAnimating = false;
  });
});