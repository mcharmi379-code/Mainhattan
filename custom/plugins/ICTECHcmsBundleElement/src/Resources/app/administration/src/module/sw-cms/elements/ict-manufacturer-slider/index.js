const ELEMENT_NAME = 'ict-manufacturer-slider';

Shopware.Component.register('sw-cms-el-preview-ict-manufacturer-slider', () => import('./preview'));
Shopware.Component.register('sw-cms-el-config-ict-manufacturer-slider', () => import('./config'));
Shopware.Component.register('sw-cms-el-ict-manufacturer-slider', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictManufacturerSlider.label',
    component: 'sw-cms-el-ict-manufacturer-slider',
    configComponent: 'sw-cms-el-config-ict-manufacturer-slider',
    previewComponent: 'sw-cms-el-preview-ict-manufacturer-slider',
    defaultConfig: {
        manufacturerIds: {
            source: 'static',
            value: [],
        },
        showManufacturerName: {
            source: 'static',
            value: true,
        },
        maxWidth: {
            source: 'static',
            value: 180,
        },
        maxHeight: {
            source: 'static',
            value: 100,
        },
        desktopItems: {
            source: 'static',
            value: 4,
        },
        tabletItems: {
            source: 'static',
            value: 2,
        },
        mobileItems: {
            source: 'static',
            value: 1,
        },
    },
});
