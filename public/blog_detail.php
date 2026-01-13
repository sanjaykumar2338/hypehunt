<?php
require_once __DIR__ . '/../includes/functions.php';

$base = rtrim(BASE_URL, '/');
$assetBase = $base . '/assets';
$publicBase = $base;
if (substr($base, -7) === '/public') {
    $assetBase = $base . '/../assets';
    $publicBase = substr($base, 0, -7);
}

$slug = trim($_GET['slug'] ?? '', '/');

// Fallback: derive slug from path (/blog/{slug})
if ($slug === '' && !empty($_SERVER['REQUEST_URI'])) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));
    $lastSegment = $segments ? end($segments) : '';
    if ($lastSegment && !in_array($lastSegment, ['blog', 'blog.php', 'blog_detail.php'], true)) {
        $slug = $lastSegment;
    }
}

$build_image_url = function (?string $path, string $publicBase, ?string $fallback = null): ?string {
    $path = trim((string) ($path ?? ''));
    if ($path === '') {
        return $fallback;
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $clean = ltrim($path, '/');
    if (strpos($clean, 'public/') === 0) {
        $clean = substr($clean, 7);
    }
    return rtrim($publicBase, '/') . '/' . $clean;
};

$post = $slug !== '' ? get_blog_post_by_slug($slug) : null;
$fallbackImage = $assetBase . '/images/Overlay+Blur (2).png';
$articleUrl = $publicBase . '/blog/' . ($post['slug'] ?? '');

if ($post) {
    $summary = $post['meta_description']
        ?: ($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160) . '...');
    $seo_title = $post['meta_title'] ?: ($post['title'] . ' | HypeHunt Blog');
    $seo_description = $summary;
    $seo_canonical = $post['canonical_url'] ?: $articleUrl;
    $seo_og_title = $post['og_title'] ?: $post['title'];
    $seo_og_description = $post['og_description'] ?: $summary;
    $seo_og_image = $build_image_url($post['og_image'] ?? '', $publicBase, $assetBase . '/images/Group 1000002315.png');
    $seo_twitter_title = $post['twitter_title'] ?: $seo_og_title;
    $seo_twitter_description = $post['twitter_description'] ?: $summary;
    $seo_twitter_image = $build_image_url($post['twitter_image'] ?? '', $publicBase, $seo_og_image);
    $seo_type = 'article';
} else {
    http_response_code(404);
    $seo_title = 'Article Not Found | HypeHunt Blog';
    $seo_description = 'The article you were looking for is not available. Discover the latest sneaker drops and guides on our blog.';
    $seo_canonical = $publicBase . '/blog.php';
    $seo_og_title = $seo_title;
    $seo_og_description = $seo_description;
    $seo_og_image = $assetBase . '/images/Group 1000002315.png';
    $seo_type = 'article';
}

require_once __DIR__ . '/../includes/site_header.php';

if ($post) {
    $postDate = $post['published_at'] ? date('M d, Y', strtotime($post['published_at'])) : date('M d, Y', strtotime($post['created_at']));
    $readingMinutes = max(1, ceil(str_word_count(strip_tags($post['content'])) / 220));
    $tags = array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? ''))));
    $featuredImgUrl = $build_image_url($post['featured_image'] ?? '', $publicBase, null);
    $hasImage = $featuredImgUrl !== null;

    // Related posts
    $pdo = get_pdo();
    $relatedStmt = $pdo->prepare(
        'SELECT * FROM blog_posts
         WHERE status = "published" AND id != :id
         ORDER BY COALESCE(published_at, created_at) DESC
         LIMIT 3'
    );
    $relatedStmt->execute(['id' => $post['id']]);
    $relatedPosts = $relatedStmt->fetchAll();
} else {
    $relatedPosts = [];
}
?>

