const ELEMENT_NAME = 'ict-vertical-tab';

Shopware.Component.register('sw-cms-el-preview-ict-vertical-tab', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-vertical-tab', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-vertical-tab', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictVerticalTab.label',
    component: 'sw-cms-el-ict-vertical-tab',
    configComponent: 'sw-cms-el-config-ict-vertical-tab',
    previewComponent: 'sw-cms-el-preview-ict-vertical-tab',
    defaultConfig: {
        tabs: {
            source: 'static',
            value: [
                {
                    title: 'Tab 1',
                    header: 'Header 1',
                    content: 'Content 1',
                    buttonText: 'Learn More',
                    linkType: 'url',
                    linkUrl: '',
                    openInNewTab: false,
                },
            ],
        },
    },
});
