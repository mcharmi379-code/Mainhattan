import template from './sw-cms-el-config-ict-image-comparison.html.twig';
import './sw-cms-el-config-ict-image-comparison.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    data() {
        return {
            activeTab: 'general',
            beforeModalOpen: false,
            afterModalOpen: false,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        beforeUploadTag() {
            return `ict-image-comparison-before-${this.element.id}`;
        },

        afterUploadTag() {
            return `ict-image-comparison-after-${this.element.id}`;
        },

        beforeSource() {
            return this.element?.data?.beforeMedia ?? null;
        },

        afterSource() {
            return this.element?.data?.afterMedia ?? null;
        },

        layoutOptions() {
            return [
                {
                    value: 'horizontal',
                    label: this.$tc('ict-cms-bundle-element.cms.elements.ictImageComparison.config.layoutHorizontal'),
                },
                {
                    value: 'vertical',
                    label: this.$tc('ict-cms-bundle-element.cms.elements.ictImageComparison.config.layoutVertical'),
                },
            ];
        },
    },

    created() {
        this.initElementConfig('ict-image-comparison');
        this._loadMedia('beforeMediaId', 'beforeMedia');
        this._loadMedia('afterMediaId', 'afterMedia');
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

        async onBeforeUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);
            this.element.config.beforeMediaId.value = media.id;
            this._setData('beforeMedia', media);
            this.onUpdate();
        },

        onBeforeRemove() {
            this.element.config.beforeMediaId.value = null;
            this._setData('beforeMedia', null);
            this.onUpdate();
        },

        onBeforeSelect(mediaEntities) {
            const media = mediaEntities?.[0];
            if (!media) {
                return;
            }
            this.element.config.beforeMediaId.value = media.id;
            this._setData('beforeMedia', media);
            this.beforeModalOpen = false;
            this.onUpdate();
        },

        async onAfterUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);
            this.element.config.afterMediaId.value = media.id;
            this._setData('afterMedia', media);
            this.onUpdate();
        },

        onAfterRemove() {
            this.element.config.afterMediaId.value = null;
            this._setData('afterMedia', null);
            this.onUpdate();
        },

        onAfterSelect(mediaEntities) {
            const media = mediaEntities?.[0];
            if (!media) {
                return;
            }
            this.element.config.afterMediaId.value = media.id;
            this._setData('afterMedia', media);
            this.afterModalOpen = false;
            this.onUpdate();
        },
    },
};
