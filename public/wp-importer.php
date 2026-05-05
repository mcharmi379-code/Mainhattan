<?php
/**
 * WordPress → Shopware 6 CMS Importer
 * Creates proper text + image blocks visible in Shopware admin editor
 */

use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Framework\Adapter\Kernel\KernelFactory;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\ComposerPluginLoader;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use Shopware\Core\Kernel;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

define('WP_API',     'https://mainhattan-wheels.de/wp-json/wp/v2/pages');
define('WP_BASE',    'https://mainhattan-wheels.de');
define('APP_ROOT',   dirname(__DIR__));
define('DB_DSN',     'mysql:host=localhost;port=3306;dbname=mianhattandata;charset=utf8mb4');
define('DB_USER',    'root');
define('DB_PASS',    'admin123');
// Language IDs differ per installation. Resolve dynamically via DB (see getAllLanguageIds()).
define('VERSION_ID', hex2bin('0FA91CE3E96A4BC2BE4BD9CE752C3425'));
define('SC_ID',      hex2bin('019D6208D5477210B86C56A6D53D7917')); // storefront SC (demo_shop)
define('PER_PAGE',   5);

/**
 * Extract the highest-width URL from a srcset attribute.
 */
function extractBestSrcFromSrcset(string $srcset): string
{
    $bestUrl = '';
    $bestWidth = 0;

    foreach (explode(',', $srcset) as $candidate) {
        $candidate = trim($candidate);
        if ($candidate === '') {
            continue;
        }

        // "<url> <width>w" or "<url> <density>x"
        $parts = preg_split('/\\s+/', $candidate);
        $url = trim((string) ($parts[0] ?? ''));
        $descriptor = trim((string) ($parts[1] ?? ''));

        if ($url === '') {
            continue;
        }

        if (preg_match('/^(\\d+)w$/i', $descriptor, $m) === 1) {
            $w = (int) $m[1];
            if ($w > $bestWidth) {
                $bestWidth = $w;
                $bestUrl = $url;
            }
            continue;
        }

        if ($bestUrl === '') {
            $bestUrl = $url;
        }
    }

    return $bestUrl;
}

$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$action = $_POST['action'] ?? '';
$wpPage = max(1, (int)($_POST['wp_page'] ?? 1));
$output = '';
$GLOBALS['wpImporterErrors'] = [];

if ($action === 'import') {
    set_time_limit(180);
    $output = runImport($pdo, $wpPage);
} elseif ($action === 'clean') {
    $output = cleanAll($pdo);
}

