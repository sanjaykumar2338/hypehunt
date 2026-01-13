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
                const targetId = link.getAttribute('data-target');
                if (targetId) {
                    e.preventDefault();
                    scrollToSection(targetId);
                    menuBtn?.classList.remove('active');
                    slideMenu?.classList.remove('active');
                }
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

    const showConfirmation = (message) => {
        const toast = document.getElementById('confirmationToast');
        const text = document.getElementById('confirmationText');
        if (!toast || !text) return;

        text.textContent = message || 'Thanks for joining the HypeHunt waitlist!';
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 5000);
    };

    const handleFormSubmit = (form, messageEl, modalEl, defaultMessage) => {
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setMessage(messageEl, '', true);
            form.classList.add('is-submitting');
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnOriginal = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Processing...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                });

                const data = await response.json();
                const success = Boolean(data?.success);
                const msgText = data?.message || defaultMessage || 'Something went wrong.';
                if (success) {
                    // Only show toast on success; keep the header message area empty.
                    setMessage(messageEl, '', true);
                    showConfirmation(msgText);
                    setTimeout(() => {
                        form.reset();
                        modalEl?.classList.remove('active');
                        setMessage(messageEl, '', true);
                    }, 2000);
                } else {
                    // Show error inline for failures.
                    setMessage(messageEl, msgText, false);
                }
            } catch (error) {
                setMessage(messageEl, 'Unable to submit right now. Please try again.', false);
            } finally {
                form.classList.remove('is-submitting');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = btnOriginal;
                }
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
            'Thanks for joining the HypeHunt waitlist!',
        );

        handleFormSubmit(
            document.getElementById('earlyAccessForm'),
            document.getElementById('earlyMessage'),
            document.getElementById('earlyModal'),
            'Thanks for joining the HypeHunt early access waitlist!',
        );
    });
})();
