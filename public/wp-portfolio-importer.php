<?php
/**
 * WordPress Portfolio -> Shopware Portfolio importer
 *
 * NOTE: This file is a template based on `wp-blog-importer.php`.
 * You must adjust repository service names and DB table names to match
 * your Shopware portfolio plugin (repository/service names often start
 * with `s_plugin_<your_plugin>`). Review `TODO` markers below.
 */

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Adapter\Kernel\KernelFactory;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\ComposerPluginLoader;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Source WordPress APIs — adjust the `portfolio` endpoint if your WP uses a different CPT
define('WP_PORTFOLIO_API', 'https://mainhattan-wheels.de/wp-json/wp/v2/portfolio');
define('WP_MEDIA_API', 'https://mainhattan-wheels.de/wp-json/wp/v2/media');
define('WP_BASE_URL', 'https://mainhattan-wheels.de');
define('APP_ROOT', dirname(__DIR__));
define('APP_PUBLIC_URL', 'http://localhost/shopware/public');
define('DB_DSN', 'mysql:host=127.0.0.1;port=3306;dbname=shopware;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', 'admin123');
define('PER_PAGE', 10);
define('PORTFOLIO_EMPTY_UUID', '00000000000000000000000000000000');

// language/version/saleschannel constants reused from `wp-importer.php`
if (!defined('LANG_DE')) define('LANG_DE', hex2bin('019D6207880B72E2B9F9E67F38378DEE'));
if (!defined('LANG_EN')) define('LANG_EN', hex2bin('2FBB5FE2E29A4D70AA5854CE7CE3E20B'));
if (!defined('VERSION_ID')) define('VERSION_ID', hex2bin('0FA91CE3E96A4BC2BE4BD9CE752C3425'));
if (!defined('SC_ID')) define('SC_ID', hex2bin('019D6208D5477210B86C56A6D53D7917'));

$pdo = null;
// Prefer DATABASE_URL from .env if available (format: mysql://user:pass@host/dbname)
loadEnvFile(APP_ROOT . '/.env');
loadEnvFile(APP_ROOT . '/.env.local');
$databaseUrl = $_SERVER['DATABASE_URL'] ?? $_ENV['DATABASE_URL'] ?? null;
if (is_string($databaseUrl) && $databaseUrl !== '') {
    $parts = parse_url($databaseUrl);
    $dbHost = $parts['host'] ?? '127.0.0.1';
    if ($dbHost === 'localhost') {
        $dbHost = '127.0.0.1';
    }
    $dbPort = $parts['port'] ?? 3306;
    $dbName = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    $dbUser = $parts['user'] ?? DB_USER;
    $dbPass = $parts['pass'] ?? DB_PASS;
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName);
} else {
    $dsn = DB_DSN;
    $dbUser = DB_USER;
    $dbPass = DB_PASS;
}

$pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$action = $_POST['action'] ?? '';
$wpPage = max(1, (int) ($_POST['wp_page'] ?? 1));
$output = '';
$GLOBALS['wpPortfolioImporterErrors'] = [];

if ($action === 'import') {
    set_time_limit(300);
    $output = runImport($pdo, $wpPage);
} elseif ($action === 'clean') {
    $output = cleanImportedPortfolios($pdo);
}

