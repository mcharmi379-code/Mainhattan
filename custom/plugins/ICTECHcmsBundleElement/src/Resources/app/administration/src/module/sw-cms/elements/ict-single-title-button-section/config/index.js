import template from './sw-cms-el-config-ict-single-title-button-section.html.twig';
import './config.scss';

const { Mixin } = Shopware;

export default {
    template,
    emits: ['element-update'],
    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.initElementConfig('ict-single-title-button-section');
    },

    methods: {
        onInput() {
            this.$emit('element-update', this.element);
        },
    },
};
