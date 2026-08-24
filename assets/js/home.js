fetch("components/nav.html")
    .then(response => response.text())
    .then((data) => {
        document.getElementById("nav").innerHTML = data;
        
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
    .catch(error => {
        console.log("Error loading navbar:", error)
    });
console.log("script is running");

(function () {

    const track = document.querySelector('.tc-track');
    const dotsWrap = document.getElementById('tcDots');
    const prevBtn = document.querySelector('.tc-btn--prev');
    const nextBtn = document.querySelector('.tc-btn--next');
    if (!track) return;

    /* ── Configuration ───────────────────────────────────── */
    let VISIBLE = 5;
    const GAP = 16;
    const CLONES = 2;
    const N = 7;
    const allCards = Array.from(track.querySelectorAll('.tc-card'));

    /* ── State ───────────────────────────────────────────── */

    let pos = CLONES + 2;
    let realIdx = 2;
    let isMoving = false;
    let autoTimer = null;
    let CARD_W = 0;
    let STEP = 0;


    /* ── Size cards ──────────────────────────────────────── */

    function sizeCards() {
        const viewW = track.parentElement.offsetWidth;
        if (window.innerWidth <= 576) {
            VISIBLE = 1.2;
        } else if (window.innerWidth <= 768) {
            VISIBLE = 2;
        } else if (window.innerWidth <= 992) {
            VISIBLE = 3;
        } else {
            VISIBLE = 5;
        }

        CARD_W = (viewW - GAP * (VISIBLE - 1)) / VISIBLE;
        STEP = CARD_W + GAP;

        allCards.forEach(card => {

            card.style.width = CARD_W + 'px';

            card.style.marginLeft = (GAP / 2) + 'px';
            card.style.marginRight = (GAP / 2) + 'px';
        });
    }

    /* ── Calculate track position ────────────────────────── */
    function getOffset() {
        const viewW = track.parentElement.offsetWidth;
        return (viewW / 2)
            - (pos * STEP)
            - (STEP / 2);
    }

    /* ── Update active / nearby cards ────────────────────── */

    function updateClasses() {
        allCards.forEach((card, i) => {
            card.classList.remove(
                'tc-card--active',
                'tc-card--near'
            );

            const distance = Math.abs(i - pos);
            if (distance === 0) {
                card.classList.add('tc-card--active');
            } else if (distance === 1) {
                card.classList.add('tc-card--near');
            }
        });

        /* Update dots */
        if (dotsWrap) {
            Array.from(dotsWrap.children).forEach((dot, i) => {
                dot.classList.toggle(
                    'tc-dot--active',
                    i === realIdx
                );
            });
        }
    }

    /* ── Update position ─────────────────────────────────── */

    function updatePosition() {
        track.style.transform =
            `translate3d(${getOffset()}px, 0, 0)`;

    }

    /* ── Normal smooth movement ──────────────────────────── */

    function moveTo(newPos) {

        pos = newPos;
        realIdx =
            ((pos - CLONES) % N + N) % N;
        updatePosition();
        updateClasses();
    }

    /* ── Invisible ghost teleport ── */
    function snapTo(newPos) {

        pos = newPos;
        realIdx =
            ((pos - CLONES) % N + N) % N;

        /* Disable transition only for the invisible teleport */

        track.style.transition = 'none';

        updatePosition();
        updateClasses();

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                track.style.transition = '';
            });
        });
    }

    /* ── Create dots ── */
    if (dotsWrap) {
        dotsWrap.innerHTML = '';
        for (let i = 0; i < N; i++) {

            const dot = document.createElement('button');
            dot.className = 'tc-dot';

            dot.setAttribute(
                'aria-label',
                `Slide ${i + 1}`
            );

            dot.addEventListener('click', () => {
                goToReal(i);
                resetAuto();
            });
            dotsWrap.appendChild(dot);
        }
    }

    /* ── Handle transition end ── */
    track.addEventListener('transitionend', event => {

        if (
            event.target !== track ||
            event.propertyName !== 'transform'
        ) {
            return;
        }
        isMoving = false;
        if (pos < CLONES) {
            snapTo(pos + N);
        }
        else if (pos >= CLONES + N) {
            snapTo(pos - N);
        }
    });

    /* ── Move one card ───────────────────────────────────── */

    function step(direction) {
        if (isMoving) return;
        isMoving = true;
        moveTo(pos + direction);
    }

    /* ── Go to specific real card ───────────────────────── */
    function goToReal(index) {
        if (isMoving) return;
        isMoving = true;
        const target =
            CLONES + ((index % N + N) % N);
        moveTo(target);
    }

    /* ── Buttons ─────────────────────────────────────────── */

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            step(-1);
            resetAuto();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            step(1);
            resetAuto();
        });
    }

    /* ── Card click ─────────────────────────────────────── */
    allCards.forEach((card, index) => {
        card.addEventListener('click', () => {
            if (index === pos || isMoving) return;
            isMoving = true;
            moveTo(index);
            resetAuto();
        });
    });

    /* ── Keyboard ───────────────────────────────────────── */

    document.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft') {
            step(-1);
            resetAuto();
        }
        if (event.key === 'ArrowRight') {
            step(1);
            resetAuto();
        }
    });
    /* ── Touch / swipe ──────────────────────────────────── */

    let touchX = 0;
    track.addEventListener(
        'touchstart',
        event => {
            touchX = event.touches[0].clientX;
        },
        { passive: true }
    );

    track.addEventListener(
        'touchend',
        event => {
            const diff =
                touchX - event.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                step(diff > 0 ? 1 : -1);
                resetAuto();
            }
        }
    );

    /* ── Auto play ───────────────────────────────────────── */
    function startAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(() => {
            step(1);
        }, 3500);
    }

    function resetAuto() {
        clearInterval(autoTimer);
        startAuto();
    }
    /* ── Pause when mouse is over carousel ──────────────── */
    const root = document.querySelector('.tc-root');

    if (root) {
        root.addEventListener('mouseenter', () => {
            clearInterval(autoTimer);
        });
        root.addEventListener('mouseleave', () => {
            startAuto();
        });
    }

    /* ── Resize ──────────────────────────────────────────── */
    window.addEventListener('resize', () => {
        sizeCards();
        const previousTransition =
            track.style.transition;
        track.style.transition = 'none';
        updatePosition();
        requestAnimationFrame(() => {
            track.style.transition = previousTransition || '';
        });
    });
    /* ── INITIALIZATION ── */

    sizeCards();

    track.style.transition = 'none';
    updatePosition();
    updateClasses();

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            track.style.transition = '';
            startAuto();
        });
    });
})();

