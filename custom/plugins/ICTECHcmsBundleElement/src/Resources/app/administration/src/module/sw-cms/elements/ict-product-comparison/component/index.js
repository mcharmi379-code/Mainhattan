import template from './sw-cms-el-ict-product-comparison.html.twig';
import './sw-cms-el-ict-product-comparison.scss';

const ELEMENT_NAME = 'ict-product-comparison';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        assignmentType() {
            return this.element.config?.productAssignmentType?.value ?? 'manual';
        },

        productIds() {
            return this.element.config?.productIds?.value ?? [];
        },

        productStreamId() {
            return this.element.config?.productStreamId?.value ?? null;
        },

        highlightFirst() {
            return this.element.config?.highlightFirstProduct?.value === true;
        },

        hasProducts() {
            if (this.assignmentType === 'stream') {
                return typeof this.productStreamId === 'string' && this.productStreamId.length > 0;
            }

            return Array.isArray(this.productIds) && this.productIds.length > 0;
        },

        previewColumns() {
            const count = this.assignmentType === 'stream' ? 3 : Math.min(this.productIds.length, 5);
            const cols = [];

            for (let i = 0; i < count; i++) {
                cols.push({
                    name: `Product ${i + 1}`,
                    price: '€0.00',
                });
            }

            return cols;
        },

        previewRows() {
            return ['Name', 'Price', 'Rating', 'Manufacturer'];
        },
    },

    created() {
        this.initElementConfig(ELEMENT_NAME);
    },
};
