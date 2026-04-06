const { Component } = Shopware;
import template from './sw-cms-el-preview-netzp-blog6.html.twig';
import './sw-cms-el-preview-netzp-blog6.scss';

Component.register('sw-cms-el-preview-netzp-blog6', {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
});
