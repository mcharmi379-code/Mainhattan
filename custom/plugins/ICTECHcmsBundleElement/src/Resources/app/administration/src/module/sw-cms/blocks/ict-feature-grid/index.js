Shopware.Component.register('sw-cms-preview-ict-feature-grid', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-feature-grid', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-feature-grid',
    label: 'ict-cms-bundle-element.cms.blocks.ictFeatureGrid.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-feature-grid',
    previewComponent: 'sw-cms-preview-ict-feature-grid',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        featureGrid: {
            type: 'ict-feature-grid',
        },
    },
});
