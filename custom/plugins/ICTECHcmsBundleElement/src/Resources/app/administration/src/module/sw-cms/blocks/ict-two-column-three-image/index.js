Shopware.Component.register('sw-cms-preview-ict-two-column-three-image', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-two-column-three-image', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-two-column-three-image',
    label: 'sw-cms.blocks.ictTwoColumnThreeImage.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-two-column-three-image',
    previewComponent: 'sw-cms-preview-ict-two-column-three-image',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        ictTwoColumnThreeImage: {
            type: 'ict-two-column-three-image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'standard' },
                },
                left: {
                    type: 'image',
                    default: {
                        config: {
                            displayMode: { source: 'static', value: 'cover' },
                        },
                        data: {
                            media: {
                                value: 'bundles/administration/static/img/cms/preview_camera_large.jpg',
                                source: 'default',
                            },
                        },
                    },
                },
                right: {
                    type: 'image',
                    default: {
                        config: {
                            displayMode: { source: 'static', value: 'cover' },
                        },
                        data: {
                            media: {
                                value: 'bundles/administration/static/img/cms/preview_glasses_large.jpg',
                                source: 'default',
                            },
                        },
                    },
                },
            },
        },
    },
});