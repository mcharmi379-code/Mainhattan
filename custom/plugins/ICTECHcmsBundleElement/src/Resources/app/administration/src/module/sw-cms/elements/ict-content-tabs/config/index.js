import template from './sw-cms-el-config-ict-content-tabs.html.twig';
import './sw-cms-el-config-ict-content-tabs.scss';

const ELEMENT_NAME = 'ict-content-tabs';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {};
    },

    computed: {
        tabs() {
            if (!Array.isArray(this.element.config?.tabs?.value)) {
                this.element.config.tabs.value = [];
            }
            return this.element.config.tabs.value;
        },
    },

    created() {
        this.initElementConfig(ELEMENT_NAME);
        this.ensureDefaultTab();
    },

    methods: {
        ensureDefaultTab() {
            if (!Array.isArray(this.element.config?.tabs?.value)) {
                this.element.config.tabs.value = [];
            }
            if (this.element.config.tabs.value.length === 0) {
                this.element.config.tabs.value = [
                    { label: 'Tab', content: '' },
                ];
                this.$emit('element-update', this.element);
            }
        },

        addTab() {
            this.tabs.push({ label: 'Tab', content: '' });
            this.$emit('element-update', this.element);
        },

        removeTab(index) {
            if (this.tabs.length <= 1) {
                return;
            }
            this.tabs.splice(index, 1);
            this.$emit('element-update', this.element);
        },

        moveTabUp(index) {
            if (index === 0) {
                return;
            }
            const tab = this.tabs.splice(index, 1)[0];
            this.tabs.splice(index - 1, 0, tab);
            this.$emit('element-update', this.element);
        },

        moveTabDown(index) {
            if (index >= this.tabs.length - 1) {
                return;
            }
            const tab = this.tabs.splice(index, 1)[0];
            this.tabs.splice(index + 1, 0, tab);
            this.$emit('element-update', this.element);
        },

        duplicateTab(index) {
            const original = this.tabs[index];
            const copy = { label: original.label + ' (Copy)', content: original.content };
            this.tabs.splice(index + 1, 0, copy);
            this.$emit('element-update', this.element);
        },

        onTabLabelChange() {
            this.$emit('element-update', this.element);
        },

        onTabContentChange(index, value) {
            this.tabs[index].content = value;
            this.$emit('element-update', this.element);
        },
    },
};
