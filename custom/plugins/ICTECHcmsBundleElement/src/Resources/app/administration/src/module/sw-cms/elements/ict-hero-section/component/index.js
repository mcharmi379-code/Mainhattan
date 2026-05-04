import template from './sw-cms-el-ict-hero-section.html.twig';
import './sw-cms-el-ict-hero-section.scss';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.initElementConfig('ict-hero-section');
    },
};
