import template from './netzp-blog6-category-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('netzp-blog6-category-detail', {
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
            category: null,
            customFieldSets: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            repositoryCustomerGroups: null,
            repositorySalesChannels: null,
            salesChannels: [],
            customerGroups: []
        };
    },

    computed: {
        ...mapPropertyErrors('category', ['title']),

        customFieldSetRepository()
        {
            return this.repositoryFactory.create('custom_field_set');
        },

        customFieldSetCriteria()
        {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('relations.entityName', 's_plugin_netzp_blog_category'));
            criteria.getAssociation('customFields').addSorting(Criteria.sort('config.customFieldPosition', 'ASC', true));

            return criteria;
        }
    },

    watch: {
        '$route.params.id'()
        {
            this.createdComponent();
        }
    },

    created()
    {
        this.createdComponent();
    },

    methods: {
        createdComponent()
        {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog_category');
            this.repositoryCustomerGroups = this.repositoryFactory.create('customer_group');
            this.repositorySalesChannels = this.repositoryFactory.create('sales_channel');
            this.getCategory();
            this.getCustomFieldsets();
            this.getCustomerGroups();
            this.getSalesChannels();

            if (this.category && this.category.isNew() &&
                Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.systemLanguageId);
            }
        },

        getCategory()
        {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.isLoading = false;
                    this.category = entity;
                });
        },

        getCustomFieldsets()
        {
            this.customFieldSetRepository.search(this.customFieldSetCriteria, Shopware.Context.api)
                .then((customFieldSets) => {
                    this.customFieldSets = customFieldSets;
                });
        },

        getSalesChannels()
        {
            var criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.repositorySalesChannels.search(criteria, Shopware.Context.api).then((result) => {
                this.salesChannels = result;
                this.salesChannels.unshift({
                    id: '00000000000000000000000000000000',
                    name: this.$t('netzp-blog6-category.detail.label.reset')
                });
            });
        },

        getCustomerGroups()
        {
            var criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.repositoryCustomerGroups.search(criteria, Shopware.Context.api).then((result) => {
                this.customerGroups = result;
                this.customerGroups.unshift({
                    id: '00000000000000000000000000000000',
                    name: this.$t('netzp-blog6-category.detail.label.reset')
                });
            });
        },

        cmsPageCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        navigationCategoryCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        onChangeLanguage()
        {
            this.getCategory();
        },

        onClickSave()
        {
            this.isLoading = true;

            this.repository
                .save(this.category, Shopware.Context.api)
                .then(() => {
                    this.getCategory();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('netzp-blog6-category.detail.error.title'),
                        message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
