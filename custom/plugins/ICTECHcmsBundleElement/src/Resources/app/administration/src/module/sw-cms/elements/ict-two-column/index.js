const ELEMENT_NAME = 'ict-two-column';
Shopware.Component.register('sw-cms-el-preview-ict-two-column', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-two-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictTwoColumn.label',
    hidden: true,
    component: 'sw-cms-el-ict-two-column',
    previewComponent: 'sw-cms-el-preview-ict-two-column',
    defaultData: {
        media: null,
        mediaId: null,
    },
});
