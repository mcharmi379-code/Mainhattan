const ELEMENT_NAME = 'ict-seven-column';

Shopware.Component.register('sw-cms-el-ict-seven-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictSevenColumn.label',
    component: 'sw-cms-el-ict-seven-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
