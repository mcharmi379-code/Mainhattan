import template from './sw-cms-el-config-ict-accordion.html.twig';
import './sw-cms-el-config-ict-accordion.scss';

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

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        entries() {
            return this.element?.config?.entries?.value ?? [];
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

            if (!this.element.config.entries) {
                this.element.config.entries = defaultConfig.entries;
                changed = true;
            }

            if (!Array.isArray(this.element.config.entries.value)) {
                this.element.config.entries.value = createAccordionEntries();
                changed = true;
            }

            const normalizedEntries = this.element.config.entries.value
                .filter((entry) => entry && typeof entry === 'object' && !Array.isArray(entry))
                .map((entry, index) => this.normalizeEntry(entry, index));

            if (JSON.stringify(normalizedEntries) !== JSON.stringify(this.element.config.entries.value)) {
                this.element.config.entries.value = normalizedEntries;
                changed = true;
            }

            return changed;
        },

        normalizeEntry(entry, index) {
            return {
                title: typeof entry.title === 'string' && entry.title.trim().length > 0
                    ? entry.title
                    : createAccordionEntry(index + 1).title,
                content: typeof entry.content === 'string'
                    ? entry.content
                    : createAccordionEntry(index + 1).content,
                expanded: typeof entry.expanded === 'boolean'
                    ? entry.expanded
                    : index === 0,
            };
        },

        addEntry() {
            this.entries.push(createAccordionEntry(this.entries.length + 1));
            this.emitElementUpdate();
        },

        duplicateEntry(index) {
            const entry = this.entries[index];

            if (!entry) {
                return;
            }

            this.entries.splice(index + 1, 0, {
                title: entry.title,
                content: entry.content,
                expanded: entry.expanded,
            });

            this.emitElementUpdate();
        },

        moveEntry(index, direction) {
            const targetIndex = index + direction;

            if (targetIndex < 0 || targetIndex >= this.entries.length) {
                return;
            } 

            const [entry] = this.entries.splice(index, 1);
            this.entries.splice(targetIndex, 0, entry);
            this.emitElementUpdate();
        },

        removeEntry(index) {
            this.entries.splice(index, 1);

            if (this.entries.length === 0) {
                this.entries.push(createAccordionEntry(1));
            }

            this.emitElementUpdate();
        },

        onEntryTitleChange(index, value) {
            this.entries[index].title = value;
            this.emitElementUpdate();
        },

        onEntryContentChange(index, value) {
            this.entries[index].content = value;
            this.emitElementUpdate();
        },

        onEntryExpandedChange(index, value) {
            this.entries[index].expanded = value;
            this.emitElementUpdate();
        },

        onInput() {
            this.emitElementUpdate();
        },

        getEntryLabel(entry, index) {
            if (entry?.title) {
                return entry.title;
            }

            return `${this.$tc('ict-cms-bundle-element.cms.elements.ictAccordion.config.entry.fallbackLabel')} ${index + 1}`;
        },

        emitElementUpdate() {
            this.ensureConfigShape();
            this.normalizeDisplayMode();
            this.$emit('element-update', this.element);
        },

        normalizeDisplayMode() {
            if (this.element.config.displayMode?.value !== 'single') {
                return;
            }

            let expandedFound = false;

            const entries = this.element?.config?.entries?.value ?? [];

            entries.forEach((entry) => {
                if (entry.expanded && !expandedFound) {
                    expandedFound = true;
                    return;
                }

                if (expandedFound) {
                    entry.expanded = false;
                }
            });
        },
    },
};
