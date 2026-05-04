const BLOCK_NAME = 'ict-breadcrumb-navigation';
const ELEMENT_NAME = 'ict-breadcrumb-navigation';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-breadcrumb-navigation-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-breadcrumb-navigation', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictBreadcrumbNavigation.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-breadcrumb-navigation',
    previewComponent: 'sw-cms-preview-ict-breadcrumb-navigation-block',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: ELEMENT_NAME,
        },
    },
});
