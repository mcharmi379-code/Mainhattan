import Plugin from 'src/plugin-system/plugin.class';
import { tns } from 'tiny-slider/src/tiny-slider';

export default class IctBannerSliderPlugin extends Plugin {
    static options = {
        sliderContainerSelector: '[data-ict-banner-slider-container="true"]',
        slider: {
            items: 1,
            mode: 'carousel',
            autoplay: false,
            autoplayButtonOutput: false,
            autoplayHoverPause: true,
            nav: true,
            controls: false,
            mouseDrag: true,
            touchDrag: true,
            edgePadding: 0,
            gutter: 0,
            loop: true,
            rewind: false,
            speed: 400,
            autoplayTimeout: 5000,
            navPosition: 'bottom',
        }
    };

    init() {
        this._container = this.el.querySelector(this.options.sliderContainerSelector);

        if (!this._container || this._container.children.length < 2) {
            return;
        }

        this._initSlider();
    }

    _initSlider() {
        const sliderOptions = this._getSliderOptions();
        const controlsContainer = this.el.querySelector('[data-ict-banner-slider-controls="true"]');
        const navContainer = this.el.querySelector('[data-ict-banner-slider-nav="true"]');

        this._slider = tns({
            container: this._container,
            ...this.options.slider,
            ...sliderOptions,
            controlsContainer,
            navContainer,
        });
    }

    _getSliderOptions() {
        const optionsAttribute = this.el.dataset.ictBannerSliderOptions;

        if (!optionsAttribute) {
            return {};
        }

        try {
            const parsedOptions = JSON.parse(optionsAttribute);

            if (!parsedOptions || typeof parsedOptions !== 'object') {
                return {};
            }

            return parsedOptions.slider || {};
        } catch {
            return {};
        }
    }

    destroy() {
        if (this._slider && typeof this._slider.destroy === 'function') {
            this._slider.destroy();
            this._slider = null;
        }

        super.destroy();
    }
}
