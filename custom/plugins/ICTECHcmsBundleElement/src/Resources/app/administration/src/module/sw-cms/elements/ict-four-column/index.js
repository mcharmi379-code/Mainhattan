const ELEMENT_NAME = 'ict-four-column';

Shopware.Component.register('sw-cms-el-ict-four-column', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictFourColumn.label',
    component: 'sw-cms-el-ict-four-column',
    hidden: true,
    defaultData: {
        media: null,
        mediaId: null,
    },
});
