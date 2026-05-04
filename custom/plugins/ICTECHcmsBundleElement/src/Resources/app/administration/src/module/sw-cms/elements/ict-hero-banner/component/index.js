import template from './sw-cms-el-ict-hero-banner.html.twig';
import './sw-cms-el-ict-hero-banner.scss';

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

        logoMedia() {
            return this.element?.data?.logoMedia ?? null;
        },

        heroMedia() {
            return this.element?.data?.heroMedia ?? null;
        },

        headline() {
            return this.element?.config?.headline?.value ?? '';
        },

        backgroundColor() {
            return this.element?.config?.backgroundColor?.value ?? '#111111';
        },

        leftStyle() {
            return { backgroundColor: this.backgroundColor };
        },
    },

    created() {
        this.initElementConfig('ict-hero-banner');
        this._loadMedia('logoMediaId', 'logoMedia');
        this._loadMedia('heroMediaId', 'heroMedia');
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
                // ignore missing media
            }
        },
    },
};
