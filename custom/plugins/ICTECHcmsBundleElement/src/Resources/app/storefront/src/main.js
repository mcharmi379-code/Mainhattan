import IctImageComparisonPlugin from './plugin/ict-image-comparison/ict-image-comparison.plugin';
import IctVerticalTabPlugin from './plugin/ict-vertical-tab/ict-vertical-tab.plugin';
import IctGalleryLightboxPlugin from './plugin/ict-gallery-lightbox/ict-gallery-lightbox.plugin';
import IctContentTabsPlugin from './plugin/ict-content-tabs/ict-content-tabs.plugin';
import IctProductMatrixPlugin from './plugin/ict-product-matrix/ict-product-matrix.plugin';

window.PluginManager.register('IctImageComparison', IctImageComparisonPlugin, '[data-ict-image-comparison]');
window.PluginManager.register('IctVerticalTab', IctVerticalTabPlugin, '[data-ict-vertical-tab]');
window.PluginManager.register('IctGalleryLightbox', IctGalleryLightboxPlugin, '[data-ict-gallery]');
window.PluginManager.register('IctContentTabs', IctContentTabsPlugin, '[data-ict-content-tabs]');
window.PluginManager.register('IctProductMatrix', IctProductMatrixPlugin, '[data-ict-product-matrix]');