function runImport(PDO $pdo, int $pageNumber): string
{
    ensurePortfolioCategory();

    $response = curlGetJson(WP_PORTFOLIO_API . '?per_page=' . PER_PAGE . '&page=' . $pageNumber . '&status=publish&_embed=1');
    if ($response === null || $response === [] || !is_array($response)) {
        return alert('success', '✅ All portfolios imported!');
    }

    $imported = 0;
    $skipped = 0;

    foreach ($response as $post) {
        $slug = (string) ($post['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        if (portfolioExists($pdo, $slug)) {
            $skipped++;
            continue;
        }

        importWordpressPortfolio($post);
        $imported++;
    }

    // If nothing was imported and all were skipped, or if the response count < PER_PAGE, stop auto-import.
    if (($imported === 0 && $skipped > 0) || count($response) < PER_PAGE) {
        return alert('success', '✅ All portfolios imported!')
            . alert('info', "Last batch: <strong>{$imported} imported</strong>, {$skipped} skipped.");
    }

    $next = $pageNumber + 1;
    return alert('info', "Batch {$pageNumber}: <strong>{$imported} imported</strong>, {$skipped} skipped.<br>Auto-loading next batch in 3 seconds...")
        . '<form id="auto-form" method="post"><input type="hidden" name="action" value="import"><input type="hidden" name="wp_page" value="' . $next . '"></form>'
        . '<script>setTimeout(()=>document.getElementById("auto-form").submit(),3000);</script>';
}

function importWordpressPortfolio(array $post): void
{
    // Create a Shopware landing page (CMS) from the portfolio post, reusing
    // the same approach as `wp-importer.php` so portfolio items behave like pages.
    global $pdo;

    $title = decodeText($post['title']['rendered'] ?? '');
    $slug = (string) ($post['slug'] ?? '');
    $contents = (string) ($post['content']['rendered'] ?? '');

    // Parse WP HTML into blocks (text/image)
    $blocks = parseWpContent($contents);

    // Avoid duplicates
    if (landingPageExists($pdo, $slug)) {
        $GLOBALS['wpPortfolioImporterErrors'][] = 'Skipping existing landing page for slug: ' . $slug;
        return;
    }

    // Create landing page (CMS) using the same SQL flow as wp-importer
    $lpId   = randomBin();
    $pageId = randomBin();
    $secId  = randomBin();
    $now    = date('Y-m-d H:i:s.000');

    // cms_page
    $pdo->prepare("INSERT INTO cms_page (id, version_id, type, locked, created_at) VALUES (?,?,'landingpage',0,?)")
        ->execute([$pageId, VERSION_ID, $now]);
    $langSystem = getSystemLanguageBinary($pdo);
    $langGerman = getGermanLanguageBinary($pdo);
    foreach (array_unique([$langSystem, $langGerman]) as $lang) {
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
                $GLOBALS['wpPortfolioImporterErrors'][] = sprintf('Skipping image block because media import failed: %s', normalizeWpUrl($block['src']));
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

        foreach (array_unique([$langSystem, $langGerman]) as $lang) {
            $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
        }
        $position++;
    }

    // landing_page
    $pdo->prepare("INSERT INTO landing_page (id, version_id, active, cms_page_id, cms_page_version_id, created_at) VALUES (?,?,1,?,?,?)")
        ->execute([$lpId, VERSION_ID, $pageId, VERSION_ID, $now]);
    foreach (array_unique([$langSystem, $langGerman]) as $lang) {
        $pdo->prepare("INSERT INTO landing_page_translation (landing_page_id, landing_page_version_id, language_id, name, url, created_at) VALUES (?,?,?,?,?,?)")
            ->execute([$lpId, VERSION_ID, $lang, $title, $slug, $now]);
    }
    $salesChannelId = getAnySalesChannelBinary($pdo) ?: SC_ID;
    $pdo->prepare("INSERT INTO landing_page_sales_channel (landing_page_id, landing_page_version_id, sales_channel_id) VALUES (?,?,?)")
        ->execute([$lpId, VERSION_ID, $salesChannelId]);

    // seo_url for both languages
    foreach (array_unique([$langSystem, $langGerman]) as $lang) {
        // Use a distinct seo_path_info namespace so we can identify imported
        // landing pages later (wp-portfolio/{slug}).
        $seoPath = 'wp-portfolio/' . $slug;
        $pdo->prepare("INSERT IGNORE INTO seo_url (id, language_id, sales_channel_id, foreign_key, route_name, path_info, seo_path_info, is_canonical, is_modified, is_deleted, created_at) VALUES (?,?,?,?,'frontend.landing.page',?,?,1,0,0,?)")
            ->execute([randomBin(), $lang, $salesChannelId, $lpId, '/landingPage/' . bin2hex($lpId), $seoPath, $now]);
    }
}

function getFeaturedImageUrl(array $post): ?string
{
    $embedded = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
    if (is_string($embedded) && $embedded !== '') {
        return $embedded;
    }

    $featuredMediaId = (int) ($post['featured_media'] ?? 0);
    if ($featuredMediaId <= 0) {
        return null;
    }

    $media = curlGetSingleJson(WP_MEDIA_API . '/' . $featuredMediaId);

    return is_array($media) ? ($media['source_url'] ?? null) : null;
}

function randomBin(): string { return random_bytes(16); }

/**
 * Parse WP HTML into blocks (copied from wp-importer.php)
 */
function parseWpContent(string $html): array
{
    $html = preg_replace('#<script[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace('#<style[^>]*>.*?</style>#is', '', $html);

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $blocks     = [];
    $textBuffer = '';

    $walk = function(DOMNode $node) use (&$walk, &$blocks, &$textBuffer): void {
        if ($node->nodeType !== XML_ELEMENT_NODE) return;
        $tag = strtolower($node->nodeName);

        if (in_array($tag, ['script','style','noscript','iframe','form','input','button','select','textarea','nav'])) return;

        if (in_array($tag, ['a', 'span', 'figure', 'picture'])) {
            foreach ($node->childNodes as $child) $walk($child);
            return;
        }

        if ($tag === 'img') {
            $src = $node->getAttribute('src');
            $alt = $node->getAttribute('alt');
            if ($src && strpos($src, 'data:') === false && stripos($src, 'lazy') === false) {
                if (trim($textBuffer) !== '') { $blocks[] = ['type'=>'text','html'=>$textBuffer]; $textBuffer = ''; }
                $blocks[] = ['type'=>'image','src'=>$src,'alt'=>$alt];
            }
            return;
        }

        if (in_array($tag, ['h1','h2','h3','h4','h5','h6'])) {
            $text = trim($node->textContent);
            if ($text !== '') $textBuffer .= '<' . $tag . '>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</' . $tag . '>';
            return;
        }

        if ($tag === 'p') {
            $imgs = $node->getElementsByTagName('img');
            if ($imgs->length > 0) { foreach ($node->childNodes as $child) $walk($child); return; }
            $text = trim($node->textContent);
            if ($text !== '') $textBuffer .= '<p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p>';
            return;
        }

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

        foreach ($node->childNodes as $child) $walk($child);
    };

    $body = $doc->getElementsByTagName('body')->item(0);
    if ($body) foreach ($body->childNodes as $child) $walk($child);

    if (trim($textBuffer) !== '') $blocks[] = ['type'=>'text','html'=>$textBuffer];

    // Merge consecutive text blocks
    $merged = []; $buf = '';
    foreach ($blocks as $b) {
        if ($b['type'] === 'text') { $buf .= $b['html']; }
        else { if ($buf !== '') { $merged[] = ['type'=>'text','html'=>$buf]; $buf = ''; } $merged[] = $b; }
    }
    if ($buf !== '') $merged[] = ['type'=>'text','html'=>$buf];

    return $merged ?: [['type'=>'text','html'=>'<p>' . htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8') . '</p>']];
}

function landingPageExists(PDO $pdo, string $slug): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM landing_page_translation WHERE url=?");
    $stmt->execute([$slug]);
    return (int)$stmt->fetchColumn() > 0;
}

function getSystemLanguageBinary(PDO $pdo): string
{
    static $id = null;
    if ($id !== null) return $id;
    $stmt = $pdo->query("SELECT id FROM language ORDER BY created_at ASC LIMIT 1");
    $id = $stmt->fetchColumn();
    return $id;
}

function getGermanLanguageBinary(PDO $pdo): string
{
    static $id = null;
    if ($id !== null) return $id;
    $stmt = $pdo->query("SELECT id FROM language WHERE name = 'Deutsch' LIMIT 1");
    $id = $stmt->fetchColumn() ?: getSystemLanguageBinary($pdo);
    return $id;
}

function localizeInlineImages(string &$html, string $slug): array
{
    $galleryMediaIds = [];

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $body = $doc->getElementsByTagName('body')->item(0);
    if (!$body) {
        return [];
    }

    $images = $body->getElementsByTagName('img');
    $replacements = [];
    foreach ($images as $img) {
        $src = trim((string) $img->getAttribute('src'));
        if ($src === '' || str_starts_with($src, 'data:')) {
            continue;
        }
        $replacements[] = $img;
    }

    foreach ($replacements as $index => $img) {
        $src = normalizeWpUrl((string) $img->getAttribute('src'));
        $mediaId = importMediaFromUrl($src, $slug . '-inline-' . $index);
        if ($mediaId === null) {
            continue;
        }

        $galleryMediaIds[] = $mediaId;
        $img->setAttribute('src', mediaUrlById($mediaId) ?? $src);
    }

    $html = innerHtml($body);

    return array_values(array_unique($galleryMediaIds));
}

function ensurePortfolioCategory(): ?string
{
    // No-op: importer creates landing pages directly without relying on a
    // third-party portfolio/category plugin. Return null to indicate no
    // plugin category was created.
    return null;
}

function findPortfolioCategoryId(): ?string
{
    return null;
}

function portfolioExists(PDO $pdo, string $slug): bool
{
    try {
        // Check landing_page_translation.url
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM landing_page_translation WHERE url = ?");
        $stmt->execute([$slug]);
        if ((int) $stmt->fetchColumn() > 0) return true;

        // Also check seo_url for our importer namespace
        $seo = 'wp-portfolio/' . $slug;
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM seo_url WHERE seo_path_info = ? AND route_name = 'frontend.landing.page'");
        $stmt2->execute([$seo]);
        return (int) $stmt2->fetchColumn() > 0;
    } catch (Throwable $e) {
        $GLOBALS['wpPortfolioImporterErrors'][] = 'Unable to check existing portfolio entries: ' . $e->getMessage();
        return false;
    }
}

function buildPortfolioTranslations(string $title, string $slug, string $excerpt, string $contents, int $wpId): array
{
    $payload = [
        'title' => $title,
        'slug' => $slug,
        'teaser' => limitText($excerpt, 255),
        'contents' => $contents,
        'metatitle' => $title,
        'metadescription' => limitText(trim(strip_tags($excerpt ?: $contents)), 255),
        'customFields' => [
            'wp_source_slug' => $slug,
            'wp_source_id' => $wpId,
        ],
    ];

    $translations = [
        getSystemLanguageId() => $payload,
    ];

    $germanId = getGermanLanguageId();
    if ($germanId !== getSystemLanguageId()) {
        $translations[$germanId] = $payload;
    }

    return $translations;
}

function buildCategoryTranslations(string $title, string $teaser): array
{
    $payload = ['title' => $title, 'teaser' => $teaser];
    $translations = [
        getSystemLanguageId() => $payload,
    ];

    $germanId = getGermanLanguageId();
    if ($germanId !== getSystemLanguageId()) {
        $translations[$germanId] = $payload;
    }

    return $translations;
}

function getSystemLanguageId(): string
{
    static $id = null;
    if (is_string($id)) {
        return $id;
    }

    global $pdo;
    $stmt = $pdo->query("SELECT LOWER(HEX(id)) FROM language ORDER BY created_at ASC LIMIT 1");
    $id = (string) $stmt->fetchColumn();

    return $id;
}

function getGermanLanguageId(): string
{
    static $id = null;
    if (is_string($id)) {
        return $id;
    }

    global $pdo;
    $stmt = $pdo->query("SELECT LOWER(HEX(id)) FROM language WHERE name = 'Deutsch' LIMIT 1");
    $id = (string) ($stmt->fetchColumn() ?: getSystemLanguageId());

    return $id;
}

function cleanImportedPortfolios(PDO $pdo): string
{
    // Identify landing pages created by this importer via the seo_url namespace
    $stmt = $pdo->prepare("SELECT LOWER(HEX(foreign_key)) FROM seo_url WHERE seo_path_info LIKE 'wp-portfolio/%' AND route_name = 'frontend.landing.page'");
    $stmt->execute();
    $ids = array_values(array_unique(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN))));

    if ($ids === []) {
        // No seo namespace entries found — attempt to fall back and delete
        // landing pages that match current WP portfolio slugs (covers older
        // imports that didn't set seo_path_info).
        try {
            $wpList = curlGetJson(WP_PORTFOLIO_API . '?per_page=100');
            $slugs = [];
            if (is_array($wpList)) {
                foreach ($wpList as $p) {
                    if (isset($p['slug']) && is_string($p['slug'])) $slugs[] = $p['slug'];
                }
            }
            if ($slugs === []) {
                return alert('info', 'No imported WordPress portfolio landing pages found.');
            }

            // Find landing_page ids for these slugs
            $place = implode(',', array_fill(0, count($slugs), '?'));
            $stmt = $pdo->prepare('SELECT LOWER(HEX(lp.id)) AS id, LOWER(HEX(lp.cms_page_id)) AS cms_page_id FROM landing_page lp INNER JOIN landing_page_translation lpt ON lpt.landing_page_id = lp.id WHERE lpt.url IN (' . $place . ')');
            $stmt->execute($slugs);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === []) {
                return alert('info', 'No imported WordPress portfolio landing pages found.');
            }

            $ids = array_map(fn($r)=>$r['id'],$rows);
        } catch (Throwable $e) {
            return alert('info', 'No imported WordPress portfolio landing pages found.');
        }
    }

    // Map landing_page ids to cms_page ids so we can remove CMS entries
    $placeholders = implode(',', array_fill(0, count($ids), 'UNHEX(?)'));
    $sql = "SELECT LOWER(HEX(id)) AS id, LOWER(HEX(cms_page_id)) AS cms_page_id FROM landing_page WHERE id IN (" . $placeholders . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdo->beginTransaction();
    try {
        foreach ($rows as $row) {
            $lpId = $row['id'];
            $cmsPageId = $row['cms_page_id'] ?? null;

            // Delete seo urls
            $pdo->prepare("DELETE FROM seo_url WHERE foreign_key = UNHEX(?) AND route_name = 'frontend.landing.page'")->execute([$lpId]);
            // Delete landing page sales channel and translations
            $pdo->prepare("DELETE FROM landing_page_sales_channel WHERE landing_page_id = UNHEX(?)")->execute([$lpId]);
            $pdo->prepare("DELETE FROM landing_page_translation WHERE landing_page_id = UNHEX(?)")->execute([$lpId]);
            $pdo->prepare("DELETE FROM landing_page WHERE id = UNHEX(?)")->execute([$lpId]);

            // Remove the cms_page (this will remove sections/blocks/slots if foreign keys cascade)
            if ($cmsPageId !== null && $cmsPageId !== '') {
                $pdo->prepare("DELETE FROM cms_page WHERE id = UNHEX(?)")->execute([$cmsPageId]);
            }
        }

        // Find media referenced by the removed cms pages and delete them
        $mediaIds = findImportedPortfolioMediaIds($pdo, $ids);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        return alert('danger', 'Cleanup failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }

    $deletedMedia = deleteMediaIds($mediaIds);

    return alert('success', count($rows) . ' imported WordPress portfolio landing pages removed.')
        . ($deletedMedia > 0 ? alert('info', "Deleted {$deletedMedia} imported portfolio media files.") : '');
}

function importMediaFromUrl(string $url, string $title): ?string
{
    $url = normalizeWpUrl($url);
    if ($url === '') {
        return null;
    }

    try {
        $container = getShopwareContainer();
        $context = getShopwareContext();
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $container->get('media.repository');
        /** @var EntityRepository $defaultFolderRepository */
        $defaultFolderRepository = $container->get('media_default_folder.repository');
        /** @var FileSaver $fileSaver */
        $fileSaver = $container->get(FileSaver::class);

        $download = downloadRemoteFile($url, $title);
        $mediaId = Uuid::randomHex();
        $mediaFolderId = getPortfolioMediaFolderId();

        $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($mediaRepository, $mediaId, $mediaFolderId, $download, $fileSaver): void {
            $payload = ['id' => $mediaId, 'private' => false];
            if ($mediaFolderId !== null) {
                $payload['mediaFolderId'] = $mediaFolderId;
            }

            $mediaRepository->create([$payload], $scopedContext);
            $fileSaver->persistFileToMedia(
                new MediaFile($download['path'], $download['mimeType'], $download['extension'], $download['size']),
                $download['fileName'],
                $mediaId,
                $scopedContext
            );
        });

        @unlink($download['path']);

        return $mediaId;
    } catch (Throwable $e) {
        // If a media file with the same name already exists, try to find
        // that existing media and reuse its id instead of failing.
        $msg = $e->getMessage();
        $foundId = null;
        if (isset($download) && is_array($download) && (str_contains($msg, 'already exists') || str_contains($msg, 'A file with the name'))) {
            try {
                global $pdo;
                $fileWithExt = $download['fileName'] . '.' . $download['extension'];
                // Try to find by exact file_name
                $stmt = $pdo->prepare("SELECT LOWER(HEX(id)) FROM media WHERE file_name = ? LIMIT 1");
                $stmt->execute([$fileWithExt]);
                $foundId = $stmt->fetchColumn() ?: null;

                if ($foundId === null) {
                    // Try to find by path ending with the filename
                    $stmt2 = $pdo->prepare("SELECT LOWER(HEX(id)) FROM media WHERE path LIKE ? LIMIT 1");
                    $stmt2->execute(['%' . $fileWithExt]);
                    $foundId = $stmt2->fetchColumn() ?: null;
                }
            } catch (Throwable $inner) {
                // ignore and fall through to original error
            }
        }

        if (isset($download['path']) && is_file($download['path'])) {
            @unlink($download['path']);
        }

        if ($foundId !== null) {
            return $foundId;
        }

        $GLOBALS['wpPortfolioImporterErrors'][] = sprintf('Image import failed for %s: %s', $url, $msg);
        return null;
    }
}

function findImportedPortfolioMediaIds(PDO $pdo, array $portfolioIds): array
{
    if ($portfolioIds === []) {
        return [];
    }

    // Fetch media IDs used in cms_slot_translation.config for landing pages
    // matching the given landing_page ids (cms_page association).
    $placeholders = implode(',', array_fill(0, count($portfolioIds), '?'));

    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(cst.config, '$.media.value')) AS media_id
            FROM landing_page lp
            INNER JOIN cms_section cs ON cs.cms_page_id = lp.cms_page_id AND cs.cms_page_version_id = lp.cms_page_version_id
            INNER JOIN cms_block cb ON cb.cms_section_id = cs.id AND cb.cms_section_version_id = cs.version_id
            INNER JOIN cms_slot cst_slot ON cst_slot.cms_block_id = cb.id AND cst_slot.cms_block_version_id = cb.version_id AND cst_slot.type = 'image'
            INNER JOIN cms_slot_translation cst ON cst.cms_slot_id = cst_slot.id AND cst.cms_slot_version_id = cst_slot.version_id
            WHERE lp.id IN (" . implode(',', array_fill(0, count($portfolioIds), 'UNHEX(?)')) . ")";

    $params = $portfolioIds;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return array_values(array_unique(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN), static function ($id) {
        return is_string($id) && preg_match('/^[0-9a-f]{32}$/i', $id) === 1;
    })));
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
        $GLOBALS['wpPortfolioImporterErrors'][] = 'Media cleanup failed: ' . $e->getMessage();
        return 0;
    }
}

