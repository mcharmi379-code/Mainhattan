const { Snippet } = Shopware;

Shopware.Component.register('sw-cms-el-mainhattan-three-card', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-mainhattan-three-card', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-mainhattan-three-card', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'mainhattan-three-card',
    label: 'sw-cms.elements.mainhattanThreeCard.label',
    component: 'sw-cms-el-mainhattan-three-card',
    configComponent: 'sw-cms-el-config-mainhattan-three-card',
    previewComponent: 'sw-cms-el-preview-mainhattan-three-card',
    defaultConfig: {
        heading: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.heading'),
        },
        cardOneImage: {
            source: 'static',
            value: null,
            entity: {
                name: 'media',
            },
        },
        cardOneTitle: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardOneTitle'),
        },
        cardOneDescription: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardOneDescription'),
        },
        cardOneButtonText: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.buttonText'),
        },
        cardOneButtonLink: {
            source: 'static',
            value: '#',
        },
        cardTwoImage: {
            source: 'static',
            value: null,
            entity: {
                name: 'media',
            },
        },
        cardTwoTitle: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardTwoTitle'),
        },
        cardTwoDescription: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardTwoDescription'),
        },
        cardTwoButtonText: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.buttonText'),
        },
        cardTwoButtonLink: {
            source: 'static',
            value: '#',
        },
        cardThreeImage: {
            source: 'static',
            value: null,
            entity: {
                name: 'media',
            },
        },
        cardThreeTitle: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardThreeTitle'),
        },
        cardThreeDescription: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.cardThreeDescription'),
        },
        cardThreeButtonText: {
            source: 'static',
            value: Snippet.tc('sw-cms.elements.mainhattanThreeCard.defaults.buttonText'),
        },
        cardThreeButtonLink: {
            source: 'static',
            value: '#',
        },
    },
});
