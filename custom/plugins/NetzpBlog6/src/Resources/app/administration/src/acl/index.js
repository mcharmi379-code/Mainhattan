Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'additional_permissions',
    parent: null,
    key: 'blog',
    roles: {
        edit_blog: {
            privileges: ['netzpblog6:posts'],
            dependencies: []
        },

        edit_authors: {
            privileges: ['netzpblog6:authors'],
            dependencies: []
        },

        edit_categories: {
            privileges: ['netzpblog6:categories'],
            dependencies: []
        }
    }
});
