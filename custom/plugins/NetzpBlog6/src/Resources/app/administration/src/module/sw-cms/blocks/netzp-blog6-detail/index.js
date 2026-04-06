import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'netzp-blog6-detail-block',
    label: 'sw-cms.blocks.netzp-blog6-detail.label',
    category: 'netzp-blog6',
    component: 'sw-cms-block-netzp-blog6-detail-block',
    previewComponent: 'sw-cms-preview-netzp-blog6-detail-block',

    defaultConfig: {
        marginBottom: '20px',
        marginTop:    '0px',
        marginLeft:   '0px',
        marginRight:  '0px',
        sizingMode:   'boxed'
    },
    slots: {
        content: {
            type: 'netzp-blog6-detail'
        }
    }
});
