const BLOCK_NAME = 'ict-four-column';
const ELEMENT_NAME = 'ict-four-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-four-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-four-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictFourColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-four-column',
    previewComponent: 'sw-cms-preview-ict-four-column',
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
        centerLeft: {
            type: ELEMENT_NAME,
        },
        centerRight: {
            type: ELEMENT_NAME,
        },
        right: {
            type: ELEMENT_NAME,
        },
    },
});
