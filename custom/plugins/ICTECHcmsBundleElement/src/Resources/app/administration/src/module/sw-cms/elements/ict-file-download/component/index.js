import template from './sw-cms-el-ict-file-download.html.twig';
import './sw-cms-el-ict-file-download.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        title() {
            return this.element?.config?.title?.value || '';
        },

        subtitle() {
            return this.element?.config?.subtitle?.value || '';
        },

        buttonText() {
            return this.element?.config?.buttonText?.value
                || this.$tc('ict-cms-bundle-element.cms.elements.ictFileDownload.defaultButtonText');
        },

        showSpecifications() {
            return this.element?.config?.showSpecifications?.value === true;
        },
    },

    created() {
        this.initElementConfig('ict-file-download');
    },
};
