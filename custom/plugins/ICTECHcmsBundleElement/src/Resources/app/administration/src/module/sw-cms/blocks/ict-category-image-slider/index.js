import preview from './preview';
import component from './component';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-category-image-slider', preview);
Shopware.Component.register('sw-cms-block-ict-category-image-slider', component);

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-category-image-slider',
    label: 'ict-cms-bundle-element.cms.blocks.ictCategoryImageSlider.label',
   category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-category-image-slider',
    previewComponent: 'sw-cms-preview-ict-category-image-slider',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        slider: 'ict-category-image-slider',
    },
});

