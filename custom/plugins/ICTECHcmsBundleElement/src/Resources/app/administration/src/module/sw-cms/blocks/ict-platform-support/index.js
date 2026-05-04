Shopware.Component.register('sw-cms-preview-ict-platform-support', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-platform-support', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-platform-support',
    label: 'ict-cms-bundle-element.cms.blocks.ictPlatformSupport.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-platform-support',
    previewComponent: 'sw-cms-preview-ict-platform-support',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        platformSupport: {
            type: 'ict-platform-support',
        },
    },
});
