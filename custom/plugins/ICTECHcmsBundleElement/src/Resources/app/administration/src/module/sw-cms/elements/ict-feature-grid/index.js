Shopware.Component.register('sw-cms-el-ict-feature-grid', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-feature-grid', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-feature-grid', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-feature-grid',
    label: 'ict-cms-bundle-element.cms.elements.ictFeatureGrid.label',
    component: 'sw-cms-el-ict-feature-grid',
    configComponent: 'sw-cms-el-config-ict-feature-grid',
    previewComponent: 'sw-cms-el-preview-ict-feature-grid',
    defaultConfig: {
        numberOfColumns: { source: 'static', value: 3 },
        cardSpacing: { source: 'static', value: 20 },
        cards: { source: 'static', value: [] },
    },
});
