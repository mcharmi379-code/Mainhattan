import template from './sw-cms-el-preview-mainhattan-three-card.html.twig';
import './sw-cms-el-preview-mainhattan-three-card.scss';
import { defaultCards } from '../shared/default-cards';

export default {
    template,

    data() {
        return {
            cards: defaultCards,
        };
    },
};
