import template from './netzp-blog6-author-list.html.twig';
import './netzp-blog6-author-list.scss';

const { Component, Mixin} = Shopware;
const { Criteria } = Shopware.Data;

Component.register('netzp-blog6-author-list', {
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
            author: null
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
                    property: 'name',
                    dataIndex: 'name',
                    label: this.$t('netzp-blog6-author.list.columns.name'),
                    routerLink: 'netzp.blog6.author.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'image',
                    dataIndex: 'image',
                    sortable: false,
                    label: this.$t('netzp-blog6-author.list.columns.image')
                }
            ]
        }
    },

    methods: {
        getList()
        {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog_author');
            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.repository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.author = result;
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
