const BLOCK_NAME = 'ict-eight-column';
const ELEMENT_NAME = 'ict-eight-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-eight-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-eight-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictEightColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-eight-column',
    previewComponent: 'sw-cms-preview-ict-eight-column',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        column1: {
            type: ELEMENT_NAME,
        },
        column2: {
            type: ELEMENT_NAME,
        },
        column3: {
            type: ELEMENT_NAME,
        },
        column4: {
            type: ELEMENT_NAME,
        },
        column5: {
            type: ELEMENT_NAME,
        },
        column6: {
            type: ELEMENT_NAME,
        },
        column7: {
            type: ELEMENT_NAME,
        },
        column8: {
            type: ELEMENT_NAME,
        },
    },
});
