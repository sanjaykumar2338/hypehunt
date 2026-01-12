; (() => {

    const 𝕏 = window;
    const λ = (a, b) => a?.classList[b]("active");
    const ψ = (q) => document["getElementById"](q);

    const Ω = Object.freeze({
        α: ψ("openModal"),
        β: ψ("customModal"),
        γ: ψ("closeModal")
    });

    const δ = (e, f) =>
        (e === Ω.β && λ(Ω.β, f));

    Ω?.α?.addEventListener("click", _ => λ(Ω.β, "add"));

    Ω?.γ?.addEventListener("click", _ => λ(Ω.β, "remove"));

    Ω?.β?.addEventListener("click", e =>
        !!(e.target === Ω.β) && δ(e.target, "remove")
    );

})();



// const menuBtn = document.getElementById('mobile-menu');
// const slideMenu = document.getElementById('slideMenu');
// const closeBtn = document.getElementById('closeSlideMenu');

// menuBtn.addEventListener('click', () => {
//     menuBtn.classList.toggle('active');
//     slideMenu.classList.toggle('active');
// });

// closeBtn.addEventListener('click', () => {
//     menuBtn.classList.remove('active');
//     slideMenu.classList.remove('active');
// });

const menuBtn = document.getElementById('mobile-menu');
const slideMenu = document.getElementById('slideMenu');
const closeBtn = document.getElementById('closeSlideMenu');
const navLinks = document.querySelectorAll('.header-nav-link');


menuBtn.addEventListener('click', () => {
    menuBtn.classList.toggle('active');
    slideMenu.classList.toggle('active');
});

closeBtn.addEventListener('click', () => {
    menuBtn.classList.remove('active');
    slideMenu.classList.remove('active');
});

navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('data-target');
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
            window.scrollTo({
                top: targetSection.offsetTop,
                behavior: 'smooth'
            });
        }

        menuBtn.classList.remove('active');
        slideMenu.classList.remove('active');
    });
});





(() => {

    const openBtn = document.querySelector('.js-open-early');
    const modal = document.getElementById('earlyModal');
    const closeBtn = modal?.querySelector('.js-close-early');

    const openModal = () => modal.classList.add('active');
    const closeModal = () => modal.classList.remove('active');

    openBtn?.addEventListener('click', e => {
        e.preventDefault();
        openModal();
    });

    closeBtn?.addEventListener('click', closeModal);

    modal?.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });

})();



document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.header-nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
});







