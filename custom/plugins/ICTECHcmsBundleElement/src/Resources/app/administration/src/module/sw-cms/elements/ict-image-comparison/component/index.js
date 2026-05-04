import template from './sw-cms-el-ict-image-comparison.html.twig';
import './sw-cms-el-ict-image-comparison.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        beforeMedia() {
            return this.element?.data?.beforeMedia ?? null;
        },

        afterMedia() {
            return this.element?.data?.afterMedia ?? null;
        },

        layout() {
            return this.element?.config?.layout?.value ?? 'horizontal';
        },

        isVertical() {
            return this.layout === 'vertical';
        },
    },

    created() {
        this.initElementConfig('ict-image-comparison');
        this._loadMedia('beforeMediaId', 'beforeMedia');
        this._loadMedia('afterMediaId', 'afterMedia');
    },

    methods: {
        async _loadMedia(configKey, dataKey) {
            if (this.element?.data?.[dataKey]) {
                return;
            }
            const mediaId = this.element?.config?.[configKey]?.value;
            if (!mediaId) {
                return;
            }
            try {
                const media = await this.mediaRepository.get(mediaId);
                if (!this.element.data) {
                    this.element.data = {};
                }
                this.element.data[dataKey] = media;
            } catch {
                // ignore
            }
        },
    },
};