function getPortfolioMediaFolderId(): ?string
{
    global $pdo;
    static $folderId = false;

    if ($folderId !== false) {
        return $folderId;
    }

    // Keep default folder id as in blog importer or adjust to your folder id
    $sql = "SELECT LOWER(HEX(mf.id)) AS id
            FROM media_folder mf
            INNER JOIN media_default_folder mdf ON mdf.id = mf.default_folder_id
            WHERE LOWER(HEX(mdf.id)) = LOWER(?) LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['59D4F5B90E944D44B997ED0A60804034']);
        $folderId = $stmt->fetchColumn() ?: null;

    return $folderId;
}

function mediaUrlById(string $mediaId): ?string
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT path FROM media WHERE id = UNHEX(?) LIMIT 1");
    $stmt->execute([$mediaId]);
    $path = $stmt->fetchColumn();
    if (!is_string($path) || $path === '') {
        return null;
    }

    return '/media/' . ltrim($path, '/');
}

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

function getShopwareContext(): Context
{
    static $context = null;
    if ($context instanceof Context) {
        return $context;
    }
    $context = Context::createCLIContext();
    return $context;
}

function getAnySalesChannelBinary(PDO $pdo): ?string
{
    static $id = null;
    if ($id !== null) return $id;

    $stmt = $pdo->query("SELECT id FROM sales_channel LIMIT 1");
    $id = $stmt->fetchColumn();
    return $id ?: null;
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
        if (($value[0] ?? '') === '"' && str_ends_with($value, '"')) {
            $value = stripcslashes(substr($value, 1, -1));
        } elseif (($value[0] ?? '') === '\'' && str_ends_with($value, '\'')) {
            $value = substr($value, 1, -1);
        }
        $_SERVER[$name] = $value;
        $_ENV[$name] = $value;
        putenv($name . '=' . $value);
    }
}

