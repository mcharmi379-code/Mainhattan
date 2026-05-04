const ELEMENT_NAME = 'ict-six-column';
Shopware.Component.register('sw-cms-el-preview-ict-six-column', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-six-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictSixColumn.label',
    hidden: true,
    component: 'sw-cms-el-ict-six-column',
    previewComponent: 'sw-cms-el-preview-ict-six-column',
    defaultData: {
        media: null,
        mediaId: null,
    },
});
