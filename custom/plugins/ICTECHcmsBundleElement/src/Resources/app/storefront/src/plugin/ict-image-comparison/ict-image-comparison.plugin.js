import Plugin from 'src/plugin-system/plugin.class';

export default class IctImageComparisonPlugin extends Plugin {
    init() {
        this._layout    = this.el.getAttribute('data-layout') || 'horizontal';
        this._container = this.el.querySelector('.ict-img-compare__container');
        this._before    = this.el.querySelector('.ict-img-compare__before');
        this._after     = this.el.querySelector('.ict-img-compare__after');
        this._handle    = this.el.querySelector('.ict-img-compare__handle');

        if (!this._container || !this._before || !this._after || !this._handle) {
            return;
        }

        this._position = 50;
        this._dragging = false;

        this._onPointerDown = this._onPointerDown.bind(this);
        this._onPointerMove = this._onPointerMove.bind(this);
        this._onPointerUp   = this._onPointerUp.bind(this);
        this._onKeyDown     = this._onKeyDown.bind(this);
        this._onResize      = this._onResize.bind(this);

        this._container.addEventListener('pointerdown', this._onPointerDown);
        this._handle.addEventListener('keydown', this._onKeyDown);

        if (typeof ResizeObserver !== 'undefined') {
            this._ro = new ResizeObserver(this._onResize);
            this._ro.observe(this._container);
        }

        this._syncImageSizes();
        this._applyPosition(50);
    }

    _syncImageSizes() {
        const w = this._container.offsetWidth;
        const h = this._container.offsetHeight;

        this._container.style.setProperty('--ict-compare-w', w + 'px');
        this._container.style.setProperty('--ict-compare-h', h + 'px');
    }

    _onResize() {
        this._syncImageSizes();
        this._applyPosition(this._position);
    }

    _onPointerDown(event) {
        event.preventDefault();
        this._dragging = true;
        this._container.setPointerCapture(event.pointerId);
        this._container.addEventListener('pointermove', this._onPointerMove);
        this._container.addEventListener('pointerup', this._onPointerUp);
        this._container.addEventListener('pointercancel', this._onPointerUp);
        this._updateFromEvent(event);
    }

    _onPointerMove(event) {
        if (!this._dragging) {
            return;
        }
        this._updateFromEvent(event);
    }

    _onPointerUp(event) {
        this._dragging = false;
        this._container.releasePointerCapture(event.pointerId);
        this._container.removeEventListener('pointermove', this._onPointerMove);
        this._container.removeEventListener('pointerup', this._onPointerUp);
        this._container.removeEventListener('pointercancel', this._onPointerUp);
    }

    _onKeyDown(event) {
        const step = event.shiftKey ? 10 : 2;
        const isH  = this._layout === 'horizontal';

        if ((isH && event.key === 'ArrowLeft') || (!isH && event.key === 'ArrowUp')) {
            event.preventDefault();
            this._applyPosition(Math.max(0, this._position - step));
        } else if ((isH && event.key === 'ArrowRight') || (!isH && event.key === 'ArrowDown')) {
            event.preventDefault();
            this._applyPosition(Math.min(100, this._position + step));
        }
    }

    _updateFromEvent(event) {
        const rect = this._container.getBoundingClientRect();
        let pct;

        if (this._layout === 'vertical') {
            pct = ((event.clientY - rect.top) / rect.height) * 100;
        } else {
            pct = ((event.clientX - rect.left) / rect.width) * 100;
        }

        this._applyPosition(Math.min(100, Math.max(0, pct)));
    }

    _applyPosition(pct) {
        this._position = pct;
        const w = this._container.offsetWidth;
        const h = this._container.offsetHeight;

        if (this._layout === 'vertical') {
            const beforeH = (pct / 100) * h;

            this._before.style.height = beforeH + 'px';
            this._before.style.width  = '';
            this._after.style.height  = (h - beforeH) + 'px';
            this._after.style.width   = '';
            this._after.style.top     = beforeH + 'px';

            // After image: always full height, anchored to bottom of panel
            const afterImg = this._after.querySelector('img, .ict-img-compare__placeholder');
            if (afterImg) {
                afterImg.style.top    = 'auto';
                afterImg.style.bottom = '0';
            }

            this._handle.style.top  = beforeH + 'px';
            this._handle.style.left = '';
        } else {
            const beforeW = (pct / 100) * w;

            this._before.style.width  = beforeW + 'px';
            this._before.style.height = '';
            this._after.style.width   = (w - beforeW) + 'px';
            this._after.style.height  = '';
            this._after.style.left    = beforeW + 'px';
            this._after.style.right   = '';

            // After image: always full width, anchored to right so it reveals from right
            const afterContent = this._after.querySelectorAll('img, .ict-img-compare__placeholder');
            afterContent.forEach((el) => {
                el.style.left  = 'auto';
                el.style.right = '0';
            });

            this._handle.style.left = beforeW + 'px';
            this._handle.style.top  = '';
        }

        this._handle.setAttribute('aria-valuenow', Math.round(pct).toString());
    }
}
