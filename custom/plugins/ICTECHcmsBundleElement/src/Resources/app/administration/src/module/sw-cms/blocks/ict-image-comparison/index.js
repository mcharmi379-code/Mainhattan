const BLOCK_NAME = 'ict-image-comparison';
const ELEMENT_NAME = 'ict-image-comparison';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-image-comparison-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-image-comparison', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictImageComparison.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-image-comparison',
    previewComponent: 'sw-cms-preview-ict-image-comparison-block',
    defaultConfig: {
        marginBottom: '16px',
        marginTop: '16px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: ELEMENT_NAME,
        },
    },
});
