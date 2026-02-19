function navToggle() {
    let toggleBtn = document.getElementById("js-navToggle");
    let headerMenu = document.getElementById("js-spNav");
    let body = document.querySelector("body");

    toggleBtn.addEventListener("click", function () {
        console.log("click")
        toggleBtn.classList.toggle("is-open");
        if (toggleBtn.classList.contains("is-open")) {
            headerMenu.classList.add("is-open");
            body.classList.add("no-scroll");
        } else {
            headerMenu.classList.remove("is-open");
            body.classList.remove("no-scroll");
        }
    })
}

function modalBox() {
    const triggers = document.querySelectorAll('.js-modaltrigger');
    const modals = document.querySelectorAll('.js-modal');
    const closeButtons = document.querySelectorAll('.js-closebtn');
    const modalContents = document.querySelectorAll('.modal-content');
    const body = document.body;

    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const targetModal = trigger.getAttribute('attr-modal');

            modals.forEach(m => closeModal(m));

            const modal = document.querySelector(`.js-modal[attr-modal="${targetModal}"]`);
            if (!modal) return;

            modal.classList.add('show-modal');
            body.classList.add('no-scroll');

            const iframe = modal.querySelector('iframe.yt-video');
            iframe.src = iframe.dataset.src;
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.js-modal');
            if (!modal) return;
            closeModal(modal);
        });
    });

    modals.forEach(modal => {
        modal.addEventListener('click', () => {
            closeModal(modal);
        });
    });

    modalContents.forEach(content => {
        content.addEventListener('click', e => {
            e.stopPropagation();
        });
    });

    function closeModal(modal) {
        modal.classList.remove('show-modal');
        body.classList.remove('no-scroll');

        const iframe = modal.querySelector('iframe.yt-video');
        if (iframe) {
            iframe.src = '';
        }
    }
}

function videoSlider() {
    const sliderEl = document.querySelector('#movie-slides');
    if (!sliderEl) return;

    const wrapper = sliderEl.querySelector('.swiper-wrapper');
    if (!wrapper) return;

    // 🔹 get real slides
    const realSlides = Array.from(wrapper.children);
    const realSlideCount = realSlides.length;

    // 🔁 clone slides to allow loop for 2–5 slides
    if (realSlideCount >= 2 && realSlideCount <= 5) {
        // For 2 slides, clone them twice (total 6 slides) for smooth loop
        const cloneTimes = realSlideCount === 2 ? 2 : 1;
        for (let i = 0; i < cloneTimes; i++) {
            realSlides.forEach(slide => {
                const clone = slide.cloneNode(true);
                clone.classList.add('is-manual-clone');
                wrapper.appendChild(clone);
            });
        }
    }

    // 🔹 init Swiper
    const swiper = new Swiper(sliderEl, {
        slidesPerView: 'auto',
        centeredSlides: true,
        loop: true,
        speed: 800,
        spaceBetween: 16,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: (index, className) => {
                return index < realSlideCount
                    ? `<span class="${className}"></span>`
                    : '';
            }
        },
        breakpoints: {
            767: {
                spaceBetween: 30,
            },
        },

        on: {
            init(swiper) {
                // wait for bullets to render
                setTimeout(() => syncPagination(swiper, realSlideCount), 0);
            },
            slideChange(swiper) {
                syncPagination(swiper, realSlideCount);
            }
        }
    });

    // 🔧 pagination sync helper
    function syncPagination(swiper, realCount) {
        if (!swiper.pagination?.el) return;

        const bullets = swiper.pagination.el.children;
        const realIndex = swiper.realIndex % realCount;

        Array.from(bullets).forEach((bullet, i) => {
            bullet.classList.toggle(
                'swiper-pagination-bullet-active',
                i === realIndex
            );
        });
    }
}

function searchMap() {
    const checkboxes = document.querySelectorAll(".js-checkbox-location");
    const svgPaths = document.querySelectorAll(".map-path");
    const mapTags = document.querySelectorAll(".search-main__map-tag");

    function updatePaths() {
        const checkedIds = Array.from(checkboxes)
            .filter(c => c.checked)
            .map(c => c.dataset.id);

        svgPaths.forEach(path => {
            path.style.opacity =
                checkedIds.length === 0 || checkedIds.includes(path.id)
                    ? 1
                    : 0.2;
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", updatePaths);
    });
    svgPaths.forEach(path => {
        path.addEventListener("click", () => {
            const checkbox = Array.from(checkboxes)
                .find(c => c.dataset.id === path.id);

            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updatePaths();
            }
        });
    });

    mapTags.forEach(tag => {
        const tagId = tag.dataset.id;
        tag.addEventListener("mouseenter", () => {
            const path = document.getElementById(tagId);
            if (path) {
                path.style.opacity = 0.7;
            }
        });
        tag.addEventListener("mouseleave", updatePaths);


        tag.addEventListener("click", () => {
            const checkbox = Array.from(checkboxes)
                .find(c => c.dataset.id === tagId);

            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updatePaths();
            }
        });
    });

}

function catalogSwiper() {
    var swiper = new Swiper(".catalog-swiper", {
        slidesPerView: 1,
        spaceBetween: 60,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            767: {
                slidesPerView: 2,
                spaceBetween: 30,
            },
        },
    });

}

function floatingMenu() {
    if (window.innerWidth < 767) return;

    const floatingMenu = document.querySelector(".exhibitors-detail__floatingMenu");
    const floatingMenuBtn = document.querySelector(".exhibitors-detail__floatingMenu-btn");

    if (!floatingMenu || !floatingMenuBtn) return;

    floatingMenuBtn.addEventListener("click", function () {
        if (floatingMenu.classList.contains("is-open")) {
            floatingMenu.classList.remove("is-open");
        } else {
            floatingMenu.classList.add("is-open");
        }
    })
}

document.addEventListener("DOMContentLoaded", function () {
    navToggle();
    modalBox();
    videoSlider();
    searchMap();
    catalogSwiper();
    floatingMenu();
})