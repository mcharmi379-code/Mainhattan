const ELEMENT_NAME = 'ict-image-gallery';

Shopware.Component.register('sw-cms-el-preview-ict-image-gallery', () => import('./preview'));
Shopware.Component.register('sw-cms-el-config-ict-image-gallery', () => import('./config'));
Shopware.Component.register('sw-cms-el-ict-image-gallery', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictImageGallery.label',
    component: 'sw-cms-el-ict-image-gallery',
    configComponent: 'sw-cms-el-config-ict-image-gallery',
    previewComponent: 'sw-cms-el-preview-ict-image-gallery',
    defaultConfig: {
        galleryTitle: {
            source: 'static',
            value: '',
        },
        columns: {
            source: 'static',
            value: 4,
        },
        galleryItems: {
            source: 'static',
            value: [],
            type: Array,
            required: true,
        },
    },
});
