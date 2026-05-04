import template from './sw-cms-el-ict-manufacturer-slider.html.twig';
import './sw-cms-el-ict-manufacturer-slider.scss';

const ELEMENT_NAME = 'ict-manufacturer-slider';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            manufacturers: [],
        };
    },

    computed: {
        manufacturerRepository() {
            return this.repositoryFactory.create('product_manufacturer');
        },

        manufacturerIds() {
            const manufacturerIds = this.element?.config?.manufacturerIds?.value;

            return Array.isArray(manufacturerIds) ? manufacturerIds : [];
        },

        showManufacturerName() {
            return this.element?.config?.showManufacturerName?.value ?? true;
        },

        desktopItems() {
            const items = Number.parseInt(`${this.element?.config?.desktopItems?.value ?? 4}`, 10);

            if (Number.isNaN(items) || items < 1) {
                return 4;
            }

            return items;
        },

        previewManufacturers() {
            if (this.manufacturers.length > 0) {
                return this.manufacturers;
            }

            return [
                { id: 'preview-1', name: 'Brand One' },
                { id: 'preview-2', name: 'Brand Two' },
                { id: 'preview-3', name: 'Brand Three' },
                { id: 'preview-4', name: 'Brand Four' },
            ];
        },

        visibleManufacturers() {
            return this.previewManufacturers.slice(0, this.desktopItems);
        },

    },

    watch: {
        manufacturerIds: {
            handler() {
                this.loadManufacturers();
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
            this.loadManufacturers();
        },

        async loadManufacturers() {
            if (this.manufacturerIds.length === 0) {
                this.manufacturers = [];
                return;
            }

            const criteria = new Shopware.Data.Criteria(1, this.manufacturerIds.length);
            criteria.setIds(this.manufacturerIds);
            criteria.addAssociation('media');

            try {
                const result = await this.manufacturerRepository.search(criteria, Shopware.Context.api);

                this.manufacturers = this.manufacturerIds
                    .map((manufacturerId) => result.get(manufacturerId))
                    .filter((manufacturer) => manufacturer);
            } catch {
                this.manufacturers = [];
            }
        },

        getManufacturerName(manufacturer) {
            return manufacturer?.translated?.name || manufacturer?.name || '';
        },

        getManufacturerMediaUrl(manufacturer) {
            return manufacturer?.media?.url || '';
        },
    },
};
