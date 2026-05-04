import preview from './preview';
import component from './component';

Shopware.Component.register('sw-cms-preview-ict-banner-slider', preview);
Shopware.Component.register('sw-cms-block-ict-banner-slider', component);

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-banner-slider',
    label: 'ict-cms-bundle-element.cms.blocks.ictBannerSlider.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-banner-slider',
    previewComponent: 'sw-cms-preview-ict-banner-slider',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        slider: 'ict-banner-slider',
    },
});
