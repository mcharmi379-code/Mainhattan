import template from './sw-cms-el-ict-ten-column.html.twig';
import './sw-cms-el-ict-ten-column.scss';

const ELEMENT_NAME = 'ict-ten-column';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    props: {
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    mounted() {
        this.syncSlotState();
    },

    updated() {
        this.syncSlotState();
    },

    beforeDestroy() {
        this.clearSlotState();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
            this.initElementData(ELEMENT_NAME);

            if (!this.element.data) {
                this.element.data = {};
            }
        },

        onPlaceholderActivate() {
            if (this.disabled) {
                return;
            }

            let parent = this.$parent;

            while (parent) {
                if (typeof parent.onElementButtonClick === 'function') {
                    parent.onElementButtonClick();
                    return;
                }

                parent = parent.$parent;
            }
        },

        syncSlotState() {
            const slotElement = this.$el?.closest('.sw-cms-slot');

            if (!slotElement) {
                return;
            }

            slotElement.classList.add('sw-cms-slot--ict-ten-column');
            slotElement.classList.add('sw-cms-slot--ict-ten-column-placeholder');
        },

        clearSlotState() {
            const slotElement = this.$el?.closest('.sw-cms-slot');

            if (!slotElement) {
                return;
            }

            slotElement.classList.remove('sw-cms-slot--ict-ten-column');
            slotElement.classList.remove('sw-cms-slot--ict-ten-column-placeholder');
        },
    },
};
