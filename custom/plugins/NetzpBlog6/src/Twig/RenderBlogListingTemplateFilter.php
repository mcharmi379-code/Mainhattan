<?php declare(strict_types=1);

namespace NetzpBlog6\Twig;

use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderBlogListingTemplateFilter extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_blog_listing_template',
                [$this, 'renderBlogListingTemplate'],
                ['needs_environment' => true]
            ),
        ];
    }

    public function renderBlogListingTemplate(Environment $twig, ?string $tpl, BlogEntity $post, string $detailUrl = ''): string
    {
        if($tpl == null) {
            $tpl = '';
        }
        $result = $twig->createTemplate((string) $tpl);

        return $result->render([
            'post'      => $post,
            'url'       => $detailUrl
        ]);
    }
}
