const ELEMENT_NAME = 'ict-product-comparison';

Shopware.Component.register('sw-cms-el-ict-product-comparison', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-product-comparison', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-product-comparison', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictProductComparison.label',
    component: 'sw-cms-el-ict-product-comparison',
    configComponent: 'sw-cms-el-config-ict-product-comparison',
    previewComponent: 'sw-cms-el-preview-ict-product-comparison',
    defaultConfig: {
        productAssignmentType: {
            source: 'static',
            value: 'manual',
        },
        productIds: {
            source: 'static',
            value: [],
        },
        productStreamId: {
            source: 'static',
            value: null,
        },
        productStreamSorting: {
            source: 'static',
            value: 'name:ASC',
        },
        productStreamLimit: {
            source: 'static',
            value: 10,
        },
        defaultProductValues: {
            source: 'static',
            value: ['name', 'price'],
        },
        propertyGroups: {
            source: 'static',
            value: ['color', 'size'],
        },
        highlightFirstProduct: {
            source: 'static',
            value: false,
        },
    },
});
