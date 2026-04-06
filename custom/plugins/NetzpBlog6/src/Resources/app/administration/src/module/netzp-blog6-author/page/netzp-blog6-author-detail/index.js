import template from './netzp-blog6-author-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('netzp-blog6-author-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            author: null,
            customFieldSets: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            imageUploadTag: 'netzp-blog-author-image-upload-tag',
        };
    },

    computed: {
        ...mapPropertyErrors('author', ['name']),

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        mediaItem() {
            return this.author !== null ? this.author.image : null;
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        customFieldSetCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('relations.entityName', 's_plugin_netzp_blog_author'));
            criteria.getAssociation('customFields').addSorting(Criteria.sort('config.customFieldPosition', 'ASC', true));

            return criteria;
        }
    },

    watch: {
        '$route.params.id'() {
            this.createdComponent();
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog_author');
            this.getAuthor();
            this.getCustomFieldsets();

            if (this.author && this.author.isNew() &&
                Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.systemLanguageId);
            }
        },

        getAuthor() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.isLoading = false;
                    this.author = entity;
                });
        },

        getCustomFieldsets() {
            this.customFieldSetRepository.search(this.customFieldSetCriteria, Shopware.Context.api)
                .then((customFieldSets) => {
                    this.customFieldSets = customFieldSets;
                });
        },
        onChangeLanguage() {
            this.getAuthor();
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.author, Shopware.Context.api)
                .then(() => {
                    this.getAuthor();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('netzp-blog6-author.detail.error.title'),
                        message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

        setMediaItemFromSidebar(sideBarMedia) {
            this.mediaRepository.get(sideBarMedia.id, Shopware.Context.api).then((media) => {
                this.author.imageid = media.id;
                this.author.image = media;
            });
        },

        onSetMediaItem({ targetId }) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.author.imageid = targetId;
                this.author.image = updatedMedia;
            });
        },

        onRemoveMediaItem() {
            this.author.imageid = null;
            this.author.image = null;
        },

        onMediaDropped(dropItem) {
            this.onSetMediaItem({ targetId: dropItem.id });
        }
    }
});
