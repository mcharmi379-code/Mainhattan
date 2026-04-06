const { Application, Component } = Shopware;

import './np-media-list-selection-item-v2';
import './np-media-list-selection-v2';

import './page/netzp-blog6-list';
import './page/netzp-blog6-create';
import './page/netzp-blog6-detail';

import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

import defaultSearchConfiguration from './default-search-configuration';

Shopware.Module.register('netzp-blog6', {
    type: 'plugin',
    name: 'Blog',
    title: 'netzp-blog6.main.menuLabel',
    description: 'netzp-blog6.main.menuDescription',
    color: '#ccb2fb',
    icon: 'regular-file-edit',
    entity: 's_plugin_netzp_blog',
    entityDisplayProperty: 'title',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'netzp-blog6-list',
            path: 'list'
        },
        detail: {
            component: 'netzp-blog6-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'netzp.blog6.list'
            }
        },
        create: {
            component: 'netzp-blog6-create',
            path: 'create',
            meta: {
                parentPath: 'netzp.blog6.list'
            }
        }
    },

    navigation: [{
        label: 'netzp-blog6.main.menuLabel',
        color: '#82b1ff',
        path: 'netzp.blog6.list',
        icon: 'regular-content',
        parent: 'sw-content',
        position: 100,
        privilege: 'netzpblog6:posts'
    }],

    defaultSearchConfiguration
});
