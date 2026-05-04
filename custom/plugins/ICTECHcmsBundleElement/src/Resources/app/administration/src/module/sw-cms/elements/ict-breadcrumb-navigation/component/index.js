import template from './sw-cms-el-ict-breadcrumb-navigation.html.twig';
import './sw-cms-el-ict-breadcrumb-navigation.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        previewCrumbs() {
            return ['Home', 'Category', 'Subcategory', 'Current Page'];
        },

        alignmentStyle() {
            const alignment = this.element?.config?.alignment?.value || 'left';

            return {
                justifyContent: alignment === 'center' ? 'center' : alignment === 'right' ? 'flex-end' : 'flex-start',
            };
        },

        colorStyle() {
            const color = this.element?.config?.textColor?.value || 'var(--bs-body-color)';

            return { '--ict-bc-color': color };
        },
    },

    created() {
        this.initElementConfig('ict-breadcrumb-navigation');
    },
};
