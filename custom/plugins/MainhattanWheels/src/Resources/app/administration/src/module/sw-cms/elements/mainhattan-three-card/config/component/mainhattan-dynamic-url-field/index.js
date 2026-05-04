import template from './mainhattan-dynamic-url-field.html.twig';

Shopware.Component.extend('mainhattan-dynamic-url-field', 'sw-dynamic-url-field', {
    template,

    props: {
        value: {
            type: String,
            required: false,
            default: '',
        },
        label: {
            type: String,
            required: false,
            default: '',
        },
    },

    watch: {
        value: {
            async handler(value) {
                if (value === this.lastEmittedLink || typeof value !== 'string') {
                    return;
                }

                const parsedResult = await this.parseLink(value);
                this.linkCategory = ['email', 'phone'].includes(parsedResult.type) ? 'link' : parsedResult.type;
                this.linkTarget = ['email', 'phone'].includes(parsedResult.type) ? '' : parsedResult.target;
            },
            immediate: true,
        },
    },
});
