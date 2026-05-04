const BLOCK_NAME = 'ict-accordion';
const ELEMENT_NAME = 'ict-accordion';
const BLOCK_CATEGORY = 'ict-cms-elements';

Shopware.Component.register('sw-cms-preview-ict-accordion-block', () => import('./preview'));
Shopware.Component.register('sw-cms-block-ict-accordion', () => import('./component'));

Shopware.Service('cmsService').registerCmsBlock({
    name: BLOCK_NAME,
    label: 'ict-cms-bundle-element.cms.blocks.ictAccordion.label',
    category: BLOCK_CATEGORY,
    component: 'sw-cms-block-ict-accordion',
    previewComponent: 'sw-cms-preview-ict-accordion-block',
    defaultConfig: {
        marginBottom: '32px',
        marginTop: '32px',
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
