import template from './sw-cms-el-config-ict-breadcrumb-navigation.html.twig';
import './sw-cms-el-config-ict-breadcrumb-navigation.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        alignmentOptions() {
            return [
                { value: 'left',   label: this.$tc('ict-cms-bundle-element.cms.elements.ictBreadcrumbNavigation.config.alignLeft') },
                { value: 'center', label: this.$tc('ict-cms-bundle-element.cms.elements.ictBreadcrumbNavigation.config.alignCenter') },
                { value: 'right',  label: this.$tc('ict-cms-bundle-element.cms.elements.ictBreadcrumbNavigation.config.alignRight') },
            ];
        },
    },

    created() {
        this.initElementConfig('ict-breadcrumb-navigation');
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },
    },
};
