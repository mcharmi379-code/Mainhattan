import template from './sw-cms-el-ict-accordion.html.twig';
import './sw-cms-el-ict-accordion.scss';

const ELEMENT_NAME = 'ict-accordion';

function createAccordionEntry(index = 1) {
    return {
        title: `Accordion item ${index}`,
        content: '<p>Add accordion content here.</p>',
        expanded: index === 1,
    };
}

function createAccordionEntries() {
    return [
        createAccordionEntry(1),
        createAccordionEntry(2),
    ];
}

function createAccordionConfig() {
    return {
        title: {
            source: 'static',
            value: '',
        },
        description: {
            source: 'static',
            value: '',
        },
        displayMode: {
            source: 'static',
            value: 'single',
        },
        entries: {
            source: 'static',
            value: createAccordionEntries(),
            type: Array,
            required: true,
        },
    };
}

const { Mixin } = Shopware;

export default {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        title() {
            return this.element.config?.title?.value || '';
        },

        description() {
            return this.element.config?.description?.value || '';
        },

        entries() {
            return this.element?.config?.entries?.value ?? [];
        },

        previewEntries() {
            return this.entries.slice(0, 3);
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig(ELEMENT_NAME);
            this.ensureConfigShape();
        },

        ensureConfigShape() {
            const defaultConfig = createAccordionConfig();
            let changed = false;

            if (!this.element.config) {
                this.element.config = defaultConfig;
                changed = true;
            }

            ['title', 'description', 'displayMode', 'entries'].forEach((key) => {
                if (!this.element.config[key]) {
                    this.element.config[key] = defaultConfig[key];
                    changed = true;
                }
            });

            if (!Array.isArray(this.element.config.entries.value)) {
                this.element.config.entries = defaultConfig.entries;
                changed = true;
            }

            if (this.element.config.entries.value.length === 0) {
                this.element.config.entries.value = createAccordionEntries();
                changed = true;
            }

            return changed;
        },

        stripTags(value) {
            if (typeof value !== 'string') {
                return '';
            }

            return value.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        },
    },
};
