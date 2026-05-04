const BLOCK_NAME = 'ict-seven-column';
const ELEMENT_NAME = 'ict-seven-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-seven-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-seven-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictSevenColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-seven-column',
    previewComponent: 'sw-cms-preview-ict-seven-column',
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
    },
});
