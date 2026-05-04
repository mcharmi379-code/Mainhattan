import template from './sw-cms-preview-mainhattan-three-card.html.twig';
import './sw-cms-preview-mainhattan-three-card.scss';
import { defaultCards } from '../../../elements/mainhattan-three-card/shared/default-cards';

export default {
    template,

    data() {
        return {
            cards: defaultCards,
        };
    },
};
