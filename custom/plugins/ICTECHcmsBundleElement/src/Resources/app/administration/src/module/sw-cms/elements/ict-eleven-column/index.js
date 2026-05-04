const ELEMENT_NAME = 'ict-eleven-column';

Shopware.Component.register('sw-cms-el-ict-eleven-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictElevenColumn.label',
    component: 'sw-cms-el-ict-eleven-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