function cleanAll(PDO $pdo): string
{
    $mediaIds = findImportedCmsMediaIds($pdo);

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $pdo->exec('DELETE FROM seo_url');
    $pdo->exec('DELETE FROM landing_page_sales_channel');
    $pdo->exec('DELETE FROM landing_page_translation');
    $pdo->exec('DELETE FROM landing_page');
    $pdo->exec("DELETE FROM cms_page WHERE type='landingpage'");
    // Also remove all media imported by this script so re-import starts clean.
    if (!empty($mediaIds)) {
        $placeholders = implode(',', array_fill(0, count($mediaIds), '?'));
        $pdo->prepare("DELETE FROM media WHERE LOWER(HEX(id)) IN ({$placeholders})")
            ->execute(array_map('strtolower', $mediaIds));
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    return alert('success', '🗑 All imported pages and media removed. Ready to re-import.');
}

function runImport(PDO $pdo, int $pageNumber): string
{
    $url      = WP_API . '?per_page=' . PER_PAGE . '&page=' . $pageNumber . '&status=publish';
    $response = curlGet($url);
    if (!$response) return alert('success', '✅ All pages imported!');

    $pages = json_decode($response, true);
    if (empty($pages) || !is_array($pages)) return alert('success', '✅ All pages imported!');

    $imported = 0;
    $skipped  = 0;

    foreach ($pages as $page) {
        if (!is_array($page)) {
            $GLOBALS['wpImporterErrors'][] = 'Skipping unexpected WP API item (not an array).';
            continue;
        }

        $slug = (string) ($page['slug'] ?? '');
        if ($slug === '') {
            $GLOBALS['wpImporterErrors'][] = 'Skipping WP page because slug is missing.';
            continue;
        }

        $title = html_entity_decode(strip_tags((string) ($page['title']['rendered'] ?? $slug)), ENT_QUOTES, 'UTF-8');
        $content = (string) ($page['content']['rendered'] ?? '');

        if (landingPageExists($pdo, $slug)) { $skipped++; continue; }

        $blocks = parseWpContent($content);
        createLandingPage($pdo, $title, $slug, $blocks);
        $imported++;
    }

    if ($imported === 0 && $skipped === 0) return alert('success', '✅ All pages imported!');

    $next = $pageNumber + 1;
    return alert('info', "Batch {$pageNumber}: <strong>{$imported} imported</strong>, {$skipped} skipped.<br>Auto-loading next batch in 3 seconds...")
        . '<form id="auto-form" method="post"><input type="hidden" name="action" value="import"><input type="hidden" name="wp_page" value="' . $next . '"></form>'
        . '<script>setTimeout(()=>document.getElementById("auto-form").submit(),3000);</script>';
}

/**
 * Parse WP HTML using DOM traversal.
 * Returns array of:
 *   ['type'=>'text',  'html'=>'<h2>...</h2><p>...</p>']
 *   ['type'=>'image', 'src'=>'https://...', 'alt'=>'...']
 */
function parseWpContent(string $html): array
{
    $html = preg_replace('#<script[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace('#<style[^>]*>.*?</style>#is', '', $html);

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?><body>' . $html . '</body>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $blocks     = [];
    $textBuffer = '';

    $getInnerHtml = static function (DOMNode $node): string {
        $inner = '';
        foreach ($node->childNodes as $child) {
            $inner .= $node->ownerDocument?->saveHTML($child) ?? '';
        }
        return $inner;
    };

    $extractEnfoldGalleryColumns = static function (DOMDocument $doc, string $galleryUniqueClass): ?int {
        $galleryUniqueClass = trim($galleryUniqueClass);
        if ($galleryUniqueClass === '') {
            return null;
        }

        $styles = $doc->getElementsByTagName('style');
        foreach ($styles as $style) {
            if (!($style instanceof DOMElement)) {
                continue;
            }

            $css = (string) $style->textContent;
            if ($css === '' || !str_contains($css, $galleryUniqueClass)) {
                continue;
            }

            // Example:
            // #top .avia-gallery.av-xxx ... .avia-gallery-thumb a{ width:25%; }
            if (preg_match('/\\bwidth\\s*:\\s*(\\d+(?:\\.\\d+)?)%\\s*;?/i', $css, $m) === 1) {
                $percent = (float) $m[1];
                if ($percent > 0.0) {
                    $cols = (int) round(100.0 / $percent);
                    if ($cols >= 1 && $cols <= 12) {
                        return $cols;
                    }
                }
            }
        }

        return null;
    };

    $extractGalleryMeta = static function (DOMElement $node): ?array {
        $class = trim((string) $node->getAttribute('class'));
        if ($class === '') {
            return null;
        }

        // Gutenberg gallery blocks often contain `wp-block-gallery` and `columns-<n>` classes
        if (!str_contains($class, 'gallery') && !str_contains($class, 'wp-block-gallery')) {
            return null;
        }

        if (preg_match('/(?:^|\\s)columns-(\\d+)(?:\\s|$)/', $class, $m) !== 1) {
            return null;
        }

        $columns = (int) $m[1];
        if ($columns < 2 || $columns > 12) {
            return null;
        }

        $isLightbox = false;

        foreach (['data-elementor-open-lightbox', 'data-fancybox', 'data-lightbox', 'data-rel'] as $attr) {
            if ($node->hasAttribute($attr)) {
                $isLightbox = true;
                break;
            }
        }

        // If images are wrapped with <a href="...jpg"> it's usually a lightbox gallery.
        if (!$isLightbox) {
            $links = $node->getElementsByTagName('a');
            foreach ($links as $link) {
                if (!($link instanceof DOMElement)) {
                    continue;
                }
                $href = trim((string) $link->getAttribute('href'));
                if ($href === '') {
                    continue;
                }
                if (preg_match('/\\.(?:jpe?g|png|gif|webp)(?:\\?.*)?$/i', $href) === 1) {
                    $isLightbox = true;
                    break;
                }
            }
        }

        return ['columns' => $columns, 'lightbox' => $isLightbox];
    };

    $walk = function(DOMNode $node) use (&$walk, &$blocks, &$textBuffer, $extractGalleryMeta, $extractEnfoldGalleryColumns): void {
        if ($node->nodeType !== XML_ELEMENT_NODE) return;
        $tag = strtolower($node->nodeName);

        // Skip non-content tags entirely
        if (in_array($tag, ['script','style','noscript','iframe','form','input','button','select','textarea','nav'])) {
            return;
        }

        // Enfold / Avia slideshow (homepage hero) => ICT banner slider element.
        if ($node instanceof DOMElement) {
            $classAttr = trim((string) $node->getAttribute('class'));
            $class = ' ' . $classAttr . ' ';
            if (
                $classAttr !== ''
                && (
                    str_contains($class, ' avia-slideshow ')
                    || str_contains($class, ' avia-fullscreen-slider ')
                    || str_contains($class, ' avia-slideshow-inner ')
                )
            ) {
                $xpath = new DOMXPath($node->ownerDocument);

                $slides = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' slide-entry ')]", $node);
                if (!$slides || $slides->length === 0) {
                    $slides = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' avia-slide-wrap ')]", $node);
                }

                $extractImageUrl = static function (DOMElement $scope): string {
                    $style = (string) $scope->getAttribute('style');
                    if ($style !== '' && preg_match('/background-image\\s*:\\s*url\\(([^)]+)\\)/i', $style, $m) === 1) {
                        $url = trim($m[1], " \t\n\r\0\x0B\"'");
                        if ($url !== '') {
                            return $url;
                        }
                    }

                    $imgs = $scope->getElementsByTagName('img');
                    foreach ($imgs as $img) {
                        if (!($img instanceof DOMElement)) {
                            continue;
                        }
                        $src = trim((string) $img->getAttribute('src'));
                        if ($src === '' || stripos($src, 'lazy') !== false) {
                            $src = trim((string) ($img->getAttribute('data-src') ?: $img->getAttribute('data-lazy-src')));
                        }

                        $srcset = trim((string) ($img->getAttribute('srcset') ?: $img->getAttribute('data-srcset')));
                        if ($srcset !== '') {
                            $best = extractBestSrcFromSrcset($srcset);
                            if ($best !== '') {
                                $src = $best;
                            }
                        }

                        if ($src !== '' && strpos($src, 'data:') === false) {
                            return $src;
                        }
                    }

                    return '';
                };

                $extractText = static function (DOMElement $scope, string $tagName): string {
                    $nodes = $scope->getElementsByTagName($tagName);
                    foreach ($nodes as $n) {
                        if (!($n instanceof DOMElement)) {
                            continue;
                        }
                        $txt = trim((string) $n->textContent);
                        if ($txt !== '') {
                            return $txt;
                        }
                    }
                    return '';
                };

                $extractLink = static function (DOMElement $scope, string $prefix): array {
                    $links = $scope->getElementsByTagName('a');
                    foreach ($links as $a) {
                        if (!($a instanceof DOMElement)) {
                            continue;
                        }
                        $href = trim((string) $a->getAttribute('href'));
                        if ($href === '' || stripos($href, $prefix) !== 0) {
                            continue;
                        }
                        $label = trim((string) $a->textContent);
                        return [$href, $label];
                    }
                    return ['', ''];
                };

                $items = [];
                $slideNodes = ($slides && $slides->length > 0) ? $slides : [$node];

                foreach ($slideNodes as $slide) {
                    if (!($slide instanceof DOMElement)) {
                        continue;
                    }

                    $imgUrl = $extractImageUrl($slide);
                    if ($imgUrl === '') {
                        continue;
                    }

                    $mainTitle = $extractText($slide, 'h1');
                    if ($mainTitle === '') {
                        $mainTitle = $extractText($slide, 'h2');
                    }
                    $subTitle = $extractText($slide, 'h3');
                    if ($subTitle === '') {
                        $subTitle = $extractText($slide, 'h4');
                    }
                    $description = $extractText($slide, 'p');

                    [$telHref, $telLabel] = $extractLink($slide, 'tel:');
                    [$mailHref, $mailLabel] = $extractLink($slide, 'mailto:');

                    $items[] = [
                        'src' => $imgUrl,
                        'mainTitle' => $mainTitle,
                        'subTitle' => $subTitle,
                        'description' => $description,
                        'callButtonText' => $telLabel,
                        'callButtonNumber' => $telHref !== '' ? substr($telHref, 4) : '',
                        'emailButtonText' => $mailLabel,
                        'emailButtonAddress' => $mailHref !== '' ? substr($mailHref, 7) : '',
                    ];
                }

                if ($items !== []) {
                    if (trim($textBuffer) !== '') {
                        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                        $textBuffer = '';
                    }

                    $blocks[] = [
                        'type' => 'ict-banner-slider',
                        'items' => $items,
                    ];

                    return;
                }
            }
        }

        // Enfold "avia-gallery" (lightbox gallery) => ICT Image Gallery element.
        if ($node instanceof DOMElement) {
            $classAttr = trim((string) $node->getAttribute('class'));
            $class = ' ' . $classAttr . ' ';
            if ($classAttr !== '' && str_contains($class, ' avia-gallery ')) {
                $xpath = new DOMXPath($node->ownerDocument);

                $extractLinksAsImages = static function (mixed $links): array {
                    $images = [];
                    if (!$links) {
                        return $images;
                    }
                    foreach ($links as $link) {
                        if (!($link instanceof DOMElement)) {
                            continue;
                        }
                        $src = trim((string) $link->getAttribute('href'));
                        $alt = trim((string) ($link->getAttribute('title') ?: $link->getAttribute('data-title')));
                        $srcset = trim((string) $link->getAttribute('data-srcset'));
                        if ($srcset !== '') {
                            $best = extractBestSrcFromSrcset($srcset);
                            if ($best !== '') {
                                $src = $best;
                            }
                        }
                        if ($src !== '' && strpos($src, 'data:') === false) {
                            $images[] = ['src' => $src, 'alt' => $alt];
                        }
                    }
                    return $images;
                };

                // Standard gallery: thumbnails in .avia-gallery-thumb > a
                $links = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' avia-gallery-thumb ')]//a[@href]", $node);
                $images = $extractLinksAsImages($links);

                // Slideshow variant: full-size images in .avia-gallery-big > a (avia-gallery-big-wrapper)
                if ($images === []) {
                    $bigLinks = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' avia-gallery-big ')]", $node);
                    $images = $extractLinksAsImages($bigLinks);
                }

                // Fallback: <img> tags inside .avia-gallery-thumb
                if ($images === []) {
                    $thumbImgs = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' avia-gallery-thumb ')]//img", $node);
                    if ($thumbImgs) {
                        foreach ($thumbImgs as $img) {
                            if (!($img instanceof DOMElement)) {
                                continue;
                            }
                            $src = trim((string) $img->getAttribute('src'));
                            if ($src === '' || stripos($src, 'lazy') !== false) {
                                $src = trim((string) ($img->getAttribute('data-src') ?: $img->getAttribute('data-lazy-src')));
                            }
                            $alt = trim((string) $img->getAttribute('alt'));
                            $srcset = trim((string) ($img->getAttribute('srcset') ?: $img->getAttribute('data-srcset')));
                            if ($srcset !== '') {
                                $best = extractBestSrcFromSrcset($srcset);
                                if ($best !== '') {
                                    $src = $best;
                                }
                            }
                            if ($src !== '' && strpos($src, 'data:') === false) {
                                $images[] = ['src' => $src, 'alt' => $alt];
                            }
                        }
                    }
                }

                if ($images !== []) {
                    if (trim($textBuffer) !== '') {
                        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                        $textBuffer = '';
                    }

                    $uniqueClass = '';
                    foreach (preg_split('/\\s+/', $classAttr) as $c) {
                        if (str_starts_with($c, 'av-')) {
                            $uniqueClass = $c;
                            break;
                        }
                    }

                    $columns = $uniqueClass !== '' && $node->ownerDocument
                        ? ($extractEnfoldGalleryColumns($node->ownerDocument, $uniqueClass) ?? null)
                        : null;

                    // Admin config + storefront element support 1–6 columns only.
                    $columns = $columns ?? max(1, count($images));
                    $columns = max(1, min(6, (int) $columns));

                    $blocks[] = [
                        'type' => 'ict-image-gallery',
                        'columns' => $columns,
                        'images' => $images,
                    ];

                    return;
                }
            }
        }

        // Enfold portfolio grid (used on e.g. Felgen pulverbeschichten) => treat as ICT column layout.
        // The rendered markup does not include Gutenberg "columns-X" classes; it uses grid containers like:
        //   <div class="grid-sort-container ... grid-col-5 ..."> ... <img ...>
        if ($node instanceof DOMElement) {
            $class = ' ' . trim((string) $node->getAttribute('class')) . ' ';
            if ($class !== '  ' && str_contains($class, ' grid-sort-container ')) {
                $imgs = $node->getElementsByTagName('img');
                if ($imgs->length > 0) {
                    if (trim($textBuffer) !== '') {
                        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                        $textBuffer = '';
                    }

                    $images = [];
                    foreach ($imgs as $img) {
                        if (!($img instanceof DOMElement)) {
                            continue;
                        }

                        $src = $img->getAttribute('src');
                        $alt = $img->getAttribute('alt');

                        // Prefer higher-res if available.
                        $srcset = trim((string) $img->getAttribute('srcset'));
                        if ($srcset !== '') {
                            $best = extractBestSrcFromSrcset($srcset);
                            if ($best !== '') {
                                $src = $best;
                            }
                        }

                        if ($src && strpos($src, 'data:') === false && stripos($src, 'lazy') === false) {
                            $images[] = ['src' => $src, 'alt' => $alt];
                        }
                    }

                    if ($images !== []) {
                        $columns = 5;
                        if (preg_match('/(?:^|\\s)grid-col-(\\d+)(?:\\s|$)/', $class, $m) === 1) {
                            $columns = max(2, min(12, (int) $m[1]));
                        }
                        foreach (array_chunk($images, $columns) as $chunk) {
                            $blocks[] = ['type' => 'ict-columns', 'columns' => $columns, 'images' => $chunk];
                        }

                        return;
                    }
                }
            }
        }

        // Enfold toggle container => ICT accordion element.
        if ($node instanceof DOMElement) {
            $class = ' ' . trim((string) $node->getAttribute('class')) . ' ';
            if (
                $class !== '  '
                && (str_contains($class, ' av_toggle_container ') || str_contains($class, ' togglecontainer '))
            ) {
                $entries = [];

                $xpath = new DOMXPath($node->ownerDocument);
                $sections = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' av_toggle_section ')]", $node);

                if ($sections) {
                    foreach ($sections as $section) {
                        if (!($section instanceof DOMElement)) {
                            continue;
                        }

                        $togglerNode = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' toggler ')]", $section)?->item(0);
                        $contentNode = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' toggle_content ')]", $section)?->item(0);

                        $title = '';
                        if ($togglerNode instanceof DOMNode) {
                            $title = trim($togglerNode->textContent ?? '');
                        }

                        $content = '';
                        if ($contentNode instanceof DOMNode) {
                            $content = trim($GLOBALS['__wp_get_inner_html']($contentNode));
                        }

                        if ($title === '' && $content === '') {
                            continue;
                        }

                        $entries[] = [
                            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                            'content' => $content,
                            'expanded' => false,
                        ];
                    }
                }

                if ($entries !== []) {
                    if (trim($textBuffer) !== '') {
                        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                        $textBuffer = '';
                    }

                    // Expand the first item by default.
                    $entries[0]['expanded'] = true;

                    $blocks[] = [
                        'type' => 'ict-accordion',
                        'title' => '',
                        'description' => '',
                        'displayMode' => 'single',
                        'entries' => $entries,
                    ];
                }

                return;
            }
        }

        // Detect WP galleries and import them as ICT column layout blocks.
        if ($node instanceof DOMElement) {
            $meta = $extractGalleryMeta($node);
            if ($meta !== null) {
                $columns = (int) ($meta['columns'] ?? 0);
                $isLightbox = (bool) ($meta['lightbox'] ?? false);
                $imgs = $node->getElementsByTagName('img');
                if ($imgs->length > 0) {
                    if (trim($textBuffer) !== '') {
                        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                        $textBuffer = '';
                    }

                    $images = [];
                    foreach ($imgs as $img) {
                        if (!($img instanceof DOMElement)) {
                            continue;
                        }
                        $src = $img->getAttribute('src');
                        $alt = $img->getAttribute('alt');

                        // Prefer lightbox href (full image) if available.
                        $parent = $img->parentNode;
                        if ($parent instanceof DOMElement && strtolower($parent->tagName) === 'a') {
                            $href = trim((string) $parent->getAttribute('href'));
                            if ($href !== '' && preg_match('/\\.(?:jpe?g|png|gif|webp)(?:\\?.*)?$/i', $href) === 1) {
                                $src = $href;
                            }
                        }

                        if ($src && strpos($src, 'data:') === false && stripos($src, 'lazy') === false) {
                            $images[] = ['src' => $src, 'alt' => $alt];
                        }
                    }

                    if ($images !== []) {
                        foreach (array_chunk($images, $columns) as $chunk) {
                            $blocks[] = [
                                'type' => $isLightbox ? 'ict-image-gallery' : 'ict-columns',
                                'columns' => $columns,
                                'images' => $chunk,
                            ];
                        }
                        return;
                    }
                }
            }
        }

        if (in_array($tag, ['a', 'span', 'figure', 'picture'])) {
            foreach ($node->childNodes as $child) {
                $walk($child);
            }
            return;
        }

        // Image — flush text buffer, add image block
        if ($tag === 'img') {
            $src = $node->getAttribute('src');
            if ($src === '' || stripos($src, 'lazy') !== false) {
                $src = (string) ($node->getAttribute('data-src') ?: $node->getAttribute('data-lazy-src'));
            }
            $alt = $node->getAttribute('alt');
            $srcset = trim((string) ($node->getAttribute('srcset') ?: $node->getAttribute('data-srcset')));
            if ($srcset !== '') {
                $best = extractBestSrcFromSrcset($srcset);
                if ($best !== '') {
                    $src = $best;
                }
            }
            if ($src && strpos($src, 'data:') === false && stripos($src, 'lazy') === false) {
                if (trim($textBuffer) !== '') {
                    $blocks[] = ['type' => 'text', 'html' => $textBuffer];
                    $textBuffer = '';
                }
                $blocks[] = ['type' => 'image', 'src' => $src, 'alt' => $alt];
            }
            return;
        }

        // Headings — add directly to text buffer
        if (in_array($tag, ['h1','h2','h3','h4','h5','h6'])) {
            $text = trim($node->textContent);
            if ($text !== '') {
                $textBuffer .= '<' . $tag . '>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</' . $tag . '>';
            }
            return;
        }

        // Paragraph — check for nested images first
        if ($tag === 'p') {
            $imgs = $node->getElementsByTagName('img');
            if ($imgs->length > 0) {
                foreach ($node->childNodes as $child) $walk($child);
                return;
            }
            $text = trim($node->textContent);
            if ($text !== '') {
                $textBuffer .= '<p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p>';
            }
            return;
        }

        // Lists
        if ($tag === 'ul' || $tag === 'ol') {
            $inner = '';
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && strtolower($child->nodeName) === 'li') {
                    $t = trim($child->textContent);
                    if ($t !== '') $inner .= '<li>' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</li>';
                }
            }
            if ($inner !== '') $textBuffer .= '<' . $tag . '>' . $inner . '</' . $tag . '>';
            return;
        }

        // Recurse into div, section, article, etc.
        foreach ($node->childNodes as $child) $walk($child);
    };

    // Expose helper for inner HTML to keep the walker diff small.
    $GLOBALS['__wp_get_inner_html'] = $getInnerHtml;

    $body = $doc->getElementsByTagName('body')->item(0);
    if ($body) {
        foreach ($body->childNodes as $child) $walk($child);
    }

    if (trim($textBuffer) !== '') {
        $blocks[] = ['type' => 'text', 'html' => $textBuffer];
    }

    // Merge consecutive text blocks
    $merged = [];
    $buf    = '';
    foreach ($blocks as $b) {
        if ($b['type'] === 'text') {
            $buf .= $b['html'];
        } else {
            if ($buf !== '') { $merged[] = ['type' => 'text', 'html' => $buf]; $buf = ''; }
            $merged[] = $b;
        }
    }
    if ($buf !== '') $merged[] = ['type' => 'text', 'html' => $buf];

    // Fallback: group long consecutive image runs into 5-column rows (common in the migrated site)
    $final = [];
    $imageRun = [];
    $flushImageRun = static function () use (&$final, &$imageRun): void {
        if ($imageRun === []) {
            return;
        }

        // Only auto-group when it's clearly a gallery-like run.
        if (count($imageRun) >= 5) {
            foreach (array_chunk($imageRun, 5) as $chunk) {
                $final[] = ['type' => 'ict-columns', 'columns' => 5, 'images' => $chunk];
            }
        } else {
            foreach ($imageRun as $img) {
                $final[] = ['type' => 'image', 'src' => $img['src'], 'alt' => $img['alt'] ?? ''];
            }
        }

        $imageRun = [];
    };

    foreach ($merged as $b) {
        if (($b['type'] ?? '') === 'image') {
            $imageRun[] = $b;
            continue;
        }

        $flushImageRun();
        $final[] = $b;
    }
    $flushImageRun();

    return $final ?: [['type' => 'text', 'html' => '<p>' . htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8') . '</p>']];
}

function createLandingPage(PDO $pdo, string $title, string $slug, array $blocks): void
{
    $lpId   = randomBin();
    $pageId = randomBin();
    $secId  = randomBin();
    $now    = date('Y-m-d H:i:s.000');
    $languageIds = getAllLanguageIds($pdo);

    // cms_page
    $pdo->prepare("INSERT INTO cms_page (id, version_id, type, locked, created_at) VALUES (?,?,'landingpage',0,?)")
        ->execute([$pageId, VERSION_ID, $now]);
    foreach ($languageIds as $lang) {
        $pdo->prepare("INSERT INTO cms_page_translation (cms_page_id, cms_page_version_id, language_id, name, created_at) VALUES (?,?,?,?,?)")
            ->execute([$pageId, VERSION_ID, $lang, $title, $now]);
    }

    // cms_section
    $pdo->prepare("INSERT INTO cms_section (id, version_id, cms_page_id, cms_page_version_id, position, type, sizing_mode, mobile_behavior, locked, created_at) VALUES (?,?,?,?,0,'default','boxed','wrap',0,?)")
        ->execute([$secId, VERSION_ID, $pageId, VERSION_ID, $now]);

    $position = 0;
    foreach ($blocks as $block) {
        $blockId = randomBin();
        $slotId  = randomBin();

        if (($block['type'] ?? '') === 'ict-banner-slider') {
            $items = $block['items'] ?? [];

            $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'ict-banner-slider','main','20px','20px',NULL,NULL,0,?)")
                ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);

            $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'ict-banner-slider','slider',0,?)")
                ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);

            $sliderItems = [];
            foreach (is_array($items) ? $items : [] as $item) {
                if (!is_array($item) || ($item['src'] ?? '') === '') {
                    continue;
                }

                $mediaId = importMediaFromUrl((string) $item['src'], $title . ' ' . ($position + 1));
                if ($mediaId === null) {
                    $GLOBALS['wpImporterErrors'][] = sprintf('Skipping banner slide because media import failed: %s', normalizeWpUrl((string) $item['src']));
                    continue;
                }

                $sliderItems[] = [
                    'mediaId' => $mediaId,
                    'mediaUrl' => null,
                    'mainTitle' => (string) ($item['mainTitle'] ?? ''),
                    'mainTitleColor' => '#ffffff',
                    'subTitle' => (string) ($item['subTitle'] ?? ''),
                    'subTitleColor' => '#ffffff',
                    'description' => (string) ($item['description'] ?? ''),
                    'descriptionColor' => '#ffffff',
                    'callButtonText' => (string) ($item['callButtonText'] ?? ''),
                    'callButtonNumber' => (string) ($item['callButtonNumber'] ?? ''),
                    'emailButtonText' => (string) ($item['emailButtonText'] ?? ''),
                    'emailButtonAddress' => (string) ($item['emailButtonAddress'] ?? ''),
                    'buttonColor' => '#f15a24',
                    'buttonTextColor' => '#ffffff',
                ];
            }

            $config = json_encode([
                'sliderItems' => ['value' => $sliderItems, 'source' => 'static'],
                'autoTransition' => ['value' => false, 'source' => 'static'],
                'navigationDots' => ['value' => 'none', 'source' => 'static'],
                'navigationArrows' => ['value' => 'outside', 'source' => 'static'],
                'displayMode' => ['value' => 'contain', 'source' => 'static'],
                'verticalAlign' => ['value' => null, 'source' => 'static'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            foreach ($languageIds as $lang) {
                $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                    ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
            }
            persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);

            $position++;
            continue;
        }

        if (($block['type'] ?? '') === 'ict-image-gallery') {
            $columns = (int) ($block['columns'] ?? 0);
            $images  = $block['images'] ?? [];

            $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'ict-image-gallery','main','20px','20px',NULL,NULL,0,?)")
                ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);

            $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'ict-image-gallery','gallery',0,?)")
                ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);

            $galleryItems = [];
            foreach ($images as $img) {
                if (!is_array($img) || ($img['src'] ?? '') === '') {
                    continue;
                }

                $mediaId = importMediaFromUrl((string) $img['src'], $title . ' ' . ($position + 1));
                if ($mediaId === null) {
                    $GLOBALS['wpImporterErrors'][] = sprintf('Skipping gallery item because media import failed: %s', normalizeWpUrl((string) $img['src']));
                    continue;
                }

                $galleryItems[] = [
                    'mediaId' => $mediaId,
                    'title' => (string) ($img['alt'] ?? ''),
                ];
            }

            $config = json_encode([
                'galleryTitle' => ['value' => '', 'source' => 'static'],
                'columns' => ['value' => $columns, 'source' => 'static'],
                'galleryItems' => ['value' => $galleryItems, 'source' => 'static'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            foreach ($languageIds as $lang) {
                $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                    ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
            }
            persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);

            $position++;
            continue;
        }

        if (($block['type'] ?? '') === 'ict-accordion') {
            $blockTitle = (string) ($block['title'] ?? '');
            $blockDescription = (string) ($block['description'] ?? '');
            $displayMode = (string) ($block['displayMode'] ?? 'single');
            $entries = $block['entries'] ?? [];

            $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'ict-accordion','main','20px','20px',NULL,NULL,0,?)")
                ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);

            $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'ict-accordion','content',0,?)")
                ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);

            $config = json_encode([
                'title' => ['value' => $blockTitle, 'source' => 'static'],
                'description' => ['value' => $blockDescription, 'source' => 'static'],
                'displayMode' => ['value' => $displayMode, 'source' => 'static'],
                'entries' => ['value' => array_values(is_array($entries) ? $entries : []), 'source' => 'static'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            foreach ($languageIds as $lang) {
                $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                    ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
            }
            persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);

            $position++;
            continue;
        }

        if (($block['type'] ?? '') === 'ict-columns') {
            $columns = (int) ($block['columns'] ?? 0);
            $images  = $block['images'] ?? [];

            $map = getIctColumnBlockDefinition($columns);
            if ($map === null) {
                // Fallback to plain image blocks when columns are unknown.
                foreach ($images as $img) {
                    if (!is_array($img) || ($img['src'] ?? '') === '') {
                        continue;
                    }

                    $mediaId = importMediaFromUrl($img['src'], $title . ' ' . ($position + 1));
                    if ($mediaId === null) {
                        $GLOBALS['wpImporterErrors'][] = sprintf('Skipping image block because media import failed: %s', normalizeWpUrl((string) $img['src']));
                        continue;
                    }

                    $blockId = randomBin();
                    $slotId = randomBin();

                    $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'image','main','20px','20px',NULL,NULL,0,?)")
                        ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);
                    $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'image','image',0,?)")
                        ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);
                    $config = json_encode([
                        'url'             => ['value' => null,      'source' => 'static'],
                        'media'           => ['value' => $mediaId,  'source' => 'static'],
                        'newTab'          => ['value' => false,     'source' => 'static'],
                        'minHeight'       => ['value' => '340px',   'source' => 'static'],
                        'displayMode'     => ['value' => 'standard','source' => 'static'],
                        'verticalAlign'   => ['value' => null,      'source' => 'static'],
                        'horizontalAlign' => ['value' => null,      'source' => 'static'],
                        'isDecorative'    => ['value' => false,     'source' => 'static'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    foreach ($languageIds as $lang) {
                        $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                            ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
                    }
                    persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);

                    $position++;
                }

                continue;
            }

            $blockType   = $map['blockType'];
            $elementType = $map['elementType'];
            $slotKeys    = $map['slots'];

            $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,?,'main','20px','20px',NULL,NULL,0,?)")
                ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $blockType, $now]);

            // Create one slot per column (remaining slots stay empty)
            foreach ($slotKeys as $i => $slotKey) {
                $slotId = randomBin();
                $img = $images[$i] ?? null;
                $mediaId = null;

                if (is_array($img) && ($img['src'] ?? '') !== '') {
                    $mediaId = importMediaFromUrl($img['src'], $title . ' ' . ($position + 1) . ' ' . ($i + 1));
                }
                if ($mediaId === null && is_array($img) && ($img['src'] ?? '') !== '') {
                    $GLOBALS['wpImporterErrors'][] = sprintf(
                        'ICT columns: media import failed for slot "%s": %s',
                        (string) $slotKey,
                        normalizeWpUrl((string) $img['src'])
                    );
                }

                $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,?,?,0,?)")
                    ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $elementType, $slotKey, $now]);

                $config = null;
                if ($elementType === 'image') {
                    $config = json_encode([
                        'url'             => ['value' => $mediaId === null && is_array($img) ? (string) ($img['src'] ?? null) : null, 'source' => 'static'],
                        'media'           => ['value' => $mediaId,  'source' => 'static'],
                        'newTab'          => ['value' => false,     'source' => 'static'],
                        'minHeight'       => ['value' => '340px',   'source' => 'static'],
                        'displayMode'     => ['value' => 'standard','source' => 'static'],
                        'verticalAlign'   => ['value' => null,      'source' => 'static'],
                        'horizontalAlign' => ['value' => null,      'source' => 'static'],
                        'isDecorative'    => ['value' => false,     'source' => 'static'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $config = json_encode([
                        'media' => ['value' => $mediaId, 'source' => 'static'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                foreach ($languageIds as $lang) {
                    $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                        ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
                }
                persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);
            }

            $position++;
            continue;
        }

        if ($block['type'] === 'image') {
            $mediaId = importMediaFromUrl($block['src'], $title . ' ' . ($position + 1));

            if ($mediaId !== null) {
                $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'image','main','20px','20px',NULL,NULL,0,?)")
                    ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);
                $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'image','image',0,?)")
                    ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);
                $config = json_encode([
                    'url'             => ['value' => null,      'source' => 'static'],
                    'media'           => ['value' => $mediaId,  'source' => 'static'],
                    'newTab'          => ['value' => false,     'source' => 'static'],
                    'minHeight'       => ['value' => '340px',   'source' => 'static'],
                    'displayMode'     => ['value' => 'standard','source' => 'static'],
                    'verticalAlign'   => ['value' => null,      'source' => 'static'],
                    'horizontalAlign' => ['value' => null,      'source' => 'static'],
                    'isDecorative'    => ['value' => false,     'source' => 'static'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $GLOBALS['wpImporterErrors'][] = sprintf('Skipping image block because media import failed: %s', normalizeWpUrl($block['src']));
                continue;
            }
        } else {
            $pdo->prepare("INSERT INTO cms_block (id, version_id, cms_section_id, cms_section_version_id, position, type, section_position, margin_top, margin_bottom, margin_left, margin_right, locked, created_at) VALUES (?,?,?,?,?,'text','main','20px','20px',NULL,NULL,0,?)")
                ->execute([$blockId, VERSION_ID, $secId, VERSION_ID, $position, $now]);
            $pdo->prepare("INSERT INTO cms_slot (id, version_id, cms_block_id, cms_block_version_id, type, slot, locked, created_at) VALUES (?,?,?,?,'text','content',0,?)")
                ->execute([$slotId, VERSION_ID, $blockId, VERSION_ID, $now]);
            $config = json_encode([
                'content'       => ['value' => $block['html'], 'source' => 'static'],
                'verticalAlign' => ['value' => null,           'source' => 'static'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        foreach ($languageIds as $lang) {
            $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
        }
        persistCmsSlotBaseConfig($pdo, $slotId, $config, $now);
        $position++;
    }

    // landing_page
    $pdo->prepare("INSERT INTO landing_page (id, version_id, active, cms_page_id, cms_page_version_id, created_at) VALUES (?,?,1,?,?,?)")
        ->execute([$lpId, VERSION_ID, $pageId, VERSION_ID, $now]);
    foreach ($languageIds as $lang) {
        $pdo->prepare("INSERT INTO landing_page_translation (landing_page_id, landing_page_version_id, language_id, name, url, created_at) VALUES (?,?,?,?,?,?)")
            ->execute([$lpId, VERSION_ID, $lang, $title, $slug, $now]);
    }
    $pdo->prepare("INSERT INTO landing_page_sales_channel (landing_page_id, landing_page_version_id, sales_channel_id) VALUES (?,?,?)")
        ->execute([$lpId, VERSION_ID, SC_ID]);

    // seo_url for both languages
    foreach ($languageIds as $lang) {
        $pdo->prepare("INSERT IGNORE INTO seo_url (id, language_id, sales_channel_id, foreign_key, route_name, path_info, seo_path_info, is_canonical, is_modified, is_deleted, created_at) VALUES (?,?,?,?,'frontend.landing.page',?,?,1,0,0,?)")
            ->execute([randomBin(), $lang, SC_ID, $lpId, '/landingPage/' . bin2hex($lpId), $slug, $now]);
    }
}

function getAllLanguageIds(PDO $pdo): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $ids = [];
    $stmt = $pdo->query('SELECT id FROM language');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'] ?? null;
        if (is_string($id) && strlen($id) === 16) {
            $ids[] = $id;
        }
    }

    $cache = $ids ?: [hex2bin('2fbb5fe2e29a4d70aa5854ce7ce3e20b')]; // system fallback
    return $cache;
}

/**
 * Some Shopware admin screens read `cms_slot.config` (base) while others read
 * `cms_slot_translation.config` (translated). Persist both so the editor
 * reliably shows preselected config values.
 */
function persistCmsSlotBaseConfig(PDO $pdo, string $slotId, string $config, string $now): void
{
    try {
        $pdo->prepare('UPDATE cms_slot SET config = ?, updated_at = ? WHERE id = ? AND version_id = ?')
            ->execute([$config, $now, $slotId, VERSION_ID]);
    } catch (Throwable $e) {
        // Non-fatal: schema can differ between Shopware versions.
    }
}

/**
 * Returns the CMS block/slot definition for ICT column-layout blocks.
 *
 * @return array{blockType:string, elementType:string, slots:array<int,string>}|null
 */
function getIctColumnBlockDefinition(int $columns): ?array
{
    // Names follow the ICTECHcmsBundleElement block registrations.
    // Some blocks use semantic slot keys, others use columnX/colX.
    // Use Shopware's built-in "image" element in the slots so the imported media is preselected
    // (otherwise Admin shows "Replace element" for empty ICT elements).
    $definitions = [
        2 => ['blockType' => 'ict-two-column', 'elementType' => 'image', 'slots' => ['left', 'right']],
        3 => ['blockType' => 'ict-three-column', 'elementType' => 'image', 'slots' => ['left', 'center', 'right']],
        4 => ['blockType' => 'ict-four-column', 'elementType' => 'image', 'slots' => ['left', 'centerLeft', 'centerRight', 'right']],
        5 => ['blockType' => 'ict-five-column', 'elementType' => 'image', 'slots' => ['left', 'leftCenter', 'center', 'rightCenter', 'right']],
        6 => ['blockType' => 'ict-six-column', 'elementType' => 'image', 'slots' => ['left', 'leftCenter', 'centerLeft', 'centerRight', 'rightCenter', 'right']],
        7 => ['blockType' => 'ict-seven-column', 'elementType' => 'image', 'slots' => ['column1','column2','column3','column4','column5','column6','column7']],
        8 => ['blockType' => 'ict-eight-column', 'elementType' => 'image', 'slots' => ['column1','column2','column3','column4','column5','column6','column7','column8']],
        9 => ['blockType' => 'ict-nine-column', 'elementType' => 'image', 'slots' => ['col1','col2','col3','col4','col5','col6','col7','col8','col9']],
        10 => ['blockType' => 'ict-ten-column', 'elementType' => 'image', 'slots' => ['column1','column2','column3','column4','column5','column6','column7','column8','column9','column10']],
        11 => ['blockType' => 'ict-eleven-column', 'elementType' => 'image', 'slots' => ['column1','column2','column3','column4','column5','column6','column7','column8','column9','column10','column11']],
        12 => ['blockType' => 'ict-twelve-column', 'elementType' => 'image', 'slots' => ['column1','column2','column3','column4','column5','column6','column7','column8','column9','column10','column11','column12']],
    ];

    return $definitions[$columns] ?? null;
}

function landingPageExists(PDO $pdo, string $slug): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM landing_page_translation WHERE url=?");
    $stmt->execute([$slug]);
    return (int)$stmt->fetchColumn() > 0;
}

function findImportedCmsMediaIds(PDO $pdo): array
{
    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(cst.config, '$.media.value')) AS media_id
            FROM landing_page lp
            INNER JOIN cms_section cs
                ON cs.cms_page_id = lp.cms_page_id
               AND cs.cms_page_version_id = lp.cms_page_version_id
            INNER JOIN cms_block cb
                ON cb.cms_section_id = cs.id
               AND cb.cms_section_version_id = cs.version_id
            INNER JOIN cms_slot cst_slot
                ON cst_slot.cms_block_id = cb.id
               AND cst_slot.cms_block_version_id = cb.version_id
               AND cst_slot.type = 'image'
            INNER JOIN cms_slot_translation cst
                ON cst.cms_slot_id = cst_slot.id
               AND cst.cms_slot_version_id = cst_slot.version_id
            WHERE lp.version_id = ?
              AND JSON_EXTRACT(cst.config, '$.media.value') IS NOT NULL";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([VERSION_ID]);

    return array_values(array_unique(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN), static function ($id) {
        return is_string($id) && preg_match('/^[0-9a-f]{32}$/i', $id) === 1;
    })));
}

