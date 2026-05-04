const BLOCK_NAME = 'ict-vertical-tab';
const ELEMENT_NAME = 'ict-vertical-tab';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-vertical-tab-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-vertical-tab', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictVerticalTab.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-vertical-tab',
    previewComponent: 'sw-cms-preview-ict-vertical-tab-block',
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
