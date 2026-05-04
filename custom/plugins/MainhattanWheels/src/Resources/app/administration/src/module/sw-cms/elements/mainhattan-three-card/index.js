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
            value: 'Mainhattan Wheels',
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
            value: 'Alufelgen reparieren & -veredeln',
        },
        cardOneDescription: {
            source: 'static',
            value: 'Professionelle Felgenreparatur. Technische und Optische Instandsetzung von Felgen. Felgenveredelung in hoechster Qualitaet.',
        },
        cardOneButtonText: {
            source: 'static',
            value: 'Zum Felgendoktor - alle Bereiche',
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
            value: 'Anlagen fuer die Felgen- & Radbearbeitung',
        },
        cardTwoDescription: {
            source: 'static',
            value: 'CNC-Maschinen fuer die Instandsetzung glanzgedrehter Felgen, Pulverbeschichtungsanlagen, Richtmaschinen u.v.m. Inkl. Schulung und Support!',
        },
        cardTwoButtonText: {
            source: 'static',
            value: 'Maschinen fuer die Felgenbearbeitung',
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
            value: 'Mainhattan-Wheels Leichtmetallraeder. Deluxe-Wheels Felgendesigns, eigene Radentwicklung und Realisierung von Kundenprojekten.',
        },
        cardThreeButtonText: {
            source: 'static',
            value: 'Zum Felgenvertrieb',
        },
        cardThreeButtonLink: {
            source: 'static',
            value: '#',
        },
    },
});
