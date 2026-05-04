import template from './sw-cms-el-config-ict-hero-banner.html.twig';
import './sw-cms-el-config-ict-hero-banner.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    data() {
        return {
            logoModalOpen: false,
            heroModalOpen: false,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        logoUploadTag() {
            return `ict-hero-banner-logo-${this.element.id}`;
        },

        heroUploadTag() {
            return `ict-hero-banner-hero-${this.element.id}`;
        },

        logoSource() {
            return this.element?.data?.logoMedia ?? null;
        },

        heroSource() {
            return this.element?.data?.heroMedia ?? null;
        },
    },

    created() {
        this.initElementConfig('ict-hero-banner');
        this._loadMedia('logoMediaId', 'logoMedia');
        this._loadMedia('heroMediaId', 'heroMedia');
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },

        async _loadMedia(configKey, dataKey) {
            const mediaId = this.element?.config?.[configKey]?.value;
            if (!mediaId) {
                return;
            }
            try {
                const media = await this.mediaRepository.get(mediaId);
                this._setData(dataKey, media);
            } catch {
                // ignore
            }
        },

        _setData(key, value) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (this.isCompatEnabled('INSTANCE_SET')) {
                this.$set(this.element.data, key, value);
            } else {
                this.element.data[key] = value;
            }
        },

        async onLogoUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);
            this.element.config.logoMediaId.value = media.id;
            this._setData('logoMedia', media);
            this.onUpdate();
        },

        onLogoRemove() {
            this.element.config.logoMediaId.value = null;
            this._setData('logoMedia', null);
            this.onUpdate();
        },

        onLogoSelect(mediaEntities) {
            const media = mediaEntities?.[0];
            if (!media) {
                return;
            }
            this.element.config.logoMediaId.value = media.id;
            this._setData('logoMedia', media);
            this.logoModalOpen = false;
            this.onUpdate();
        },

        async onHeroUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);
            this.element.config.heroMediaId.value = media.id;
            this._setData('heroMedia', media);
            this.onUpdate();
        },

        onHeroRemove() {
            this.element.config.heroMediaId.value = null;
            this._setData('heroMedia', null);
            this.onUpdate();
        },

        onHeroSelect(mediaEntities) {
            const media = mediaEntities?.[0];
            if (!media) {
                return;
            }
            this.element.config.heroMediaId.value = media.id;
            this._setData('heroMedia', media);
            this.heroModalOpen = false;
            this.onUpdate();
        },
    },
};
