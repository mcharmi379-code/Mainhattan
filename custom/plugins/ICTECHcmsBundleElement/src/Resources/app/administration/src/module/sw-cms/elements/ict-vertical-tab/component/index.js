import template from './sw-cms-el-ict-vertical-tab.html.twig';
import './sw-cms-el-ict-vertical-tab.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        tabs() {
            return this.element?.config?.tabs?.value ?? [];
        },

        activeTab() {
            return this.tabs[0] ?? null;
        },
    },

    created() {
        this.initElementConfig('ict-vertical-tab');
    },
};
