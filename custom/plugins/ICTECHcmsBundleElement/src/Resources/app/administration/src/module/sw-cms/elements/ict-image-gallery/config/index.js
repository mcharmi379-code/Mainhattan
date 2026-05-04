import template from './sw-cms-el-config-ict-image-gallery.html.twig';
import './sw-cms-el-config-ict-image-gallery.scss';

const ELEMENT_NAME = 'ict-image-gallery';
const { Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: [
        'repositoryFactory',
    ],

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            mediaModalIsOpen: false,
            mediaItems: [],
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `cms-element-media-config-${this.element.id}`;
        },

        defaultFolderName() {
            return this.cmsPageState.pageEntityName;
        },

        galleryItems() {
            if (!this.element.config.galleryItems.value) {
                this.element.config.galleryItems.value = [];
            }

            if (!Array.isArray(this.element.config.galleryItems.value)) {
                this.element.config.galleryItems.value = [];
            }

            const cleaned = this.element.config.galleryItems.value.filter((item) => {
                return item && typeof item === 'object' && !Array.isArray(item);
            });

            if (cleaned.length !== this.element.config.galleryItems.value.length) {
                this.element.config.galleryItems.value = cleaned;
            }

            return this.element.config.galleryItems.value;
        },

        columnsOptions() {
            return [1, 2, 3, 4, 5, 6];
        },
    },

    watch: {
        'element.config.galleryItems.value': {
            handler(newValue) {
                if (Array.isArray(newValue)) {
                    const cleaned = newValue.filter((item) => {
                        return item && typeof item === 'object' && !Array.isArray(item);
                    });
                    if (cleaned.length !== newValue.length) {
                        this.element.config.galleryItems.value = cleaned;
                    }
                }
            },
            deep: true,
            immediate: true,
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig(ELEMENT_NAME);

            if (!Array.isArray(this.element.config.galleryItems.value)) {
                this.element.config.galleryItems.value = [];
            }

            this.cleanGalleryItems();
            this.normalizeColumns();
            await this.loadMediaItems();
        },

        normalizeColumns() {
            const columns = Number.parseInt(`${this.element.config.columns.value}`, 10);

            if (Number.isNaN(columns) || columns < 1) {
                this.element.config.columns.value = 4;
                return;
            }

            this.element.config.columns.value = Math.min(columns, 6);
        },

        async loadMediaItems() {
            const mediaIds = this.galleryItems
                .filter((item) => item && typeof item === 'object')
                .map((item) => item.mediaId)
                .filter((mediaId) => typeof mediaId === 'string' && mediaId.length > 0);

            if (mediaIds.length === 0) {
                this.mediaItems = [];
                return;
            }

            const criteria = new Criteria(1, mediaIds.length);
            criteria.setIds(mediaIds);

            const searchResult = await this.mediaRepository.search(criteria);
            this.mediaItems = mediaIds.map((mediaId) => {
                return searchResult.get(mediaId) ?? null;
            });
        },

        async onImageUpload({ targetId } = {}) {
            if (typeof targetId !== 'string' || targetId.length === 0) {
                return;
            }

            const mediaEntity = await this.mediaRepository.get(targetId, Shopware.Context.api);
            this.addMediaItem(mediaEntity);
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        onCloseMediaModal() {
            this.mediaModalIsOpen = false;
        },

        onMediaSelectionChange(selectedMedia) {
            if (!Array.isArray(selectedMedia)) {
                return;
            }

            selectedMedia.forEach((mediaEntity) => {
                this.addMediaItem(mediaEntity);
            });
        },

        addMediaItem(mediaEntity) {
            if (!mediaEntity) {
                return;
            }

            const exists = this.galleryItems.some((item) => item.mediaId === mediaEntity.id);
            if (exists) {
                return;
            }

            this.galleryItems.push({
                mediaId: mediaEntity.id,
                mediaUrl: mediaEntity.url,
                title: mediaEntity.translated?.alt || mediaEntity.translated?.title || '',
            });

            this.mediaItems.push(mediaEntity);
            this.emitElementUpdate();
        },

        onItemRemove(index) {
            this.galleryItems.splice(index, 1);
            this.mediaItems.splice(index, 1);
            this.emitElementUpdate();
        },

        onTitleChange() {
            this.emitElementUpdate();
        },

        onColumnsChange() {
            this.normalizeColumns();
            this.emitElementUpdate();
        },

        emitElementUpdate() {
            this.cleanGalleryItems();
            this.$emit('element-update', this.element);
        },

        cleanGalleryItems() {
            if (!Array.isArray(this.element.config.galleryItems.value)) {
                this.element.config.galleryItems.value = [];
                return;
            }

            this.element.config.galleryItems.value = this.element.config.galleryItems.value.filter((item) => {
                return item && typeof item === 'object' && !Array.isArray(item);
            });
        },

        getItemPreviewUrl(item, index) {
            if (!item) {
                return this.mediaItems[index]?.url || '';
            }
            return item.mediaUrl || this.mediaItems[index]?.url || '';
        },

        getItemPreviewAlt(item) {
            if (!item) {
                return '';
            }
            return item.media?.translated?.alt || item.title || item.media?.translated?.title || '';
        },

        getItemLabel(item, index) {
            if (!item) {
                return `${index + 1}`;
            }
            return item.title || item.media?.translated?.title || item.mediaId || `${index + 1}`;
        },
    },
};
