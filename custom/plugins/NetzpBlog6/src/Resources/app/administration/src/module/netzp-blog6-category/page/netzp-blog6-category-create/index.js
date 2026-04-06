const { Component } = Shopware;
const { date } = Shopware.Utils.format;

Component.extend('netzp-blog6-category-create', 'netzp-blog6-category-detail', {
    methods: {
        getCategory()
        {
            this.category = this.repository.create(Shopware.Context.api);
        },

        onClickSave()
        {
            this.isLoading = true;

            this.repository
                .save(this.category, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'netzp.blog6.category.detail', params: { id: this.category.id } });
                }).catch((exception) => {
                    this.isLoading = false;
                    if (exception.response.data && exception.response.data.errors) {
                        exception.response.data.errors.forEach((error) => {
                            this.createNotificationWarning({
                                title: this.$t('netzp-blog6.detail.error.title'),
                                message: this.$t('netzp-blog6.detail.error.missingFields'),
                                duration: 10000
                            });
                        });
                    }
                });
        }
    }
});
