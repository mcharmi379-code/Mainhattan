const { Component } = Shopware;
import template from './sw-cms-el-preview-netzp-blog6-detail.html.twig';
import './sw-cms-el-preview-netzp-blog6-detail.scss';

Component.register('sw-cms-el-preview-netzp-blog6-detail', {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});
