Shopware.Component.register('sw-cms-el-ict-platform-support', () => import('./component'));
Shopware.Component.register('sw-cms-el-config-ict-platform-support', () => import('./config'));
Shopware.Component.register('sw-cms-el-preview-ict-platform-support', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'ict-platform-support',
    label: 'ict-cms-bundle-element.cms.elements.ictPlatformSupport.label',
    component: 'sw-cms-el-ict-platform-support',
    configComponent: 'sw-cms-el-config-ict-platform-support',
    previewComponent: 'sw-cms-el-preview-ict-platform-support',
    platformSupportTemplates: {
        iconDisplayType: 'icon',
        platformIcon: null,
        iconSizePx: '48',
        iconColor: '#ffffff',
        iconBackgroundType: 'solid',
        iconBackgroundColor: '#005AE5',
        iconGradientColor1: '#005AE5',
        iconGradientColor2: '#0080FF',
        iconGradientDirection: 'to bottom right',
        iconBackgroundShape: 'rounded',
        platformIconShadow: false,
        platformShadowColor: '#808080',
        platformLabel: 'Item Name',
        platformLabelSizePx: '14',
        platformLabelWeight: '500',
        platformLabelColor: '#1a1a2e',
        textAlignment: 'center-bottom',
        textHoverUnderline: false,
        textHoverColorChange: false,
        textHoverColor: '#005AE5',
        linkType: 'external',
        platformLinkUrl: '',
        platformLinkTarget: '_self'
    },
    defaultConfig: {
        platformBlockTitle: {
            source: 'static',
            value: 'Our Integrations'
        },
        platformTitleSize: {
            source: 'static',
            value: '28'
        },
        platformTitleLineHeight: {
            source: 'static',
            value: '34'
        },
        platformTitleColor: {
            source: 'static',
            value: '#1a1a2e'
        },
        platformTitleAlignment: {
            source: 'static',
            value: 'center'
        },
        backgroundType: {
            source: 'static',
            value: 'color'
        },
        platformBlockBackground: {
            source: 'static',
            value: '#f8f9fa'
        },
        backgroundImage: {
            source: 'static',
            value: null
        },
        backgroundImageMinHeight: {
            source: 'static',
            value: '400px'
        },
        backgroundVideo: {
            source: 'static',
            value: null
        },
        backgroundVideoUploadType: {
            source: 'static',
            value: 'file'
        },
        backgroundVideoUploadUrl: {
            source: 'static',
            value: ''
        },
        gradientColor1: {
            source: 'static',
            value: '#005AE5'
        },
        gradientColor2: {
            source: 'static',
            value: '#0080FF'
        },
        gradientDirection: {
            source: 'static',
            value: 'to right'
        },
        hoverGradientColor1: {
            source: 'static',
            value: '#0044bb'
        },
        hoverGradientColor2: {
            source: 'static',
            value: '#0066cc'
        },
        platformBlockBorderRadius: {
            source: 'static',
            value: 12
        },
        platformAlignment: {
            source: 'static',
            value: 'center'
        },
        platformItems: {
            source: 'static',
            value: []
        }
    }
});
