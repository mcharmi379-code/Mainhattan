const ELEMENT_NAME = 'ict-three-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-three-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-three-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictThreeColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-three-column',
    previewComponent: 'sw-cms-preview-ict-three-column',
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
        center: {
            type: ELEMENT_NAME,
        },
        right: {
            type: ELEMENT_NAME,
        },
    },
});
