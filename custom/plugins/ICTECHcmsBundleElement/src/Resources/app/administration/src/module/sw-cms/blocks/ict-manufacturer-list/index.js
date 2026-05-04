const BLOCK_NAME = 'ict-manufacturer-list';
const ELEMENT_NAME = 'ict-manufacturer-list';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-manufacturer-list-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-manufacturer-list', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictManufacturerList.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-manufacturer-list',
    previewComponent: 'sw-cms-preview-ict-manufacturer-list-block',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
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
