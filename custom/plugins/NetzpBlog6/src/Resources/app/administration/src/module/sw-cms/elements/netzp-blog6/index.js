import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'netzp-blog6',
    label: 'sw-cms.elements.netzp-blog6.label',
    component: 'sw-cms-el-netzp-blog6',
    configComponent: 'sw-cms-el-config-netzp-blog6',
    previewComponent: 'sw-cms-el-preview-netzp-blog6',

    defaultConfig: {
        sortOrder: {
            source: 'static',
            value: 'desc'
        },
        numberOfPosts: {
            source: 'static',
            value: 5
        },
        noPagination: {
            source: 'static',
            value: false
        },
        layout: {
            source: 'static',
            value: 'cards'
        },
        imageMode: {
            source: 'static',
            value: 'cover'
        },
        category: {
            source: 'static',
            value: '00000000000000000000000000000000'
        },
        author: {
            source: 'static',
            value: '00000000000000000000000000000000'
        },
        tags: {
            source: 'static',
            value: null
        },
        filterCategory: {
            source: 'static',
            value: true,
        },
        filterTags: {
            source: 'static',
            value: false,
        },
        filterAuthor: {
            source: 'static',
            value: false,
        },
        backgroundColor: {
            source: 'static',
            value: ''
        },
        customTemplate: {
            source: 'static',
            value: ''
        },
        titleTag: {
            source: 'static',
            value: 'h4'
        },
        blogPost: {
            source: 'static',
            value: '00000000000000000000000000000000'
        },
    }
});
