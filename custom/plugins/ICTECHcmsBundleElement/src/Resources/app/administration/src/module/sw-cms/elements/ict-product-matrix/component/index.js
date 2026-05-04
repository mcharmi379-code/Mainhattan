import template from './sw-cms-el-ict-product-matrix.html.twig';
import './sw-cms-el-ict-product-matrix.scss';

const ELEMENT_NAME = 'ict-product-matrix';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        productId() {
            return this.element.config?.product?.value ?? null;
        },

        hasProduct() {
            return !!this.productId;
        },

        skeletonRows() {
            return [1, 2, 3, 4, 5];
        },
    },

    created() {
        this.initElementConfig(ELEMENT_NAME);
    },
};
