import template from './sw-cms-el-config-ict-subcategory-navigation.html.twig';
import './sw-cms-el-config-ict-subcategory-navigation.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        isManual() {
            return this.element?.config?.entryPoint?.value === 'manual';
        },

        isCustom() {
            return this.element?.config?.entryPoint?.value === 'custom';
        },

        entryPointOptions() {
            return [
                { value: 'current', label: this.$tc('ict-cms-bundle-element.cms.elements.ictSubcategoryNavigation.config.entryPointCurrent') },
                { value: 'custom', label: this.$tc('ict-cms-bundle-element.cms.elements.ictSubcategoryNavigation.config.entryPointCustom') },
                { value: 'manual', label: this.$tc('ict-cms-bundle-element.cms.elements.ictSubcategoryNavigation.config.entryPointManual') },
            ];
        },
    },

    created() {
        this.initElementConfig('ict-subcategory-navigation');
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },

        onEntryPointChange(value) {
            this.element.config.entryPoint.value = value;

            if (value === 'manual') {
                this.element.config.entryCategory.value = null;
                this.element.config.showProductCount.value = false;
            }

            this.onUpdate();
        },

        onCategoryLevelsChange(value) {
            const parsed = Number.parseInt(String(value), 10);
            this.element.config.categoryLevels.value = Number.isFinite(parsed) ? parsed : 1;
            this.onUpdate();
        },

        onEntryCategorySelect(value) {
            // @update:value emits the selected entity id (string) or null
            this.element.config.entryCategory.value = value || null;
            this.onUpdate();
        },

        onManualCategoriesChange(ids) {
            this.element.config.manualCategories.value = Array.isArray(ids) ? ids : [];
            this.onUpdate();
        },
    },
};
