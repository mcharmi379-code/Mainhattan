import template from './sw-cms-el-mainhattan-three-card.html.twig';
import './sw-cms-el-mainhattan-three-card.scss';
import { defaultCards, defaultMediaEntity } from '../shared/default-cards';

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
            const defaultCard = defaultCards.find((card) => card.titleKey === `card${suffix}Title`) ?? { imageUrl: '' };
            const titleConfig = this.element?.config?.[`card${suffix}Title`] ?? { value: '' };
            const descriptionConfig = this.element?.config?.[`card${suffix}Description`] ?? { value: '' };
            const buttonTextConfig = this.element?.config?.[`card${suffix}ButtonText`] ?? { value: '' };

            return {
                title: titleConfig.value || '',
                description: descriptionConfig.value || '',
                buttonText: buttonTextConfig.value || '',
                media: this.element?.data?.[mediaKey] ?? defaultMediaEntity(defaultCard.imageUrl),
            };
        },
    },
};
