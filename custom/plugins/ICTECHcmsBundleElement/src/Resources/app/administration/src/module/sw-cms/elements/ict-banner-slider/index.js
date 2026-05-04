import './preview/sw-cms-el-preview-ict-banner-slider.scss';
import preview from './preview';
import config from './config';
import component from './component';

Shopware.Component.register('sw-cms-el-preview-ict-banner-slider', preview);
Shopware.Component.register('sw-cms-el-config-ict-banner-slider', config);
Shopware.Component.register('sw-cms-el-ict-banner-slider', component);

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-banner-slider',
    label: 'ict-cms-bundle-element.cms.elements.ictBannerSlider.label',
    component: 'sw-cms-el-ict-banner-slider',
    configComponent: 'sw-cms-el-config-ict-banner-slider',
    previewComponent: 'sw-cms-el-preview-ict-banner-slider',
    defaultConfig: {
        sliderItems: {
            source: 'static',
            value: [],
        },
        autoTransition: {
            source: 'static',
            value: false,
        },
        navigationDots: {
            source: 'static',
            value: 'none',
        },
        navigationArrows: {
            source: 'static',
            value: 'outside',
        },
        displayMode: {
            source: 'static',
            value: 'contain',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
    },
});
