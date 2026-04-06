const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

import template from './sw-cms-el-config-netzp-blog6-detail.html.twig';

Component.register('sw-cms-el-config-netzp-blog6-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
        };
    },

    created() {
    },

    methods: {
        createdComponent() {
            this.initElementConfig('netzp-blog6-detail');
        }
    }
});
