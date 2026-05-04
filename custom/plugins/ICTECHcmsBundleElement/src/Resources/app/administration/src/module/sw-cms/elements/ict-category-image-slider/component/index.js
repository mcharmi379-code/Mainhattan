import template from './sw-cms-el-ict-category-image-slider.html.twig';
import './sw-cms-el-ict-category-image-slider.scss';

const ELEMENT_NAME = 'ict-category-image-slider';
const VISIBLE_COUNT = 3;
const { Mixin, Filter } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    inject: ['repositoryFactory'],

    data() {
        return {
            sliderPos: 0,
            categoryCache: {},
        };
    },

    computed: {
        assetFilter() {
            return Filter.getByName('asset');
        },

        placeholderImage() {
            return this.assetFilter('/administration/static/img/cms/preview_mountain_small.jpg');
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        cards() {
            const cards = this.element.config.cards.value;

            if (Array.isArray(cards) && cards.length > 0) {
                return cards;
            }

            return [
                { title: 'Category 1', categoryId: null },
                { title: 'Category 2', categoryId: null },
                { title: 'Category 3', categoryId: null },
            ];
        },

        visibleCards() {
            return this.cards.slice(this.sliderPos, this.sliderPos + VISIBLE_COUNT);
        },

        showPrev() {
            return this.sliderPos > 0;
        },

        showNext() {
            return this.sliderPos + VISIBLE_COUNT < this.cards.length;
        },

        showArrows() {
            return this.cards.length > VISIBLE_COUNT;
        },
    },

    watch: {
        'element.config.cards.value': {
            handler() {
                this.sliderPos = 0;
                this.loadCategoryImages();
            },
            deep: true,
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
            this.loadCategoryImages();
        },

        prevSlide() {
            if (this.showPrev) {
                this.sliderPos -= 1;
            }
        },

        nextSlide() {
            if (this.showNext) {
                this.sliderPos += 1;
            }
        },

        getCardImage(card) {
            if (!card.categoryId) {
                return this.placeholderImage;
            }

            const category = this.categoryCache[card.categoryId];
            return category?.media?.url || this.placeholderImage;
        },

        getCardName(card) {
            if (!card.categoryId) {
                return card.title || 'Category';
            }

            const category = this.categoryCache[card.categoryId];
            return card.title || category?.translated?.name || category?.name || 'Category';
        },

        async loadCategoryImages() {
            const { Criteria } = Shopware.Data;

            const ids = this.cards
                .map((c) => c.categoryId)
                .filter((id) => typeof id === 'string' && id.length > 0 && !this.categoryCache[id]);

            if (ids.length === 0) {
                return;
            }

            const criteria = new Criteria(1, ids.length);
            criteria.setIds(ids);
            criteria.addAssociation('media');

            try {
                const result = await this.categoryRepository.search(criteria, Shopware.Context.api);
                result.forEach((category) => {
                    this.categoryCache[category.id] = category;
                });
            } catch {
                // silently ignore load errors in preview
            }
        },
    },
};
