const ELEMENT_NAME = 'ict-eight-column';

Shopware.Component.register('sw-cms-el-ict-eight-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictEightColumn.label',
    component: 'sw-cms-el-ict-eight-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
