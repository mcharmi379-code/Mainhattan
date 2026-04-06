import './page/netzp-blog6-author-list';
import './page/netzp-blog6-author-create';
import './page/netzp-blog6-author-detail';

import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

Shopware.Module.register('netzp-blog6-author', {
    type: 'plugin',
    name: 'Blogauthor',
    title: 'netzp-blog6-author.main.menuLabel',
    description: 'netzp-blog6-author.main.menuDescription',
    color: '#ff3d58',
    icon: 'regular-eye',
    entity: 's_plugin_netzp_blog_author',
    entityDisplayProperty: 'name',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'netzp-blog6-author-list',
            path: 'list',
            meta: {
                parentPath: 'sw.settings.index'
            }
        },
        detail: {
            component: 'netzp-blog6-author-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'netzp.blog6.author.list'
            }
        },
        create: {
            component: 'netzp-blog6-author-create',
            path: 'create',
            meta: {
                parentPath: 'netzp.blog6.author.list'
            }
        }
    },

    settingsItem: {
        name: 'netzp-blog6-author',
        to: 'netzp.blog6.author.list',
        label: 'netzp-blog6-author.main.menuLabel',
        group: 'plugins',
        icon: 'regular-users',
        privilege: 'netzpblog6:authors'
    }
});
