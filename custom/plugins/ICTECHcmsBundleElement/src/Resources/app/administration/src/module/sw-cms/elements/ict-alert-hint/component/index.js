import template from './sw-cms-el-ict-alert-hint.html.twig';
import './sw-cms-el-ict-alert-hint.scss';

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        alertType() {
            return this.element?.config?.alertType?.value || 'info';
        },

        layoutType() {
            return this.element?.config?.layoutType?.value || 'custom-callout';
        },

        activateIcon() {
            return this.element?.config?.activateIcon?.value !== false;
        },

        title() {
            return this.element?.config?.title?.value || '';
        },

        content() {
            return this.element?.config?.content?.value || '';
        },

        isCallout() {
            return this.layoutType === 'custom-callout';
        },

        alertClasses() {
            return [
                'ict-alert-hint',
                `ict-alert-hint--${this.alertType}`,
                this.isCallout ? 'ict-alert-hint--callout' : 'ict-alert-hint--sw-standard',
            ];
        },
    },

    created() {
        this.initElementConfig('ict-alert-hint');
    },
};
