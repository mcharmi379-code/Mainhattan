Shopware.Component.register('sw-cms-preview-ict-content-tabs', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-content-tabs', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-content-tabs',
    label: 'ict-cms-bundle-element.cms.blocks.ictContentTabs.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-content-tabs',
    previewComponent: 'sw-cms-preview-ict-content-tabs',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        contentTabs: {
            type: 'ict-content-tabs',
        },
    },
});
