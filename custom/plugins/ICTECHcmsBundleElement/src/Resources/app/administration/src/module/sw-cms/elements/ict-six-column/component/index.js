import template from './sw-cms-el-ict-six-column.html.twig';
import './sw-cms-el-ict-six-column.scss';

const ELEMENT_NAME = 'ict-six-column';
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

    computed: {
        media() {
            return this.element.data?.media || null;
        },

        eyebrow() {
            return this.element.config?.eyebrow?.value || '';
        },

        title() {
            return this.element.config?.title?.value || '';
        },

        text() {
            return this.element.config?.text?.value || '';
        },

        linkText() {
            return this.element.config?.linkText?.value || '';
        },

        url() {
            return this.element.config?.url?.value || '';
        },

        isPlaceholder() {
            return !this.media
                && !this.eyebrow
                && !this.title
                && !this.text
                && !this.linkText
                && !this.url;
        },
    },

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
            if (this.disabled || !this.isPlaceholder) {
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

            slotElement.classList.add('sw-cms-slot--ict-six-column');
            slotElement.classList.toggle('sw-cms-slot--ict-six-column-placeholder', this.isPlaceholder);
        },

        clearSlotState() {
            const slotElement = this.$el?.closest('.sw-cms-slot');

            if (!slotElement) {
                return;
            }

            slotElement.classList.remove('sw-cms-slot--ict-six-column');
            slotElement.classList.remove('sw-cms-slot--ict-six-column-placeholder');
        },
    },
};
