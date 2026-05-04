const ELEMENT_NAME = 'ict-ten-column';

Shopware.Component.register('sw-cms-el-ict-ten-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictTenColumn.label',
    component: 'sw-cms-el-ict-ten-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
