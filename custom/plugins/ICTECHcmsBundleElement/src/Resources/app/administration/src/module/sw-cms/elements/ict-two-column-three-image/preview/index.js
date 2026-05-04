import template from './sw-cms-el-preview-ict-two-column-three-image.html.twig';
import './sw-cms-el-preview-ict-two-column-three-image.scss';

export default {
    template,
    compatConfig: Shopware.compatConfig,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
};