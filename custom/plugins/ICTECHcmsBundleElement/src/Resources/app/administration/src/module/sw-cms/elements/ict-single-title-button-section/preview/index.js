import template from './sw-cms-el-preview-ict-single-title-button-section.html.twig';
import './sw-cms-el-preview-ict-single-title-button-section.scss';

export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
};