import template from './sw-cms-el-preview-ict-banner-slider.html.twig';

export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
};
