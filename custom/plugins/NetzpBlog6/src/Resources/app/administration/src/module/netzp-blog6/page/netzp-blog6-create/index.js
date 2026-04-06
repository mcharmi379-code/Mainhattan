const { Component } = Shopware;
const { date } = Shopware.Utils.format;

Component.extend('netzp-blog6-create', 'netzp-blog6-detail', {
    methods: {
        getBlog()
        {
            this.blog = this.repository.create(Shopware.Context.api);

            var now = new Date().toISOString().slice(0, 19).replace('T', ' ');
            this.blog.postdate = now;
            this.blog.categoryid = '00000000000000000000000000000000';
        },

        onClickSave()
        {
            this.isLoading = true;

            this.repository
                .save(this.blog, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'netzp.blog6.detail', params: { id: this.blog.id } });
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
