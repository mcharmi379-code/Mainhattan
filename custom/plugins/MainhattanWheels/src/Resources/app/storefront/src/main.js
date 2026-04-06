import mainhattanHeaderHoverAddRemove from './js/mainhattan-header-hover-add-remove-class.plugin';

const PluginManager = window.PluginManager;

PluginManager.override('FlyoutMenu',mainhattanHeaderHoverAddRemove,'[data-flyout-menu]');