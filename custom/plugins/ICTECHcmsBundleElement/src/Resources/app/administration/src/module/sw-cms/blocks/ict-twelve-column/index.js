const BLOCK_NAME = 'ict-twelve-column';
const ELEMENT_NAME = 'ict-twelve-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-twelve-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-twelve-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictTwelveColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-twelve-column',
    previewComponent: 'sw-cms-preview-ict-twelve-column',
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
        column9: {
            type: ELEMENT_NAME,
        },
        column10: {
            type: ELEMENT_NAME,
        },
        column11: {
            type: ELEMENT_NAME,
        },
        column12: {
            type: ELEMENT_NAME,
        },
    },
});
