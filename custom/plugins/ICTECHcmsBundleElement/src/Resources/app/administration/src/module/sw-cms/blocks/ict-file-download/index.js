const BLOCK_NAME = 'ict-file-download';
const ELEMENT_NAME = 'ict-file-download';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-file-download-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-file-download', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictFileDownload.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-file-download',
    previewComponent: 'sw-cms-preview-ict-file-download-block',
    defaultConfig: {
        marginBottom: '16px',
        marginTop: '16px',
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
