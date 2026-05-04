const BLOCK_NAME = 'ict-story-card';
const ELEMENT_NAME = 'ict-story-card';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-story-card-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-story-card', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictStoryCard.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-story-card',
    previewComponent: 'sw-cms-preview-ict-story-card-block',
    defaultConfig: {
        marginBottom: '16px',
        marginTop: '16px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: ELEMENT_NAME,
        },
    },
});

