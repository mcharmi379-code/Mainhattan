const defaultIconBasePath = '/bundles/mainhattanwheels/storefront/default-cms-icons';

export const defaultCards = [
    {
        imageUrl: `${defaultIconBasePath}/layer-1.svg`,
        titleKey: 'cardOneTitle',
        descriptionKey: 'cardOneDescription',
        buttonTextKey: 'cardOneButtonText',
        buttonLinkKey: 'cardOneButtonLink',
    },
    {
        imageUrl: `${defaultIconBasePath}/frame.svg`,
        titleKey: 'cardTwoTitle',
        descriptionKey: 'cardTwoDescription',
        buttonTextKey: 'cardTwoButtonText',
        buttonLinkKey: 'cardTwoButtonLink',
    },
    {
        imageUrl: `${defaultIconBasePath}/group.svg`,
        titleKey: 'cardThreeTitle',
        descriptionKey: 'cardThreeDescription',
        buttonTextKey: 'cardThreeButtonText',
        buttonLinkKey: 'cardThreeButtonLink',
    },
];

export function defaultMediaEntity(url) {
    return {
        url,
        alt: '',
        title: '',
    };
}
