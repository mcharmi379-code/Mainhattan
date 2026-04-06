import { LightGallery } from 'lightgallery.js';
import { LightGalleryVideo } from 'lg-video.js';
import { LightGalleryZoom } from 'lg-zoom.js';

// https://sachinchoolur.github.io/lightgallery.js/docs/#main-features

export default class Netzp6BlogGallery extends window.PluginBaseClass {
    static options = {
        selectorId: "",
        selector: "",
        download: false,
        counter: true,
        captionFromTitleOrAlt: true
    };

    init()
    {
        // keep references for shopware bundler
        const tmpLg = LightGallery;
        const tmpLgVideo = LightGalleryVideo;
        const tmpLgZoom = LightGalleryZoom;

        lightGallery(document.getElementById(this.options.selectorId), {
            download: this.options.download,
            counter: this.options.counter,
            getCaptionFromTitleOrAlt: this.options.captionFromTitleOrAlt,
            selector: this.options.selector,
            zoom: true
        });

        // hide/show netzpShariff when opening/closing gallery (if shariff plugin is present)
        let lg = document.getElementById(this.options.selectorId);
        lg.addEventListener('onBeforeOpen', function(e){
            document.querySelectorAll('.netzp-shariff6').forEach(function(el) {
                el.style.display = 'none';
            });
        }, false);
        lg.addEventListener('onBeforeClose', function(e){
            document.querySelectorAll('.netzp-shariff6').forEach(function(el) {
                el.style.display = 'block';
            });
        }, false);
    }
}
