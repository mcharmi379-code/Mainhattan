import template from './sw-cms-el-ict-subcategory-navigation.html.twig';
import './sw-cms-el-ict-subcategory-navigation.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        previewCards() {
            return Array.from({ length: 3 }, (_, i) => i);
        },
    },

    created() {
        this.initElementConfig('ict-subcategory-navigation');
    },
};
