Shopware.Component.register('sw-cms-el-ict-two-column-three-image', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-two-column-three-image', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-two-column-three-image', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-two-column-three-image',
    label: 'sw-cms.elements.ictTwoColumnThreeImage.label',
    component: 'sw-cms-el-ict-two-column-three-image',
    configComponent: 'sw-cms-el-config-ict-two-column-three-image',
    previewComponent: 'sw-cms-el-preview-ict-two-column-three-image',
    defaultConfig: {
        leftImage: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        leftImageHeight: {
            source: 'static',
            value: '100%'
        },
        leftImageObjectFit: {
            source: 'static',
            value: 'cover'
        },
        leftImageDescription: {
            source: 'static',
            value: 'Headline'
        },
        leftButtonTitle: {
            source: 'static',
            value: 'LEARN MORE'
        },
        leftButtonLinkType: {
            source: 'static',
            value: 'internal'
        },
        leftButtonLink: {
            source: 'static',
            value: ''
        },
        leftButtonTarget: {
            source: 'static',
            value: '_self'
        },
        leftButtonBackgroundColor: {
            source: 'static',
            value: '#005AE5'
        },
        leftButtonHoverBackgroundColor: {
            source: 'static',
            value: '#00f5ff'
        },
        leftButtonTextColor: {
            source: 'static',
            value: '#ffffff'
        },
        leftButtonHoverTextColor: {
            source: 'static',
            value: '#000000'
        },
        leftButtonBorderColor: {
            source: 'static',
            value: '#00f5ff'
        },
        leftButtonHoverBorderColor: {
            source: 'static',
            value: '#f3f4f6'
        },
        leftButtonIcon: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        leftButtonIconSize: {
            source: 'static',
            value: '25'
        },
        leftButtonIconHoverColor: {
            source: 'static',
            value: '#000000'
        },
        leftButtonIconHoverActive: {
            source: 'static',
            value: false
        },
        rightTopImage: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        rightTopImageHeight: {
            source: 'static',
            value: 300
        },
        rightTopImageObjectFit: {
            source: 'static',
            value: 'cover'
        },
        rightTopImageDescription: {
            source: 'static',
            value: 'Headline'
        },
        rightTopButtonTitle: {
            source: 'static',
            value: 'LEARN MORE'
        },
        rightTopButtonLinkType: {
            source: 'static',
            value: 'internal'
        },
        rightTopButtonLink: {
            source: 'static',
            value: ''
        },
        rightTopButtonTarget: {
            source: 'static',
            value: '_self'
        },
        rightTopButtonBackgroundColor: {
            source: 'static',
            value: '#005AE5'
        },
        rightTopButtonHoverBackgroundColor: {
            source: 'static',
            value: '#00f5ff'
        },
        rightTopButtonTextColor: {
            source: 'static',
            value: '#ffffff'
        },
        rightTopButtonHoverTextColor: {
            source: 'static',
            value: '#000000'
        },
        rightTopButtonBorderColor: {
            source: 'static',
            value: '#00f5ff'
        },
        rightTopButtonHoverBorderColor: {
            source: 'static',
            value: '#f3f4f6'
        },
        rightTopButtonIcon: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        rightTopButtonIconSize: {
            source: 'static',
            value: '25'
        },
        rightTopButtonIconHoverColor: {
            source: 'static',
            value: '#000000'
        },
        rightTopButtonIconHoverActive: {
            source: 'static',
            value: false
        },
        rightBottomImage: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        rightBottomImageHeight: {
            source: 'static',
            value: 300
        },
        rightBottomImageObjectFit: {
            source: 'static',
            value: 'cover'
        },
        rightBottomImageDescription: {
            source: 'static',
            value: 'Headline'
        },
        rightBottomButtonTitle: {
            source: 'static',
            value: 'LEARN MORE'
        },
        rightBottomButtonLinkType: {
            source: 'static',
            value: 'internal'
        },
        rightBottomButtonLink: {
            source: 'static',
            value: ''
        },
        rightBottomButtonTarget: {
            source: 'static',
            value: '_self'
        },
        rightBottomButtonBackgroundColor: {
            source: 'static',
            value: '#005AE5'
        },
        rightBottomButtonHoverBackgroundColor: {
            source: 'static',
            value: '#00f5ff'
        },
        rightBottomButtonTextColor: {
            source: 'static',
            value: '#ffffff'
        },
        rightBottomButtonHoverTextColor: {
            source: 'static',
            value: '#000000'
        },
        rightBottomButtonBorderColor: {
            source: 'static',
            value: '#00f5ff'
        },
        rightBottomButtonHoverBorderColor: {
            source: 'static',
            value: '#f3f4f6'
        },
        rightBottomButtonIcon: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        rightBottomButtonIconSize: {
            source: 'static',
            value: '25'
        },
        rightBottomButtonIconHoverColor: {
            source: 'static',
            value: '#000000'
        },
        rightBottomButtonIconHoverActive: {
            source: 'static',
            value: false
        },
    }
});
