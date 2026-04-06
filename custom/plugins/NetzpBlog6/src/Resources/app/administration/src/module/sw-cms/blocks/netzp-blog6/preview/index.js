const { Component } = Shopware;
import template from './sw-cms-preview-netzp-blog6.html.twig';
import './sw-cms-preview-netzp-blog6.scss';

Component.register('sw-cms-preview-netzp-blog6-block', {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        }
    }
});
