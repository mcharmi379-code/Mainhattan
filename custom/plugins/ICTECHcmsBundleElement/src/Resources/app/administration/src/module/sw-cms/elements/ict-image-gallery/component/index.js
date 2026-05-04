import template from './sw-cms-el-ict-image-gallery.html.twig';
import './sw-cms-el-ict-image-gallery.scss';

const ELEMENT_NAME = 'ict-image-gallery';
const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    props: {
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },

        galleryTitle() {
            return this.element.config?.galleryTitle?.value || '';
        },

        columns() {
            const columns = Number.parseInt(this.element.config?.columns?.value, 10);

            if (Number.isNaN(columns) || columns < 1) {
                return 4;
            }

            return Math.min(columns, 6);
        },

        previewImages() {
            return [
                '/administration/static/img/cms/preview_mountain_small.jpg',
                '/administration/static/img/cms/preview_glasses_small.jpg',
                '/administration/static/img/cms/preview_plant_small.jpg',
                '/administration/static/img/cms/preview_camera_small.jpg',
            ];
        },

        galleryItems() {
            const configItems = this.element.config?.galleryItems?.value || [];
            const dataItems = this.element.data?.galleryItems || [];

            const items = dataItems.length > 0 ? dataItems : configItems;

            if (!Array.isArray(items)) {
                return [];
            }

            const cleaned = items.filter((item) => {
                return item && typeof item === 'object' && !Array.isArray(item);
            });

            if (cleaned.length !== items.length) {
                if (dataItems.length > 0) {
                    this.element.data.galleryItems = cleaned;
                } else {
                    this.element.config.galleryItems.value = cleaned;
                }
            }

            if (cleaned.length === 0) {
                return this.previewImages.map((url) => ({
                    mediaUrl: url,
                    title: '',
                    isPreview: true,
                }));
            }

            return cleaned.map((item) => ({
                ...item,
                isPreview: false,
            }));
        },

        isPlaceholder() {
            return false;
        },
    },

    watch: {
        'element.config.galleryItems.value': {
            handler(newValue) {
                if (Array.isArray(newValue)) {
                    const cleaned = newValue.filter((item) => {
                        return item && typeof item === 'object' && !Array.isArray(item);
                    });
                    if (cleaned.length !== newValue.length) {
                        this.element.config.galleryItems.value = cleaned;
                    }
                }
            },
            deep: true,
            immediate: true,
        },
        'element.data.galleryItems': {
            handler(newValue) {
                if (Array.isArray(newValue)) {
                    const cleaned = newValue.filter((item) => {
                        return item && typeof item === 'object' && !Array.isArray(item);
                    });
                    if (cleaned.length !== newValue.length) {
                        this.element.data.galleryItems = cleaned;
                    }
                }
            },
            deep: true,
            immediate: true,
        },
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.syncSlotState();
    },

    updated() {
        this.syncSlotState();
    },

    beforeDestroy() {
        this.clearSlotState();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);

            if (!this.element.data) {
                this.element.data = {};
            }

            this.cleanData();
        },

        cleanData() {
            const configItems = this.element.config?.galleryItems?.value;
            const dataItems = this.element.data?.galleryItems;

            if (Array.isArray(configItems)) {
                this.element.config.galleryItems.value = configItems.filter((item) => {
                    return item && typeof item === 'object' && !Array.isArray(item);
                });
            }

            if (Array.isArray(dataItems)) {
                this.element.data.galleryItems = dataItems.filter((item) => {
                    return item && typeof item === 'object' && !Array.isArray(item);
                });
            }
        },

        onPlaceholderActivate() {
            if (this.disabled || !this.isPlaceholder) {
                return;
            }

            let parent = this.$parent;

            while (parent) {
                if (typeof parent.onElementButtonClick === 'function') {
                    parent.onElementButtonClick();
                    return;
                }

                parent = parent.$parent;
            }
        },

        syncSlotState() {
            const slotElement = this.$el?.closest('.sw-cms-slot');

            if (!slotElement) {
                return;
            }

            slotElement.classList.add('sw-cms-slot--ict-image-gallery');
            slotElement.classList.toggle('sw-cms-slot--ict-image-gallery-placeholder', this.isPlaceholder);
        },

        clearSlotState() {
            const slotElement = this.$el?.closest('.sw-cms-slot');

            if (!slotElement) {
                return;
            }

            slotElement.classList.remove('sw-cms-slot--ict-image-gallery');
            slotElement.classList.remove('sw-cms-slot--ict-image-gallery-placeholder');
        },
    },
};
