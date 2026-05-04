import template from './sw-cms-el-ict-single-title-button-section.html.twig';
import './sw-cms-el-ict-single-title-button-section.scss';

const {Mixin} = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('ict-single-title-button-section');
        }
    }
};