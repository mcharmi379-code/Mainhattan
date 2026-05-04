import template from './sw-cms-el-ict-manufacturer-list.html.twig';
import './sw-cms-el-ict-manufacturer-list.scss';

const ELEMENT_NAME = 'ict-manufacturer-list';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        alphabet() {
            return ['#', ...'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')];
        },

        previewItems() {
            return Array.from({ length: 20 }, (_, index) => ({
                id: `preview-${index + 1}`,
            }));
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
        },
    },
};
