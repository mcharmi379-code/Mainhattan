Shopware.Component.register('sw-cms-preview-ict-overview-cards-6', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-overview-cards-6', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-overview-cards-6',
    label: 'ict-cms-bundle-element.cms.blocks.ictOverviewCards6.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-overview-cards-6',
    previewComponent: 'sw-cms-preview-ict-overview-cards-6',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        overviewCards: {
            type: 'ict-overview-cards-6',
        },
    },
});
