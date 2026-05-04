const ELEMENT_NAME = 'ict-accordion';

function createAccordionEntry(index = 1) {
    return {
        title: `Accordion item ${index}`,
        content: '<p>Add accordion content here.</p>',
        expanded: index === 1,
    };
}

function createAccordionEntries() {
    return [
        createAccordionEntry(1),
        createAccordionEntry(2),
    ];
}

Shopware.Component.register('sw-cms-el-preview-ict-accordion', () => import('./preview'));
Shopware.Component.register('sw-cms-el-config-ict-accordion', () => import('./config'));
Shopware.Component.register('sw-cms-el-ict-accordion', () => import('./component'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictAccordion.label',
    component: 'sw-cms-el-ict-accordion',
    configComponent: 'sw-cms-el-config-ict-accordion',
    previewComponent: 'sw-cms-el-preview-ict-accordion',
    defaultConfig: {
        title: {
            source: 'static',
            value: '',
        },
        description: {
            source: 'static',
            value: '',
        },
        displayMode: {
            source: 'static',
            value: 'single',
        },
        entries: {
            source: 'static',
            value: createAccordionEntries(),
            type: Array,
            required: true,
        },
    },
});
