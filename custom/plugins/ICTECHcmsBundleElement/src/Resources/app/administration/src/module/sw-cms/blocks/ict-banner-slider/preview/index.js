import template from './sw-cms-preview-ict-banner-slider.html.twig';
import './sw-cms-preview-ict-banner-slider.scss';

export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
};