function curlGetJson(string $url): ?array
{
    $result = curlGet($url);
    if ($result === false) {
        return null;
    }

    $decoded = json_decode($result, true);
    return is_array($decoded) ? $decoded : null;
}

function curlGetSingleJson(string $url): ?array
{
    $decoded = curlGetJson($url);
    return is_array($decoded) ? $decoded : null;
}

function curlGet(string $url): string|false
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'WP Portfolio Importer',
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function downloadRemoteFile(string $url, string $title): array
{
    $pathInfo = (string) parse_url($url, PHP_URL_PATH);
    $baseName = pathinfo($pathInfo, PATHINFO_FILENAME) ?: $title ?: 'image';
    $fileName = slugify($baseName) . '-' . substr(md5($url), 0, 12);
    $tempFile = (string) tempnam(sys_get_temp_dir(), 'wpport_');
    $fp = fopen($tempFile, 'wb');
    if ($fp === false) {
        throw new RuntimeException('Unable to create temp file.');
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'WP Portfolio Importer',
    ]);
    $ok = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    fclose($fp);

    if ($ok === false || $httpCode >= 400 || !is_file($tempFile) || filesize($tempFile) === 0) {
        @unlink($tempFile);
        throw new RuntimeException('Unable to download remote image: ' . $url);
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

    return $fallback !== '' ? trim(explode(';', $fallback)[0]) : 'application/octet-stream';
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
    ];

    return $mimeMap[$mimeType] ?? 'jpg';
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

    return rtrim(WP_BASE_URL, '/') . '/' . ltrim($url, '/');
}

