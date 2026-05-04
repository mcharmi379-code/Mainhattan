const ELEMENT_NAME = 'ict-two-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-two-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-two-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictTwoColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-two-column',
    previewComponent: 'sw-cms-preview-ict-two-column',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        left: {
            type: ELEMENT_NAME,
        },
        right: {
            type: ELEMENT_NAME,
        },
    },
});
