import Plugin from 'src/plugin-system/plugin.class';

export default class IctGalleryLightboxPlugin extends Plugin {
    init() {
        this.items = Array.from(this.el.querySelectorAll('[data-ict-gallery-item]'));
        this.modal = this.el.querySelector('[data-ict-gallery-modal]');
        this.backdrop = this.el.querySelector('[data-ict-gallery-backdrop]');
        this.closeButton = this.el.querySelector('[data-ict-gallery-close]');
        this.prevButton = this.el.querySelector('[data-ict-gallery-prev]');
        this.nextButton = this.el.querySelector('[data-ict-gallery-next]');
        this.image = this.el.querySelector('[data-ict-gallery-modal-image]');
        this.caption = this.el.querySelector('[data-ict-gallery-modal-caption]');
        this.counter = this.el.querySelector('[data-ict-gallery-counter]');
        this.activeIndex = 0;
        this.lastFocusedElement = null;

        if (!this.modal || this.items.length === 0) {
            return;
        }

        this.registerEvents();
        this.syncNavigationState();
    }

    registerEvents() {
        this.openHandlers = this.items.map((item, index) => {
            const handler = this.onItemClick.bind(this, index);
            item.addEventListener('click', handler);

            return {
                element: item,
                handler,
            };
        });

        this.onKeyDownHandler = this.onKeyDown.bind(this);
        this.closeHandler = this.close.bind(this);
        this.backdropHandler = this.onBackdropClick.bind(this);
        this.prevHandler = this.showPrevious.bind(this);
        this.nextHandler = this.showNext.bind(this);

        if (this.closeButton) {
            this.closeButton.addEventListener('click', this.closeHandler);
        }

        if (this.prevButton) {
            this.prevButton.addEventListener('click', this.prevHandler);
        }

        if (this.nextButton) {
            this.nextButton.addEventListener('click', this.nextHandler);
        }

        if (this.backdrop) {
            this.backdrop.addEventListener('click', this.backdropHandler);
        }

        document.addEventListener('keydown', this.onKeyDownHandler);
    }

    destroy() {
        if (this.modal && this.modal.classList.contains('is-open')) {
            this.close();
        }

        if (Array.isArray(this.openHandlers)) {
            this.openHandlers.forEach(({ element, handler }) => {
                element.removeEventListener('click', handler);
            });
        }

        if (this.closeButton && this.closeHandler) {
            this.closeButton.removeEventListener('click', this.closeHandler);
        }

        if (this.backdrop && this.backdropHandler) {
            this.backdrop.removeEventListener('click', this.backdropHandler);
        }

        if (this.prevButton && this.prevHandler) {
            this.prevButton.removeEventListener('click', this.prevHandler);
        }

        if (this.nextButton && this.nextHandler) {
            this.nextButton.removeEventListener('click', this.nextHandler);
        }

        if (this.onKeyDownHandler) {
            document.removeEventListener('keydown', this.onKeyDownHandler);
        }

        super.destroy();
    }

    onItemClick(index, event) {
        event.preventDefault();
        this.open(index);
    }

    open(index) {
        if (!this.modal) {
            return;
        }

        this.lastFocusedElement = document.activeElement;
        this.setActiveIndex(index);
        this.modal.classList.add('is-open');
        this.modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('ict-image-gallery-is-open');

        if (this.closeButton) {
            this.closeButton.focus();
        }
    }

    close() {
        if (!this.modal) {
            return;
        }

        this.modal.classList.remove('is-open');
        this.modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('ict-image-gallery-is-open');

        if (this.lastFocusedElement instanceof HTMLElement) {
            this.lastFocusedElement.focus();
        }
    }

    showNext() {
        this.setActiveIndex(this.activeIndex + 1);
    }

    showPrevious() {
        this.setActiveIndex(this.activeIndex - 1);
    }

    setActiveIndex(index) {
        const totalItems = this.items.length;

        if (totalItems === 0) {
            return;
        }

        this.activeIndex = ((index % totalItems) + totalItems) % totalItems;

        const activeItem = this.items[this.activeIndex];
        const src = activeItem.dataset.ictGallerySrc || '';
        const alt = activeItem.dataset.ictGalleryAlt || '';
        const caption = activeItem.dataset.ictGalleryCaption || '';

        if (this.image) {
            this.image.src = src;
            this.image.alt = alt;
        }

        if (this.caption) {
            this.caption.textContent = caption;
        }

        if (this.counter) {
            this.counter.textContent = `${this.activeIndex + 1} / ${totalItems}`;
        }

        this.syncNavigationState();
    }

    syncNavigationState() {
        const shouldHideNavigation = this.items.length < 2;

        if (this.prevButton) {
            this.prevButton.hidden = shouldHideNavigation;
            this.prevButton.disabled = shouldHideNavigation;
        }

        if (this.nextButton) {
            this.nextButton.hidden = shouldHideNavigation;
            this.nextButton.disabled = shouldHideNavigation;
        }
    }

    onBackdropClick(event) {
        if (event.target === this.backdrop) {
            this.close();
        }
    }

    onKeyDown(event) {
        if (!this.modal || !this.modal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            this.close();
        }

        if (event.key === 'ArrowLeft') {
            this.showPrevious();
        }

        if (event.key === 'ArrowRight') {
            this.showNext();
        }
    }
}
