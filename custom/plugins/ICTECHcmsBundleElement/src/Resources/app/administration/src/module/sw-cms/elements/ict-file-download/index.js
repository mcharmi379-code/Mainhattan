const ELEMENT_NAME = 'ict-file-download';

Shopware.Component.register('sw-cms-el-preview-ict-file-download', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-file-download', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-file-download', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictFileDownload.label',
    component: 'sw-cms-el-ict-file-download',
    configComponent: 'sw-cms-el-config-ict-file-download',
    previewComponent: 'sw-cms-el-preview-ict-file-download',
    defaultConfig: {
        title: {
            source: 'static',
            value: '',
        },
        subtitle: {
            source: 'static',
            value: '',
        },
        buttonText: {
            source: 'static',
            value: '',
        },
        showSpecifications: {
            source: 'static',
            value: false,
        },
        mediaId: {
            source: 'static',
            value: null,
            required: false,
        },
        previewImageId: {
            source: 'static',
            value: null,
            required: false,
        },
    },
});
