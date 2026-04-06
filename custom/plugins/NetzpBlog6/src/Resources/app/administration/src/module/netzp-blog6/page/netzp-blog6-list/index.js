import template from './netzp-blog6-list.html.twig';
import './netzp-blog6-list.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('netzp-blog6-list', {
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
            blog: null,
            filterLoading: false,
            availableCategories: [],
            categoriesFilter: null,
            availableAuthors: [],
            authorsFilter: null,
            tagsFilter: null,
            term: null
        };
    },

    metaInfo() {
        return
        {
            title: this.$createTitle()
        };
    },

    computed: {
        dateFilter() {
            return Shopware.Filter.getByName('date');
        },

        truncateFilter() {
            return Shopware.Filter.getByName('truncate');
        },

        categoriesRepository()
        {
            return this.repositoryFactory.create('s_plugin_netzp_blog_category');
        },

        authorsRepository()
        {
            return this.repositoryFactory.create('s_plugin_netzp_blog_author');
        },

        reset()
        {
            return { id: 0, label: '- alle -' };
        },

        defaultCriteria()
        {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('products');
            criteria.addAssociation('categories');
            criteria.addAssociation('tags');
            criteria.addAssociation('author');
            criteria.addSorting(Criteria.sort('postdate', 'DESC'));

            if (this.term !== null) {
                criteria.setTerm(this.term);
            }

            if (this.categoriesFilter) {
                criteria.addFilter(Criteria.equals('categoryid', this.categoriesFilter));
            }

            if (this.authorsFilter) {
                criteria.addFilter(Criteria.equals('authorid', this.authorsFilter));
            }

            if (this.tagsFilter) {
                criteria.addFilter(Criteria.equals('tags.id', this.tagsFilter));
            }

            return criteria;
        },

        filterCategoriesSelectCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('title', 'ASC'));

            return criteria;
        },

        filterAuthorsSelectCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    label: this.$t('netzp-blog6.list.columns.title'),
                    routerLink: 'netzp.blog6.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'postdate',
                    dataIndex: 'postdate',
                    label: this.$t('netzp-blog6.list.columns.postdate')
                },
                {
                    property: 'showfrom',
                    dataIndex: 'showfrom',
                    label: this.$t('netzp-blog6.list.columns.showfrom'),
                },
                {
                    property: 'showuntil',
                    dataIndex: 'showuntil',
                    label: this.$t('netzp-blog6.list.columns.showuntil'),
                },
                {
                    property: 'sticky',
                    dataIndex: 'sticky',
                    label: this.$t('netzp-blog6.list.columns.sticky'),
                    inlineEdit: 'boolean'
                },
                {
                    property: 'category',
                    dataIndex: 'category.title',
                    label: this.$t('netzp-blog6.list.columns.category'),
                },
                {
                    property: 'author',
                    dataIndex: 'author.name',
                    label: this.$t('netzp-blog6.list.columns.author'),
                },
                {
                    property: 'products',
                    dataIndex: 'products.name',
                    label: this.$t('netzp-blog6.list.columns.products')
                },
                {
                    property: 'image',
                    dataIndex: 'image',
                    sortable: false,
                    label: this.$t('netzp-blog6.list.columns.image')
                },
            ]
        }
    },

    methods: {
        getList()
        {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog');

            this.repository
                .search(this.defaultCriteria, Shopware.Context.api)
                .then((result) => {
                    this.blog = result;
                });
        },

        onRefresh()
        {
            this.getList();
        },

        getCategory(id)
        {
            return this.availableCategories.get(id);
        },

        getAuthor(id)
        {
            return this.availableAuthors.get(id);
        },

        getBlogPost(entityId)
        {
            return this.repository.get(entityId, Shopware.Context.api)
        },

        saveBlogPost(blogEntity)
        {
            return this.repository.save(blogEntity, Shopware.Context.api);
        },

        loadCategoriesFilterValues()
        {
            this.filterLoading = true;
            return this.categoriesRepository.search(this.filterCategoriesSelectCriteria, Shopware.Context.api)
                .then((result) => {
                    this.availableCategories = result;
                    this.filterLoading = false;

                    return result;
                }).catch(() => {
                    this.filterLoading = false;
                });
        },

        loadAuthorsFilterValues()
        {
            this.filterLoading = true;
            return this.authorsRepository.search(this.filterAuthorsSelectCriteria, Shopware.Context.api)
                .then((result) => {
                    this.availableAuthors = result;
                    this.filterLoading = false;

                    return result;
                }).catch(() => {
                    this.filterLoading = false;
                });
        },

        onChangeCategoriesFilter(value)
        {
            this.categoriesFilter = value;
            this.getList();
        },

        onChangeAuthorsFilter(value)
        {
            this.authorsFilter = value;
            this.getList();
        },

        onChangeTagsFilter(value)
        {
            this.TagsFilter = value;
            this.getList();
        },

        onSearch(value = null)
        {
            if (!value.length || value.length <= 0) {
                this.term = null;
            } else {
                this.term = value;
            }

            this.resetList();
        },

        async onDuplicate(blogItem)
        {
            this.repository.clone(blogItem.id).then(async (newBlogItem) =>
            {
                newBlogItem = await this.getBlogPost(newBlogItem.id);

                newBlogItem.title += ' *';
                newBlogItem.products = blogItem.products;
                newBlogItem.categories = blogItem.categories;
                newBlogItem.tags = blogItem.tags;

                await this.saveBlogPost(newBlogItem);
                this.$router.push({
                    name: 'netzp.blog6.detail',
                    params: { id: newBlogItem.id }
                });
            });
        },

        resetList()
        {
            this.page = 1;
            this.pages = [];
            this.updateRoute({
                page: this.page,
                limit: this.limit,
                term: this.term,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            });
            this.getList();
        },

        changeLanguage()
        {
            this.resetList();
        }
    },

    created()
    {
        this.getList();
        this.loadCategoriesFilterValues();
        this.loadAuthorsFilterValues();
    }
});