function innerHtml(DOMNode $node): string
{
    $html = '';
    foreach ($node->childNodes as $child) {
        $html .= $node->ownerDocument->saveHTML($child);
    }
    return $html;
}

function decodeText(string $html): string
{
    return trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? 'item';
    $value = trim($value, '-');
    return $value !== '' ? $value : 'item';
}

function limitText(string $value, int $limit): string
{
    $value = trim($value);
    return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit - 1) : $value;
}

function alert(string $type, string $message): string
{
    $bg = ['success' => '#d4edda', 'info' => '#d1ecf1', 'danger' => '#f8d7da'][$type] ?? '#fff';
    return "<div style='background:{$bg};border:1px solid #ccc;padding:12px 16px;border-radius:4px;margin-bottom:16px;'>{$message}</div>";
}

function renderImporterErrors(): string
{
    $errors = $GLOBALS['wpPortfolioImporterErrors'] ?? [];
    if ($errors === []) {
        return '';
    }

    $html = "<div style='background:#fff3cd;border:1px solid #e0b84f;padding:12px 16px;border-radius:4px;margin-bottom:16px;'><strong>Import warnings</strong><ul style='margin:8px 0 0 18px;'>";
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    $html .= '</ul></div>';

    return $html;
}

$totalImported = 0;
try {
    // If the plugin table doesn't exist (plugin not installed), treat as zero imported.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    $stmt->execute(['s_plugin_portfolio']);
    $exists = (int) $stmt->fetchColumn() > 0;
    if ($exists) {
        $totalImported = (int) $pdo->query("SELECT COUNT(*) FROM s_plugin_portfolio")->fetchColumn();
    } else {
        $totalImported = 0;
    }
} catch (Throwable $e) {
    // Table does not exist or other DB issue; treat as zero imported and continue.
    $GLOBALS['wpPortfolioImporterErrors'][] = 'Unable to read imported portfolio count: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WP Portfolio -> Shopware Portfolio Importer</title>
    <style>
        body{font-family:Arial,sans-serif;max-width:760px;margin:40px auto;padding:0 20px;color:#333}
        h1{font-size:22px;border-bottom:2px solid #e85630;padding-bottom:8px}
        .btn{background:#e85630;color:#fff;border:none;padding:12px 24px;font-size:15px;border-radius:4px;cursor:pointer}
        .btn:hover{background:#c94520}
        .info-box{background:#f8f8f8;border:1px solid #ddd;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:14px}
    </style>
</head>
<body>
<h1>WordPress Portfolio -> Shopware Portfolio Importer</h1>
<div class="info-box">
    <strong>Source:</strong> <?= WP_BASE_URL ?>/<br>
    <strong>Imported portfolio items:</strong> <?= $totalImported ?><br>
    <strong>Current batch:</strong> <?= $wpPage ?>
</div>
<?= $output ?>
<?= renderImporterErrors() ?>
<form method="post" style="margin-bottom:10px">
    <input type="hidden" name="action" value="clean">
    <button class="btn" type="submit" style="background:#dc3545" onclick="return confirm('Delete imported WordPress portfolio items?')">Clean Imported Portfolios</button>
</form>
<form method="post">
    <input type="hidden" name="action" value="import">
    <input type="hidden" name="wp_page" value="<?= $wpPage ?>">
    <button class="btn" type="submit">Import Portfolio Batch <?= $wpPage ?></button>
</form>
</body>
</html>
