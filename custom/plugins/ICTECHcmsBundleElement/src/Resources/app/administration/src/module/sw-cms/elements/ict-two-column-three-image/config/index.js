import template from './sw-cms-el-config-ict-two-column-three-image.html.twig';
import './sw-cms-el-config-ict-two-column-three-image.scss';

const {Mixin} = Shopware;

export default {
    template,
    compatConfig: Shopware.compatConfig,
    emits: ['element-update'],
    mixins: [
        Mixin.getByName('cms-element'),
        Mixin.getByName('notification'),
    ],
    inject: ['repositoryFactory'],
    created() {
        this.createdComponent();
    },

    mounted() {
        this.loadMedia();
    },

    props: {
        fileAccept: {
            type: String,
            required: false,
            default: 'image/*',
        },
        buttonImageAccept: {
            type: String,
            required: false,
            default: 'image/*',
        }
    },
    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },
        leftButtonIconUploadTag() {
            return `cms-element-left-button-${this.element.id}`;
        },
        leftButtonIconMediaItem() {
            return this.element.data?.leftButtonIcon || null;
        },
        leftImageUploadTag() {
            return `cms-element-left-${this.element.id}`;
        },
        leftImageMediaItem() {
            return this.element.data?.leftMedia || this.element.data?.leftImage || null;
        },
        rightTopButtonIconUploadTag() {
            return `cms-element-right-top-button-${this.element.id}`;
        },
        rightTopButtonIconMediaItem() {
            return this.element.data?.rightTopButtonIcon || null;
        },
        rightTopImageUploadTag() {
            return `cms-element-right-top-${this.element.id}`;
        },
        rightTopImageMediaItem() {
            return this.element.data?.rightTopMedia || null;
        },
        rightBottomButtonIconUploadTag() {
            return `cms-element-right-bottom-button-${this.element.id}`;
        },
        rightBottomButtonIconMediaItem() {
            return this.element.data?.rightBottomButtonIcon || null;
        },
        rightBottomImageUploadTag() {
            return `cms-element-right-bottom-${this.element.id}`;
        },
        rightBottomImageMediaItem() {
            return this.element.data?.rightBottomMedia || null;
        },
        linkTypeOptions() {
            return [
                {value: 'internal', label: 'Internal Link'},
                {value: 'external', label: 'External Link'}
            ];
        },
        objectFitOptions() {
            return [
                { value: 'cover', label: 'Cover' },
                { value: 'contain', label: 'Contain' }
            ];
        },
        buttonIconSizeOptions() {
            return [
                {value: '20', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size20')},
                {value: '25', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size25')},
                {value: '50', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size50')},
                {value: '80', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size80')},
                {value: '120', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size120')},
                {value: '220', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size220')}
            ];
        },
    },
    data() {
        return {
            leftImageMediaModalIsOpen: false,
            leftButtonIconMediaModalIsOpen: false,
            rightTopImageMediaModalIsOpen: false,
            rightTopButtonIconMediaModalIsOpen: false,
            rightBottomImageMediaModalIsOpen: false,
            rightBottomButtonIconMediaModalIsOpen: false,
        }
    },
    methods: {

        createdComponent() {
            this.initElementConfig('ict-two-column-three-image');
        },

        async loadMedia() {
            if (this.element.config.leftImage?.value && !this.element.data?.leftMedia && !this.element.data?.leftImage) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.leftImage.value);
                this.updateElementData(mediaEntity);
            }

            if (this.element.config.leftButtonIcon?.value && !this.element.data?.leftButtonMediaId) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.leftButtonIcon.value);
                this.updateButtonIconElementData(mediaEntity);
            }

            if (this.element.config.rightTopImage?.value && !this.element.data?.rightTopMedia) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.rightTopImage.value);
                this.updateRightTopElementData(mediaEntity);
            }

            if (this.element.config.rightTopButtonIcon?.value && !this.element.data?.rightTopButtonMediaId) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.rightTopButtonIcon.value);
                this.updateRightTopButtonIconElementData(mediaEntity);
            }

            if (this.element.config.rightBottomImage?.value && !this.element.data?.rightBottomMedia) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.rightBottomImage.value);
                this.updateRightBottomElementData(mediaEntity);
            }

            if (this.element.config.rightBottomButtonIcon?.value && !this.element.data?.rightBottomButtonMediaId) {
                const mediaEntity = await this.mediaRepository.get(this.element.config.rightBottomButtonIcon.value);
                this.updateRightBottomButtonIconElementData(mediaEntity);
            }
        },
        onLeftImageUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.leftImage.value = targetId;
                this.updateElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        updateElementData(media = null) {
            const leftImageId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {
                    leftImageId,
                    leftMedia: media,
                    leftImage: media
                };
                return;
            }
            this.element.data.leftImageId = leftImageId;
            this.element.data.leftMedia = media;
            this.element.data.leftImage = media;
        },

        onLeftImageSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.leftImage.value = media.id;
                this.updateElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictGetInTouchSection.config.notification.errorImageMessage'),
                });
            }
        },

        onLeftImageRemove() {
            this.element.config.leftImage.value = null;
            this.updateElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenLeftImageModal() {
            this.leftImageMediaModalIsOpen = true;
        },

        onCloseLeftImageModal() {
            this.leftImageMediaModalIsOpen = false;
        },

        onLeftImageDescriptionInput(value) {
            if (!this.element?.config?.leftImageDescription) return;
            this.element.config.leftImageDescription.value = value;
            this.$emit('element-update', this.element);
        },

        onLeftImageHeight(value) {
            this.element.config.leftImageHeight.value = value;
            this.$emit('element-update', this.element);
        },

        onLeftImageObjectFit(value) {
            this.element.config.leftImageObjectFit.value = value;
            this.$emit('element-update', this.element);
        },

        onLeftButtonTitleInput(value) {
            this.element.config.leftButtonTitle.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonLinkType(value) {
            this.element.config.leftButtonLinkType.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonLink(value) {
            this.element.config.leftButtonLink.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonTarget(value) {
            this.element.config.leftButtonTarget.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonIconSizeChange(value) {
            this.element.config.leftButtonIconSize.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonIconHoverActiveChange(value) {
            this.element.config.leftButtonIconHoverActive.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonIconHoverColorChange(value) {
            this.element.config.leftButtonIconHoverColor.value = value;
            this.$emit('element-update', this.element);
        },

        // === Left Button config handlers ===
        onLeftButtonBackgroundColorChange(value) {
            this.element.config.leftButtonBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonHoverBackgroundColorChange(value) {
            this.element.config.leftButtonHoverBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonTextColorChange(value) {
            this.element.config.leftButtonTextColor.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonHoverTextColorChange(value) {
            this.element.config.leftButtonHoverTextColor.value = value;
            this.$emit('element-update', this.element);
        },

        onLeftButtonBorderColorChange(value) {
            this.element.config.leftButtonBorderColor.value = value;
            this.$emit('element-update', this.element);
        },
        onLeftButtonHoverBorderColorChange(value) {
            this.element.config.leftButtonHoverBorderColor.value = value;
            this.$emit('element-update', this.element);
        },

        // Left button icon

        onLeftButtonIconUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.leftButtonIcon.value = targetId;
                this.updateButtonIconElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        onLeftButtonIconSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.leftButtonIcon.value = media.id;
                this.updateButtonIconElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.notification.errorImageMessage'),
                });
            }
        },

        onLeftButtonIconRemove() {
            this.element.config.leftButtonIcon.value = null;
            this.updateButtonIconElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenLeftButtonIconModal() {
            this.leftButtonIconMediaModalIsOpen = true;
        },

        onCloseLeftButtonIconModal() {
            this.leftButtonIconMediaModalIsOpen = false;
        },

        updateButtonIconElementData(media = null) {
            const leftButtonMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {leftButtonMediaId, leftButtonIcon: media};
                return;
            }
            this.element.data.leftButtonMediaId = leftButtonMediaId;
            this.element.data.leftButtonIcon = media;
        },

        // Right Top Image Methods
        onRightTopImageUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.rightTopImage.value = targetId;
                this.updateRightTopElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        updateRightTopElementData(media = null) {
            const rightTopMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {rightTopMediaId, rightTopMedia: media};
                return;
            }
            this.element.data.rightTopMediaId = rightTopMediaId;
            this.element.data.rightTopMedia = media;
        },
        // Right Bottom Image Methods
        updateRightBottomElementData(media = null) {
            const rightBottomMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {rightBottomMediaId, rightBottomMedia: media};
                return;
            }
            this.element.data.rightBottomMediaId = rightBottomMediaId;
            this.element.data.rightBottomMedia = media;
        },

        updateRightBottomButtonIconElementData(media = null) {
            const rightBottomButtonMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {rightBottomButtonMediaId, rightBottomButtonIcon: media};
                return;
            }
            this.element.data.rightBottomButtonMediaId = rightBottomButtonMediaId;
            this.element.data.rightBottomButtonIcon = media;
        },

        onRightTopImageSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.rightTopImage.value = media.id;
                this.updateRightTopElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictGetInTouchSection.config.notification.errorImageMessage'),
                });
            }
        },

        onRightTopImageRemove() {
            this.element.config.rightTopImage.value = null;
            this.updateRightTopElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenRightTopImageModal() {
            this.rightTopImageMediaModalIsOpen = true;
        },

        onCloseRightTopImageModal() {
            this.rightTopImageMediaModalIsOpen = false;
        },

        onRightTopImageHeight(value) {
            this.element.config.rightTopImageHeight.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopImageObjectFit(value) {
            this.element.config.rightTopImageObjectFit.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopImageDescriptionInput(value) {
            if (!this.element?.config?.rightTopImageDescription) return;
            this.element.config.rightTopImageDescription.value = value;
            this.$emit('element-update', this.element);
        },

        onRightImageHeight(value) {
            this.element.config.rightImageHeight.value = value;
            this.$emit('element-update', this.element);
        },

        onRightImageObjectFit(value) {
            this.element.config.rightImageObjectFit.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonTitleInput(value) {
            this.element.config.rightTopButtonTitle.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonLinkType(value) {
            this.element.config.rightTopButtonLinkType.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonLink(value) {
            this.element.config.rightTopButtonLink.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonTarget(value) {
            this.element.config.rightTopButtonTarget.value = value;
            this.$emit('element-update', this.element);
        },
        onRightTopButtonIconSizeChange(value) {
            this.element.config.rightTopButtonIconSize.value = value;
            this.$emit('element-update', this.element);
        },
        onRightTopButtonIconHoverActiveChange(value) {
            this.element.config.rightTopButtonIconHoverActive.value = value;
            this.$emit('element-update', this.element);
        },
        onRightTopButtonIconHoverColorChange(value) {
            this.element.config.rightTopButtonIconHoverColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonBackgroundColorChange(value) {
            this.element.config.rightTopButtonBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonHoverBackgroundColorChange(value) {
            this.element.config.rightTopButtonHoverBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonTextColorChange(value) {
            this.element.config.rightTopButtonTextColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonHoverTextColorChange(value) {
            this.element.config.rightTopButtonHoverTextColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonBorderColorChange(value) {
            this.element.config.rightTopButtonBorderColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonHoverBorderColorChange(value) {
            this.element.config.rightTopButtonHoverBorderColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightTopButtonIconUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.rightTopButtonIcon.value = targetId;
                this.updateRightTopButtonIconElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        onRightTopButtonIconSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.rightTopButtonIcon.value = media.id;
                this.updateRightTopButtonIconElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.notification.errorImageMessage'),
                });
            }
        },

        onRightTopButtonIconRemove() {
            this.element.config.rightTopButtonIcon.value = null;
            this.updateRightTopButtonIconElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenRightTopButtonIconModal() {
            this.rightTopButtonIconMediaModalIsOpen = true;
        },

        onCloseRightTopButtonIconModal() {
            this.rightTopButtonIconMediaModalIsOpen = false;
        },

        updateRightTopButtonIconElementData(media = null) {
            const rightTopButtonMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {rightTopButtonMediaId, rightTopButtonIcon: media};
                return;
            }
            this.element.data.rightTopButtonMediaId = rightTopButtonMediaId;
            this.element.data.rightTopButtonIcon = media;
        },

        // Right Bottom Image Methods
        onRightBottomImageUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.rightBottomImage.value = targetId;
                this.updateRightBottomElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        updateCenterElementData(media = null) {
            const centerMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {centerMediaId, centerMedia: media};
                return;
            }
            this.element.data.centerMediaId = centerMediaId;
            this.element.data.centerMedia = media;
        },

        onRightBottomImageSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.rightBottomImage.value = media.id;
                this.updateRightBottomElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictGetInTouchSection.config.notification.errorImageMessage'),
                });
            }
        },

        onRightBottomImageRemove() {
            this.element.config.rightBottomImage.value = null;
            this.updateRightBottomElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenRightBottomImageModal() {
            this.rightBottomImageMediaModalIsOpen = true;
        },

       onCloseRightBottomImageModal() {
            this.rightBottomImageMediaModalIsOpen = false;
        },

        onRightBottomDescriptionInput(value) {
            if (!this.element?.config?.rightBottomImageDescription) return;
            this.element.config.rightBottomImageDescription.value = value;
            this.$emit('element-update', this.element);
        },

        // Template uses onRightBottomImageDescriptionInput — add delegator
        onRightBottomImageDescriptionInput(value) {
            this.onRightBottomDescriptionInput(value);
        },

        onRightBottomHeight(value) {
            this.element.config.rightBottomImageHeight.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomObjectFit(value) {
            this.element.config.rightBottomImageObjectFit.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonTitleInput(value) {
            this.element.config.rightBottomButtonTitle.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonLinkType(value) {
            this.element.config.rightBottomButtonLinkType.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonLink(value) {
            this.element.config.rightBottomButtonLink.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonTarget(value) {
            this.element.config.rightBottomButtonTarget.value = value;
            this.$emit('element-update', this.element);
        },
        onRightBottomButtonIconSizeChange(value) {
            this.element.config.rightBottomButtonIconSize.value = value;
            this.$emit('element-update', this.element);
        },
        onRightBottomButtonIconHoverActiveChange(value) {
            this.element.config.rightBottomButtonIconHoverActive.value = value;
            this.$emit('element-update', this.element);
        },
        onRightBottomButtonIconHoverColorChange(value) {
            this.element.config.rightBottomButtonIconHoverColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonBackgroundColorChange(value) {
            this.element.config.rightBottomButtonBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonHoverBackgroundColorChange(value) {
            this.element.config.rightBottomButtonHoverBackgroundColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonTextColorChange(value) {
            this.element.config.rightBottomButtonTextColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonHoverTextColorChange(value) {
            this.element.config.rightBottomButtonHoverTextColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonBorderColorChange(value) {
            this.element.config.rightBottomButtonBorderColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonHoverBorderColorChange(value) {
            this.element.config.rightBottomButtonHoverBorderColor.value = value;
            this.$emit('element-update', this.element);
        },

        onRightBottomButtonIconUpload({targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.rightBottomButtonIcon.value = targetId;
                this.updateRightBottomButtonIconElementData(mediaEntity);
                this.$emit('element-update', this.element);
            });
        },

        onRightBottomButtonIconSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.element.config.rightBottomButtonIcon.value = media.id;
                this.updateRightBottomButtonIconElementData(media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.notification.errorImageMessage'),
                });
            }
        },

        onRightBottomButtonIconRemove() {
            this.element.config.rightBottomButtonIcon.value = null;
            this.updateRightBottomButtonIconElementData(null);
            this.$emit('element-update', this.element);
        },

        onOpenRightBottomButtonIconModal() {
            this.rightBottomButtonIconMediaModalIsOpen = true;
        },

       onCloseRightBottomButtonIconModal() {
            this.rightBottomButtonIconMediaModalIsOpen = false;
        },

        updateCenterButtonIconElementData(media = null) {
            const rightBottomButtonMediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.element.data = {rightBottomButtonMediaId, media};
                return;
            }
            this.element.data.rightBottomButtonMediaId = rightBottomButtonMediaId;
            this.element.data.rightBottomButtonIcon = media;
        },
    }
};
