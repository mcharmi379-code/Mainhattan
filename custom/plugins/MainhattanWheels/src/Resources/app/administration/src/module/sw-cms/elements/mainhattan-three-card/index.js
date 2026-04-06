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
            value: 'Heading.',
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
            value: 'Felgen reparieren & veredeln',
        },
        cardOneDescription: {
            source: 'static',
            value: '<p>Professionelle Felgenreparatur und technische Instandsetzung.</p>',
        },
        cardOneButtonText: {
            source: 'static',
            value: 'Mehr erfahren',
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
            value: 'Anlagen fuer die Felgenbearbeitung',
        },
        cardTwoDescription: {
            source: 'static',
            value: '<p>Moderne Maschinen fuer glanzgedrehte Felgen und Polierbeschichtungen.</p>',
        },
        cardTwoButtonText: {
            source: 'static',
            value: 'Mehr erfahren',
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
            value: 'Felgenproduktion',
        },
        cardThreeDescription: {
            source: 'static',
            value: '<p>Leichtmetallraeder, Radentwicklungen und kundenorientierte Produktion.</p>',
        },
        cardThreeButtonText: {
            source: 'static',
            value: 'Mehr erfahren',
        },
        cardThreeButtonLink: {
            source: 'static',
            value: '#',
        },
    },
});
