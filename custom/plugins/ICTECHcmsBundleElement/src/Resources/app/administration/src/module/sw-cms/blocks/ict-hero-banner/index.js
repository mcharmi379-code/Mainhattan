const BLOCK_NAME = 'ict-hero-banner';
const ELEMENT_NAME = 'ict-hero-banner';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-hero-banner-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-hero-banner', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictHeroBanner.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-hero-banner',
    previewComponent: 'sw-cms-preview-ict-hero-banner-block',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'full_width',
    },
    slots: {
        content: {
            type: ELEMENT_NAME,
        },
    },
});
