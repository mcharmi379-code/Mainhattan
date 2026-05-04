const ELEMENT_NAME = 'ict-image-comparison';

Shopware.Component.register('sw-cms-el-preview-ict-image-comparison', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-image-comparison', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-image-comparison', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictImageComparison.label',
    component: 'sw-cms-el-ict-image-comparison',
    configComponent: 'sw-cms-el-config-ict-image-comparison',
    previewComponent: 'sw-cms-el-preview-ict-image-comparison',
    defaultConfig: {
        layout: {
            source: 'static',
            value: 'horizontal',
        },
        beforeMediaId: {
            source: 'static',
            value: null,
        },
        afterMediaId: {
            source: 'static',
            value: null,
        },
    },
});
