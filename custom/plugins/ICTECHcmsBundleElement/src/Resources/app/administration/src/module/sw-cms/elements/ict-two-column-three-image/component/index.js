import template from './sw-cms-el-ict-two-column-three-image.html.twig';
import './sw-cms-el-ict-two-column-three-image.scss';

const {Mixin, Filter} = Shopware;
export default {
    template,
    compatConfig: Shopware.compatConfig,
    inject: ['feature', 'repositoryFactory'],
    mixins: [Mixin.getByName('cms-element')],

    computed: {
        assetFilter() {
            return Filter.getByName('asset');
        },
        leftMediaUrl() {
            const media = this.element.data?.leftMedia || this.element.data?.leftImage;
            return media?.url ?? this.assetFilter('/administration/static/img/cms/preview_mountain_large.jpg');
        },
        rightTopMediaUrl() {
            const media = this.element.data?.rightTopMedia || this.element.data?.rightTopImage;
            return media?.url ?? this.assetFilter('/administration/static/img/cms/preview_glasses_large.jpg');
        },
        rightBottomMediaUrl() {
            const media = this.element.data?.rightBottomMedia || this.element.data?.rightBottomImage;
            return media?.url ?? this.assetFilter('/administration/static/img/cms/preview_camera_large.jpg');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('ict-two-column-three-image');
            this.initElementData('ict-two-column-three-image');
        }
    }
};
