const BLOCK_NAME = 'ict-alert-hint';
const ELEMENT_NAME = 'ict-alert-hint';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-alert-hint-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-alert-hint', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictAlertHint.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-alert-hint',
    previewComponent: 'sw-cms-preview-ict-alert-hint-block',
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
