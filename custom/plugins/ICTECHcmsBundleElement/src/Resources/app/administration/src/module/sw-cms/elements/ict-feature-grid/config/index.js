import template from './sw-cms-el-config-ict-feature-grid.html.twig';
import './sw-cms-el-config-ict-feature-grid.scss';

const { Mixin } = Shopware;

export default {
    template,
    compatConfig: Shopware.compatConfig,
    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
        Mixin.getByName('notification'),
    ],

    inject: ['repositoryFactory'],

    data() {
        return {
            openIndex: null,
            openButtonIndexes: {},
            cardImageModals: {},
            cardVideoModals: {},
            iconImageModals: {},
        };
    },

    computed: {
        columnOptions() {
            return [
                { value: 1, label: '1 Column' },
                { value: 2, label: '2 Columns' },
                { value: 3, label: '3 Columns' },
                { value: 4, label: '4 Columns' },
            ];
        },

        linkTypeOptions() {
            return [
                { value: 'internal', label: 'Internal Link' },
                { value: 'external', label: 'External Link' },
            ];
        },

        targetOptions() {
            return [
                { value: '_self', label: 'Same tab' },
                { value: '_blank', label: 'New tab' },
            ];
        },

        borderStyleOptions() {
            return [
                { value: 'solid', label: 'Solid' },
                { value: 'dotted', label: 'Dotted' },
                { value: 'dashed', label: 'Dashed' },
            ];
        },

        mediaDisplayModeOptions() {
            return [
                { value: 'crop', label: 'Crop' },
                { value: 'fit', label: 'Fit' },
            ];
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        fileAccept() { return 'image/*'; },
        videoFileAccept() { return 'video/*'; },
    },

    created() {
        this.initElementConfig('ict-feature-grid');
        this.ensureCards();
    },

    mounted() {
        this.loadMedia();
    },

    watch: {
        'element.config': {
            deep: true,
            handler() {
                if (this.element) this.$emit('element-update', this.element);
            },
        },
    },

    methods: {
        ensureCards() {
            if (!this.element?.config?.cards) return;
            if (!Array.isArray(this.element.config.cards.value)) {
                this.element.config.cards.value = [];
            }
            if (this.element.config.cards.value.length === 0) {
                this.element.config.cards.value = [
                    this.makeCard({ headline: 'Feature One', bodyText: 'Describe the first key feature here.' }),
                    this.makeCard({ headline: 'Feature Two', bodyText: 'Describe the second key feature here.' }),
                    this.makeCard({ headline: 'Feature Three', bodyText: 'Describe the third key feature here.' }),
                ];
                this.$emit('element-update', this.element);
            }
        },

        makeCard(overrides = {}) {
            return Object.assign({
                headline: 'Feature',
                subheadline: '',
                bodyText: 'Short description.',
                headlineColor: '#1e293b',
                subheadlineColor: '#64748b',
                bodyTextColor: '#64748b',
                iconImage: null,
                iconColor: '#6366f1',
                buttons: [this.makeButton()],
                cardBackgroundColor: '#ffffff',
                cardAccentColor: '#6366f1',
                mediaType: 'image',
                mediaDisplayMode: 'crop',
                mediaHeight: '200',
                cardBackgroundImage: null,
                cardBackgroundVideo: null,
                cardBackgroundVideoUploadType: 'file',
                cardBackgroundVideoUploadUrl: '',
            }, overrides);
        },

        makeButton(overrides = {}) {
            return Object.assign({
                show: true,
                buttonText: 'Learn More',
                buttonLinkType: 'internal',
                buttonUrl: '',
                buttonTarget: '_self',
                buttonTextColor: '#ffffff',
            }, overrides);
        },

        getCardButtons(card) {
            return Array.isArray(card?.buttons) ? card.buttons : [];
        },

        toggleAccordion(index) {
            this.openIndex = this.openIndex === index ? null : index;
        },

        toggleButtonAccordion(cardIndex, buttonIndex) {
            const key = `${cardIndex}-${buttonIndex}`;
            this.openButtonIndexes = { ...this.openButtonIndexes, [key]: !this.openButtonIndexes[key] };
        },

        isButtonOpen(cardIndex, buttonIndex) {
            return !!this.openButtonIndexes[`${cardIndex}-${buttonIndex}`];
        },

        addCard() {
            const cards = Array.isArray(this.element.config.cards.value)
                ? [...this.element.config.cards.value]
                : [];
            cards.push(this.makeCard());
            this.element.config.cards.value = cards;
            this.$emit('element-update', this.element);
        },

        removeCard(index) {
            if (!Array.isArray(this.element.config.cards.value)) return;
            const cards = [...this.element.config.cards.value];
            cards.splice(index, 1);
            this.element.config.cards.value = cards;
            this.$emit('element-update', this.element);
        },

        addButton(cardIndex) {
            const card = this.element.config.cards.value[cardIndex];
            if (!Array.isArray(card.buttons)) card.buttons = [];
            card.buttons.push(this.makeButton());
            this.$emit('element-update', this.element);
        },

        removeButton(cardIndex, buttonIndex) {
            const card = this.element.config.cards.value[cardIndex];
            if (!Array.isArray(card?.buttons)) return;
            card.buttons.splice(buttonIndex, 1);
            this.$emit('element-update', this.element);
        },

        onElementUpdate() {
            this.$emit('element-update', this.element);
        },

        // ── Icon image ────────────────────────────────────────────────
        getIconUploadTag(cardIndex) { return `ifg-icon-${this.element.id}-${cardIndex}`; },
        getIconMediaItem(cardIndex) { return this.element.data?.iconImages?.[cardIndex] ?? null; },

        onOpenIconModal(cardIndex) { this.iconImageModals = { ...this.iconImageModals, [cardIndex]: true }; },
        onCloseIconModal(cardIndex) { this.iconImageModals = { ...this.iconImageModals, [cardIndex]: false }; },

        onIconUpload(cardIndex, { targetId }) {
            this.mediaRepository.get(targetId).then((media) => {
                this.element.config.cards.value[cardIndex].iconImage = targetId;
                this.setIconData(cardIndex, media);
                this.$emit('element-update', this.element);
            });
        },

        onIconRemove(cardIndex) {
            this.element.config.cards.value[cardIndex].iconImage = null;
            this.setIconData(cardIndex, null);
            this.$emit('element-update', this.element);
        },

        onIconSelectionChange(cardIndex, mediaEntity) {
            if (!mediaEntity?.length) return;
            const media = mediaEntity[0];
            if (media && ['IMAGE', 'VECTOR_GRAPHIC'].includes(media.mediaType?.name)) {
                this.element.config.cards.value[cardIndex].iconImage = media.id;
                this.setIconData(cardIndex, media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({ message: 'Please select a valid image or SVG file' });
            }
        },

        setIconData(cardIndex, media) {
            if (!this.element.data) this.element.data = {};
            if (!this.element.data.iconImages) this.element.data.iconImages = {};
            this.element.data.iconImages[cardIndex] = media;
        },

        // ── Card image ────────────────────────────────────────────────
        getCardImageUploadTag(cardIndex) { return `ifg-card-img-${this.element.id}-${cardIndex}`; },
        getCardImageMediaItem(cardIndex) { return this.element.data?.cardImages?.[cardIndex] ?? null; },

        onOpenCardImageModal(cardIndex) { this.cardImageModals = { ...this.cardImageModals, [cardIndex]: true }; },
        onCloseCardImageModal(cardIndex) { this.cardImageModals = { ...this.cardImageModals, [cardIndex]: false }; },

        onCardImageUpload(cardIndex, { targetId }) {
            this.mediaRepository.get(targetId).then((media) => {
                this.element.config.cards.value[cardIndex].cardBackgroundImage = targetId;
                this.setCardImageData(cardIndex, media);
                this.$emit('element-update', this.element);
            });
        },

        onCardImageRemove(cardIndex) {
            this.element.config.cards.value[cardIndex].cardBackgroundImage = null;
            this.setCardImageData(cardIndex, null);
            this.$emit('element-update', this.element);
        },

        onCardImageSelectionChange(cardIndex, mediaEntity) {
            if (!mediaEntity?.length) return;
            const media = mediaEntity[0];
            if (media && media.mediaType?.name === 'IMAGE') {
                this.element.config.cards.value[cardIndex].cardBackgroundImage = media.id;
                this.setCardImageData(cardIndex, media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({ message: 'Please select a valid image file' });
            }
        },

        setCardImageData(cardIndex, media) {
            if (!this.element.data) this.element.data = {};
            if (!this.element.data.cardImages) this.element.data.cardImages = {};
            this.element.data.cardImages[cardIndex] = media;
        },

        // ── Card video ────────────────────────────────────────────────
        getCardVideoUploadTag(cardIndex) { return `ifg-card-vid-${this.element.id}-${cardIndex}`; },
        getCardVideoMediaItem(cardIndex) { return this.element.data?.cardVideos?.[cardIndex] ?? null; },

        onOpenCardVideoModal(cardIndex) { this.cardVideoModals = { ...this.cardVideoModals, [cardIndex]: true }; },
        onCloseCardVideoModal(cardIndex) { this.cardVideoModals = { ...this.cardVideoModals, [cardIndex]: false }; },

        onCardVideoUpload(cardIndex, { targetId }) {
            this.mediaRepository.get(targetId).then((media) => {
                this.element.config.cards.value[cardIndex].cardBackgroundVideo = targetId;
                this.setCardVideoData(cardIndex, media);
                this.$emit('element-update', this.element);
            });
        },

        onCardVideoRemove(cardIndex) {
            this.element.config.cards.value[cardIndex].cardBackgroundVideo = null;
            this.setCardVideoData(cardIndex, null);
            this.$emit('element-update', this.element);
        },

        onCardVideoSelectionChange(cardIndex, mediaEntity) {
            if (!mediaEntity?.length) return;
            const media = mediaEntity[0];
            if (media && media.mediaType?.name === 'VIDEO') {
                this.element.config.cards.value[cardIndex].cardBackgroundVideo = media.id;
                this.setCardVideoData(cardIndex, media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({ message: 'Please select a valid video file' });
            }
        },

        setCardVideoData(cardIndex, media) {
            if (!this.element.data) this.element.data = {};
            if (!this.element.data.cardVideos) this.element.data.cardVideos = {};
            this.element.data.cardVideos[cardIndex] = media;
        },

        // ── Load existing media on mount ──────────────────────────────
        async loadMedia() {
            const cards = this.element.config.cards?.value;
            if (!Array.isArray(cards)) return;

            for (let i = 0; i < cards.length; i++) {
                const card = cards[i];

                if (card.iconImage && !this.element.data?.iconImages?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.iconImage);
                        this.setIconData(i, media);
                    } catch (_) { /* noop */ }
                }

                if (card.cardBackgroundImage && !this.element.data?.cardImages?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.cardBackgroundImage);
                        this.setCardImageData(i, media);
                    } catch (_) { /* noop */ }
                }

                if (card.cardBackgroundVideo && !this.element.data?.cardVideos?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.cardBackgroundVideo);
                        this.setCardVideoData(i, media);
                    } catch (_) { /* noop */ }
                }
            }
        },
    },
};
