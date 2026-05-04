import template from './sw-cms-preview-ict-hero-section.html.twig';
import './sw-cms-preview-ict-hero-section.scss';

export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
};