const BLOCK_NAME = 'ict-nine-column';
const DEFAULT_ELEMENT_NAME = 'ict-nine-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-nine-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-nine-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictNineColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-nine-column',
    previewComponent: 'sw-cms-preview-ict-nine-column',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        col1: { type: DEFAULT_ELEMENT_NAME },
        col2: { type: DEFAULT_ELEMENT_NAME },
        col3: { type: DEFAULT_ELEMENT_NAME },
        col4: { type: DEFAULT_ELEMENT_NAME },
        col5: { type: DEFAULT_ELEMENT_NAME },
        col6: { type: DEFAULT_ELEMENT_NAME },
        col7: { type: DEFAULT_ELEMENT_NAME },
        col8: { type: DEFAULT_ELEMENT_NAME },
        col9: { type: DEFAULT_ELEMENT_NAME },
    },
});
