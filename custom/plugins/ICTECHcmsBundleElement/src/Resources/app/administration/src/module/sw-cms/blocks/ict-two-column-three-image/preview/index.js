import template from './sw-cms-preview-ict-three-column-image.html.twig';
import './sw-cms-preview-ict-three-column-image.scss';

export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
}