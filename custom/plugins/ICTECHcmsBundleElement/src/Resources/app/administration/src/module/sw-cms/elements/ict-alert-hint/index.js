const ELEMENT_NAME = 'ict-alert-hint';

Shopware.Component.register('sw-cms-el-preview-ict-alert-hint', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-alert-hint', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-alert-hint', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictAlertHint.label',
    component: 'sw-cms-el-ict-alert-hint',
    configComponent: 'sw-cms-el-config-ict-alert-hint',
    previewComponent: 'sw-cms-el-preview-ict-alert-hint',
    defaultConfig: {
        alertType: {
            source: 'static',
            value: 'info',
        },
        layoutType: {
            source: 'static',
            value: 'custom-callout',
        },
        activateIcon: {
            source: 'static',
            value: true,
        },
        title: {
            source: 'static',
            value: '',
        },
        content: {
            source: 'static',
            value: '',
        },
    },
});
