import template from './sw-cms-el-config-ict-hero-section-simple.html.twig';
import './config.scss';

const { Mixin } = Shopware;

export default {
    template,
    emits: ['element-update'],
    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.initElementConfig('ict-hero-section');
        this.normalizeFeatureItems();
    },

    methods: {
        onInput() {
            this.normalizeFeatureItems();
            this.$emit('element-update', this.element);
        },

        normalizeFeatureItems() {
            const items = this.element.config.featureItems.value;
            if (!Array.isArray(items) || items.length === 0) {
                this.element.config.featureItems.value = [
                    { title: 'Lorem feature one', description: 'Lorem short text.' },
                    { title: 'Lorem feature two', description: 'Lorem short text.' },
                    { title: 'Lorem feature three', description: 'Lorem short text.' },
                    { title: 'Lorem feature four', description: 'Lorem short text.' },
                ];
                return;
            }

            this.element.config.featureItems.value = items.map((item) => ({
                title: `${item?.title || ''}`.trim() || 'Lorem feature',
                description: `${item?.description || ''}`.trim() || 'Lorem short text.',
            }));
        },

        addFeatureItem() {
            this.element.config.featureItems.value.push({
                title: 'Lorem feature',
                description: 'Lorem short text.',
            });
            this.onInput();
        },

        removeFeatureItem(index) {
            this.element.config.featureItems.value.splice(index, 1);
            this.onInput();
        },
    },
};

