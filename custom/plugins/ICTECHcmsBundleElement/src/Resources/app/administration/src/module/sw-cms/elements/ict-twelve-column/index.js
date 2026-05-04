const ELEMENT_NAME = 'ict-twelve-column';

Shopware.Component.register('sw-cms-el-ict-twelve-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictTwelveColumn.label',
    component: 'sw-cms-el-ict-twelve-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