function curlGet(string $url): string|false
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false]);
    $r = curl_exec($ch); curl_close($ch);
    return $r;
}

function randomBin(): string { return random_bytes(16); }

function getShopwareContainer(): ContainerInterface
{
    static $container = null;

    if ($container instanceof ContainerInterface) {
        return $container;
    }

    loadEnvFile(APP_ROOT . '/.env');
    loadEnvFile(APP_ROOT . '/.env.local');

    $classLoader = require APP_ROOT . '/vendor/autoload.php';
    $pluginLoader = null;

    if ((bool) ($_SERVER['COMPOSER_PLUGIN_LOADER'] ?? false)) {
        $pluginLoader = new ComposerPluginLoader($classLoader, null);
    } elseif (trim((string) ($_SERVER['DATABASE_URL'] ?? '')) !== '') {
        $pluginLoader = new DbalKernelPluginLoader($classLoader, null, Kernel::getConnection());
    }

    $kernel = KernelFactory::create(
        environment: $_SERVER['APP_ENV'] ?? 'dev',
        debug: (bool) ($_SERVER['APP_DEBUG'] ?? true),
        classLoader: $classLoader,
        pluginLoader: $pluginLoader,
    );

    $kernel->boot();
    $container = $kernel->getContainer();

    return $container;
}

function loadEnvFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
            $value = stripcslashes(substr($value, 1, -1));
        } elseif (($value[0] ?? '') === '\'' && substr($value, -1) === '\'') {
            $value = substr($value, 1, -1);
        }

        $_SERVER[$name] = $value;
        $_ENV[$name] = $value;
        putenv($name . '=' . $value);
    }
}

function getShopwareContext(): Context
{
    static $context = null;

    if ($context instanceof Context) {
        return $context;
    }

    $context = Context::createCLIContext();

    return $context;
}

function normalizeWpUrl(string $url): string
{
    $url = trim($url);

    if ($url === '') {
        return '';
    }

    if (str_starts_with($url, '//')) {
        return 'https:' . $url;
    }

    if (preg_match('#^https?://#i', $url) === 1) {
        return $url;
    }

    return rtrim(WP_BASE, '/') . '/' . ltrim($url, '/');
}

function importMediaFromUrl(string $url, string $title): ?string
{
    $url = normalizeWpUrl($url);
    if ($url === '') {
        return null;
    }

    $pathInfo = (string) parse_url($url, PHP_URL_PATH);
    $baseName = pathinfo($pathInfo, PATHINFO_FILENAME) ?: $title ?: 'image';
    $fileName = slugify($baseName) . '-' . substr(md5($url), 0, 12);

    // Reuse already-imported media with the same filename to avoid duplicate errors.
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare('SELECT LOWER(HEX(id)) FROM media WHERE file_name = ? LIMIT 1');
        $stmt->execute([$fileName]);
        $existing = $stmt->fetchColumn();
        if (is_string($existing) && $existing !== '') {
            return $existing;
        }
    } catch (Throwable) {
        // fall through
    }

    try {
        $container = getShopwareContainer();
        $context   = getShopwareContext();
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $container->get('media.repository');
        /** @var FileSaver $fileSaver */
        $fileSaver = $container->get(FileSaver::class);
        $download  = downloadRemoteFile($url, $title);
        $mediaFile = new MediaFile(
            $download['path'],
            $download['mimeType'],
            $download['extension'],
            $download['size']
        );
        $mediaId = null;

        $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($mediaRepository, $fileSaver, $mediaFile, $fileName, &$mediaId): void {
            $mediaId = Uuid::randomHex();
            $mediaRepository->create([['id' => $mediaId, 'private' => false]], $scopedContext);
            $fileSaver->persistFileToMedia($mediaFile, $fileName, $mediaId, $scopedContext);
        });

        @unlink($download['path']);

        return $mediaId;
    } catch (Throwable $e) {
        $GLOBALS['wpImporterErrors'][] = sprintf('Image import failed for %s: %s', $url, $e->getMessage());
        return null;
    }
}

