import template from './sw-cms-el-config-mainhattan-three-card.html.twig';
import './sw-cms-el-config-mainhattan-three-card.scss';
import './component/mainhattan-dynamic-url-field';
import { defaultCards } from '../shared/default-cards';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            mediaModalIsOpen: false,
            activeMediaField: null,
            activeCardIndex: 1,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        cardDefinitions() {
            return [
                this.createCardDefinition('One', 1),
                this.createCardDefinition('Two', 2),
                this.createCardDefinition('Three', 3),
            ];
        },

        currentMediaValue() {
            if (this.activeMediaField === null) {
                return null;
            }

            return this.element.config[this.activeMediaField].value;
        },
    },

    created() {
        this.initElementConfig('mainhattan-three-card');
        this.initializeElementData();
    },

    methods: {
        createCardDefinition(suffix, index) {
            const defaultCard = defaultCards[index - 1];

            return {
                index,
                mediaKey: `card${suffix}Image`,
                titleKey: `card${suffix}Title`,
                descriptionKey: `card${suffix}Description`,
                buttonTextKey: `card${suffix}ButtonText`,
                buttonLinkKey: `card${suffix}ButtonLink`,
                defaultImageUrl: defaultCard.imageUrl,
            };
        },

        initializeElementData() {
            if (!this.element.data) {
                this.$set(this.element, 'data', {});
            }

            this.cardDefinitions.forEach((card) => {
                if (typeof this.element.data[card.mediaKey] === 'undefined') {
                    this.$set(this.element.data, card.mediaKey, null);
                }
            });
        },

        uploadTag(mediaKey) {
            return `cms-element-${this.element.id}-${mediaKey}`;
        },

        previewSource(mediaKey) {
            return this.element.data?.[mediaKey] ?? this.element.config[mediaKey].value;
        },

        async onImageUpload(mediaKey, { targetId }) {
            const mediaEntity = await this.mediaRepository.get(targetId);

            this.element.config[mediaKey].value = mediaEntity.id;
            this.element.config[mediaKey].source = 'static';

            this.updateElementData(mediaKey, mediaEntity);
            this.emitUpdate();
        },

        onImageRemove(mediaKey) {
            this.element.config[mediaKey].value = null;
            this.updateElementData(mediaKey, null);
            this.emitUpdate();
        },

        onOpenMediaModal(mediaKey) {
            this.activeMediaField = mediaKey;
            this.mediaModalIsOpen = true;
        },

        onCloseModal() {
            this.mediaModalIsOpen = false;
            this.activeMediaField = null;
        },

        onSelectionChanges(mediaEntities) {
            const media = mediaEntities[0];

            if (!media || this.activeMediaField === null) {
                this.onCloseModal();

                return;
            }

            this.element.config[this.activeMediaField].value = media.id;
            this.element.config[this.activeMediaField].source = 'static';

            this.updateElementData(this.activeMediaField, media);
            this.emitUpdate();
            this.onCloseModal();
        },

        updateElementData(mediaKey, media) {
            this.$set(this.element.data, mediaKey, media);
        },

        onInputValue(configKey, value) {
            this.element.config[configKey].value = value;
            this.emitUpdate();
        },

        onInputDescription(configKey, value) {
            this.element.config[configKey].value = value;
            this.emitUpdate();
        },

        toggleCard(index) {
            this.activeCardIndex = this.activeCardIndex === index ? 0 : index;
        },

        isCardOpen(index) {
            return this.activeCardIndex === index;
        },

        emitUpdate() {
            this.$emit('element-update', this.element);
        },
    },
};
