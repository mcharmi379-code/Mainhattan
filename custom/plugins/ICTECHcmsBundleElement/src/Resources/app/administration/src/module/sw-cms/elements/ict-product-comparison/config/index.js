import template from './sw-cms-el-config-ict-product-comparison.html.twig';
import './sw-cms-el-config-ict-product-comparison.scss';

const ELEMENT_NAME = 'ict-product-comparison';
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
            activeTab: 'products',
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        productStreamRepository() {
            return this.repositoryFactory.create('product_stream');
        },

        productSelectCriteria() {
            const criteria = new Shopware.Data.Criteria(1, 25);
            criteria.addSorting(Shopware.Data.Criteria.sort('name', 'ASC', false));
            criteria.addAssociation('manufacturer');
            criteria.addAssociation('cover.media');

            return criteria;
        },

        productStreamSelectCriteria() {
            const criteria = new Shopware.Data.Criteria(1, 25);
            criteria.addSorting(Shopware.Data.Criteria.sort('name', 'ASC', false));

            return criteria;
        },

        assignmentTypeOptions() {
            return [
                { value: 'manual', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.assignment.manual') },
                { value: 'stream', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.assignment.stream') },
            ];
        },

        productStreamSortingOptions() {
            return [
                { value: 'name:ASC', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.streamSorting.nameAsc') },
                { value: 'name:DESC', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.streamSorting.nameDesc') },
                { value: 'price:ASC', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.streamSorting.priceAsc') },
                { value: 'price:DESC', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.streamSorting.priceDesc') },
            ];
        },

        defaultProductValueOptions() {
            return [
                { value: 'name', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.defaultValues.options.name') },
                { value: 'price', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.defaultValues.options.price') },
                { value: 'description', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.defaultValues.options.description') },
                { value: 'rating', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.defaultValues.options.rating') },
                { value: 'manufacturer', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.defaultValues.options.manufacturer') },
            ];
        },

        propertyGroupOptions() {
            return [
                { value: 'target_group', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.propertyGroups.options.targetGroup') },
                { value: 'color', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.propertyGroups.options.color') },
                { value: 'ingredients', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.propertyGroups.options.ingredients') },
                { value: 'size', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.propertyGroups.options.size') },
                { value: 'material', label: this.$tc('ict-cms-bundle-element.cms.elements.ictProductComparison.config.propertyGroups.options.material') },
            ];
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
            this.normalizeConfig();
        },

        normalizeConfig() {
            this.element.config.productAssignmentType.value = this.normalizeAssignmentType(
                this.element.config.productAssignmentType.value,
            );
            this.element.config.productIds.value = this.normalizeStringArray(this.element.config.productIds.value);
            this.element.config.defaultProductValues.value = this.normalizeStringArray(this.element.config.defaultProductValues.value);
            this.element.config.propertyGroups.value = this.normalizeStringArray(this.element.config.propertyGroups.value);
            this.element.config.highlightFirstProduct.value = !!this.element.config.highlightFirstProduct.value;

            const streamLimit = Number.parseInt(`${this.element.config.productStreamLimit.value}`, 10);
            this.element.config.productStreamLimit.value = Number.isNaN(streamLimit) || streamLimit <= 0 ? 10 : streamLimit;

            this.element.config.productStreamSorting.value = this.normalizeStreamSorting(
                this.element.config.productStreamSorting.value,
            );
        },

        normalizeAssignmentType(value) {
            return value === 'stream' ? 'stream' : 'manual';
        },

        normalizeStreamSorting(value) {
            const allowed = new Set(['name:ASC', 'name:DESC', 'price:ASC', 'price:DESC']);
            return allowed.has(value) ? value : 'name:ASC';
        },

        normalizeStringArray(values) {
            if (!Array.isArray(values)) {
                return [];
            }

            return [...new Set(values.filter((value) => typeof value === 'string' && value.length > 0))];
        },

        onSelectionChange(ids) {
            this.element.config.productIds.value = this.normalizeStringArray(ids);
            this.emitElementUpdate();
        },

        onAssignmentTypeChange(value) {
            this.element.config.productAssignmentType.value = this.normalizeAssignmentType(value);
            this.emitElementUpdate();
        },

        onInput() {
            this.normalizeConfig();
            this.emitElementUpdate();
        },

        emitElementUpdate() {
            this.$emit('element-update', this.element);
        },
    },
};
