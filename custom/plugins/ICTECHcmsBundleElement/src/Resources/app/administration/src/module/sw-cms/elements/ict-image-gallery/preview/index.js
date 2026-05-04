import template from './sw-cms-el-preview-ict-image-gallery.html.twig';
import './sw-cms-el-preview-ict-image-gallery.scss';

export default {
    template,

    compatConfig: Shopware.compatConfig,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },

        previewImages() {
            return [
                '/administration/static/img/cms/preview_mountain_small.jpg',
                '/administration/static/img/cms/preview_glasses_small.jpg',
                '/administration/static/img/cms/preview_plant_small.jpg',
                '/administration/static/img/cms/preview_camera_small.jpg',
            ];
        },
    },
};
