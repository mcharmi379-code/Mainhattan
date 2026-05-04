import Plugin from 'src/plugin-system/plugin.class';

export default class IctVerticalTabPlugin extends Plugin {
    init() {
        this._navItems = this.el.querySelectorAll('.ict-vtab__nav-item');
        this._panels   = this.el.querySelectorAll('.ict-vtab__panel');

        this._navItems.forEach((btn) => {
            btn.addEventListener('click', this._onTabClick.bind(this));
            btn.addEventListener('keydown', this._onKeyDown.bind(this));
        });
    }

    _onTabClick(event) {
        const index = parseInt(event.currentTarget.getAttribute('data-tab-index'), 10);
        if (!isNaN(index)) {
            this._activateTab(index);
        }
    }

    _onKeyDown(event) {
        const key = event.key;
        const current = parseInt(event.currentTarget.getAttribute('data-tab-index'), 10);
        const total = this._navItems.length;

        if (key === 'ArrowDown' || key === 'ArrowRight') {
            event.preventDefault();
            this._activateTab((current + 1) % total);
            this._navItems[(current + 1) % total].focus();
        } else if (key === 'ArrowUp' || key === 'ArrowLeft') {
            event.preventDefault();
            this._activateTab((current - 1 + total) % total);
            this._navItems[(current - 1 + total) % total].focus();
        }
    }

    _activateTab(index) {
        this._navItems.forEach((btn, i) => {
            const active = i === index;
            btn.classList.toggle('ict-vtab__nav-item--active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        this._panels.forEach((panel, i) => {
            const active = i === index;
            panel.classList.toggle('ict-vtab__panel--active', active);
            if (active) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
    }
}
