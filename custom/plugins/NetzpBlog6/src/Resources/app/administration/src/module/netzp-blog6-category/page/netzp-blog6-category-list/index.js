import template from './netzp-blog6-category-list.html.twig';
import './netzp-blog6-category-list.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('netzp-blog6-category-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('listing')
    ],

    data() {
        return {
            repository: null,
            category: null
        };
    },

    metaInfo() {
        return
        {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    label: this.$t('netzp-blog6-category.list.columns.title'),
                    routerLink: 'netzp.blog6.category.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'saleschannel',
                    dataIndex: 'saleschannel.name',
                    label: this.$t('netzp-blog6-category.list.columns.saleschannel')
                },
                {
                    property: 'customergroup',
                    dataIndex: 'customergroup.name',
                    label: this.$t('netzp-blog6-category.list.columns.customergroup')
                },
                {
                    property: 'onlyloggedin',
                    dataIndex: 'onlyloggedin',
                    label: this.$t('netzp-blog6-category.list.columns.onlyloggedin'),
                    width: '100px'
                },
                {
                    property: 'includeinrss',
                    dataIndex: 'includeinrss',
                    label: this.$t('netzp-blog6-category.list.columns.includeinrss'),
                    width: '100px'
                }
            ]
        }
    },

    methods: {
        getList()
        {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog_category');
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('saleschannel');
            criteria.addAssociation('customergroup');
            criteria.addSorting(Criteria.sort('title', 'ASC'));

            this.repository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.category = result;
                });
        },

        changeLanguage()
        {
            this.getList();
        }
    },

    created()
    {
        this.getList();
    }
});
