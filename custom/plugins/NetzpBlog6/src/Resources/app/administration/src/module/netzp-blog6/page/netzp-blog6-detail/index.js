import template from './netzp-blog6-detail.html.twig';
import './netzp-blog6-detail.scss';
import Swal from 'sweetalert2';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('netzp-blog6-detail', {
    template,

    inject: ['repositoryFactory'],

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
            active: 'main',
            blog: null,
            categories: [],
            authors: [],
            customFieldSets: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            repositoryCategories: null,
            repositoryAuthors: null,
            currentItem: null,
            mediaModalIsOpen: false,
            imageUploadTag: 'netzp-blog-image-upload-tag',
            imagepreviewUploadTag: 'netzp-blog-image-preview-upload-tag',
            blogMediaUploadTag: 'netzp-blog-media-upload-tag'
        };
    },

    computed: {
        ...mapPropertyErrors('blog', ['title', 'slug', 'contents', 'postdate']),

        mediaItem()
        {
            return this.blog !== null ? this.blog.image : null;
        },

        mediaPreviewItem()
        {
            return this.blog !== null ? this.blog.imagepreview : null;
        },

        mediaRepository()
        {
            return this.repositoryFactory.create('media');
        },

        blogItemRepository()
        {
            return this.repositoryFactory.create('s_plugin_netzp_blog_item');
        },

        blogMediaRepository()
        {
            return this.repositoryFactory.create('s_plugin_netzp_blog_media');
        },

        customFieldSetRepository()
        {
            return this.repositoryFactory.create('custom_field_set');
        },

        productSelectionCriteria()
        {
            const criteria = new Criteria();
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        customFieldSetCriteria()
        {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('relations.entityName', 's_plugin_netzp_blog'));
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

    created() {
        this.createdComponent();
    },

    methods: {
        count: function (value)
        {
            if (!value) return 0;
            return value.length;
        },

        createdComponent()
        {
            this.repository = this.repositoryFactory.create('s_plugin_netzp_blog');
            this.repositoryCategories = this.repositoryFactory.create('s_plugin_netzp_blog_category');
            this.repositoryAuthors = this.repositoryFactory.create('s_plugin_netzp_blog_author');

            this.getBlog();
            this.getCategories();
            this.getAuthors();
            this.getCustomFieldsets();

            if (this.blog && this.blog.isNew() &&
                Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.systemLanguageId);
            }
        },

        slugify(s)
        {
            if(s === null) return '';

            s = s.toString().trim().toLowerCase();

            // remove accents, swap ñ for n, etc
            const from = "åàáãâèéëêìíïîòóôùúûñç·/_,:;";
            const to   = "aaaaaeeeeiiiiooouuunc------";

            for (let i = 0, l = from.length; i < l; i++) {
                s = s.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
            }

            return s
                .replace(/ä/g, "ae")
                .replace(/ö/g, "oe")
                .replace(/ü/g, "ue")
                .replace(/[^a-z0-9 -]/g, "") // remove invalid chars
                .replace(/\s+/g, "-") // collapse whitespace and replace by -
                .replace(/-+/g, "-") // collapse dashes
                .replace(/^-+/, "") // trim - from start of text
                .replace(/-+$/, ""); // trim - from end of text
        },

        updateSlug(title)
        {
            if( ! this.blog.slug || this.blog.slug === '') {
                this.blog.slug = this.slugify(this.blog.title)
            }
        },

        getBlog()
        {
            var criteria = new Criteria();
            criteria.addAssociation('categories');
            criteria.addAssociation('products');
            criteria.addAssociation('tags');
            criteria.addAssociation('blogmedia');
            criteria.getAssociation('blogmedia').addSorting(Criteria.sort('number', 'ASC'));
            criteria.addAssociation('items');
            criteria.getAssociation('items').addSorting(Criteria.sort('number', 'ASC'));
            // fix - criteria.getAssociation('products').setLimit(99);

            this.repository
                .get(this.$route.params.id, { ...Shopware.Context.api, inheritance: true }, criteria)
                .then((entity) => {
                    this.blog = entity;
                    this.isLoading = false;
                });
        },

        getCategories()
        {
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

        getAuthors()
        {
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

        getCustomFieldsets()
        {
            this.customFieldSetRepository.search(this.customFieldSetCriteria, Shopware.Context.api)
                .then((customFieldSets) => {
                    this.customFieldSets = customFieldSets;
                });
        },

        onChangeLanguage()
        {
            this.getBlog();
        },

        onSetCurrentItem(item)
        {
            this.currentItem = item;
        },

        openMediaSidebar()
        {
            this.$refs.mediaSidebarItem.openContent();
        },

        onOpenMediaModal()
        {
            this.mediaModalIsOpen = true;
        },

        onCloseMediaModal()
        {
            this.mediaModalIsOpen = false;
        },

        setMediaItemFromSidebar(sideBarMedia)
        {
            this.mediaRepository.get(sideBarMedia.id, Shopware.Context.api).then((media) => {
                this.blog.imageid = media.id;
                this.blog.image = media;
            });
        },

        onSetMediaItem({ targetId })
        {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.blog.imageid = targetId;
                this.blog.image = updatedMedia;
            });
        },

        onRemoveMediaItem()
        {
            this.blog.imageid = null;
            this.blog.image = null;
        },

        onMediaDropped(dropItem)
        {
            this.onSetMediaItem({ targetId: dropItem.id });
        },

        onMediaPreviewDropped(dropItem)
        {
            this.onSetMediaPreviewItem({ targetId: dropItem.id });
        },

        setMediaPreviewItemFromSidebar(sideBarMedia)
        {
            this.mediaRepository.get(sideBarMedia.id, Shopware.Context.api).then((media) => {
                this.blog.imagepreviewid = media.id;
                this.blog.imagepreview = media;
            });
        },

        onSetMediaPreviewItem({ targetId })
        {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.blog.imagepreviewid = targetId;
                this.blog.imagepreview = updatedMedia;
            });
        },

        onRemoveMediaPreviewItem()
        {
            this.blog.imagepreviewid = null;
            this.blog.imagepreview = null;
        },

        onSetImage([media])
        {
            this.currentItem.imageid = media.id;
            this.currentItem.image = media;
        },

        onImageDropped(dropItem)
        {
            this.currentItem.imageid = dropItem.id;
            this.currentItem.image = dropItem;
        },

        successfulUploadImage(media)
        {
            this.currentItem.imageid = media.targetId;
            this.mediaRepository.get(media.targetId, Shopware.Context.api).then((mediaItem) => {
                this.currentItem.image = mediaItem;
            });
        },

        removeImage()
        {
            this.currentItem.imageid = null;
            this.currentItem.image = null;
        },

        onBlogMediaSelectionChange(mediaItems)
        {
            var number = this.blog.blogmedia.length > 0 ? this.blog.blogmedia.last().number + 1 : 0;
            mediaItems.forEach((item) => {
                var blogMedia = this.blogMediaRepository.create(Shopware.Context.api);
                blogMedia.blogId = this.blog.id;
                blogMedia.number = number;
                blogMedia.mediaId = item.id;
                blogMedia.media = item;
                this.blog.blogmedia.add(blogMedia);
                number++;
            });
        },

        onBlogMediaItemRemove(mediaItem, index)
        {
            this.blog.blogmedia.remove(mediaItem.id);
            this.updateBlogMediaItemNumbers();
        },

        onBlogMediaImageUpload(mediaItem)
        {
            var blogMedia = this.blogMediaRepository.create(Shopware.Context.api);
            blogMedia.blogId = this.blog.id;
            blogMedia.number = this.blog.blogmedia.length > 0 ? this.blog.blogmedia.last().number + 1 : 0;
            blogMedia.mediaId = mediaItem.id;
            blogMedia.media = mediaItem;
            this.blog.blogmedia.add(blogMedia);
        },

        addItem()
        {
            if(this.blog.items.length == 0)
            {
                var itemMaterial = this.blogItemRepository.create(Shopware.Context.api);
                itemMaterial.blogId = this.blog.id;
                itemMaterial.number = 0;
                itemMaterial.title = '';
                itemMaterial.content = '';

                this.blog.items.add(itemMaterial);
            }

            var newItem = this.blogItemRepository.create(Shopware.Context.api);
            newItem.blogId = this.blog.id;
            newItem.number = this.blog.items.length == 0 ? 1 : this.blog.items[this.blog.items.length - 1].number + 1;
            newItem.title = '';
            newItem.content = '';

            this.blog.items.add(newItem);
        },

        moveUpItem(item)
        {
            if(item.number <= 1) {
                return;
            }

            var thisNumber = item.number;
            this.blog.items[thisNumber].number = thisNumber - 1;
            this.blog.items[thisNumber - 1].number = thisNumber;
            this.onClickSave();
        },

        moveDownItem(item)
        {
            if(item.number >= this.blog.items.length - 1) {
                return;
            }

            var thisNumber = item.number;
            this.blog.items[thisNumber].number = thisNumber + 1;
            this.blog.items[thisNumber + 1].number = thisNumber;
            this.onClickSave();
        },

        removeItem(item)
        {
            Swal.fire({
                title: this.$tc('netzp-blog6.detail.msg.attention'),
                text: this.$tc('netzp-blog6.detail.msg.removeItem'),
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: this.$tc('netzp-blog6.detail.msg.cancel'),
                confirmButtonText: this.$tc('netzp-blog6.detail.msg.ok')
            }).then((result) => {
                if (result.value) {
                    this.blog.items.remove(item.id);
                }
            })
        },

        onMediaItemDragSort(dragData, dropData, valid)
        {
            if(dropData == null) return;

            this.blog.blogmedia.moveItem(dragData.number, dropData.number);
            this.updateBlogMediaItemNumbers();
        },

        updateBlogMediaItemNumbers()
        {
            this.blog.blogmedia.forEach((medium, index) => {
                medium.number = index;
            });
        },

        gotoPluginCategories()
        {
            this.$router.push({ name: 'netzp.blog6.category.list' });
        },

        onClickSave() {
            this.updateSlug(this.blog.title);

            this.isLoading = true;
            this.repository
                .save(this.blog, Shopware.Context.api)
                .then(() => {
                    this.getBlog();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('netzp-blog6.detail.error.title'),
                        message: this.$t('netzp-blog6.detail.error.missingFields'),
                });
            });
        },

        saveFinish()
        {
            this.processSuccess = false;
        }
    }
});
