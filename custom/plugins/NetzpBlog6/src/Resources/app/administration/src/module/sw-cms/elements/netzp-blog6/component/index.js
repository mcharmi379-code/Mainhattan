const { Component, Mixin } = Shopware;
import template from './sw-cms-el-netzp-blog6.html.twig';
import './sw-cms-el-netzp-blog6.scss';

Component.register('sw-cms-el-netzp-blog6', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    computed: {
        numberOfPosts() {
            if(this.element.config.numberOfPosts.value == 0) {
                return 3;
            }
            return this.element.config.numberOfPosts.value;
        },

        getCardStyle() {
            var s = '';
            if(this.element.config.backgroundColor.value) {
                s += 'background-color: ' + this.element.config.backgroundColor.value;
            }
            return s;
        },

        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('netzp-blog6');
        }
    }
});