<style>
    .article-page {
        background: #050509;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .article-page::before,
    .article-page::after {
        content: '';
        position: absolute;
        pointer-events: none;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.4;
    }

    .article-page::before {
        width: 520px;
        height: 520px;
        background: radial-gradient(circle at 30% 30%, rgba(61, 255, 142, 0.25), transparent 60%);
        top: -160px;
        left: -140px;
    }

    .article-page::after {
        width: 620px;
        height: 620px;
        background: radial-gradient(circle at 70% 20%, rgba(124, 92, 255, 0.14), transparent 60%);
        top: 120px;
        right: -220px;
    }

    .article-shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 140px 20px 110px;
        color: #e5e7eb;
        position: relative;
        z-index: 1;
    }

    .breadcrumb {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 13px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .breadcrumb a {
        color: #3DFF8E;
        text-decoration: none;
        font-weight: 700;
    }

    .article-hero {
        background: radial-gradient(circle at 20% 20%, rgba(61, 255, 142, 0.08), transparent 55%), #0b0c12;
        border: 1px solid #252529;
        border-radius: 18px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 42px rgba(0, 0, 0, 0.45);
        margin-bottom: 26px;
    }

    .article-hero::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 22px;
        right: -60px;
        top: -60px;
        transform: rotate(18deg);
        opacity: 0.35;
    }

    .article-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .article-hero.has-image .article-hero-grid {
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
    }

    @media (max-width: 900px) {
        .article-hero.has-image .article-hero-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(61, 255, 142, 0.4);
        background: rgba(61, 255, 142, 0.12);
        color: #3DFF8E;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .article-title {
        font-size: clamp(32px, 4.6vw, 48px);
        margin: 16px 0 12px;
        color: #fff;
        line-height: 1.15;
        letter-spacing: -0.4px;
    }

    .article-dek {
        margin: 0;
        color: #9ca3af;
        font-size: 17px;
        line-height: 1.65;
        max-width: 680px;
    }

    .meta-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        color: #a0aec0;
        font-size: 14px;
        margin-top: 12px;
    }

    .meta-row .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3DFF8E;
    }

    .meta-row .time-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
    }

    .hero-image {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid #252529;
        min-height: 320px;
        background: #0f1117;
        margin-top: 6px;
    }

    .hero-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.45s ease;
    }

    .hero-image::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.45));
    }

    .hero-image:hover img {
        transform: scale(1.05);
    }

    .tag-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    .pill {
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(61, 255, 142, 0.08));
        color: #d1d5db;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .pill-light {
        background: rgba(61, 255, 142, 0.12);
        border-color: rgba(61, 255, 142, 0.4);
        color: #3DFF8E;
    }

    .article-body {
        background: #0b0c12;
        border: 1px solid #252529;
        border-radius: 16px;
        padding: 26px 24px;
        margin: 0 auto 22px;
        box-shadow: 0 10px 34px rgba(0, 0, 0, 0.35);
        line-height: 1.8;
        color: #d1d5db;
        font-size: 16px;
    }

    .article-body h2,
    .article-body h3 {
        color: #fff;
        margin-top: 26px;
        margin-bottom: 12px;
        font-weight: 800;
    }

    .article-body p {
        margin: 0 0 16px;
    }

    .article-body a {
        color: #3DFF8E;
        font-weight: 700;
    }

    .article-body ul,
    .article-body ol {
        padding-left: 20px;
        margin: 0 0 16px;
    }

    .article-body blockquote {
        margin: 18px 0;
        padding: 14px 18px;
        border-left: 3px solid #3DFF8E;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 12px;
        color: #e5e7eb;
        font-style: italic;
    }

    .article-footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        padding: 16px 0 4px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 28px;
    }

    .copy-link {
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .copy-link:hover {
        background: #3DFF8E;
        color: #050509;
        border-color: #3DFF8E;
    }

    .related-section {
        margin-top: 40px;
    }

    .related-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .related-header h4 {
        margin: 0;
        color: #fff;
        font-size: 20px;
        letter-spacing: -0.1px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .related-card {
        background: #0b0c12;
        border: 1px solid #252529;
        border-radius: 14px;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .related-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
        border-color: rgba(61, 255, 142, 0.35);
    }

    .related-media {
        height: 160px;
        overflow: hidden;
        position: relative;
    }

    .related-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .related-card:hover .related-media img {
        transform: scale(1.05);
    }

    .related-body {
        padding: 16px;
    }

    .related-body h5 {
        margin: 8px 0 8px;
        font-size: 17px;
        color: #f8fafc;
        line-height: 1.35;
    }

    .related-body p {
        margin: 0 0 10px;
        color: #94a3b8;
        font-size: 14px;
        line-height: 1.55;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #3DFF8E;
        font-weight: 700;
        margin-top: 6px;
    }

    .empty-article {
        background: #0b0c12;
        border: 1px solid #252529;
        border-radius: 16px;
        padding: 60px;
        text-align: center;
        color: #cbd5e1;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
    }
</style>

<div class="article-page">
    <div class="article-shell">
        <?php if (!$post): ?>
            <div class="empty-article">
                <h2 style="color: #fff; margin-bottom: 12px;">Article not found</h2>
                <p style="margin-bottom: 18px;">The story you’re looking for isn’t available. Explore more release news and guides below.</p>
                <a class="back-link" href="<?php echo $publicBase; ?>/blog.php">
                    <i class="fa-solid fa-arrow-left"></i> Back to the blog
                </a>
            </div>
        <?php else: ?>
            <div class="breadcrumb">
                <a href="<?php echo $publicBase; ?>/blog.php">Blog</a>
                <span>/</span>
                <span><?php echo escape_html($post['title']); ?></span>
            </div>

            <div class="article-hero <?php echo !empty($hasImage) ? 'has-image' : ''; ?>">
                <div class="article-hero-grid">
                    <div>
                        <div class="eyebrow">HypeHunt Article</div>
                        <h1 class="article-title"><?php echo escape_html($post['title']); ?></h1>
                        <p class="article-dek"><?php echo escape_html($post['excerpt'] ?: 'Drop intelligence, legit checks, and market notes from the HypeHunt team.'); ?></p>
                        <div class="meta-row">
                            <span class="dot"></span>
                            <span><?php echo escape_html($post['author'] ?: 'HypeHunt Team'); ?></span>
                            <span>•</span>
                            <span><?php echo $postDate; ?></span>
                            <span>•</span>
                            <span class="time-chip"><i class="fa-regular fa-clock"></i> <?php echo $readingMinutes; ?> min read</span>
                        </div>
                        <?php if (!empty($tags)): ?>
                            <div class="tag-row">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="pill pill-light"><?php echo escape_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($hasImage)): ?>
                        <div class="hero-image">
                            <img src="<?php echo escape_html($featuredImgUrl); ?>" alt="<?php echo escape_html($post['title']); ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <article class="article-body">
                <?php echo $post['content']; ?>
            </article>

            <div class="article-footer">
                <?php if (!empty($tags)): ?>
                    <div class="tag-row">
                        <?php foreach ($tags as $tag): ?>
                            <span class="pill"><?php echo escape_html($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <button class="copy-link" type="button" data-url="<?php echo escape_html($articleUrl); ?>">Copy link</button>
            </div>

            <?php if (!empty($relatedPosts)): ?>
                <section class="related-section">
                    <div class="related-header">
                        <h4>Keep reading</h4>
                        <a class="back-link" href="<?php echo $publicBase; ?>/blog.php">
                            View all posts <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="related-grid">
                        <?php foreach ($relatedPosts as $related): ?>
                            <?php
                                $relatedImg = $build_image_url($related['featured_image'] ?? '', $publicBase, $fallbackImage);
                                $relatedExcerpt = $related['excerpt'] ?: substr(strip_tags($related['content']), 0, 90) . '...';
                                $relatedDate = $related['published_at'] ? date('M d, Y', strtotime($related['published_at'])) : date('M d, Y', strtotime($related['created_at']));
                            ?>
                            <a href="<?php echo $publicBase; ?>/blog/<?php echo escape_html($related['slug']); ?>" class="related-card">
                                <div class="related-media">
                                    <img src="<?php echo escape_html($relatedImg); ?>" alt="<?php echo escape_html($related['title']); ?>">
                                </div>
                                <div class="related-body">
                                    <div class="meta-row" style="font-size: 12px; gap: 6px; margin-top: 0;">
                                        <span class="dot"></span>
                                        <span><?php echo $relatedDate; ?></span>
                                    </div>
                                    <h5><?php echo escape_html($related['title']); ?></h5>
                                    <p><?php echo escape_html($relatedExcerpt); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.copy-link').forEach(function (button) {
        button.addEventListener('click', function () {
            var url = button.getAttribute('data-url') || window.location.href;
            navigator.clipboard.writeText(url).then(function () {
                button.textContent = 'Link copied';
                setTimeout(function () {
                    button.textContent = 'Copy link';
                }, 1600);
            }).catch(function () {
                button.textContent = 'Unable to copy';
            });
        });
    });
</script>

<?php
$show_confirmation_toast = false;
require_once __DIR__ . '/../includes/site_footer.php';
?>
