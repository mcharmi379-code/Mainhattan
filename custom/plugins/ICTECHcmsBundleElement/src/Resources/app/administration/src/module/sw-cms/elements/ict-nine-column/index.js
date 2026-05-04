const ELEMENT_NAME = 'ict-nine-column';
Shopware.Component.register('sw-cms-el-preview-ict-nine-column', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-nine-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictNineColumn.label',
    hidden: true,
    component: 'sw-cms-el-ict-nine-column',
    previewComponent: 'sw-cms-el-preview-ict-nine-column',
    defaultData: {
        media: null,
        mediaId: null,
    },
});
