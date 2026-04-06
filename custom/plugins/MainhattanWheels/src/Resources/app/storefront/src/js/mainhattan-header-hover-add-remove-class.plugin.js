import FlyoutMenuPlugin from 'src/plugin/main-menu/flyout-menu.plugin';
import DeviceDetection from 'src/helper/device-detection.helper';
import DomAccess from 'src/helper/dom-access.helper';
import Iterator from 'src/helper/iterator.helper';

export default class mainhattanHeaderHoverAddRemove extends FlyoutMenuPlugin
{
    init() {
        super.init();
    }

    /**
     * opens a single flyout
     *
     * @param {Element} flyoutEl
     * @param {Element} triggerEl
     * @private
     */
    _openFlyout(flyoutEl, triggerEl) {
        if (!this._isOpen(triggerEl)) {
            this._closeAllFlyouts();
            flyoutEl.classList.add(this.options.activeCls);
            triggerEl.classList.add(this.options.activeCls);

            // add custom code for add class
            let headerMainElement = document.getElementsByClassName("mainhattan-header-main");
            headerMainElement[0].classList.add("hovered");
            window.addEventListener("scroll", function(){
                headerMainElement[0].classList.remove("hovered");
            });
        }

        this.$emitter.publish('openFlyout');
    }

    /**
     * closes a single flyout
     *
     * @param {Element} flyoutEl
     * @param {Element} triggerEl
     * @private
     */
    _closeFlyout(flyoutEl, triggerEl) {
        if (this._isOpen(triggerEl)) {
            flyoutEl.classList.remove(this.options.activeCls);
            triggerEl.classList.remove(this.options.activeCls);
            // add custom code for remove class
            let headerMainElement = document.getElementsByClassName("mainhattan-header-main");
            headerMainElement[0].classList.remove("hovered");
        }

        this.$emitter.publish('closeFlyout');
    }
}