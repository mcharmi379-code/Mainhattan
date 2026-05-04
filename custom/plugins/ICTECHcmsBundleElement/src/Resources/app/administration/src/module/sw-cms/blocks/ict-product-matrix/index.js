Shopware.Component.register('sw-cms-preview-ict-product-matrix', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-product-matrix', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-product-matrix',
    label: 'ict-cms-bundle-element.cms.blocks.ictProductMatrix.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-product-matrix',
    previewComponent: 'sw-cms-preview-ict-product-matrix',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        productMatrix: {
            type: 'ict-product-matrix',
        },
    },
});
