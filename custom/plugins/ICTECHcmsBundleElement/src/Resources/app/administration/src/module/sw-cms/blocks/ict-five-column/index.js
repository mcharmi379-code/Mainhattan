const BLOCK_NAME = 'ict-five-column';
const DEFAULT_ELEMENT_NAME = 'ict-five-column';
const BLOCK_CATEGORY = 'ict-cms-layouts';

Shopware.Component.register('sw-cms-preview-ict-five-column', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-five-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictFiveColumn.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-five-column',
    previewComponent: 'sw-cms-preview-ict-five-column',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        left: {
            type: DEFAULT_ELEMENT_NAME,
        },
        leftCenter: {
            type: DEFAULT_ELEMENT_NAME,
        },
        center: {
            type: DEFAULT_ELEMENT_NAME,
        },
        rightCenter: {
            type: DEFAULT_ELEMENT_NAME,
        },
        right: {
            type: DEFAULT_ELEMENT_NAME,
        },
    },
});
