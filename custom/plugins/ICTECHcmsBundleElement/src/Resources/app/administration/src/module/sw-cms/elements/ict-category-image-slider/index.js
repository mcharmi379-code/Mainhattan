import './preview/sw-cms-el-preview-ict-category-image-slider.scss';
import preview from './preview';
import config from './config';
import component from './component';

Shopware.Component.register('sw-cms-el-preview-ict-category-image-slider', preview);
Shopware.Component.register('sw-cms-el-config-ict-category-image-slider', config);
Shopware.Component.register('sw-cms-el-ict-category-image-slider', component);

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-category-image-slider',
    label: 'ict-cms-bundle-element.cms.elements.ictCategoryImageSlider.label',
    component: 'sw-cms-el-ict-category-image-slider',
    configComponent: 'sw-cms-el-config-ict-category-image-slider',
    previewComponent: 'sw-cms-el-preview-ict-category-image-slider',
    defaultConfig: {
        cards: {
            source: 'static',
            value: [],
        },
        validationToken: {
            source: 'static',
            value: '',
            required: true,
        },
    },
});

