<?php
/**
 * WordPress News -> NetzpBlog6 importer
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

define('WP_POSTS_API', 'https://mainhattan-wheels.de/wp-json/wp/v2/posts');
define('WP_MEDIA_API', 'https://mainhattan-wheels.de/wp-json/wp/v2/media');
define('WP_BASE_URL', 'https://mainhattan-wheels.de');
define('APP_ROOT', dirname(__DIR__));
define('APP_PUBLIC_URL', 'http://localhost/shopware66101/public');
define('DB_DSN', 'mysql:host=localhost;port=3306;dbname=shopware66101;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', 'admin123');
define('PER_PAGE', 10);
define('BLOG_EMPTY_UUID', '00000000000000000000000000000000');

$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$action = $_POST['action'] ?? '';
$wpPage = max(1, (int) ($_POST['wp_page'] ?? 1));
$output = '';
$GLOBALS['wpBlogImporterErrors'] = [];

if ($action === 'import') {
    set_time_limit(300);
    $output = runImport($pdo, $wpPage);
} elseif ($action === 'clean') {
    $output = cleanImportedBlogs($pdo);
}

function runImport(PDO $pdo, int $pageNumber): string
{
    ensureNewsCategory();

    $response = curlGetJson(WP_POSTS_API . '?per_page=' . PER_PAGE . '&page=' . $pageNumber . '&status=publish&_embed=1');
    if ($response === null || $response === []) {
        return alert('success', 'All news posts imported.');
    }

    $imported = 0;
    $skipped = 0;

    foreach ($response as $post) {
        $slug = (string) ($post['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        if (blogExists($pdo, $slug)) {
            $skipped++;
            continue;
        }

        importWordpressPost($post);
        $imported++;
    }

    $next = $pageNumber + 1;

    return alert('info', "Batch {$pageNumber}: <strong>{$imported} imported</strong>, {$skipped} skipped.")
        . '<form id="auto-form" method="post"><input type="hidden" name="action" value="import"><input type="hidden" name="wp_page" value="' . $next . '"></form>'
        . '<script>setTimeout(()=>document.getElementById("auto-form").submit(),3000);</script>';
}

function importWordpressPost(array $post): void
{
    $container = getShopwareContainer();
    $context = getShopwareContext();
    /** @var EntityRepository $blogRepository */
    $blogRepository = $container->get('s_plugin_netzp_blog.repository');

    $title = decodeText($post['title']['rendered'] ?? '');
    $slug = (string) ($post['slug'] ?? '');
    $contents = (string) ($post['content']['rendered'] ?? '');
    $teaser = decodeText($post['excerpt']['rendered'] ?? '');
    $postDate = substr((string) ($post['date'] ?? date('Y-m-d')), 0, 10);

    $mainImageUrl = getFeaturedImageUrl($post);
    $imageId = $mainImageUrl ? importMediaFromUrl($mainImageUrl, $slug . '-featured') : null;
    $galleryMedia = localizeInlineImages($contents, $slug);

    $categoryId = ensureNewsCategory();
    $blogId = Uuid::randomHex();

    $payload = [
        'id' => $blogId,
        'postdate' => $postDate,
        'showfrom' => $postDate,
        'showuntil' => null,
        'sticky' => false,
        'noindex' => false,
        'categoryid' => $categoryId ?? BLOG_EMPTY_UUID,
                'translations' => buildBlogTranslations($title, $slug, $teaser, $contents, (int) ($post['id'] ?? 0)),
            ];

    if ($imageId !== null) {
        $payload['imageid'] = $imageId;
        $payload['imagepreviewid'] = $imageId;
    }

    if ($galleryMedia !== []) {
        $payload['blogmedia'] = [];
        foreach ($galleryMedia as $index => $mediaId) {
            $payload['blogmedia'][] = [
                'id' => Uuid::randomHex(),
                'mediaId' => $mediaId,
                'number' => $index,
            ];
        }
    }

    $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($blogRepository, $payload): void {
        $blogRepository->create([$payload], $scopedContext);
    });
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

function ensureNewsCategory(): ?string
{
    static $categoryId = null;

    if (is_string($categoryId)) {
        return $categoryId;
    }

    $container = getShopwareContainer();
    $context = getShopwareContext();
    /** @var EntityRepository $categoryRepository */
    $categoryRepository = $container->get('s_plugin_netzp_blog_category.repository');
    $existingId = findNewsCategoryId();

    if ($existingId !== null) {
        $categoryId = $existingId;
        return $categoryId;
    }

    $categoryId = Uuid::randomHex();
    $payload = [[
        'id' => $categoryId,
        'saleschannelid' => BLOG_EMPTY_UUID,
        'customergroupid' => BLOG_EMPTY_UUID,
        'onlyloggedin' => false,
        'includeinrss' => true,
        'translations' => buildCategoryTranslations('News', 'Imported WordPress news'),
    ]];

    $context->scope(Context::SYSTEM_SCOPE, function (Context $scopedContext) use ($categoryRepository, $payload): void {
        $categoryRepository->upsert($payload, $scopedContext);
    });

    return $categoryId;
}

function findNewsCategoryId(): ?string
{
    global $pdo;

    $sql = "SELECT LOWER(HEX(c.id)) AS id
            FROM s_plugin_netzp_blog_category c
            INNER JOIN s_plugin_netzp_blog_category_translation t
                ON t.s_plugin_netzp_blog_category_id = c.id
            WHERE t.language_id = UNHEX(?) AND LOWER(t.title) = 'news'
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([getGermanLanguageId()]);
    $id = $stmt->fetchColumn();

    return is_string($id) ? $id : null;
}

function blogExists(PDO $pdo, string $slug): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM s_plugin_netzp_blog_translation WHERE slug = ?");
    $stmt->execute([$slug]);
    return (int) $stmt->fetchColumn() > 0;
}

