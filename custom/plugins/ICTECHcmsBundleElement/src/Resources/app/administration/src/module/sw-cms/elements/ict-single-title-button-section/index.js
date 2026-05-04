Shopware.Component.register('sw-cms-el-ict-single-title-button-section', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-single-title-button-section', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-single-title-button-section', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-single-title-button-section',
    label: 'sw-cms.elements.ictSingleTitleButtonSection.label',
    component: 'sw-cms-el-ict-single-title-button-section',
    configComponent: 'sw-cms-el-config-ict-single-title-button-section',
    previewComponent: 'sw-cms-el-preview-ict-single-title-button-section',
    defaultConfig: {
        backgroundType: {
            source: 'static',
            value: 'gbColor',
        },
        backgroundColor: {
            source: 'static',
            value: '#0b5c8a',
        },
        backgroundGradientColor1: {
            source: 'static',
            value: '#0b5c8a',
        },
        backgroundGradientColor2: {
            source: 'static',
            value: '#1559d6',
        },
        minHeight: {
            source: 'static',
            value: '300px',
        },
        title: {
            source: 'static',
            value: 'Lorem ipsum headline',
        },
        titleColor: {
            source: 'static',
            value: '#ffffff',
        },
        description: {
            source: 'static',
            value: '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
        },
        descriptionColor: {
            source: 'static',
            value: '#e9ecef',
        },
        leftSectionButtonText: {
            source: 'static',
            value: 'Primary CTA',
        },
        leftSectionButtonTarget: {
            source: 'static',
            value: '_self',
        },
        leftSectionButtonLinkType: {
            source: 'static',
            value: 'internal',
        },
        leftSectionButtonLink: {
            source: 'static',
            value: '',
        },
        leftSectionButtonBackgroundColor: {
            source: 'static',
            value: '#0064ff',
        },
        leftSectionButtonTextColor: {
            source: 'static',
            value: '#ffffff',
        },
        rightSectionButtonText: {
            source: 'static',
            value: 'Secondary CTA',
        },
        rightSectionButtonTarget: {
            source: 'static',
            value: '_self',
        },
        rightSectionButtonLinkType: {
            source: 'static',
            value: 'internal',
        },
        rightSectionButtonLink: {
            source: 'static',
            value: '',
        },
        rightSectionButtonBackgroundColor: {
            source: 'static',
            value: '#6c757d',
        },
        rightSectionButtonTextColor: {
            source: 'static',
            value: '#ffffff',
        },
    },
});
