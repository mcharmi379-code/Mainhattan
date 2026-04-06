import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'netzp-blog6-detail',
    label: 'sw-cms.elements.netzp-blog6-detail.label',
    component: 'sw-cms-el-netzp-blog6-detail',
    configComponent: 'sw-cms-el-config-netzp-blog6-detail',
    previewComponent: 'sw-cms-el-preview-netzp-blog6-detail',

    defaultConfig: {
        part: {
            source: 'static',
            value: 'title'
        },
        titleTag: {
            source: 'static',
            value: 'h2'
        },
        imageType: {
            source: 'static',
            value: 'post'
        },
        imageMode: {
            source: 'static',
            value: 'full'
        },
        height: {
            source: 'static',
            value: '12'
        },
        showDate: {
            source: 'static',
            value: true
        },
        showAuthor: {
            source: 'static',
            value: true
        },
        showCategory: {
            source: 'static',
            value: true
        },
        showTags: {
            source: 'static',
            value: true
        },
        showAuthorAvatar: {
            source: 'static',
            value: true
        },
        showAuthorName: {
            source: 'static',
            value: true
        },
        showAuthorBio: {
            source: 'static',
            value: true
        },
        productLayout: {
            source: 'static',
            value: 'minimal'
        },
        productDisplayMode: {
            source: 'static',
            value: 'cover'
        },
        productMinWidth: {
            source: 'static',
            value: '300'
        },
        showGalleryCounter: {
            source: 'static',
            value: true
        },
        showGalleryCaption: {
            source: 'static',
            value: true
        },
        showThumbnailCaption: {
            source: 'static',
            value: true
        },
    }
});
