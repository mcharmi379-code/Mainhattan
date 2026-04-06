const { Application, Component, Locale} = Shopware;

import './acl';
import './module/netzp-blog6';
import './module/netzp-blog6-category';
import './module/netzp-blog6-author';

import './module/sw-cms/elements/netzp-blog6';
import './module/sw-cms/blocks/netzp-blog6';

import './module/sw-cms/elements/netzp-blog6-detail';
import './module/sw-cms/blocks/netzp-blog6-detail';

import './module/sw-cms/component/sw-cms-sidebar'; // add new category to blocks

import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);

Component.override('sw-admin-menu', {
    inject: [
        'customFieldDataProviderService'
    ],

    mounted: function() {
        this.customFieldDataProviderService.addEntityName('s_plugin_netzp_blog');
        this.customFieldDataProviderService.addEntityName('s_plugin_netzp_blog_category');
        this.customFieldDataProviderService.addEntityName('s_plugin_netzp_blog_author');
    }
});
