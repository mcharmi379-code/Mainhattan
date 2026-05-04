import template from './sw-cms-el-config-ict-file-download.html.twig';
import './sw-cms-el-config-ict-file-download.scss';

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
            previewModalIsOpen: false,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTagMedia() {
            return `ict-file-download-media-${this.element.id}`;
        },

        uploadTagPreview() {
            return `ict-file-download-preview-${this.element.id}`;
        },

        mediaSource() {
            if (this.element?.data?.media?.id) {
                return this.element.data.media;
            }

            return null;
        },

        previewSource() {
            if (this.element?.data?.previewMedia?.id) {
                return this.element.data.previewMedia;
            }

            return null;
        },
    },

    created() {
        this.initElementConfig('ict-file-download');
        this.loadExistingMedia();
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },

        async loadExistingMedia() {
            const mediaId = this.element?.config?.mediaId?.value;
            const previewId = this.element?.config?.previewImageId?.value;

            if (mediaId) {
                try {
                    const media = await this.mediaRepository.get(mediaId);
                    this.updateElementData({ mediaId, media });
                } catch {
                    // ignore missing media
                }
            }

            if (previewId) {
                try {
                    const previewMedia = await this.mediaRepository.get(previewId);
                    this.updateElementData({ previewImageId: previewId, previewMedia });
                } catch {
                    // ignore missing media
                }
            }
        },

        async onFileUploadFinish({ targetId }) {
            const media = await this.mediaRepository.get(targetId);

            this.element.config.mediaId.value = media.id;
            this.element.config.mediaId.source = 'static';

            this.updateElementData({ mediaId: media.id, media });
            this.onUpdate();
        },

        async onPreviewUploadFinish({ targetId }) {
            const previewMedia = await this.mediaRepository.get(targetId);

            this.element.config.previewImageId.value = previewMedia.id;
            this.element.config.previewImageId.source = 'static';

            this.updateElementData({ previewImageId: previewMedia.id, previewMedia });
            this.onUpdate();
        },

        onRemoveFile() {
            this.element.config.mediaId.value = null;
            this.updateElementData({ mediaId: null, media: null });
            this.onUpdate();
        },

        onRemovePreview() {
            this.element.config.previewImageId.value = null;
            this.updateElementData({ previewImageId: null, previewMedia: null });
            this.onUpdate();
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        onCloseMediaModal() {
            this.mediaModalIsOpen = false;
        },

        onOpenPreviewModal() {
            this.previewModalIsOpen = true;
        },

        onClosePreviewModal() {
            this.previewModalIsOpen = false;
        },

        onSelectFile(mediaEntities) {
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

        onSelectPreview(mediaEntities) {
            const previewMedia = mediaEntities?.[0];
            if (!previewMedia) {
                return;
            }

            this.element.config.previewImageId.value = previewMedia.id;
            this.element.config.previewImageId.source = 'static';
            this.updateElementData({ previewImageId: previewMedia.id, previewMedia });
            this.onClosePreviewModal();
            this.onUpdate();
        },

        updateElementData({
            mediaId = undefined,
            media = undefined,
            previewImageId = undefined,
            previewMedia = undefined,
        }) {
            if (!this.element.data) {
                const data = {};

                if (mediaId !== undefined) {
                    data.mediaId = mediaId;
                    data.media = media || null;
                }

                if (previewImageId !== undefined) {
                    data.previewImageId = previewImageId;
                    data.previewMedia = previewMedia || null;
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

            if (previewImageId !== undefined) {
                if (this.isCompatEnabled('INSTANCE_SET')) {
                    this.$set(this.element.data, 'previewImageId', previewImageId);
                    this.$set(this.element.data, 'previewMedia', previewMedia || null);
                } else {
                    this.element.data.previewImageId = previewImageId;
                    this.element.data.previewMedia = previewMedia || null;
                }
            }
        },
    },
};
