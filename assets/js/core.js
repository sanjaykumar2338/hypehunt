(() => {
    const qs = (selector) => document.querySelector(selector);
    const qsa = (selector) => Array.from(document.querySelectorAll(selector));

    const scrollToSection = (targetId) => {
        const section = document.getElementById(targetId);
        if (section) {
            window.scrollTo({
                top: section.offsetTop,
                behavior: 'smooth',
            });
        }
    };

    const setupNavigation = () => {
        const menuBtn = document.getElementById('mobile-menu');
        const slideMenu = document.getElementById('slideMenu');
        const closeBtn = document.getElementById('closeSlideMenu');
        const links = qsa('.header-nav-link');

        if (menuBtn && slideMenu) {
            menuBtn.addEventListener('click', () => {
                menuBtn.classList.toggle('active');
                slideMenu.classList.toggle('active');
            });
        }

        closeBtn?.addEventListener('click', () => {
            menuBtn?.classList.remove('active');
            slideMenu?.classList.remove('active');
        });

        links.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('data-target');
                scrollToSection(targetId);
                menuBtn?.classList.remove('active');
                slideMenu?.classList.remove('active');
            });
        });
    };

    const setupModal = (openSelector, modalId, closeSelector) => {
        const openers = qsa(openSelector);
        const modal = document.getElementById(modalId);
        const closer = modal ? modal.querySelector(closeSelector) : null;

        if (!modal) return;

        const openModal = (e) => {
            e?.preventDefault();
            modal.classList.add('active');
        };

        const closeModal = () => modal.classList.remove('active');

        openers.forEach((btn) => btn.addEventListener('click', openModal));
        closer?.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    };

    const setMessage = (el, message, isSuccess) => {
        if (!el) return;
        el.textContent = message;
        el.classList.remove('success', 'error');
        if (message) {
            el.classList.add(isSuccess ? 'success' : 'error');
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    };

    const handleFormSubmit = (form, messageEl, modalEl) => {
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setMessage(messageEl, '', true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                });

                const data = await response.json();
                const success = Boolean(data?.success);
                setMessage(messageEl, data?.message || 'Something went wrong.', success);

                if (success) {
                    setTimeout(() => {
                        form.reset();
                        modalEl?.classList.remove('active');
                        setMessage(messageEl, '', true);
                    }, 2000);
                }
            } catch (error) {
                setMessage(messageEl, 'Unable to submit right now. Please try again.', false);
            }
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        setupNavigation();
        setupModal('#openModal', 'customModal', '#closeModal');
        setupModal('.js-open-early', 'earlyModal', '.js-close-early');

        handleFormSubmit(
            document.getElementById('notifyForm'),
            document.getElementById('notifyMessage'),
            document.getElementById('customModal'),
        );

        handleFormSubmit(
            document.getElementById('earlyAccessForm'),
            document.getElementById('earlyMessage'),
            document.getElementById('earlyModal'),
        );
    });
})();