fetch("components/footer.html")
    .then(response => response.text())
    .then((data) => {
        document.getElementById("footer").innerHTML = data;
        // Logo path is relative to index.html — already correct
    })
    .catch(error => {
        console.log("Error loading footer:", error)
    });

fetch("forms/contact-form.html")
    .then(response => response.text())
    .then((data) => {
        document.getElementById("contact").innerHTML = data;
    })
    .catch(error => {
        console.log("Error loading contact form:", error)
    });

// ── Auto-Popup Contact Form Behavior ─────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const popupOverlay = document.getElementById("popupOverlay");
    const popupCloseBtn = document.getElementById("popupCloseBtn");

    if (popupOverlay) {
        // Check if the popup was already shown during this session
        const hasSeenPopup = sessionStorage.getItem('whiteCloudsPopupShown');

        if (!hasSeenPopup) {
            setTimeout(() => {
                // Ensure display is set to flex before adding transition class
                popupOverlay.style.display = 'flex';
                // Trigger a reflow to start transition
                popupOverlay.offsetHeight;
                popupOverlay.classList.add('show');
                document.body.style.overflow = "hidden"; // Prevent background scrolling
                
                // Set the session flag
                sessionStorage.setItem('whiteCloudsPopupShown', 'true');
            }, 3500); // Strictly 3.5 seconds delay
        }

        const closePopup = () => {
            popupOverlay.classList.remove("show");
            popupOverlay.classList.remove("active");
            // Wait for transition to complete before hiding display
            setTimeout(() => {
                if (!popupOverlay.classList.contains("show")) {
                    popupOverlay.style.display = "none";
                }
            }, 400); // Match CSS transition time
            document.body.style.overflow = ""; // Restore scrolling
        };

        // Close on '✕' click
        if (popupCloseBtn) {
            popupCloseBtn.addEventListener("click", closePopup);
        }

        // Close when clicking outside the popup box (overlay backdrop)
        popupOverlay.addEventListener("click", (e) => {
            if (e.target === popupOverlay) {
                closePopup();
            }
        });
    }
});
