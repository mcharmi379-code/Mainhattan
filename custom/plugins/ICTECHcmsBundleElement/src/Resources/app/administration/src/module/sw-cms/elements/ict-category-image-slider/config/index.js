import template from './sw-cms-el-config-ict-category-image-slider.html.twig';
import './sw-cms-el-config-ict-category-image-slider.scss';

const ELEMENT_NAME = 'ict-category-image-slider';
const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    data() {
        return {
            categoryCache: {},
            invalidCards: {},
            isLoadingCard: {},
        };
    },

    computed: {
        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        categoryCriteria() {
            const { Criteria } = Shopware.Data;
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('active', true));
            criteria.addFilter(Criteria.equals('type', 'page'));
            return criteria;
        },

        cards() {
            if (!Array.isArray(this.element.config.cards.value)) {
                this.element.config.cards.value = [];
            }

            return this.element.config.cards.value;
        },

        hasValidationErrors() {
            return this.element.config.validationToken.value !== 'ok';
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
            this.loadExistingCards();
            this.updateValidationToken();
        },

        emitElementUpdate() {
            this.$emit('element-update', this.element);
        },

        addCard() {
            this.cards.push({
                title: '',
                categoryId: null,
            });

            this.updateValidationToken();
            this.emitElementUpdate();
        },

        removeCard(index) {
            this.cards.splice(index, 1);
            this.invalidCards = {};
            this.isLoadingCard = {};

            this.loadExistingCards();
        },

        async onCategoryChange(categoryId, index) {
            this.cards[index].categoryId = categoryId || null;
            await this.validateCard(index);

            this.updateValidationToken();
            this.emitElementUpdate();
        },

        categoryName(index) {
            const categoryId = this.cards[index]?.categoryId;
            if (!categoryId || typeof categoryId !== 'string') {
                return '';
            }

            const category = this.categoryCache[categoryId];
            if (!category) {
                return '';
            }

            return category.translated?.name || category.name || '';
        },

        categoryMediaUrl(index) {
            const categoryId = this.cards[index]?.categoryId;
            if (!categoryId || typeof categoryId !== 'string') {
                return '';
            }

            const category = this.categoryCache[categoryId];
            const media = category?.media;

            return media?.url || '';
        },

        async loadExistingCards() {
            if (this.cards.length === 0) {
                return;
            }

            await Promise.all(this.cards.map((card, index) => {
                if (!card || typeof card.categoryId !== 'string' || card.categoryId.length === 0) {
                    return Promise.resolve();
                }

                return this.validateCard(index);
            }));

            this.updateValidationToken();
            this.emitElementUpdate();
        },

        async validateCard(index) {
            const categoryId = this.cards[index]?.categoryId;

            if (!categoryId || typeof categoryId !== 'string') {
                this.invalidCards[index] = true;
                return;
            }

            try {
                this.isLoadingCard[index] = true;

                const criteria = new Criteria();
                criteria.addAssociation('media');

                const category = await this.categoryRepository.get(categoryId, Shopware.Context.api, criteria);

                this.categoryCache[categoryId] = category;
                this.invalidCards[index] = !(category?.mediaId && category?.media);
            } catch {
                this.invalidCards[index] = true;
            } finally {
                this.isLoadingCard[index] = false;
            }
        },

        updateValidationToken() {
            const hasAnyCard = this.cards.length > 0;
            const hasAllCategoryIds = this.cards.every((card) => card && typeof card.categoryId === 'string' && card.categoryId.length > 0);
            const hasNoInvalidCards = Object.values(this.invalidCards).every((value) => value === false);

            this.element.config.validationToken.value = (hasAnyCard && hasAllCategoryIds && hasNoInvalidCards) ? 'ok' : '';
        },
    },
};
