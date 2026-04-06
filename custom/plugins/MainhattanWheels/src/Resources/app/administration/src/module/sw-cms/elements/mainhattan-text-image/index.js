Shopware.Component.register('sw-cms-el-mainhattan-text-image',()=>import('./component'));
Shopware.Component.register('sw-cms-el-config-mainhattan-text-image',()=>import('./config'));
Shopware.Component.register('sw-cms-el-preview-mainhattan-text-image',()=>import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name:'mainhattan-text-image',
    label:'sw-cms.blocks.mainhattanTextimage.label',
    component:'sw-cms-el-mainhattan-text-image',
    configComponent:'sw-cms-el-config-mainhattan-text-image',
    previewComponent:'sw-cms-el-preview-mainhattan-text-image',
    defaultConfig:{
        heading:{
            source: 'static',
            value:'This is the Heading',
        },
        media: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'media',
            },
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        url: {
            source: 'static',
            value: null,
        },
        newTab: {
            source: 'static',
            value: false,
        },
        minHeight: {
            source: 'static',
            value: '340px',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
    }
});