function deleteMediaIds(array $mediaIds): int
{
    $mediaIds = array_values(array_unique(array_filter($mediaIds, static function ($id) {
        return is_string($id) && preg_match('/^[0-9a-f]{32}$/i', $id) === 1;
    })));

    if ($mediaIds === []) {
        return 0;
    }

    try {
        $container = getShopwareContainer();
        $context = getShopwareContext();
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $container->get('media.repository');
        $payload = array_map(static fn(string $id): array => ['id' => $id], $mediaIds);

        $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($mediaRepository, $payload): void {
            $mediaRepository->delete($payload, $scopedContext);
        });

        return count($payload);
    } catch (Throwable $e) {
        $GLOBALS['wpImporterErrors'][] = 'Media cleanup failed: ' . $e->getMessage();
        return 0;
    }
}

function downloadRemoteFile(string $url, string $title): array
{
    $tryDownload = static function (string $url, string $tempFile): array {
        $fp = fopen($tempFile, 'wb');
        if ($fp === false) {
            throw new RuntimeException('Unable to create temporary image file.');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            // Some hosts block "unknown" download clients; mimic a real browser.
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0 Safari/537.36',
            CURLOPT_REFERER => WP_BASE . '/',
            CURLOPT_HTTPHEADER => [
                'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            ],
        ]);

        $ok = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $err = (string) curl_error($ch);
        curl_close($ch);
        fclose($fp);

        return [$ok, $httpCode, $contentType, $err];
    };

    $pathInfo = (string) parse_url($url, PHP_URL_PATH);
    $baseName = pathinfo($pathInfo, PATHINFO_FILENAME) ?: $title ?: 'image';
    $fileName = slugify($baseName) . '-' . substr(md5($url), 0, 12);
    $tempFile = (string) tempnam(sys_get_temp_dir(), 'wpimg_');
    [$ok, $httpCode, $contentType, $curlErr] = $tryDownload($url, $tempFile);

    // WP sometimes references resized variants like `image-495x400.jpg` that can be missing.
    // Retry with the "full-size" filename when the first download fails.
    if ($ok === false || $httpCode >= 400 || !is_file($tempFile) || filesize($tempFile) === 0) {
        $altUrl = preg_replace('/-\\d+x\\d+(?=\\.[a-z0-9]+(?:\\?.*)?$)/i', '', $url) ?? $url;
        if ($altUrl !== $url) {
            @unlink($tempFile);
            $tempFile = (string) tempnam(sys_get_temp_dir(), 'wpimg_');
            [$ok, $httpCode, $contentType, $curlErr] = $tryDownload($altUrl, $tempFile);
            $url = $altUrl;
            $pathInfo = (string) parse_url($url, PHP_URL_PATH);
        }
    }

    if ($ok === false || $httpCode >= 400 || !is_file($tempFile) || filesize($tempFile) === 0) {
        @unlink($tempFile);
        $details = trim(sprintf('HTTP %s; curl: %s', $httpCode ?: '0', $curlErr));
        throw new RuntimeException('Unable to download remote image: ' . $url . ($details !== '' ? ' (' . $details . ')' : ''));
    }

    $mimeType = detectMimeType($tempFile, $contentType);
    $extension = detectExtension($tempFile, $mimeType, $pathInfo);

    return [
        'path' => $tempFile,
        'mimeType' => $mimeType,
        'extension' => $extension,
        'size' => filesize($tempFile) ?: 0,
        'fileName' => $fileName,
    ];
}

