const ELEMENT_NAME = 'ict-story-card';

Shopware.Component.register('sw-cms-el-preview-ict-story-card', () => import('./preview'));
Shopware.Component.register('sw-cms-el-ict-story-card', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-story-card', () => import('./config'));

Shopware.Service('cmsService').registerCmsElement({
    name: ELEMENT_NAME,
    label: 'ict-cms-bundle-element.cms.elements.ictStoryCard.label',
    component: 'sw-cms-el-ict-story-card',
    configComponent: 'sw-cms-el-config-ict-story-card',
    previewComponent: 'sw-cms-el-preview-ict-story-card',
    defaultConfig: {
        mediaId: {
            source: 'static',
            value: null,
            required: false,
        },
        title: {
            source: 'static',
            value: '',
        },
        subline: {
            source: 'static',
            value: '',
        },
        subtitle: {
            source: 'static',
            value: '',
        },
        linkType: {
            source: 'static',
            value: 'url',
        },
        linkUrl: {
            source: 'static',
            value: '',
        },
        openInNewTab: {
            source: 'static',
            value: false,
        },
    },
});

