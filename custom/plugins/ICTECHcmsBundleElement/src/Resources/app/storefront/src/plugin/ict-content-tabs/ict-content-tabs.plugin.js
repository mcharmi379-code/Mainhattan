import Plugin from 'src/plugin-system/plugin.class';

export default class IctContentTabsPlugin extends Plugin {
    init() {
        this._navItems = this.el.querySelectorAll('.ict-ct__nav-item');
        this._panels   = this.el.querySelectorAll('.ict-ct__panel');

        this._navItems.forEach((btn) => {
            btn.addEventListener('click', this._onTabClick.bind(this));
        });
    }

    _onTabClick(event) {
        const btn   = event.currentTarget;
        const index = parseInt(btn.getAttribute('data-tab-index'), 10);

        if (isNaN(index)) {
            return;
        }

        this._activateTab(index);
    }

    _activateTab(index) {
        this._navItems.forEach((btn, i) => {
            const isActive = i === index;
            btn.classList.toggle('ict-ct__nav-item--active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        this._panels.forEach((panel, i) => {
            const isActive = i === index;
            panel.classList.toggle('ict-ct__panel--active', isActive);
            if (isActive) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
    }
}
