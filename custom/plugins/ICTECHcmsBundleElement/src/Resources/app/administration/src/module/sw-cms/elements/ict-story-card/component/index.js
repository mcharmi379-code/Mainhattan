import template from './sw-cms-el-ict-story-card.html.twig';
import './sw-cms-el-ict-story-card.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        title() {
            return this.element?.config?.title?.value || '';
        },

        subtitle() {
            return this.element?.config?.subtitle?.value || '';
        },

        media() {
            return this.element?.data?.media || null;
        },
    },

    created() {
        this.initElementConfig('ict-story-card');
    },
};

