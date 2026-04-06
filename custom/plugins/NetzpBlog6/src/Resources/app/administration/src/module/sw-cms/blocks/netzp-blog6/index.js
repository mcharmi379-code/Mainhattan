import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'netzp-blog6-block',
    label: 'sw-cms.blocks.netzp-blog6.label',
    category: 'netzp-blog6',
    component: 'sw-cms-block-netzp-blog6-block',
    previewComponent: 'sw-cms-preview-netzp-blog6-block',

    defaultConfig: {
        marginBottom: '20px',
        marginTop:    '20px',
        marginLeft:   '20px',
        marginRight:  '20px',
        sizingMode:   'boxed'
    },

    slots: {
        content: {
            type: 'netzp-blog6'
        }
    }
});
