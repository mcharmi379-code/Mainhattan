const ELEMENT_NAME = 'ict-product-matrix';

Shopware.Component.register('sw-cms-el-ict-product-matrix', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-product-matrix', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-product-matrix', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictProductMatrix.label',
    component: 'sw-cms-el-ict-product-matrix',
    configComponent: 'sw-cms-el-config-ict-product-matrix',
    previewComponent: 'sw-cms-el-preview-ict-product-matrix',
    defaultConfig: {
        product: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'product',
            },
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
    },
});
