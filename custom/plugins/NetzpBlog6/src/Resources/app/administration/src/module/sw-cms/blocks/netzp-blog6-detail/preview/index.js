const { Component } = Shopware;
import template from './sw-cms-preview-netzp-blog6-detail.html.twig';
import './sw-cms-preview-netzp-blog6-detail.scss';

Component.register('sw-cms-preview-netzp-blog6-detail-block', {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
});
