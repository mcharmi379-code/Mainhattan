import template from './sw-cms-el-ict-platform-support.html.twig';
import './sw-cms-el-ict-platform-support.scss';

const { Mixin } = Shopware;

export default {
    template,
    compatConfig: Shopware.compatConfig,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    inject: ['repositoryFactory'],

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        previewWrapperStyle() {
            const cfg = this.element.config;
            const type = cfg.backgroundType?.value || 'color';
            const radius = (cfg.platformBlockBorderRadius?.value || 0) + 'px';
            const base = { position: 'relative', padding: '48px', borderRadius: radius, overflow: 'hidden' };

            if (type === 'color') {
                return { ...base, backgroundColor: cfg.platformBlockBackground?.value || '#f8f9fa' };
            }
            if (type === 'gradient') {
                const c1 = cfg.gradientColor1?.value || '#005AE5';
                const c2 = cfg.gradientColor2?.value || '#0080FF';
                const dir = cfg.gradientDirection?.value || 'to right';
                return { ...base, background: `linear-gradient(${dir}, ${c1}, ${c2})` };
            }
            // image and video: transparent wrapper, bg handled by child elements
            return { ...base, backgroundColor: 'transparent' };
        },

        platformItems() {
            if (!this.element.config?.platformItems?.value || this.element.config.platformItems.value.length === 0) {
                const dummies = [];
                for (let i = 0; i < 5; i++) {
                    dummies.push(JSON.parse(JSON.stringify(this.cmsElements['ict-platform-support'].platformSupportTemplates)));
                }
                return dummies;
            }
            return this.element.config.platformItems.value;
        }
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.loadMedia();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('ict-platform-support');
            this.initElementData('ict-platform-support');

            if (!this.element.config.platformItems.value) {
                this.element.config.platformItems.value = [];
            }

            if (this.element.config.platformItems.value.length === 0) {
                for (let i = 0; i < 5; i++) {
                    this.addPlatformItem();
                }
            }
        },

        addPlatformItem() {
            if (!this.element.config.platformItems.value) {
                this.element.config.platformItems.value = [];
            }
            const template = JSON.parse(JSON.stringify(this.cmsElements['ict-platform-support'].platformSupportTemplates));
            this.element.config.platformItems.value.push(template);
        },

        async loadMedia() {
            if (!this.element.config.platformItems?.value) return;
            for (let i = 0; i < this.element.config.platformItems.value.length; i++) {
                const platform = this.element.config.platformItems.value[i];
                if (platform.platformIcon && !this.element.data?.platformIcons?.[i]) {
                    try {
                        const media = await this.mediaRepository.get(platform.platformIcon);
                        if (!this.element.data) this.element.data = {};
                        if (!this.element.data.platformIcons) this.element.data.platformIcons = {};
                        this.element.data.platformIcons[i] = media;
                    } catch (_) { /* noop */ }
                }
            }

            if (this.element.config.backgroundImage?.value && !this.element.data?.backgroundImage) {
                try {
                    const media = await this.mediaRepository.get(this.element.config.backgroundImage.value);
                    if (!this.element.data) this.element.data = {};
                    this.element.data.backgroundImage = media;
                } catch (_) { /* noop */ }
            }

            if (this.element.config.backgroundVideo?.value && !this.element.data?.backgroundVideo) {
                try {
                    const media = await this.mediaRepository.get(this.element.config.backgroundVideo.value);
                    if (!this.element.data) this.element.data = {};
                    this.element.data.backgroundVideo = media;
                } catch (_) { /* noop */ }
            }
        },

        getYoutubeEmbedUrl(url) {
            if (!url || typeof url !== 'string') return '';
            let id = '';
            const clean = url.split('#')[0];
            if (clean.includes('v=')) {
                id = clean.split('v=')[1].split('&')[0].split('?')[0];
            } else if (clean.includes('youtu.be/')) {
                id = clean.split('youtu.be/')[1].split('?')[0].split('&')[0];
            } else if (clean.includes('/live/')) {
                id = clean.split('/live/')[1].split('?')[0].split('&')[0];
            } else if (clean.includes('/shorts/')) {
                id = clean.split('/shorts/')[1].split('?')[0].split('&')[0];
            } else if (clean.includes('/embed/')) {
                id = clean.split('/embed/')[1].split('?')[0].split('&')[0];
            }
            return id ? `https://www.youtube.com/embed/${id}?autoplay=1&mute=1&loop=1&playlist=${id}&controls=0&rel=0` : '';
        }
    }
};
