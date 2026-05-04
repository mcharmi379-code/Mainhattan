const ELEMENT_NAME = 'ict-five-column';
Shopware.Component.register('sw-cms-el-preview-ict-five-column', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-five-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictFiveColumn.label',
    hidden: true,
    component: 'sw-cms-el-ict-five-column',
    previewComponent: 'sw-cms-el-preview-ict-five-column',
    defaultData: {
        media: null,
        mediaId: null,
    },
});
