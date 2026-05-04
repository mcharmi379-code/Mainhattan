import template from './sw-cms-el-config-ict-platform-support.html.twig';
import './sw-cms-el-config-ict-platform-support.scss';

const { Mixin } = Shopware;

export default {
    template,
    compatConfig: Shopware.compatConfig,
    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
        Mixin.getByName('notification')
    ],

    inject: ['repositoryFactory'],

    data() {
        return {
            platformIconModals: {},
            backgroundImageModal: false,
            backgroundVideoModal: false,
            openIndex: null
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        fontWeightOptions() {
            return [
                { label: '300', value: '300' },
                { label: '400', value: '400' },
                { label: '500', value: '500' },
                { label: '600', value: '600' },
                { label: '700', value: '700' }
            ];
        },

        alignmentOptions() {
            return [
                { label: 'Left', value: 'left' },
                { label: 'Center', value: 'center' },
                { label: 'Right', value: 'right' }
            ];
        },

        backgroundTypeOptions() {
            return [
                { label: 'Color', value: 'color' },
                { label: 'Image', value: 'image' },
                { label: 'Video', value: 'video' },
                { label: 'Gradient', value: 'gradient' }
            ];
        },

        gradientDirectionOptions() {
            return [
                { label: 'To Right', value: 'to right' },
                { label: 'To Left', value: 'to left' },
                { label: 'To Bottom', value: 'to bottom' },
                { label: 'To Top', value: 'to top' },
                { label: 'To Bottom Right', value: 'to bottom right' },
                { label: 'To Bottom Left', value: 'to bottom left' },
                { label: 'To Top Right', value: 'to top right' },
                { label: 'To Top Left', value: 'to top left' }
            ];
        },


        iconShapeOptions() {
            return [
                { label: 'Circle', value: 'circle' },
                { label: 'Square', value: 'square' },
                { label: 'Rounded', value: 'rounded' },
                { label: 'None', value: 'none' }
            ];
        },

        linkTypeOptions() {
            return [
                { label: 'External', value: 'external' },
                { label: 'Internal', value: 'internal' }
            ];
        },

        videoUploadOptions() {
            return [
                { label: 'Upload File', value: 'file' },
                { label: 'Video URL', value: 'url' }
            ];
        },

        iconBackgroundTypeOptions() {
            return [
                { label: 'Solid', value: 'solid' },
                { label: 'Gradient', value: 'gradient' },
                { label: 'None', value: 'none' }
            ];
        },

        targetOptions() {
            return [
                { label: 'Same Tab', value: '_self' },
                { label: 'New Tab', value: '_blank' }
            ];
        }
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.loadMedia();
    },

    methods: {
        onElementUpdate(field, value) {
            this.element.config[field].value = value;
            this.$emit('element-update', this.element);
        },

        createdComponent() {
            this.initElementConfig('ict-platform-support');
        },

        addPlatform() {
            const newPlatform = {
                iconDisplayType: 'icon',
                platformIcon: null,
                iconSizePx: '48',
                iconColor: '#ffffff',
                iconBackgroundType: 'solid',
                iconBackgroundColor: '#005AE5',
                iconGradientColor1: '#005AE5',
                iconGradientColor2: '#0080FF',
                iconGradientDirection: 'to right',
                iconBackgroundShape: 'rounded',
                platformIconShadow: false,
                platformShadowColor: '#808080',
                platformLabel: 'Item Name',
                platformLabelSizePx: '14',
                platformLabelWeight: '500',
                platformLabelColor: '#1a1a2e',
                textAlignment: 'center-bottom',
                textHoverUnderline: false,
                textHoverColorChange: false,
                textHoverColor: '#005AE5',
                linkType: 'external',
                platformLinkUrl: '',
                platformLinkTarget: '_self'
            };
            this.element.config.platformItems.value.push(newPlatform);
        },

        removePlatform(index) {
            this.element.config.platformItems.value.splice(index, 1);
        },

        updatePlatform(index, field, value) {
            const platforms = [...this.element.config.platformItems.value];
            platforms[index][field] = value;
            this.element.config.platformItems.value = platforms;
            this.$emit('element-update', this.element);
        },

        onOpenPlatformIconModal(platformIndex) {
            this.platformIconModals = { ...this.platformIconModals, [platformIndex]: true };
        },

        onClosePlatformIconModal(platformIndex) {
            this.platformIconModals = { ...this.platformIconModals, [platformIndex]: false };
        },

        onPlatformIconUpload(platformIndex, { targetId }) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updatePlatform(platformIndex, 'platformIcon', targetId);
                this.updatePlatformIconData(platformIndex, mediaEntity);
            });
        },

        updatePlatformIconData(platformIndex, media = null) {
            if (!this.element.data) this.element.data = {};
            if (!this.element.data.platformIcons) this.element.data.platformIcons = {};
            this.element.data.platformIcons[platformIndex] = media;
        },

        onPlatformIconRemove(platformIndex) {
            this.updatePlatform(platformIndex, 'platformIcon', null);
            this.updatePlatformIconData(platformIndex, null);
        },

        onPlatformIconSelectionChanges(platformIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) return;
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE' || media.mediaType.name === 'VECTOR_GRAPHIC')) {
                this.updatePlatform(platformIndex, 'platformIcon', media.id);
                this.updatePlatformIconData(platformIndex, media);
            } else {
                this.createNotificationError({ message: 'Please select a valid image file' });
            }
        },

        getPlatformIconUploadTag(platformIndex) {
            return `cms-platform-icon-${this.element.id}-${platformIndex}`;
        },

        getPlatformIconMediaItem(platformIndex) {
            return this.element.data?.platformIcons?.[platformIndex] || null;
        },

        async loadMedia() {
            await this.loadBackgroundMedia();
            if (this.element.config.platformItems?.value) {
                for (let index = 0; index < this.element.config.platformItems.value.length; index++) {
                    const platform = this.element.config.platformItems.value[index];
                    if (platform.platformIcon && !this.element.data?.platformIcons?.[index]) {
                        const mediaEntity = await this.mediaRepository.get(platform.platformIcon);
                        this.updatePlatformIconData(index, mediaEntity);
                    }
                }
            }
        },

        toggleAccordion(index) {
            this.openIndex = this.openIndex === index ? null : index;
        },

        onOpenBackgroundImageModal() { this.backgroundImageModal = true; },
        onCloseBackgroundImageModal() { this.backgroundImageModal = false; },

        onBackgroundImageUpload({ targetId }) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.backgroundImage.value = targetId;
                this.updateBackgroundImageData(mediaEntity);
            });
        },

        updateBackgroundImageData(media = null) {
            if (!this.element.data) this.element.data = {};
            this.element.data.backgroundImage = media;
        },

        onBackgroundImageRemove() {
            this.element.config.backgroundImage.value = null;
            this.updateBackgroundImageData(null);
        },

        onBackgroundImageSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) return;
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE' || media.mediaType.name === 'VECTOR_GRAPHIC')) {
                this.element.config.backgroundImage.value = media.id;
                this.updateBackgroundImageData(media);
            } else {
                this.createNotificationError({ message: 'Please select a valid image file' });
            }
        },

        getBackgroundImageUploadTag() { return `cms-background-image-${this.element.id}`; },
        getBackgroundImageMediaItem() { return this.element.data?.backgroundImage || null; },

        onOpenBackgroundVideoModal() { this.backgroundVideoModal = true; },
        onCloseBackgroundVideoModal() { this.backgroundVideoModal = false; },

        onBackgroundVideoUpload({ targetId }) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.backgroundVideo.value = targetId;
                this.updateBackgroundVideoData(mediaEntity);
            });
        },

        updateBackgroundVideoData(media = null) {
            if (!this.element.data) this.element.data = {};
            this.element.data.backgroundVideo = media;
        },

        onBackgroundVideoRemove() {
            this.element.config.backgroundVideo.value = null;
            this.updateBackgroundVideoData(null);
        },

        onBackgroundVideoSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) return;
            const media = mediaEntity[0];
            if (media && media.mediaType.name === 'VIDEO') {
                this.element.config.backgroundVideo.value = media.id;
                this.updateBackgroundVideoData(media);
            } else {
                this.createNotificationError({ message: 'Please select a valid video file' });
            }
        },

        getBackgroundVideoUploadTag() { return `cms-background-video-${this.element.id}`; },
        getBackgroundVideoMediaItem() { return this.element.data?.backgroundVideo || null; },

        async loadBackgroundMedia() {
            if (this.element.config.backgroundImage?.value && !this.element.data?.backgroundImage) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.backgroundImage.value);
                this.updateBackgroundImageData(mediaEntity);
            }
            if (this.element.config.backgroundVideo?.value && !this.element.data?.backgroundVideo) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.backgroundVideo.value);
                this.updateBackgroundVideoData(mediaEntity);
            }
        }
    }
};
