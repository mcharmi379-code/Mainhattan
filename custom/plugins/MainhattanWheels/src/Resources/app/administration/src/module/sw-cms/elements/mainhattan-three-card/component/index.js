import template from './sw-cms-el-mainhattan-three-card.html.twig';
import './sw-cms-el-mainhattan-three-card.scss';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        cards() {
            return [
                this.createCard('One'),
                this.createCard('Two'),
                this.createCard('Three'),
            ];
        },
    },

    created() {
        this.initElementConfig('mainhattan-three-card');
        this.initElementData('mainhattan-three-card');
    },

    methods: {
        createCard(suffix) {
            const mediaKey = `card${suffix}Image`;

            return {
                title: this.element.config[`card${suffix}Title`].value,
                description: this.element.config[`card${suffix}Description`].value,
                buttonText: this.element.config[`card${suffix}ButtonText`].value,
                media: this.element.data?.[mediaKey] ?? null,
            };
        },
    },
};
