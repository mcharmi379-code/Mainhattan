Shopware.Component.register('sw-cms-block-ict-hero-section', () => import('./component'));
Shopware.Component.register('sw-cms-preview-ict-hero-section', () => import('./preview'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'ict-hero-section',
    label: 'sw-cms.blocks.ictHeroSection.label',
    category: 'ict-cms-elements',
    component: 'sw-cms-block-ict-hero-section',
    previewComponent: 'sw-cms-preview-ict-hero-section',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        heroSection: {
            type: 'ict-hero-section',
            default: {
                config: {
                    labelText: {
                        source: 'static',
                        value: 'Label Text'
                    },
                    headlineText: {
                        source: 'static',
                        value: 'Headline'
                    },
                    descriptionText: {
                        source: 'static',
                        value: 'Description'
                    },
                    primaryButtonText: {
                        source: 'static',
                        value: 'Button Text →'
                    },
                    secondaryButtonText: {
                        source: 'static',
                        value: 'Button Text'
                    },
                    showSecondaryButton: {
                        source: 'static',
                        value: true
                    },
                    backgroundType: {
                        source: 'static',
                        value: 'gbColor'
                    },
                    featureItems: {
                        source: 'static',
                        value: [
                            {
                                title: 'Modular Design',
                                description: 'Scalable configurations',
                                iconColor: '#ff6b6b'
                            },
                            {
                                title: 'Ultra-Thin Bezels',
                                description: 'Seamless viewing',
                                iconColor: '#ff6b6b'
                            },
                            {
                                title: 'Any Size',
                                description: 'Custom dimensions',
                                iconColor: '#ff6b6b'
                            },
                            {
                                title: '24/7 Operation',
                                description: 'Commercial grade',
                                iconColor: '#ff6b6b'
                            }
                        ]
                    }
                },
                data: {}
            }
        }
    }
});