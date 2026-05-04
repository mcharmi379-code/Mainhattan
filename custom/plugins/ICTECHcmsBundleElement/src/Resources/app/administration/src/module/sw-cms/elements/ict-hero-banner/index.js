const ELEMENT_NAME = 'ict-hero-banner';

Shopware.Component.register('sw-cms-el-preview-ict-hero-banner', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-hero-banner', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-hero-banner', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictHeroBanner.label',
    component: 'sw-cms-el-ict-hero-banner',
    configComponent: 'sw-cms-el-config-ict-hero-banner',
    previewComponent: 'sw-cms-el-preview-ict-hero-banner',
    defaultConfig: {
        logoMediaId: {
            source: 'static',
            value: null,
        },
        heroMediaId: {
            source: 'static',
            value: null,
        },
        headline: {
            source: 'static',
            value: '',
        },
        backgroundColor: {
            source: 'static',
            value: '#111111',
        },
    },
});