function detectMimeType(string $path, string $fallback = ''): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $mimeType = finfo_file($finfo, $path);
            finfo_close($finfo);

            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }
    }

    if ($fallback !== '') {
        return trim(explode(';', $fallback)[0]);
    }

    return 'application/octet-stream';
}

function detectExtension(string $path, string $mimeType, string $sourcePath = ''): string
{
    $extension = strtolower((string) pathinfo($sourcePath, PATHINFO_EXTENSION));
    if ($extension !== '') {
        return $extension;
    }

    $mimeMap = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/bmp' => 'bmp',
        'image/tiff' => 'tif',
        'image/x-icon' => 'ico',
    ];

    if (isset($mimeMap[$mimeType])) {
        return $mimeMap[$mimeType];
    }

    $imageType = @exif_imagetype($path);
    $imageExtension = $imageType ? image_type_to_extension($imageType, false) : '';

    return $imageExtension !== '' ? strtolower($imageExtension) : 'jpg';
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? 'image';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'image';
}

function alert(string $type, string $msg): string
{
    $bg = ['success' => '#d4edda', 'info' => '#d1ecf1', 'danger' => '#f8d7da'][$type] ?? '#fff';
    return "<div style='background:{$bg};border:1px solid #ccc;padding:12px 16px;border-radius:4px;margin-bottom:16px;'>{$msg}</div>";
}

