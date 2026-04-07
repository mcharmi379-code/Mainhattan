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
define('LANG_DE',    hex2bin('019D6207880B72E2B9F9E67F38378DEE'));
define('LANG_EN',    hex2bin('2FBB5FE2E29A4D70AA5854CE7CE3E20B'));
define('VERSION_ID', hex2bin('0FA91CE3E96A4BC2BE4BD9CE752C3425'));
define('SC_ID',      hex2bin('019D6208D5477210B86C56A6D53D7917')); // storefront SC (demo_shop)
define('PER_PAGE',   5);

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
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    $deletedMedia = deleteMediaIds($mediaIds);

    return alert('success', '🗑 All imported pages removed. Ready to re-import.')
        . ($deletedMedia > 0 ? alert('info', "Deleted {$deletedMedia} imported CMS media files.") : '');
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
        $slug    = $page['slug'];
        $title   = html_entity_decode(strip_tags($page['title']['rendered']), ENT_QUOTES, 'UTF-8');
        $content = $page['content']['rendered'];

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

    $walk = function(DOMNode $node) use (&$walk, &$blocks, &$textBuffer): void {
        if ($node->nodeType !== XML_ELEMENT_NODE) return;
        $tag = strtolower($node->nodeName);

        // Skip non-content tags entirely
        if (in_array($tag, ['script','style','noscript','iframe','form','input','button','select','textarea','nav'])) {
            return;
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
            $alt = $node->getAttribute('alt');
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

    return $merged ?: [['type' => 'text', 'html' => '<p>' . htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8') . '</p>']];
}

function createLandingPage(PDO $pdo, string $title, string $slug, array $blocks): void
{
    $lpId   = randomBin();
    $pageId = randomBin();
    $secId  = randomBin();
    $now    = date('Y-m-d H:i:s.000');

    // cms_page
    $pdo->prepare("INSERT INTO cms_page (id, version_id, type, locked, created_at) VALUES (?,?,'landingpage',0,?)")
        ->execute([$pageId, VERSION_ID, $now]);
    foreach ([LANG_DE, LANG_EN] as $lang) {
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

        foreach ([LANG_DE, LANG_EN] as $lang) {
            $pdo->prepare("INSERT INTO cms_slot_translation (cms_slot_id, cms_slot_version_id, language_id, config, created_at) VALUES (?,?,?,?,?)")
                ->execute([$slotId, VERSION_ID, $lang, $config, $now]);
        }
        $position++;
    }

    // landing_page
    $pdo->prepare("INSERT INTO landing_page (id, version_id, active, cms_page_id, cms_page_version_id, created_at) VALUES (?,?,1,?,?,?)")
        ->execute([$lpId, VERSION_ID, $pageId, VERSION_ID, $now]);
    foreach ([LANG_DE, LANG_EN] as $lang) {
        $pdo->prepare("INSERT INTO landing_page_translation (landing_page_id, landing_page_version_id, language_id, name, url, created_at) VALUES (?,?,?,?,?,?)")
            ->execute([$lpId, VERSION_ID, $lang, $title, $slug, $now]);
    }
    $pdo->prepare("INSERT INTO landing_page_sales_channel (landing_page_id, landing_page_version_id, sales_channel_id) VALUES (?,?,?)")
        ->execute([$lpId, VERSION_ID, SC_ID]);

    // seo_url for both languages
    foreach ([LANG_DE, LANG_EN] as $lang) {
        $pdo->prepare("INSERT IGNORE INTO seo_url (id, language_id, sales_channel_id, foreign_key, route_name, path_info, seo_path_info, is_canonical, is_modified, is_deleted, created_at) VALUES (?,?,?,?,'frontend.landing.page',?,?,1,0,0,?)")
            ->execute([randomBin(), $lang, SC_ID, $lpId, '/landingPage/' . bin2hex($lpId), $slug, $now]);
    }
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

    try {
        $container = getShopwareContainer();
        $context   = getShopwareContext();
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $container->get('media.repository');
        /** @var FileSaver $fileSaver */
        $fileSaver = $container->get(FileSaver::class);
        $download  = downloadRemoteFile($url, $title);
        $fileName  = $download['fileName'];
        $mediaFile = new MediaFile(
            $download['path'],
            $download['mimeType'],
            $download['extension'],
            $download['size']
        );
        $mediaId  = null;

        $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($mediaRepository, $fileSaver, $mediaFile, $fileName, &$mediaId): void {
            $mediaId = Uuid::randomHex();

            $mediaRepository->create([[
                'id' => $mediaId,
                'private' => false,
            ]], $scopedContext);

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
    $pathInfo = (string) parse_url($url, PHP_URL_PATH);
    $baseName = pathinfo($pathInfo, PATHINFO_FILENAME) ?: $title ?: 'image';
    $fileName = slugify($baseName) . '-' . substr(md5($url), 0, 12);
    $tempFile = (string) tempnam(sys_get_temp_dir(), 'wpimg_');
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
        CURLOPT_USERAGENT => 'MainHattan Shopware WP Importer',
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