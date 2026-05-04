const BLOCK_NAME = 'ict-manufacturer-slider';
const ELEMENT_NAME = 'ict-manufacturer-slider';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-manufacturer-slider-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-manufacturer-slider', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictManufacturerSlider.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-manufacturer-slider',
    previewComponent: 'sw-cms-preview-ict-manufacturer-slider-block',
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
