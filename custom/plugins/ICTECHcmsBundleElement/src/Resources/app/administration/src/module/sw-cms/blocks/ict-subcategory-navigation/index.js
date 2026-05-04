const BLOCK_NAME = 'ict-subcategory-navigation';
const ELEMENT_NAME = 'ict-subcategory-navigation';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-subcategory-navigation-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-subcategory-navigation', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictSubcategoryNavigation.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-subcategory-navigation',
    previewComponent: 'sw-cms-preview-ict-subcategory-navigation-block',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
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