function buildBlogTranslations(string $title, string $slug, string $teaser, string $contents, int $wpId): array
{
    $payload = [
        'title' => $title,
        'slug' => $slug,
        'teaser' => limitText($teaser, 255),
        'contents' => $contents,
        'metatitle' => $title,
        'metadescription' => limitText(trim(strip_tags($teaser ?: $contents)), 255),
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

function cleanImportedBlogs(PDO $pdo): string
{
    $stmt = $pdo->prepare("SELECT LOWER(HEX(s_plugin_netzp_blog_id)) FROM s_plugin_netzp_blog_translation WHERE JSON_EXTRACT(custom_fields, '$.wp_source_id') IS NOT NULL");
    $stmt->execute();
    $ids = array_values(array_unique(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN))));
    $mediaIds = findImportedBlogMediaIds($pdo, $ids);

    if ($ids === []) {
        return alert('info', 'No imported WordPress blog posts found.');
    }

    $pdo->beginTransaction();
    try {
        foreach ($ids as $id) {
            $pdo->prepare("DELETE FROM seo_url WHERE foreign_key = UNHEX(?) AND route_name = 'frontend.blog.post'")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog_tag WHERE blog_id = UNHEX(?)")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog_product WHERE blog_id = UNHEX(?)")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog_media WHERE blog_id = UNHEX(?)")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog_item WHERE blog_id = UNHEX(?)")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog_translation WHERE s_plugin_netzp_blog_id = UNHEX(?)")->execute([$id]);
            $pdo->prepare("DELETE FROM s_plugin_netzp_blog WHERE id = UNHEX(?)")->execute([$id]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        return alert('danger', 'Cleanup failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }

    $deletedMedia = deleteMediaIds($mediaIds);

    return alert('success', count($ids) . ' imported WordPress blog posts removed.')
        . ($deletedMedia > 0 ? alert('info', "Deleted {$deletedMedia} imported blog media files.") : '');
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
        $mediaFolderId = getBlogMediaFolderId();

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
        $GLOBALS['wpBlogImporterErrors'][] = sprintf('Image import failed for %s: %s', $url, $e->getMessage());
        return null;
    }
}

function findImportedBlogMediaIds(PDO $pdo, array $blogIds): array
{
    if ($blogIds === []) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($blogIds), '?'));
    $sql = "SELECT DISTINCT LOWER(HEX(media_id)) AS media_id
            FROM (
                SELECT imageid AS media_id
                FROM s_plugin_netzp_blog
                WHERE id IN (" . implode(',', array_fill(0, count($blogIds), 'UNHEX(?)')) . ")
                  AND imageid IS NOT NULL
                UNION ALL
                SELECT imagepreviewid AS media_id
                FROM s_plugin_netzp_blog
                WHERE id IN (" . implode(',', array_fill(0, count($blogIds), 'UNHEX(?)')) . ")
                  AND imagepreviewid IS NOT NULL
                UNION ALL
                SELECT media_id
                FROM s_plugin_netzp_blog_media
                WHERE blog_id IN (" . implode(',', array_fill(0, count($blogIds), 'UNHEX(?)')) . ")
                  AND media_id IS NOT NULL
            ) media_refs";

    $params = array_merge($blogIds, $blogIds, $blogIds);
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
        $GLOBALS['wpBlogImporterErrors'][] = 'Media cleanup failed: ' . $e->getMessage();
        return 0;
    }
}

function getBlogMediaFolderId(): ?string
{
    global $pdo;
    static $folderId = false;

    if ($folderId !== false) {
        return $folderId;
    }

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
        CURLOPT_USERAGENT => 'MainHattan WP Blog Importer',
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
    $tempFile = (string) tempnam(sys_get_temp_dir(), 'wpblog_');
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
        CURLOPT_USERAGENT => 'MainHattan WP Blog Importer',
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
    $errors = $GLOBALS['wpBlogImporterErrors'] ?? [];
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

$totalWpPosts = 0;
$headers = curlGet(WP_POSTS_API . '?per_page=1&status=publish');
if (is_string($headers)) {
    // no-op here, count is fetched below in a lightweight way for simplicity
}
$totalImported = (int) $pdo->query("SELECT COUNT(*) FROM s_plugin_netzp_blog")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WP News -> NetzpBlog6 Importer</title>
    <style>
        body{font-family:Arial,sans-serif;max-width:760px;margin:40px auto;padding:0 20px;color:#333}
        h1{font-size:22px;border-bottom:2px solid #e85630;padding-bottom:8px}
        .btn{background:#e85630;color:#fff;border:none;padding:12px 24px;font-size:15px;border-radius:4px;cursor:pointer}
        .btn:hover{background:#c94520}
        .info-box{background:#f8f8f8;border:1px solid #ddd;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:14px}
    </style>
</head>
<body>
<h1>WordPress News -> Shopware Blog Importer</h1>
<div class="info-box">
    <strong>Source:</strong> <?= WP_BASE_URL ?>/news<br>
    <strong>Imported blog posts:</strong> <?= $totalImported ?><br>
    <strong>Current batch:</strong> <?= $wpPage ?>
</div>
<?= $output ?>
<?= renderImporterErrors() ?>
<form method="post" style="margin-bottom:10px">
    <input type="hidden" name="action" value="clean">
    <button class="btn" type="submit" style="background:#dc3545" onclick="return confirm('Delete imported WordPress blog posts?')">Clean Imported Blogs</button>
</form>
<form method="post">
    <input type="hidden" name="action" value="import">
    <input type="hidden" name="wp_page" value="<?= $wpPage ?>">
    <button class="btn" type="submit">Import News Batch <?= $wpPage ?></button>
</form>
</body>
</html>
