const ELEMENT_NAME = 'ict-manufacturer-list';

Shopware.Component.register('sw-cms-el-preview-ict-manufacturer-list', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-manufacturer-list', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictManufacturerList.label',
    component: 'sw-cms-el-ict-manufacturer-list',
    previewComponent: 'sw-cms-el-preview-ict-manufacturer-list',
});
