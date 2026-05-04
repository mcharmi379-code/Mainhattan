import template from './sw-cms-el-config-ict-product-matrix.html.twig';
import './sw-cms-el-config-ict-product-matrix.scss';

const ELEMENT_NAME = 'ict-product-matrix';
const { Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    inject: ['repositoryFactory'],

    data() {
        return {
            selectedProduct: null,
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        productCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addFilter(Criteria.equals('parentId', null));
            criteria.addAssociation('cover');
            return criteria;
        },

        displayModeOptions() {
            return [
                {
                    value: 'standard',
                    label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductMatrix.displayMode.standard'),
                },
                {
                    value: 'highlighted',
                    label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductMatrix.displayMode.highlighted'),
                },
                {
                    value: 'compact',
                    label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductMatrix.displayMode.compact'),
                },
            ];
        },

        currentProductId() {
            return this.element.config?.product?.value ?? null;
        },
    },

    created() {
        this.initElementConfig(ELEMENT_NAME);
        this.loadSelectedProduct();
    },

    methods: {
        async loadSelectedProduct() {
            const productId = this.currentProductId;
            if (!productId || typeof productId !== 'string') {
                this.selectedProduct = null;
                return;
            }

            try {
                const criteria = new Criteria(1, 1);
                criteria.addAssociation('cover');
                this.selectedProduct = await this.productRepository.get(productId, Shopware.Context.api, criteria);
            } catch (_) {
                this.selectedProduct = null;
            }
        },

        onProductChange(productId) {
            this.element.config.product.value = productId ?? null;
            this.$emit('element-update', this.element);
            this.loadSelectedProduct();
        },

        onDisplayModeChange() {
            this.$emit('element-update', this.element);
        },
    },
};
