const BLOCK_NAME = 'ict-image-gallery';
const ELEMENT_NAME = 'ict-image-gallery';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-image-gallery', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-image-gallery', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictImageGallery.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-image-gallery',
    previewComponent: 'sw-cms-preview-ict-image-gallery',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        gallery: {
            type: ELEMENT_NAME,
        },
    },
});
