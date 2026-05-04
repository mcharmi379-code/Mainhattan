import template from './sw-cms-el-config-ict-alert-hint.html.twig';
import './sw-cms-el-config-ict-alert-hint.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        alertTypeOptions() {
            const prefix = 'ict-cms-bundle-element.cms.elements.ictAlertHint.config.alertTypes';
            return [
                { value: 'success',   label: this.$tc(`${prefix}.success`) },
                { value: 'danger',    label: this.$tc(`${prefix}.danger`) },
                { value: 'warning',   label: this.$tc(`${prefix}.warning`) },
                { value: 'info',      label: this.$tc(`${prefix}.info`) },
                { value: 'primary',   label: this.$tc(`${prefix}.primary`) },
                { value: 'secondary', label: this.$tc(`${prefix}.secondary`) },
                { value: 'light',     label: this.$tc(`${prefix}.light`) },
                { value: 'dark',      label: this.$tc(`${prefix}.dark`) },
            ];
        },

        layoutTypeOptions() {
            const prefix = 'ict-cms-bundle-element.cms.elements.ictAlertHint.config.layoutTypes';
            return [
                { value: 'custom-callout',    label: this.$tc(`${prefix}.customCallout`) },
                { value: 'shopware-standard', label: this.$tc(`${prefix}.shopwareStandard`) },
            ];
        },
    },

    created() {
        this.initElementConfig('ict-alert-hint');
    },

    methods: {
        onUpdate() {
            this.$emit('element-update', this.element);
        },

        onContentUpdate(value) {
            this.element.config.content.value = value;
            this.$emit('element-update', this.element);
        },
    },
};
