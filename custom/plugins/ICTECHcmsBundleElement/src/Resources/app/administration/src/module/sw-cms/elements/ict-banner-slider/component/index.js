import template from './sw-cms-el-ict-banner-slider.html.twig';
import './sw-cms-el-ict-banner-slider.scss';

const { Mixin, Filter } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            sliderPos: 0,
            imgPath: '/administration/static/img/cms/preview_mountain_large.jpg',
        };
    },

    computed: {
        assetFilter() {
            return Filter.getByName('asset');
        },

        sliderItems() {
            const items = this.element?.config?.sliderItems?.value;

            if (!Array.isArray(items) || items.length === 0) {
                return [
                    {
                        mediaUrl: this.assetFilter(this.imgPath),
                        mainTitle: 'Banner Title',
                        mainTitleColor: '#ffffff',
                        subTitle: 'Sub Title',
                        subTitleColor: '#ffffff',
                        description: '',
                        descriptionColor: '#ffffff',
                        callButtonText: '',
                        callButtonNumber: '',
                        emailButtonText: '',
                        emailButtonAddress: '',
                        buttonColor: '#f15a24',
                        buttonTextColor: '#ffffff',
                        isPreview: true,
                    },
                ];
            }

            return items;
        },

        currentSlide() {
            return this.sliderItems[this.sliderPos] ?? this.sliderItems[0];
        },

        displayMode() {
            return this.element?.config?.displayMode?.value ?? 'contain';
        },

        navigationArrows() {
            return this.element?.config?.navigationArrows?.value ?? 'outside';
        },

        navigationDots() {
            return this.element?.config?.navigationDots?.value ?? 'none';
        },

        showArrows() {
            return this.navigationArrows !== 'none' && this.sliderItems.length > 1;
        },

        showDots() {
            return this.navigationDots !== 'none' && this.sliderItems.length > 1;
        },

        arrowsOutside() {
            return this.navigationArrows === 'outside';
        },

        mainTitleStyle() {
            const color = this.currentSlide.mainTitleColor || '#ffffff';
            return { color };
        },

        subTitleStyle() {
            const color = this.currentSlide.subTitleColor || '#ffffff';
            return { color };
        },

        descriptionStyle() {
            const color = this.currentSlide.descriptionColor || '#ffffff';
            return { color };
        },

        buttonStyle() {
            return {
                'background-color': this.currentSlide.buttonColor || '#f15a24',
                'border-color': this.currentSlide.buttonColor || '#f15a24',
                color: this.currentSlide.buttonTextColor || '#ffffff',
            };
        },
    },

    watch: {
        sliderItems(items) {
            if (this.sliderPos >= items.length) {
                this.sliderPos = 0;
            }
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('ict-banner-slider');
        },

        prevSlide() {
            this.sliderPos = this.sliderPos <= 0
                ? this.sliderItems.length - 1
                : this.sliderPos - 1;
        },

        nextSlide() {
            this.sliderPos = this.sliderPos >= this.sliderItems.length - 1
                ? 0
                : this.sliderPos + 1;
        },

        goToSlide(index) {
            this.sliderPos = index;
        },

        isActiveSlide(index) {
            return this.sliderPos === index;
        },
    },
};
