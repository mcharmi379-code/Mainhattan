const ELEMENT_NAME = 'ict-subcategory-navigation';

Shopware.Component.register('sw-cms-el-preview-ict-subcategory-navigation', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-subcategory-navigation', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-subcategory-navigation', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictSubcategoryNavigation.label',
    component: 'sw-cms-el-ict-subcategory-navigation',
    configComponent: 'sw-cms-el-config-ict-subcategory-navigation',
    previewComponent: 'sw-cms-el-preview-ict-subcategory-navigation',
    defaultConfig: {
        entryPoint: {
            source: 'static',
            value: 'current',
        },
        entryCategory: {
            source: 'static',
            value: null,
        },
        manualCategories: {
            source: 'static',
            value: [],
        },
        categoryLevels: {
            source: 'static',
            value: 1,
        },
        showProductCount: {
            source: 'static',
            value: false,
        },
    },
});
