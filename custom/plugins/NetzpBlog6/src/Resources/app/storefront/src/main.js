window.PluginManager.register('Netzp6BlogGallery', () => import('./netzp-blog-gallery/netzp-blog-gallery.plugin'), '[data-netzp-blog-gallery]');

if (module.hot) {
    module.hot.accept();
}
