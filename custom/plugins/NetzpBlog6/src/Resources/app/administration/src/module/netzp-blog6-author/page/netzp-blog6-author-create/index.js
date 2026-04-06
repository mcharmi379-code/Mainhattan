const { Component } = Shopware;
const { date } = Shopware.Utils.format;

Component.extend('netzp-blog6-author-create', 'netzp-blog6-author-detail', {
    methods: {
        getAuthor() {
            this.author = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.author, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'netzp.blog6.author.detail', params: { id: this.author.id } });
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
