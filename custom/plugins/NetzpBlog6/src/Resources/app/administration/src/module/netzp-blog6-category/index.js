import './page/netzp-blog6-category-list';
import './page/netzp-blog6-category-create';
import './page/netzp-blog6-category-detail';

import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

Shopware.Module.register('netzp-blog6-category', {
    type: 'plugin',
    name: 'Blogcategory',
    title: 'netzp-blog6-category.main.menuLabel',
    description: 'netzp-blog6-category.main.menuDescription',
    color: '#ff3d58',
    icon: 'regular-eye',
    entity: 's_plugin_netzp_blog_category',
    entityDisplayProperty: 'title',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'netzp-blog6-category-list',
            path: 'list',
            meta: {
                parentPath: 'sw.settings.index'
            }
        },
        detail: {
            component: 'netzp-blog6-category-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'netzp.blog6.category.list'
            }
        },
        create: {
            component: 'netzp-blog6-category-create',
            path: 'create',
            meta: {
                parentPath: 'netzp.blog6.category.list'
            }
        }
    },

    settingsItem: {
        name: 'netzp-blog6-category',
        to: 'netzp.blog6.category.list',
        label: 'netzp-blog6-category.main.menuLabel',
        group: 'plugins',
        icon: 'regular-folder',
        privilege: 'netzpblog6:categories'
    }
});
