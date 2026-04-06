Shopware.Component.register('sw-cms-block-mainhattan-three-card', () => import('./component'));
Shopware.Component.register('sw-cms-preview-mainhattan-three-card', () => import('./preview'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'mainhattan-three-card',
    label: 'sw-cms.blocks.mainhattanThreeCard.label',
    category: 'text-image',
    component: 'sw-cms-block-mainhattan-three-card',
    previewComponent: 'sw-cms-preview-mainhattan-three-card',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'mainhattan-three-card',
        },
    },
});
