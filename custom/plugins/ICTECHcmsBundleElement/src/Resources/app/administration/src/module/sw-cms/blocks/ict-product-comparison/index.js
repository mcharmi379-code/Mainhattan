Shopware.Component.register('sw-cms-preview-ict-product-comparison', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-product-comparison', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-product-comparison',
    label: 'ict-cms-bundle-element.cms.blocks.ictProductComparison.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-product-comparison',
    previewComponent: 'sw-cms-preview-ict-product-comparison',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        comparison: {
            type: 'ict-product-comparison',
        },
    },
});

