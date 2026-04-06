// import CMS from "../../../constant/sw-cms.constant";
import CMS from "../../constant/sw-cms.constant";

Shopware.Component.register('sw-cms-block-mainhattan-text-image',()=>import('./component'));
Shopware.Component.register('sw-cms-preview-mainhattan-text-image',()=>import('./preview'));

Shopware.Service('cmsService').registerCmsBlock({
    name: 'mainhattan-text-image',
    label: 'sw-cms.blocks.mainhattanTextimage.label',
    category: 'text-image',
    component: 'sw-cms-block-mainhattan-text-image',
    previewComponent: 'sw-cms-preview-mainhattan-text-image',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        mainhattanImageText: {
            type: 'mainhattan-text-image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'standard' },
                },
                data: {
                    media: {
                        value: CMS.MEDIA.previewMountain,
                        source: 'default',
                    },
                },
            },
        },
    },
});
