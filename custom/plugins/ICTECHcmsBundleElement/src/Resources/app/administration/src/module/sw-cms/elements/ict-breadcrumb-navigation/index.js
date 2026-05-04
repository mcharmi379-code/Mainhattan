const ELEMENT_NAME = 'ict-breadcrumb-navigation';

Shopware.Component.register('sw-cms-el-preview-ict-breadcrumb-navigation', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-breadcrumb-navigation', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-breadcrumb-navigation', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictBreadcrumbNavigation.label',
    component: 'sw-cms-el-ict-breadcrumb-navigation',
    configComponent: 'sw-cms-el-config-ict-breadcrumb-navigation',
    previewComponent: 'sw-cms-el-preview-ict-breadcrumb-navigation',
    defaultConfig: {
        alignment: {
            source: 'static',
            value: 'left',
        },
        textColor: {
            source: 'static',
            value: 'var(--bs-body-color)',
        },
    },
});