function renderImporterErrors(): string
{
    $errors = $GLOBALS['wpImporterErrors'] ?? [];
    if ($errors === []) {
        return '';
    }

    $html = "<div style='background:#fff3cd;border:1px solid #e0b84f;padding:12px 16px;border-radius:4px;margin-bottom:16px;'><strong>Image import warnings</strong><ul style='margin:8px 0 0 18px;'>";

    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }

    $html .= '</ul></div>';

    return $html;
}

// Stats
$totalWP = 0;
$ch = curl_init(WP_API . '?per_page=1&status=publish');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_NOBODY => true, CURLOPT_SSL_VERIFYPEER => false]);
$headers = curl_exec($ch); curl_close($ch);
if (preg_match('/X-WP-Total:\s*(\d+)/i', $headers, $m)) $totalWP = (int)$m[1];
$totalLP = (int)$pdo->query("SELECT COUNT(*) FROM landing_page")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>WP → Shopware CMS Importer</title>
<style>
  body{font-family:Arial,sans-serif;max-width:720px;margin:40px auto;padding:0 20px;color:#333}
  h1{font-size:22px;border-bottom:2px solid #e85630;padding-bottom:8px}
  .btn{background:#e85630;color:#fff;border:none;padding:12px 24px;font-size:15px;border-radius:4px;cursor:pointer}
  .btn:hover{background:#c94520}
  .info-box{background:#f8f8f8;border:1px solid #ddd;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:14px}
</style>
</head>
<body>
<h1>WordPress → Shopware 6 CMS Importer</h1>
<div class="info-box">
  <strong>Source:</strong> <?= WP_BASE ?><br>
  <strong>Total WP pages:</strong> <?= $totalWP ?> (<?= ceil($totalWP / PER_PAGE) ?> batches of <?= PER_PAGE ?>)<br>
  <strong>Imported landing pages:</strong> <?= $totalLP ?> / <?= $totalWP ?><br>
  <strong>Current batch:</strong> <?= $wpPage ?>
</div>
<?= $output ?>
<?= renderImporterErrors() ?>
<form method="post" style="margin-bottom:10px">
  <input type="hidden" name="action" value="clean">
  <button class="btn" type="submit" style="background:#dc3545" onclick="return confirm('Delete all imported pages?')">🗑 Clean All Imported Pages</button>
</form>
<form method="post">
  <input type="hidden" name="action" value="import">
  <input type="hidden" name="wp_page" value="<?= $wpPage ?>">
  <button class="btn" type="submit">▶ Import Batch <?= $wpPage ?> (pages <?= (($wpPage - 1) * PER_PAGE) + 1 ?>–<?= $wpPage * PER_PAGE ?>)</button>
</form>

</body>
</html>
