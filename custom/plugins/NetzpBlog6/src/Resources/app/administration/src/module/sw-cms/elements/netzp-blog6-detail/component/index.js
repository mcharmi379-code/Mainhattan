const { Component, Mixin } = Shopware;
import template from './sw-cms-el-netzp-blog6-detail.html.twig';
import './sw-cms-el-netzp-blog6-detail.scss';

Component.register('sw-cms-el-netzp-blog6-detail', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    computed: {
        imageStyle()
        {
            var s = 'height: ' + this.element.config.height.value + 'rem;';

            if(this.element.config.imageMode.value === 'full')
            {
                s += 'width: 100% !important; object-fit: cover; object-position: center center;';
            }
            else if(this.element.config.imageMode.value === 'fullcontain')
            {
                s += 'width: 100% !important; object-fit: contain; object-position: center center;';
            }
            else if(this.element.config.imageMode.value === 'responsive')
            {
                s = 'height: auto; width: 100% !important;';
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
            this.initElementConfig('netzp-blog6-detail');
        }
    }
});
