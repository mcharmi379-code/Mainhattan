const ELEMENT_NAME = 'ict-content-tabs';

Shopware.Component.register('sw-cms-el-ict-content-tabs', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-content-tabs', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-content-tabs', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictContentTabs.label',
    component: 'sw-cms-el-ict-content-tabs',
    configComponent: 'sw-cms-el-config-ict-content-tabs',
    previewComponent: 'sw-cms-el-preview-ict-content-tabs',
    defaultConfig: {
        tabs: {
            source: 'static',
            value: [],
        },
    },
});
