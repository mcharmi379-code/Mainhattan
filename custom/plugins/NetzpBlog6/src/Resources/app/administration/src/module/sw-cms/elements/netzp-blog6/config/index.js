const { Component, Mixin } = Shopware;
import './sw-cms-el-config-netzp-blog6.scss';
const { Criteria, EntityCollection } = Shopware.Data;

import template from './sw-cms-el-config-netzp-blog6.html.twig';

Component.register('sw-cms-el-config-netzp-blog6', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            repositoryCategories: null,
            repositoryAuthors: null,
            repositoryTags: null,
            categories: [],
            authors: [],
            selectedTags: []
        };
    },

    created()
    {
        this.repositoryCategories = this.repositoryFactory.create('s_plugin_netzp_blog_category');
        this.repositoryAuthors = this.repositoryFactory.create('s_plugin_netzp_blog_author');
        this.repositoryTags = this.repositoryFactory.create('tag');

        this.getCategories();
        this.getAuthors();

        this.createdComponent();
    },

    computed: {
        tagsCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        blogPostCriteria()
        {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('title', 'ASC'));

            let categoryId = this.element.config.category.value;
            if(categoryId && categoryId != '00000000000000000000000000000000')
            {
                criteria.addFilter(Criteria.equals('category.id', categoryId));
            }

            return criteria;
        },

        context()
        {
            return { ...Shopware.Context.api, inheritance: true };
        },

        isSpecificBlogPost()
        {
            return this.element.config.numberOfPosts.value == 1 &&
                   this.element.config.noPagination.value &&
                   this.element.config.blogPost &&
                   this.element.config.blogPost != '00000000000000000000000000000000';
        }
    },

    methods: {
        createdComponent() {
            this.initElementConfig('netzp-blog6');

            if (! this.element.config.tags.value || this.element.config.tags.value.length <= 0) {
                return;
            }

            var criteria = new Criteria(1, 500);
            criteria.setIds(this.element.config.tags.value);

            this.repositoryTags
                .search(criteria, Object.assign({}, Shopware.Context.api))
                .then((result) => {
                    this.selectedTags = result.getIds();
                });
        },

        getCategories() {
            var criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('title', 'ASC'));

            this.repositoryCategories.search(criteria, Shopware.Context.api).then((result) => {
                this.categories = result;
                this.categories.unshift({
                    id: '00000000000000000000000000000000',
                    title: '---'
                })
            });
        },

        getAuthors() {
            var criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.repositoryAuthors.search(criteria, Shopware.Context.api).then((result) => {
                this.authors = result;
                this.authors.unshift({
                    id: '00000000000000000000000000000000',
                    name: '---'
                })
            });
        },

        onTagsChange() {
            this.element.config.tags.value = this.selectedTags;
        }
    }
});
