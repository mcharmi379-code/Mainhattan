import template from './sw-cms-el-config-ict-manufacturer-slider.html.twig';
import './sw-cms-el-config-ict-manufacturer-slider.scss';

const ELEMENT_NAME = 'ict-manufacturer-slider';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        manufacturerRepository() {
            return this.repositoryFactory.create('product_manufacturer');
        },

        manufacturerSelectCriteria() {
            const criteria = new Shopware.Data.Criteria(1, 25);
            criteria.addSorting(Shopware.Data.Criteria.sort('name', 'ASC', false));

            return criteria;
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
            this.element.config.manufacturerIds.value = this.normalizeIdArray(
                this.element.config.manufacturerIds.value,
            );

            [
                ['maxWidth', 180],
                ['maxHeight', 100],
                ['desktopItems', 4],
                ['tabletItems', 2],
                ['mobileItems', 1],
            ].forEach(([key, defaultValue]) => {
                const value = Number.parseInt(`${this.element.config[key].value}`, 10);

                this.element.config[key].value = Number.isNaN(value) || value <= 0
                    ? defaultValue
                    : value;
            });
        },

        normalizeIdArray(ids) {
            if (!Array.isArray(ids)) {
                return [];
            }

            return [...new Set(ids.filter((id) => typeof id === 'string' && id.length > 0))];
        },

        onSelectionChange(ids) {
            this.element.config.manufacturerIds.value = this.normalizeIdArray(ids);
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
