import template from './sw-cms-el-config-ict-story-card.html.twig';
import './sw-cms-el-config-ict-story-card.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    data() {
        return {
            mediaModalIsOpen: false,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `ict-story-card-media-${this.element.id}`;
        },

        mediaSource() {
            if (this.element?.data?.media?.id) {
                return this.element.data.media;
            }

            return null;
        },

        linkTypeOptions() {
            return [
                { value: 'url', label: this.$tc('ict-cms-bundle-element.cms.elements.ictStoryCard.config.linkTypeUrl') },
                { value: 'internal', label: this.$tc('ict-cms-bundle-element.cms.elements.ictStoryCard.config.linkTypeInternal') },
            ];
        },
    },

    watch: {
        'element.config.linkUrl.value'() {
            // `sw-dynamic-url-field` does not provide an explicit update hook in all 6.6.x versions.
            this.onUpdate();
        },
    },

    created() {
        this.initElementConfig('ict-story-card');
        this.loadExistingMedia();
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },

        async loadExistingMedia() {
            const mediaId = this.element?.config?.mediaId?.value;

            if (!mediaId) {
                return;
            }

            try {
                const media = await this.mediaRepository.get(mediaId);
                this.updateElementData({ mediaId, media });
            } catch {
                // ignore missing media
            }
        },

        async onImageUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);

            this.element.config.mediaId.value = media.id;
            this.element.config.mediaId.source = 'static';

            this.updateElementData({ mediaId: media.id, media });
            this.onUpdate();
        },

        onRemoveImage() {
            this.element.config.mediaId.value = null;
            this.updateElementData({ mediaId: null, media: null });
            this.onUpdate();
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        onCloseMediaModal() {
            this.mediaModalIsOpen = false;
        },

        onSelectImage(mediaEntities) {
            const media = mediaEntities?.[0];
            if (!media) {
                return;
            }

            this.element.config.mediaId.value = media.id;
            this.element.config.mediaId.source = 'static';
            this.updateElementData({ mediaId: media.id, media });
            this.onCloseMediaModal();
            this.onUpdate();
        },

        onRemoveLink() {
            this.element.config.linkUrl.value = '';
            this.onUpdate();
        },

        updateElementData({ mediaId = undefined, media = undefined }) {
            if (!this.element.data) {
                const data = {};

                if (mediaId !== undefined) {
                    data.mediaId = mediaId;
                    data.media = media || null;
                }

                if (this.isCompatEnabled('INSTANCE_SET')) {
                    this.$set(this.element, 'data', data);
                } else {
                    this.element.data = data;
                }

                return;
            }

            if (mediaId !== undefined) {
                if (this.isCompatEnabled('INSTANCE_SET')) {
                    this.$set(this.element.data, 'mediaId', mediaId);
                    this.$set(this.element.data, 'media', media || null);
                } else {
                    this.element.data.mediaId = mediaId;
                    this.element.data.media = media || null;
                }
            }
        },
    },
};
