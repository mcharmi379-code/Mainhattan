import template from './sw-cms-el-ict-overview-cards-6.html.twig';
import './sw-cms-el-ict-overview-cards-6.scss';

const { Mixin } = Shopware;

export default {
    template,
    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    inject: ['repositoryFactory'],

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        }
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.loadMedia();
    },

    methods: {
        createButton(overrides = {}) {
            return Object.assign({
                show: true, buttonText: 'Learn More', buttonLinkType: 'internal', buttonUrl: '',
                buttonTarget: '_self', buttonTextColor: '#FFFFFF',
                buttonHoverTextColor: '#FFFFFF', buttonBorderColor: 'transparent',
                buttonHoverBorderColor: 'transparent', buttonBorderStyle: 'solid', buttonBorderWidth: 0,
                buttonBorderRadius: 6
            }, overrides);
        },

        createDetailRow(overrides = {}) {
            return Object.assign({ title: 'Title', value: 'Value', titleColor: '#1a1a2e', valueColor: '#52667a' }, overrides);
        },

        createTag(overrides = {}) {
            return Object.assign({
                text: 'TAG', textColor: '#005AE5', backgroundColor: '#e8f0fe',
                outlineColor: '#a8c4f8', outlineThickness: 1, fontSize: '10', lineHeight: '1'
            }, overrides);
        },

        createButtonFromLegacyCard(card) {
            return this.createButton({
                show: card?.show, buttonText: card?.buttonText, buttonLinkType: card?.buttonLinkType,
                buttonUrl: card?.buttonUrl, buttonTarget: card?.buttonTarget,
                buttonTextColor: card?.buttonTextColor,
                buttonHoverTextColor: card?.buttonHoverTextColor, buttonBorderColor: card?.buttonBorderColor,
                buttonHoverBorderColor: card?.buttonHoverBorderColor, buttonBorderStyle: card?.buttonBorderStyle,
                buttonBorderWidth: card?.buttonBorderWidth, buttonBorderRadius: card?.buttonBorderRadius
            });
        },

        createdComponent() {
            this.initElementConfig('ict-overview-cards-6');
            this.ensureCardsInitialized();
        },

        cloneCardTemplate() {
            const template = this.cmsElements?.['ict-overview-cards-6']?.cardTemplates;
            if (template && typeof template === 'object') {
                return JSON.parse(JSON.stringify(template));
            }
            return { detailRows: [], buttons: [this.createButton()] };
        },

        createCard(overrides = {}) {
            return Object.assign(this.cloneCardTemplate(), overrides);
        },

        getInitialCards() {
            return [
                this.createCard({ headline: 'Card Title 1', bodyText: 'Description for card one.' }),
                this.createCard({ headline: 'Card Title 2', bodyText: 'Description for card two.' }),
                this.createCard({ headline: 'Card Title 3', bodyText: 'Description for card three.' })
            ];
        },

        ensureCardsInitialized() {
            if (!this.element?.config?.cards) return;
            if (!Array.isArray(this.element.config.cards.value)) {
                this.element.config.cards.value = [];
            }
            if (this.element.config.cards.value.length === 0) {
                this.element.config.cards.value = this.getInitialCards();
            }
        },

        isNumericSizeValue(v) {
            if (v === null || v === undefined) return false;
            return /^\d+(\.\d+)?$/.test(String(v).trim());
        },

        getCssSizeValue(v, fallback) {
            if (v === null || v === undefined) return fallback;
            const s = String(v).trim();
            if (!s) return fallback;
            return this.isNumericSizeValue(s) ? `${s}px` : s;
        },

        getLineHeightValue(v, fallback, allowUnitless = false) {
            const s = v === null || v === undefined ? '' : String(v).trim();
            if (!s) return fallback;
            if (allowUnitless && this.isNumericSizeValue(s)) return s;
            return this.isNumericSizeValue(s) ? `${s}px` : s;
        },

        getHeadlineLineHeight(sizeValue) {
            const s = sizeValue === null || sizeValue === undefined ? '' : String(sizeValue).trim();
            if (!s) return '28px';
            if (s === '24' || s === '24px') return '28px';
            if (s === '42' || s === '42px') return '55px';
            return this.isNumericSizeValue(s) ? `${s}px` : this.getCssSizeValue(s, '28px');
        },

        getSubheadlineLineHeight(sizeValue) {
            const s = sizeValue === null || sizeValue === undefined ? '' : String(sizeValue).trim();
            if (!s) return '25px';
            if (s === '20' || s === '20px') return '25px';
            return this.isNumericSizeValue(s) ? `${s}px` : this.getCssSizeValue(s, '25px');
        },

        getTextAlignment(alignment, fallback = 'left') {
            return ['left', 'center', 'right'].includes(alignment) ? alignment : fallback;
        },

        getCardButtons(card) {
            if (Array.isArray(card?.buttons) && card.buttons.length) {
                return card.buttons.filter(b => b?.show !== false);
            }
            if (card?.showButton) {
                return [this.createButtonFromLegacyCard(card)].filter(b => b?.show !== false);
            }
            return [];
        },
        getCardDetailRows(card) {
            if (!card?.showDetailRows || !Array.isArray(card?.detailRows)) return [];
            return card.detailRows.filter(r => r && typeof r === 'object' && (String(r.title || '').trim() || String(r.value || '').trim()));
        },

        getMediaDisplayMode(card) {
            return ['fill', 'fit', 'crop', 'tile'].includes(card?.mediaDisplayMode) ? card.mediaDisplayMode : 'fill';
        },

        getMediaHeight(card) {
            return this.getCssSizeValue(card?.mediaHeight, '200px');
        },

        getMediaImageFit(card) {
            const mode = this.getMediaDisplayMode(card);
            if (mode === 'crop') return 'cover';
            if (mode === 'fill') return 'fill';
            return 'contain';
        },


        isSvgUrl(url) {
            if (!url || typeof url !== 'string') return false;
            return url.toLowerCase().includes('.svg');
        },

        getYoutubeEmbedUrl(url) {
            if (!url || typeof url !== 'string') return '';
            const id = url.replace('https://www.youtube.com/watch?v=', '').replace('https://youtu.be/', '').split('&')[0];
            if (!id) return '';
            return `https://www.youtube.com/embed/${id}?autoplay=1&mute=1&loop=1&playlist=${id}&controls=0&showinfo=0&rel=0`;
        },

        async loadMedia() {
            const cards = this.element.config?.cards?.value;
            if (!Array.isArray(cards)) return;

            if (!this.element.data) this.element.data = {};

            for (let i = 0; i < cards.length; i++) {
                const card = cards[i];

                if (card.iconImage && !this.element.data.iconImages?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.iconImage);
                        if (!this.element.data.iconImages) this.element.data.iconImages = {};
                        this.element.data.iconImages[i] = media;
                    } catch (_) { /* noop */ }
                }

                if (card.cardBackgroundImage && !this.element.data.cardImages?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.cardBackgroundImage);
                        if (!this.element.data.cardImages) this.element.data.cardImages = {};
                        this.element.data.cardImages[i] = media;
                    } catch (_) { /* noop */ }
                }

                if (card.cardMainBackgroundImage && !this.element.data.cardMainImages?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(card.cardMainBackgroundImage);
                        if (!this.element.data.cardMainImages) this.element.data.cardMainImages = {};
                        this.element.data.cardMainImages[i] = media;
                    } catch (_) { /* noop */ }
                }
            }

            if (this.element.config.backgroundImage?.value && !this.element.data.backgroundMedia) {
                try {
                    const media = await this.mediaRepository.get(this.element.config.backgroundImage.value);
                    this.element.data.backgroundMedia = media;
                } catch (_) { /* noop */ }
            }
        }
    }
};
