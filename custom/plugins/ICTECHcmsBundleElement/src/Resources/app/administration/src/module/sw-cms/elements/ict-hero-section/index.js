Shopware.Component.register('sw-cms-el-ict-hero-section', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-hero-section', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-hero-section', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-hero-section',
    label: 'sw-cms.elements.ictHeroSection.label',
    component: 'sw-cms-el-ict-hero-section',
    configComponent: 'sw-cms-el-config-ict-hero-section',
    previewComponent: 'sw-cms-el-preview-ict-hero-section',
    defaultConfig: {
        backgroundType: {
            source: 'static',
            value: 'gbColor',
        },
        backgroundColor: {
            source: 'static',
            value: '#f8f9fa',
        },
        backgroundGradientColor1: {
            source: 'static',
            value: '#f8f9fa',
        },
        backgroundGradientColor2: {
            source: 'static',
            value: '#e9ecef',
        },
        minHeight: {
            source: 'static',
            value: '560px',
        },
        labelText: {
            source: 'static',
            value: 'Lorem ipsum',
        },
        labelBackgroundColor: {
            source: 'static',
            value: '#ffffff',
        },
        labelTextColor: {
            source: 'static',
            value: '#212529',
        },
        labelBorderColor: {
            source: 'static',
            value: '#dee2e6',
        },
        headlineText: {
            source: 'static',
            value: 'Lorem ipsum hero headline',
        },
        headlineColor: {
            source: 'static',
            value: '#212529',
        },
        descriptionText: {
            source: 'static',
            value: '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
        },
        descriptionColor: {
            source: 'static',
            value: '#495057',
        },
        primaryButtonText: {
            source: 'static',
            value: 'Primary action',
        },
        primaryButtonLinkType: {
            source: 'static',
            value: 'internal',
        },
        primaryButtonLink: {
            source: 'static',
            value: '',
        },
        primaryButtonTarget: {
            source: 'static',
            value: '_self',
        },
        primaryButtonBackgroundColor: {
            source: 'static',
            value: '#0064ff',
        },
        primaryButtonTextColor: {
            source: 'static',
            value: '#ffffff',
        },
        showSecondaryButton: {
            source: 'static',
            value: true,
        },
        secondaryButtonText: {
            source: 'static',
            value: 'Secondary action',
        },
        secondaryButtonLinkType: {
            source: 'static',
            value: 'internal',
        },
        secondaryButtonLink: {
            source: 'static',
            value: '',
        },
        secondaryButtonTarget: {
            source: 'static',
            value: '_self',
        },
        secondaryButtonBackgroundColor: {
            source: 'static',
            value: '#6c757d',
        },
        secondaryButtonTextColor: {
            source: 'static',
            value: '#ffffff',
        },
        featureItems: {
            source: 'static',
            value: [
                { title: 'Lorem feature one', description: 'Lorem short text.' },
                { title: 'Lorem feature two', description: 'Lorem short text.' },
                { title: 'Lorem feature three', description: 'Lorem short text.' },
                { title: 'Lorem feature four', description: 'Lorem short text.' },
            ],
        },
    },
});

