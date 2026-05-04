Shopware.Component.register('sw-cms-preview-ict-single-title-button-section', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-single-title-button-section', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-single-title-button-section',
    label: 'sw-cms.blocks.ictSingleTitleButtonSection.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-single-title-button-section',
    previewComponent: 'sw-cms-preview-ict-single-title-button-section',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        content: {
            type: 'ict-single-title-button-section'
        }
    }
});