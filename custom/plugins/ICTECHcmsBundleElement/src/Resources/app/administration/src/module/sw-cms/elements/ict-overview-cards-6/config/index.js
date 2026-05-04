import template from './sw-cms-el-config-ict-overview-cards-6.html.twig';
import './sw-cms-el-config-ict-overview-cards-6.scss';

const {Mixin} = Shopware;

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
            activeCardIndex: 0,
            openIndex: null,
            openButtonSettingsIndexes: {},
            openButtonItemIndexes: {},
            mediaModalOpen: false,
            currentMediaField: null,
            cardImageModals: {},
            cardVideoModals: {},
            cardMainImageModals: {},
            cardMainVideoModals: {},
            iconImageModals: {},
            featureIconModals: {},
            backgroundMediaModalIsOpen: false,
            backgroundVideoModalIsOpen: false
        };
    },

    computed: {
        columnOptions() {
            return [
                {value: 1, label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.columns.one')},
                {value: 2, label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.columns.two')},
                {value: 3, label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.columns.three')},
                {value: 4, label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.columns.four')}
            ];
        },
        iconShapeOptions() {
            return [
                {value: 'square', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconShape.square')},
                {value: 'round', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconShape.round')}
            ];
        },
        iconSizeOptions() {
            return [
                {value: '50', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.small')},
                {value: '80', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.medium')},
                {value: '120', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.large')},
                {value: '150', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.xl150')},
                {value: '180', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.xl180')},
                {value: '200', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.xl200')},
                {value: '220', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconSize.xl220')}
            ];
        },
        featureIconSizeOptions() {
            return [
                {value: '20', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.featureIconSize.size20')},
                {value: '25', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.featureIconSize.size25')},
                {value: '40', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.featureIconSize.size40')},
                {value: '50', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.featureIconSize.size50')}
            ];
        },
        borderStyleOptions() {
            return [
                {
                    value: 'solid',
                    label: this.$tc('sw-cms.elements.ictTopics.config.borderStyle.solid')
                },
                {
                    value: 'dotted',
                    label: this.$tc('sw-cms.elements.ictTopics.config.borderStyle.dotted')
                },
                {
                    value: 'dashed',
                    label: this.$tc('sw-cms.elements.ictTopics.config.borderStyle.dashed')
                }
            ];
        },
        linkTypeOptions() {
            return [
                {value: 'internal', label: 'Internal Link'},
                {value: 'external', label: 'External Link'}
            ];
        },
        buttonIconSizeOptions() {
            return [
                {value: '20', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size20')},
                {value: '25', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size25')},
                {value: '50', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size50')},
                {value: '80', label: this.$tc('sw-cms.elements.ictTwoColumnThreeImage.config.option.buttonIconSize.size80')},
            ];
        },
        iconHoverEffectOptions() {
            return [
                {value: 'none', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconHoverEffect.none')},
                {value: 'scale', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconHoverEffect.scale')},
                {value: 'rotate', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconHoverEffect.rotate')},
                {value: 'pulse', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.iconHoverEffect.pulse')}
            ];
        },
        outlinePositionOptions() {
            return [
                {value: 'inside', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.outlinePosition.inside')},
                {value: 'outside', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.outlinePosition.outside')}
            ];
        },
        verticalAlignmentOptions() {
            return [
                {value: 'stretch', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.verticalAlignment.stretch')},
                {value: 'start', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.verticalAlignment.top')},
                {value: 'center', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.verticalAlignment.center')},
                {value: 'end', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.verticalAlignment.bottom')}
            ];
        },
        headlineSizeOptions() {
            return [
                {value: '24', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.headlineSize.size1')},
                {value: '42', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.headlineSize.size2')}
            ];
        },
        subheadlineSizeOptions() {
            return [
                {value: '20', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.subheadlineSize.default')},
                {value: '24', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.subheadlineSize.optional')}
            ];
        },
        alignmentOptions() {
            return [
                {value: 'left', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.alignment.left')},
                {value: 'center', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.alignment.center')},
                {value: 'right', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.alignment.right')}
            ];
        },
        fontWeightOptions() {
            return [
                {value: '400', label: 'Regular'},
                {value: '700', label: 'Bold'}
            ];
        },
        mediaPositionOptions() {
            return [
                {value: 'left', label: 'Media left'},
                {value: 'right', label: 'Media right'},
                {value: 'center', label: 'Media center'}
            ];
        },
        mediaDisplayModeOptions() {
            return [
                {value: 'fill', label: 'Fill'},
                {value: 'fit', label: 'Fit'},
                {value: 'crop', label: 'Crop'},
                {value: 'tile', label: 'Tile'}
            ];
        },
        backgroundTypeOptions() {
            return [
                {value: 'color', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.backgroundType.color')},
                {value: 'gradient', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.backgroundType.gradient')},
                {value: 'image', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.backgroundType.image')},
                {value: 'video', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.backgroundType.video')}
            ];
        },
        videoUploadOptions() {
            return [
                {value: 'file', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.videoUpload.file')},
                {value: 'url', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.videoUpload.url')}
            ];
        },
        gradientDirectionOptions() {
            return [
                {value: 'top', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.gradientDirection.top')},
                {value: 'right', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.gradientDirection.right')},
                {value: 'bottom', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.gradientDirection.bottom')},
                {value: 'left', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.gradientDirection.left')}
            ];
        },
        shadowOptions() {
            return [
                {value: 'none', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.shadow.none')},
                {value: 'light', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.shadow.light')},
                {value: 'medium', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.shadow.medium')},
                {value: 'heavy', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.shadow.heavy')}
            ];
        },
        targetOptions() {
            return [
                {value: '_self', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.target.same')},
                {value: '_blank', label: this.$tc('sw-cms.elements.ictOverviewCards.config.option.target.new')}
            ];
        },
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },
        backgroundUploadTag() {
            return `cms-element-background-${this.element.id}`;
        },
        backgroundMediaItem() {
            return this.element.data?.backgroundMedia || null;
        },
        backgroundVideoUploadTag() {
            return `cms-element-background-video-${this.element.id}`;
        },
        backgroundVideoMediaItem() {
            return this.element.data?.backgroundVideoMedia || null;
        },
        fileAccept() {
            return 'image/*';
        },
        videoFileAccept() {
            return 'video/*';
        }
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.loadMedia();
    },

    watch: {
        'element.config': {
            deep: true,
            handler() {
                if (this.element) {
                    this.$emit('element-update', this.element);
                }
            }
        }
    },

    methods: {
        createdComponent() {
            this.initElementConfig('ict-overview-cards-6');
            this.ensureCardsInitialized();
            this.ensureCardDefaults();
        },

        createButton(overrides = {}) {
            return Object.assign({
                show: true,
                buttonText: 'LEARN MORE',
                buttonLinkType: 'internal',
                buttonUrl: '',
                buttonTarget: '_self',
                buttonTextColor: '#FFFFFF',
                buttonHoverTextColor: '#000000',
                buttonBorderColor: '#00f5ff',
                buttonHoverBorderColor: '#f3f4f6',
                buttonBorderStyle: 'solid',
                buttonBorderWidth: 0,
                buttonBorderRadius: 8
            }, overrides);
        },

        createDetailRow(overrides = {}) {
            return Object.assign({
                title: 'Title',
                value: 'Value',
                titleColor: '#262624',
                valueColor: '#262624'
            }, overrides);
        },

        createButtonFromLegacyCard(card) {
            return this.createButton({
                show: card?.show,
                buttonText: card?.buttonText,
                buttonLinkType: card?.buttonLinkType,
                buttonUrl: card?.buttonUrl,
                buttonTarget: card?.buttonTarget,
                buttonTextColor: card?.buttonTextColor,
                buttonHoverTextColor: card?.buttonHoverTextColor,
                buttonBorderColor: card?.buttonBorderColor,
                buttonHoverBorderColor: card?.buttonHoverBorderColor,
                buttonBorderStyle: card?.buttonBorderStyle,
                buttonBorderWidth: card?.buttonBorderWidth,
                buttonBorderRadius: card?.buttonBorderRadius
            });
        },

        ensureButtonDefaults(button) {
            const normalizedButton = this.createButton(button);

            Object.keys(normalizedButton).forEach((key) => {
                if (button[key] === undefined || button[key] === null || (typeof normalizedButton[key] === 'string' && String(button[key]).trim() === '')) {
                    button[key] = normalizedButton[key];
                }
            });

            if (String(button.buttonBorderRadius).trim() === '4') {
                button.buttonBorderRadius = 8;
            }
        },

        ensureCardButtons(card) {
            if (!Array.isArray(card.buttons)) {
                card.buttons = [this.createButtonFromLegacyCard(card)];
            }

            card.buttons.forEach((button) => {
                this.ensureButtonDefaults(button);
            });
        },

        getCardButtons(card) {
            if (Array.isArray(card?.buttons)) {
                return card.buttons;
            }

            return [this.createButtonFromLegacyCard(card)];
        },

        getButtonMediaKey(cardIndex, buttonIndex) {
            return `${cardIndex}-${buttonIndex}`;
        },

        rebuildButtonIconData(cardIndex, mediaItems = []) {
            if (!this.element.data) {
                this.element.data = {};
            }

            if (!this.element.data.buttonIcons) {
                this.element.data.buttonIcons = {};
            }

            Object.keys(this.element.data.buttonIcons).forEach((key) => {
                if (key === String(cardIndex) || key.startsWith(`${cardIndex}-`)) {
                    delete this.element.data.buttonIcons[key];
                }
            });

            mediaItems.forEach((media, index) => {
                this.updateButtonIconData(cardIndex, index, media || null);
            });
        },

        ensureCardDefaults() {
            if (!this.element?.config?.cards?.value || !Array.isArray(this.element.config.cards.value)) {
                return;
            }

            this.element.config.cards.value.forEach((card) => {
                this.ensureCardButtons(card);

                if (card.bodyTextSize === undefined || card.bodyTextSize === null || String(card.bodyTextSize).trim() === '') {
                    card.bodyTextSize = '16';
                }
                if (card.mediaPosition === undefined || card.mediaPosition === null || String(card.mediaPosition).trim() === '') {
                    card.mediaPosition = 'center';
                }
                if (card.mediaWidth === undefined || card.mediaWidth === null || String(card.mediaWidth).trim() === '') {
                    card.mediaWidth = '50';
                }
                if (card.contentWidth === undefined || card.contentWidth === null || String(card.contentWidth).trim() === '') {
                    card.contentWidth = '50';
                }
                if (card.mediaDisplayMode === undefined || card.mediaDisplayMode === null || String(card.mediaDisplayMode).trim() === '') {
                    card.mediaDisplayMode = 'fill';
                }
                if (card.mediaHeight === undefined || card.mediaHeight === null || String(card.mediaHeight).trim() === '') {
                    card.mediaHeight = '345';
                }
                if (card.cardMainBackgroundType === undefined || card.cardMainBackgroundType === null || String(card.cardMainBackgroundType).trim() === '') {
                    card.cardMainBackgroundType = 'color';
                }
                if (card.cardMainBackgroundColor === undefined || card.cardMainBackgroundColor === null || String(card.cardMainBackgroundColor).trim() === '') {
                    card.cardMainBackgroundColor = '#FFFFFF';
                }
                if (card.cardMainBackgroundImage === undefined) {
                    card.cardMainBackgroundImage = null;
                }
                if (card.cardMainBackgroundVideo === undefined) {
                    card.cardMainBackgroundVideo = null;
                }
                if (card.cardMainBackgroundVideoUploadType === undefined || card.cardMainBackgroundVideoUploadType === null || String(card.cardMainBackgroundVideoUploadType).trim() === '') {
                    card.cardMainBackgroundVideoUploadType = 'file';
                }
                if (card.cardMainBackgroundVideoUploadUrl === undefined || card.cardMainBackgroundVideoUploadUrl === null) {
                    card.cardMainBackgroundVideoUploadUrl = '';
                }
                if (card.cardMainBackgroundGradientColor1 === undefined || card.cardMainBackgroundGradientColor1 === null || String(card.cardMainBackgroundGradientColor1).trim() === '') {
                    card.cardMainBackgroundGradientColor1 = '#005AE5';
                }
                if (card.cardMainBackgroundGradientColor2 === undefined || card.cardMainBackgroundGradientColor2 === null || String(card.cardMainBackgroundGradientColor2).trim() === '') {
                    card.cardMainBackgroundGradientColor2 = '#0080FF';
                }
                if (card.cardMainBackgroundGradientDirection === undefined || card.cardMainBackgroundGradientDirection === null || String(card.cardMainBackgroundGradientDirection).trim() === '') {
                    card.cardMainBackgroundGradientDirection = 'top';
                }
                if (card.iconAlignment === undefined || card.iconAlignment === null || String(card.iconAlignment).trim() === '') {
                    card.iconAlignment = 'left';
                }
                if (card.headlineAlignment === undefined || card.headlineAlignment === null || String(card.headlineAlignment).trim() === '') {
                    card.headlineAlignment = 'left';
                }
                if (card.headlineSize === undefined || card.headlineSize === null || String(card.headlineSize).trim() === '') {
                    card.headlineSize = '24';
                }
                if (card.headlineLineHeight === undefined || card.headlineLineHeight === null || String(card.headlineLineHeight).trim() === '') {
                    card.headlineLineHeight = '28';
                }
                if (card.subheadlineAlignment === undefined || card.subheadlineAlignment === null || String(card.subheadlineAlignment).trim() === '') {
                    card.subheadlineAlignment = 'left';
                }
                if (card.subheadlineSize === undefined || card.subheadlineSize === null || String(card.subheadlineSize).trim() === '') {
                    card.subheadlineSize = '20';
                }
                if (card.subheadlineLineHeight === undefined || card.subheadlineLineHeight === null || String(card.subheadlineLineHeight).trim() === '') {
                    card.subheadlineLineHeight = '25';
                }
                if (card.bodyTextAlignment === undefined || card.bodyTextAlignment === null || String(card.bodyTextAlignment).trim() === '') {
                    card.bodyTextAlignment = 'left';
                }
                if (card.bodyTextLineHeight === undefined || card.bodyTextLineHeight === null || String(card.bodyTextLineHeight).trim() === '') {
                    card.bodyTextLineHeight = '24';
                }
                if (card.showDetailRows === undefined || card.showDetailRows === null) {
                    card.showDetailRows = true;
                }
                if (!Array.isArray(card.detailRows) || card.detailRows.length === 0) {
                    card.detailRows = [
                        this.createDetailRow({title: 'Title 1', value: 'Value 1'}),
                        this.createDetailRow({title: 'Title 2', value: 'Value 2'}),
                        this.createDetailRow({title: 'Title 3', value: 'Value 3'}),
                        this.createDetailRow({title: 'Title 4', value: 'Value 4'})
                    ];
                }
                card.detailRows = card.detailRows.map((row) => this.createDetailRow(row));
                if (card.detailRowsTitleAlignment === undefined || card.detailRowsTitleAlignment === null || String(card.detailRowsTitleAlignment).trim() === '') {
                    card.detailRowsTitleAlignment = 'left';
                }
                if (card.detailRowsTitleSize === undefined || card.detailRowsTitleSize === null || String(card.detailRowsTitleSize).trim() === '') {
                    card.detailRowsTitleSize = '16';
                }
                if (card.detailRowsTitleLineHeight === undefined || card.detailRowsTitleLineHeight === null || String(card.detailRowsTitleLineHeight).trim() === '') {
                    card.detailRowsTitleLineHeight = '24';
                }
                if (card.detailRowsTitleFontWeight === undefined || card.detailRowsTitleFontWeight === null || String(card.detailRowsTitleFontWeight).trim() === '') {
                    card.detailRowsTitleFontWeight = '700';
                }
                if (card.detailRowsValueAlignment === undefined || card.detailRowsValueAlignment === null || String(card.detailRowsValueAlignment).trim() === '') {
                    card.detailRowsValueAlignment = 'right';
                }
                if (card.detailRowsValueSize === undefined || card.detailRowsValueSize === null || String(card.detailRowsValueSize).trim() === '') {
                    card.detailRowsValueSize = '16';
                }
                if (card.detailRowsValueLineHeight === undefined || card.detailRowsValueLineHeight === null || String(card.detailRowsValueLineHeight).trim() === '') {
                    card.detailRowsValueLineHeight = '24';
                }
                if (card.detailRowsValueFontWeight === undefined || card.detailRowsValueFontWeight === null || String(card.detailRowsValueFontWeight).trim() === '') {
                    card.detailRowsValueFontWeight = '400';
                }
                if (card.detailRowsOutlineColor === undefined || card.detailRowsOutlineColor === null || String(card.detailRowsOutlineColor).trim() === '') {
                    card.detailRowsOutlineColor = '#E4E4E3';
                }
                if (card.detailRowsOutlineThickness === undefined || card.detailRowsOutlineThickness === null || String(card.detailRowsOutlineThickness).trim() === '') {
                    card.detailRowsOutlineThickness = '1';
                }
                if (card.infoText === undefined || card.infoText === null || String(card.infoText).trim() === '') {
                    card.infoText = 'Infotext';
                }
                if (card.infoTextAlignment === undefined || card.infoTextAlignment === null || String(card.infoTextAlignment).trim() === '') {
                    card.infoTextAlignment = 'left';
                }
                if (card.infoTextSize === undefined || card.infoTextSize === null || String(card.infoTextSize).trim() === '') {
                    card.infoTextSize = '16';
                }
                if (card.infoTextLineHeight === undefined || card.infoTextLineHeight === null || String(card.infoTextLineHeight).trim() === '') {
                    card.infoTextLineHeight = '24';
                }
                if (card.infoTextColor === undefined || card.infoTextColor === null || String(card.infoTextColor).trim() === '') {
                    card.infoTextColor = '#262624';
                }
                if (card.infoTextFontWeight === undefined || card.infoTextFontWeight === null || String(card.infoTextFontWeight).trim() === '') {
                    card.infoTextFontWeight = '700';
                }
                if (card.buttonText === undefined || card.buttonText === null || String(card.buttonText).trim() === '') {
                    card.buttonText = 'LEARN MORE';
                }
                if (card.buttonLinkType === undefined || card.buttonLinkType === null || String(card.buttonLinkType).trim() === '') {
                    card.buttonLinkType = 'internal';
                }
                if (card.buttonUrl === undefined || card.buttonUrl === null) {
                    card.buttonUrl = '';
                }
                if (card.buttonTarget === undefined || card.buttonTarget === null || String(card.buttonTarget).trim() === '') {
                    card.buttonTarget = '_self';
                }
                if (card.buttonTextColor === undefined || card.buttonTextColor === null || String(card.buttonTextColor).trim() === '') {
                    card.buttonTextColor = '#FFFFFF';
                }
                if (card.buttonHoverTextColor === undefined || card.buttonHoverTextColor === null || String(card.buttonHoverTextColor).trim() === '') {
                    card.buttonHoverTextColor = '#000000';
                }
                if (card.buttonBorderColor === undefined || card.buttonBorderColor === null || String(card.buttonBorderColor).trim() === '') {
                    card.buttonBorderColor = '#00f5ff';
                }
                if (card.buttonHoverBorderColor === undefined || card.buttonHoverBorderColor === null || String(card.buttonHoverBorderColor).trim() === '') {
                    card.buttonHoverBorderColor = '#f3f4f6';
                }
                if (card.buttonBorderStyle === undefined || card.buttonBorderStyle === null || String(card.buttonBorderStyle).trim() === '') {
                    card.buttonBorderStyle = 'solid';
                }
                if (card.buttonBorderWidth === undefined || card.buttonBorderWidth === null || String(card.buttonBorderWidth).trim() === '') {
                    card.buttonBorderWidth = 0;
                }
                if (card.buttonBorderRadius === undefined || card.buttonBorderRadius === null || String(card.buttonBorderRadius).trim() === '') {
                    card.buttonBorderRadius = 8;
                }
            });
        },

        cloneCardTemplate() {
            const template = this.cmsElements?.['ict-overview-cards-6']?.cardTemplates;

            if (template && typeof template === 'object') {
                return JSON.parse(JSON.stringify(template));
            }

            return {
                iconShape: 'square',
                iconSize: '80',
                iconAlignment: 'left',
                iconBackgroundColor: 'transparent',
                iconColor: '#005AE5',
                iconOutlineColor: 'transparent',
                iconOutlineThickness: '0',
                iconOutlinePosition: 'inside',
                iconBorderRadius: '8',
                iconImage: null,
                iconHoverEffect: 'none',
                headline: 'Headline',
                headlineSize: '24',
                headlineLineHeight: '28',
                headlineAlignment: 'left',
                headlineColor: '#262624',
                subheadline: 'Subheadline',
                subheadlineSize: '20',
                subheadlineLineHeight: '25',
                subheadlineAlignment: 'left',
                subheadlineColor: '#262624',
                showBodyText: true,
                bodyText: 'Description',
                bodyTextSize: '16',
                bodyTextLineHeight: '24',
                bodyTextAlignment: 'left',
                bodyTextColor: '#262624',
                showDetailRows: true,
                detailRows: [
                    this.createDetailRow({title: 'Title 1', value: 'Value 1'}),
                    this.createDetailRow({title: 'Title 2', value: 'Value 2'}),
                    this.createDetailRow({title: 'Title 3', value: 'Value 3'}),
                    this.createDetailRow({title: 'Title 4', value: 'Value 4'})
                ],
                detailRowsTitleAlignment: 'left',
                detailRowsTitleSize: '16',
                detailRowsTitleLineHeight: '24',
                detailRowsTitleFontWeight: '700',
                detailRowsValueAlignment: 'right',
                detailRowsValueSize: '16',
                detailRowsValueLineHeight: '24',
                detailRowsValueFontWeight: '400',
                detailRowsOutlineColor: '#E4E4E3',
                detailRowsOutlineThickness: '1',
                infoText: 'Infotext',
                infoTextAlignment: 'left',
                infoTextSize: '16',
                infoTextLineHeight: '24',
                infoTextColor: '#262624',
                infoTextFontWeight: '700',
                buttons: [
                    this.createButton()
                ],
                buttonText: 'LEARN MORE',
                buttonLinkType: 'internal',
                buttonUrl: '',
                buttonTarget: '_self',
                buttonTextColor: '#FFFFFF',
                buttonHoverTextColor: '#000000',
                buttonBorderColor: '#00f5ff',
                buttonHoverBorderColor: '#f3f4f6',
                buttonBorderStyle: 'solid',
                buttonBorderWidth: 0,
                buttonBorderRadius: 8,
                mediaPosition: 'center',
                mediaWidth: '50',
                contentWidth: '50',
                mediaDisplayMode: 'fill',
                mediaHeight: '345',
                cardMainBackgroundType: 'color',
                cardMainBackgroundColor: '#FFFFFF',
                cardMainBackgroundImage: null,
                cardMainBackgroundVideo: null,
                cardMainBackgroundVideoUploadType: 'file',
                cardMainBackgroundVideoUploadUrl: '',
                cardMainBackgroundGradientColor1: '#005AE5',
                cardMainBackgroundGradientColor2: '#0080FF',
                cardMainBackgroundGradientDirection: 'top',
                cardBackgroundType: 'image',
                cardBackgroundColor: '#FFFFFF',
                cardBackgroundImage: null,
                cardBackgroundVideo: null,
                cardBackgroundVideoUploadType: 'file',
                cardBackgroundVideoUploadUrl: '',
                cardBackgroundGradientColor1: '#005AE5',
                cardBackgroundGradientColor2: '#0080FF',
                cardBackgroundGradientDirection: 'top'
            };
        },

        createCard(overrides = {}) {
            return Object.assign(this.cloneCardTemplate(), overrides);
        },

        getInitialCards() {
            return [
                this.createCard({
                    headline: 'Headline',
                    subheadline: 'Subheadline',
                    bodyText: 'Description',
                    detailRows: [
                        this.createDetailRow({title: 'Title 1', value: 'Value 1'}),
                        this.createDetailRow({title: 'Title 2', value: 'Value 2'}),
                        this.createDetailRow({title: 'Title 3', value: 'Value 3'}),
                        this.createDetailRow({title: 'Title 4', value: 'Value 4'})
                    ],
                    infoText: 'Infotext',
                    buttons: [
                        this.createButton()
                    ]
                }),
                this.createCard({
                    headline: 'Headline',
                    subheadline: 'Subheadline',
                    bodyText: 'Description',
                    detailRows: [
                        this.createDetailRow({title: 'Title 1', value: 'Value 1'}),
                        this.createDetailRow({title: 'Title 2', value: 'Value 2'}),
                        this.createDetailRow({title: 'Title 3', value: 'Value 3'})
                    ],
                    infoText: 'Infotext',
                    buttons: [
                        this.createButton()
                    ]
                }),
                this.createCard({
                    headline: 'Headline',
                    subheadline: 'Subheadline',
                    bodyText: 'Description',
                    detailRows: [
                        this.createDetailRow({title: 'Title 1', value: 'Value 1'}),
                        this.createDetailRow({title: 'Title 2', value: 'Value 2'}),
                        this.createDetailRow({title: 'Title 3', value: 'Value 3'}),
                        this.createDetailRow({title: 'Title 4', value: 'Value 4'})
                    ],
                    infoText: 'Infotext',
                    buttons: [
                        this.createButton()
                    ]
                })
            ];
        },

        ensureCardsInitialized() {
            if (!this.element?.config?.cards) {
                return;
            }

            if (!Array.isArray(this.element.config.cards.value)) {
                this.element.config.cards.value = [];
            }

            if (this.element.config.cards.value.length === 0) {
                this.element.config.cards.value = this.getInitialCards();
                this.$emit('element-update', this.element);
            }
        },

        toggleAccordion(index) {
            this.openIndex = this.openIndex === index ? null : index;
        },

        toggleButtonSettingsAccordion(cardIndex) {
            this.openButtonSettingsIndexes = {
                ...this.openButtonSettingsIndexes,
                [cardIndex]: !this.openButtonSettingsIndexes[cardIndex]
            };
        },

        isButtonSettingsAccordionOpen(cardIndex) {
            return !!this.openButtonSettingsIndexes[cardIndex];
        },

        toggleButtonAccordion(cardIndex, buttonIndex) {
            const key = this.getButtonMediaKey(cardIndex, buttonIndex);
            const isOpen = this.isButtonAccordionOpen(cardIndex, buttonIndex);
            this.openButtonItemIndexes = {
                ...this.openButtonItemIndexes,
                [key]: !isOpen
            };
        },

        isButtonAccordionOpen(cardIndex, buttonIndex) {
            const key = this.getButtonMediaKey(cardIndex, buttonIndex);
            return this.openButtonItemIndexes[key] !== false;
        },

        addCard() {
            const cards = Array.isArray(this.element.config.cards.value) ? [...this.element.config.cards.value] : [];
            cards.push(this.createCard());
            this.element.config.cards.value = cards;
            this.$emit('element-update', this.element);
        },

        addButton(cardIndex) {
            const card = this.element.config.cards.value[cardIndex];

            if (!Array.isArray(card.buttons)) {
                card.buttons = [];
            }

            card.buttons.push(this.createButton());
            this.openButtonItemIndexes = {
                ...this.openButtonItemIndexes,
                [this.getButtonMediaKey(cardIndex, card.buttons.length - 1)]: true
            };
            this.$emit('element-update', this.element);
        },

        removeButton(cardIndex, buttonIndex) {
            const card = this.element.config.cards.value[cardIndex];

            if (!Array.isArray(card?.buttons)) {
                return;
            }

            const buttonMediaItems = card.buttons.map((button, index) => (
                button?.buttonIcon ? this.getButtonIconMediaItem(cardIndex, index) : null
            ));

            card.buttons.splice(buttonIndex, 1);
            buttonMediaItems.splice(buttonIndex, 1);
            this.rebuildButtonIconData(cardIndex, buttonMediaItems);
            this.$emit('element-update', this.element);
        },

        removeCard(index) {
            if (!Array.isArray(this.element.config.cards.value)) {
                return;
            }

            const cards = [...this.element.config.cards.value];
            cards.splice(index, 1);
            this.element.config.cards.value = cards;

            if (this.activeCardIndex >= cards.length) {
                this.activeCardIndex = Math.max(0, cards.length - 1);
            }

            this.$emit('element-update', this.element);
        },

        addDetailRow(cardIndex) {
            if (!this.element.config.cards.value[cardIndex].detailRows) {
                this.element.config.cards.value[cardIndex].detailRows = [];
            }
            this.element.config.cards.value[cardIndex].detailRows.push(
                this.createDetailRow({
                    title: `Title ${this.element.config.cards.value[cardIndex].detailRows.length + 1}`,
                    value: `Value ${this.element.config.cards.value[cardIndex].detailRows.length + 1}`
                })
            );
            this.$emit('element-update', this.element);
        },

        removeDetailRow(cardIndex, rowIndex) {
            this.element.config.cards.value[cardIndex].detailRows.splice(rowIndex, 1);
            this.$emit('element-update', this.element);
        },

        onElementUpdate() {
            this.$emit('element-update', this.element);
        },

        async loadMedia() {
            if (this.element.config.backgroundImage?.value && !this.element.data?.backgroundMedia) {
                try {
                    const mediaEntity = await this.mediaRepository.get(this.element.config.backgroundImage.value);
                    if (!this.element.data) this.element.data = {};
                    this.element.data.backgroundMedia = mediaEntity;
                } catch (e) {}
            }
            if (this.element.config.backgroundVideo?.value && !this.element.data?.backgroundVideoMedia) {
                try {
                    const mediaEntity = await this.mediaRepository.get(this.element.config.backgroundVideo.value);
                    if (!this.element.data) this.element.data = {};
                    this.element.data.backgroundVideoMedia = mediaEntity;
                } catch (e) {}
            }
            if (this.element.config.cards?.value) {
                for (let index = 0; index < this.element.config.cards.value.length; index++) {
                    const card = this.element.config.cards.value[index];

                    if (card.iconImage && !this.element.data?.iconImages?.[index]) {
                        try {
                            const mediaEntity = await this.mediaRepository.get(card.iconImage);
                            this.updateIconImageData(index, mediaEntity);
                        } catch (e) {}
                    }

                    if (card.cardBackgroundImage && !this.element.data?.cardImages?.[index]) {
                        try {
                            const mediaEntity = await this.mediaRepository.get(card.cardBackgroundImage);
                            this.updateCardImageData(index, mediaEntity);
                        } catch (e) {}
                    }

                    if (card.cardBackgroundVideo && !this.element.data?.cardVideos?.[index]) {
                        try {
                            const mediaEntity = await this.mediaRepository.get(card.cardBackgroundVideo);
                            this.updateCardVideoData(index, mediaEntity);
                        } catch (e) {}
                    }

                    if (card.cardMainBackgroundImage && !this.element.data?.cardMainImages?.[index]) {
                        try {
                            const mediaEntity = await this.mediaRepository.get(card.cardMainBackgroundImage);
                            this.updateCardMainImageData(index, mediaEntity);
                        } catch (e) {}
                    }

                    if (card.cardMainBackgroundVideo && !this.element.data?.cardMainVideos?.[index]) {
                        try {
                            const mediaEntity = await this.mediaRepository.get(card.cardMainBackgroundVideo);
                            this.updateCardMainVideoData(index, mediaEntity);
                        } catch (e) {}
                    }

                    if (card.featurePoints && Array.isArray(card.featurePoints)) {
                        for (let pointIndex = 0; pointIndex < card.featurePoints.length; pointIndex++) {
                            const point = card.featurePoints[pointIndex];
                            const key = `${index}-${pointIndex}`;
                            if (point.icon && !this.element.data?.featureIcons?.[key]) {
                                try {
                                    const mediaEntity = await this.mediaRepository.get(point.icon);
                                    this.updateFeatureIconData(index, pointIndex, mediaEntity);
                                } catch (e) {}
                            }
                        }
                    }

                    const buttons = this.getCardButtons(card);
                    for (let buttonIndex = 0; buttonIndex < buttons.length; buttonIndex++) {
                        const button = buttons[buttonIndex];
                        const buttonMediaKey = this.getButtonMediaKey(index, buttonIndex);

                        if (button.buttonIcon && !this.element.data?.buttonIcons?.[buttonMediaKey]) {
                            try {
                                const mediaEntity = await this.mediaRepository.get(button.buttonIcon);
                                this.updateButtonIconData(index, buttonIndex, mediaEntity);
                            } catch (e) {}
                        }
                    }
                }
            }
        },

        updateCard(index, field, value) {
            const cards = [...this.element.config.cards.value];
            cards[index][field] = value;
            this.element.config.cards.value = cards;
            this.$emit('element-update', this.element);
        },

        onOpenCardImageModal(cardIndex) {
            this.cardImageModals = {...this.cardImageModals, [cardIndex]: true};
        },
        onCloseCardImageModal(cardIndex) {
            this.cardImageModals = {...this.cardImageModals, [cardIndex]: false};
        },
        onCardImageUpload(cardIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updateCard(cardIndex, 'cardBackgroundImage', targetId);
                this.updateCardImageData(cardIndex, mediaEntity);
            });
        },
        updateCardImageData(cardIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.cardImages) {
                this.element.data.cardImages = {};
            }
            this.element.data.cardImages[cardIndex] = media;
        },
        onCardImageRemove(cardIndex) {
            this.updateCard(cardIndex, 'cardBackgroundImage', null);
            this.updateCardImageData(cardIndex, null);
        },
        onCardImageSelectionChanges(cardIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.updateCard(cardIndex, 'cardBackgroundImage', media.id);
                this.updateCardImageData(cardIndex, media);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid image file'
                });
            }
        },
        getCardImageUploadTag(cardIndex) {
            return `cms-card-image-${this.element.id}-${cardIndex}`;
        },
        getCardImageMediaItem(cardIndex) {
            return this.element.data?.cardImages?.[cardIndex] || null;
        },

        onOpenCardMainImageModal(cardIndex) {
            this.cardMainImageModals = {...this.cardMainImageModals, [cardIndex]: true};
        },
        onCloseCardMainImageModal(cardIndex) {
            this.cardMainImageModals = {...this.cardMainImageModals, [cardIndex]: false};
        },
        onCardMainImageUpload(cardIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updateCard(cardIndex, 'cardMainBackgroundImage', targetId);
                this.updateCardMainImageData(cardIndex, mediaEntity);
            });
        },
        updateCardMainImageData(cardIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.cardMainImages) {
                this.element.data.cardMainImages = {};
            }
            this.element.data.cardMainImages[cardIndex] = media;
        },
        onCardMainImageRemove(cardIndex) {
            this.updateCard(cardIndex, 'cardMainBackgroundImage', null);
            this.updateCardMainImageData(cardIndex, null);
        },
        onCardMainImageSelectionChanges(cardIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE')) {
                this.updateCard(cardIndex, 'cardMainBackgroundImage', media.id);
                this.updateCardMainImageData(cardIndex, media);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid image file'
                });
            }
        },
        getCardMainImageUploadTag(cardIndex) {
            return `cms-card-main-image-${this.element.id}-${cardIndex}`;
        },
        getCardMainImageMediaItem(cardIndex) {
            return this.element.data?.cardMainImages?.[cardIndex] || null;
        },

        onOpenCardVideoModal(cardIndex) {
            this.cardVideoModals = {...this.cardVideoModals, [cardIndex]: true};
        },
        onCloseCardVideoModal(cardIndex) {
            this.cardVideoModals = {...this.cardVideoModals, [cardIndex]: false};
        },
        onCardVideoUpload(cardIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updateCard(cardIndex, 'cardBackgroundVideo', targetId);
                this.updateCardVideoData(cardIndex, mediaEntity);
            });
        },
        updateCardVideoData(cardIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.cardVideos) {
                this.element.data.cardVideos = {};
            }
            this.element.data.cardVideos[cardIndex] = media;
        },
        onCardVideoRemove(cardIndex) {
            this.updateCard(cardIndex, 'cardBackgroundVideo', null);
            this.updateCardVideoData(cardIndex, null);
        },
        onCardVideoSelectionChanges(cardIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'VIDEO')) {
                this.updateCard(cardIndex, 'cardBackgroundVideo', media.id);
                this.updateCardVideoData(cardIndex, media);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid video file'
                });
            }
        },
        getCardVideoUploadTag(cardIndex) {
            return `cms-card-video-${this.element.id}-${cardIndex}`;
        },
        getCardVideoMediaItem(cardIndex) {
            return this.element.data?.cardVideos?.[cardIndex] || null;
        },

        onOpenCardMainVideoModal(cardIndex) {
            this.cardMainVideoModals = {...this.cardMainVideoModals, [cardIndex]: true};
        },
        onCloseCardMainVideoModal(cardIndex) {
            this.cardMainVideoModals = {...this.cardMainVideoModals, [cardIndex]: false};
        },
        onCardMainVideoUpload(cardIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updateCard(cardIndex, 'cardMainBackgroundVideo', targetId);
                this.updateCardMainVideoData(cardIndex, mediaEntity);
            });
        },
        updateCardMainVideoData(cardIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.cardMainVideos) {
                this.element.data.cardMainVideos = {};
            }
            this.element.data.cardMainVideos[cardIndex] = media;
        },
        onCardMainVideoRemove(cardIndex) {
            this.updateCard(cardIndex, 'cardMainBackgroundVideo', null);
            this.updateCardMainVideoData(cardIndex, null);
        },
        onCardMainVideoSelectionChanges(cardIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'VIDEO')) {
                this.updateCard(cardIndex, 'cardMainBackgroundVideo', media.id);
                this.updateCardMainVideoData(cardIndex, media);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid video file'
                });
            }
        },
        getCardMainVideoUploadTag(cardIndex) {
            return `cms-card-main-video-${this.element.id}-${cardIndex}`;
        },
        getCardMainVideoMediaItem(cardIndex) {
            return this.element.data?.cardMainVideos?.[cardIndex] || null;
        },

        // Icon Image Upload Methods
        onOpenIconImageModal(cardIndex) {
            this.iconImageModals = {...this.iconImageModals, [cardIndex]: true};
        },
        onCloseIconImageModal(cardIndex) {
            this.iconImageModals = {...this.iconImageModals, [cardIndex]: false};
        },
        onIconImageUpload(cardIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.updateCard(cardIndex, 'iconImage', targetId);
                this.updateIconImageData(cardIndex, mediaEntity);
            });
        },
        updateIconImageData(cardIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.iconImages) {
                this.element.data.iconImages = {};
            }
            this.element.data.iconImages[cardIndex] = media;
        },
        onIconImageRemove(cardIndex) {
            this.updateCard(cardIndex, 'iconImage', null);
            this.updateIconImageData(cardIndex, null);
        },
        onIconImageSelectionChanges(cardIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE' || media.mediaType.name === 'VECTOR_GRAPHIC')) {
                this.updateCard(cardIndex, 'iconImage', media.id);
                this.updateIconImageData(cardIndex, media);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid image or SVG file'
                });
            }
        },
        getIconImageUploadTag(cardIndex) {
            return `cms-icon-image-${this.element.id}-${cardIndex}`;
        },
        getIconImageMediaItem(cardIndex) {
            return this.element.data?.iconImages?.[cardIndex] || null;
        },

        // Feature Icon Upload Methods
        onOpenFeatureIconModal(cardIndex, pointIndex) {
            const key = `${cardIndex}-${pointIndex}`;
            this.featureIconModals = {...this.featureIconModals, [key]: true};
        },
        onCloseFeatureIconModal(cardIndex, pointIndex) {
            const key = `${cardIndex}-${pointIndex}`;
            this.featureIconModals = {...this.featureIconModals, [key]: false};
        },
        onFeatureIconUpload(cardIndex, pointIndex, {targetId}) {
            this.mediaRepository.get(targetId).then((mediaEntity) => {
                this.element.config.cards.value[cardIndex].featurePoints[pointIndex].icon = targetId;
                this.updateFeatureIconData(cardIndex, pointIndex, mediaEntity);
                this.$emit('element-update', this.element);
            });
        },
        updateFeatureIconData(cardIndex, pointIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.featureIcons) {
                this.element.data.featureIcons = {};
            }
            const key = `${cardIndex}-${pointIndex}`;
            this.element.data.featureIcons[key] = media;
        },
        onFeatureIconRemove(cardIndex, pointIndex) {
            this.element.config.cards.value[cardIndex].featurePoints[pointIndex].icon = null;
            this.updateFeatureIconData(cardIndex, pointIndex, null);
            this.$emit('element-update', this.element);
        },
        onFeatureIconSelectionChanges(cardIndex, pointIndex, mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) {
                return;
            }
            const media = mediaEntity[0];
            if (media && (media.mediaType.name === 'IMAGE' || media.mediaType.name === 'VECTOR_GRAPHIC')) {
                this.element.config.cards.value[cardIndex].featurePoints[pointIndex].icon = media.id;
                this.updateFeatureIconData(cardIndex, pointIndex, media);
                this.$emit('element-update', this.element);
            } else {
                this.createNotificationError({
                    message: 'Please select a valid image or SVG file'
                });
            }
        },
        getFeatureIconUploadTag(cardIndex, pointIndex) {
            return `cms-feature-icon-${this.element.id}-${cardIndex}-${pointIndex}`;
        },
        getFeatureIconMediaItem(cardIndex, pointIndex) {
            const key = `${cardIndex}-${pointIndex}`;
            return this.element.data?.featureIcons?.[key] || null;
        },

        updateButtonIconData(cardIndex, buttonIndex, media = null) {
            if (!this.element.data) {
                this.element.data = {};
            }
            if (!this.element.data.buttonIcons) {
                this.element.data.buttonIcons = {};
            }
            const key = this.getButtonMediaKey(cardIndex, buttonIndex);
            this.element.data.buttonIcons[key] = media;
            if (buttonIndex === 0) {
                this.element.data.buttonIcons[cardIndex] = media;
            }
        },

        getButtonIconMediaItem(cardIndex, buttonIndex) {
            const key = this.getButtonMediaKey(cardIndex, buttonIndex);
            return this.element.data?.buttonIcons?.[key] || (buttonIndex === 0 ? this.element.data?.buttonIcons?.[cardIndex] || null : null);
        },

        // Background Media Methods
        onBackgroundType() {
            this.$emit('element-update', this.element);
        },
        onOpenBackgroundModal() {
            this.backgroundMediaModalIsOpen = true;
        },
        onCloseBackgroundModal() {
            this.backgroundMediaModalIsOpen = false;
        },
        onBackgroundImageUpload({targetId}) {
            this.mediaRepository.get(targetId).then((media) => {
                this.element.config.backgroundImage.value = targetId;
                if (!this.element.data) this.element.data = {};
                this.element.data.backgroundMedia = media;
                this.$emit('element-update', this.element);
            });
        },
        onBackgroundImageRemove() {
            this.element.config.backgroundImage.value = null;
            if (this.element.data) this.element.data.backgroundMedia = null;
            this.$emit('element-update', this.element);
        },
        onBackgroundSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) return;
            const media = mediaEntity[0];
            if (media && media.mediaType.name === 'IMAGE') {
                this.element.config.backgroundImage.value = media.id;
                if (!this.element.data) this.element.data = {};
                this.element.data.backgroundMedia = media;
                this.$emit('element-update', this.element);
            }
        },
        onOpenBackgroundVideoModal() {
            this.backgroundVideoModalIsOpen = true;
        },
        onCloseBackgroundVideoModal() {
            this.backgroundVideoModalIsOpen = false;
        },
        onBackgroundVideoUpload({targetId}) {
            this.mediaRepository.get(targetId).then((media) => {
                this.element.config.backgroundVideo.value = targetId;
                if (!this.element.data) this.element.data = {};
                this.element.data.backgroundVideoMedia = media;
                this.$emit('element-update', this.element);
            });
        },
        onBackgroundVideoRemove() {
            this.element.config.backgroundVideo.value = null;
            if (this.element.data) this.element.data.backgroundVideoMedia = null;
            this.$emit('element-update', this.element);
        },
        onBackgroundVideoSelectionChanges(mediaEntity) {
            if (!mediaEntity || mediaEntity.length === 0) return;
            const media = mediaEntity[0];
            if (media && media.mediaType.name === 'VIDEO') {
                this.element.config.backgroundVideo.value = media.id;
                if (!this.element.data) this.element.data = {};
                this.element.data.backgroundVideoMedia = media;
                this.$emit('element-update', this.element);
            }
        },
        onBackgroundVideoUploadUrl(value) {
            this.element.config.backgroundVideoUploadUrl.value = value;
            this.$emit('element-update', this.element);
        }
    }
};
