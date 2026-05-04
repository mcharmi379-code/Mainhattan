import template from './sw-cms-el-ict-content-tabs.html.twig';
import './sw-cms-el-ict-content-tabs.scss';

const ELEMENT_NAME = 'ict-content-tabs';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            activeTabIndex: 0,
        };
    },

    computed: {
        tabs() {
            const value = this.element.config?.tabs?.value;
            return Array.isArray(value) ? value : [];
        },

        activeTab() {
            return this.tabs[this.activeTabIndex] ?? null;
        },

        hasContent() {
            return this.tabs.length > 0;
        },

        tabStyle() {
            return this.element.config?.tabStyle?.value || 'buttons';
        },

        displayMode() {
            return this.element.config?.displayMode?.value || 'topbar';
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
                    { label: 'Tab', content: '<h3>Tab HTML/Text Content</h3><p>Add your content here.</p>' },
                ];
            }
        },

        setActiveTab(index) {
            this.activeTabIndex = index;
        },
    },
};
