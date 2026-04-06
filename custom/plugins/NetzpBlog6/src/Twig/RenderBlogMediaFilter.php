<?php declare(strict_types=1);

namespace NetzpBlog6\Twig;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderBlogMediaFilter extends AbstractExtension
{
    public function __construct(private readonly EntityRepository $mediaRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'blog_media',
                [$this, 'renderBlogMedia'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function renderBlogMedia(Environment $twig,
                                    string $tag,
                                    string $cssClass = '',
                                    string $style = ''): string
    {
        if($tag == null) {
            $tag = '--- media tag missing ---';
        }

        $salesChannelcontext = $twig->getGlobals()['context'];
        $image = $this->getMediaFromTag($tag, $salesChannelcontext->getContext());

        if($image)
        {
            $s = '<img src="' . $image->getUrl() . '"';
            if($image->getTranslation('title'))
            {
                $s .= ' title="' . $image->getTranslation('title') . '"';
            }
            else
            {
                $s .= ' title="' . $image->getFilename() . '"';
            }

            if($image->getTranslation('alt'))
            {
                $s .= ' alt="' . $image->getTranslation('alt') . '"';
            }

            if($cssClass != '') {
                $s .= ' class="' . $cssClass . '"';
            }

            if($style != '') {
                $s .= ' style="' . $style . '"';
            }

            $s .= '>';
        }
        else {
            $s = '--- image not found for tag: ' . $tag . ' ---';
        }

        return $s;
    }

    public function getMediaFromTag(string $mediaTag, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('media.tags.name', $mediaTag));
        $criteria->addSorting(new FieldSorting('updatedAt', FieldSorting::DESCENDING));

        $media = $this->mediaRepository->search($criteria, $context)->getEntities();

        return $media ? $media->first() : null;
    }
}
