import Plugin from 'src/plugin-system/plugin.class';

export default class IctProductMatrixPlugin extends Plugin {
    init() {
        this.el.querySelectorAll('.ipm-buy-form').forEach((form) => {
            this._initForm(form);
        });
    }

    _initForm(form) {
        const input    = form.querySelector('.ipm-buy-form__qty-input');
        const decrease = form.querySelector('[data-qty-action="decrease"]');
        const increase = form.querySelector('[data-qty-action="increase"]');

        if (!input) {
            return;
        }

        if (decrease) {
            decrease.addEventListener('click', () => {
                const current = parseInt(input.value, 10) || 1;
                const min     = parseInt(input.min, 10) || 1;
                input.value   = Math.max(min, current - 1);
            });
        }

        if (increase) {
            increase.addEventListener('click', () => {
                const current = parseInt(input.value, 10) || 1;
                const max     = parseInt(input.max, 10) || 999;
                input.value   = Math.min(max, current + 1);
            });
        }

        input.addEventListener('change', () => {
            const val = parseInt(input.value, 10);
            const min = parseInt(input.min, 10) || 1;
            const max = parseInt(input.max, 10) || 999;
            if (isNaN(val) || val < min) {
                input.value = min;
            } else if (val > max) {
                input.value = max;
            }
        });
    }
}
