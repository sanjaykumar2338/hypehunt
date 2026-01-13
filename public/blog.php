<?php
require_once __DIR__ . '/../includes/functions.php';

// Base paths (Dynamic detection)
$base = rtrim(BASE_URL, '/');
$assetBase = $base . '/assets';
$publicBase = $base;
if (substr($base, -7) === '/public') {
    $assetBase = $base . '/../assets';
    $publicBase = substr($base, 0, -7);
}

// SEO Setup
$seo_title = 'HypeHunt Blog | Sneaker Drop Intel';
$seo_description = 'Latest news, legit checks, and market analysis for sneakerheads.';
require_once __DIR__ . '/../includes/site_header.php';

// --- DATABASE FETCHING ---
$pdo = get_pdo();

// Pagination Settings
$page = current_page((int)($_GET['page'] ?? 1));
$perPage = 9; // 9 posts fits a 3-column grid perfectly
$offset = ($page - 1) * $perPage;

// Count Total Posts
$countStmt = $pdo->query('SELECT COUNT(*) FROM blog_posts WHERE status = "published"');
$totalPosts = (int) $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

// Fetch Posts for Grid
$stmt = $pdo->prepare(
    'SELECT * FROM blog_posts 
     WHERE status = "published" 
     ORDER BY COALESCE(published_at, created_at) DESC 
     LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Fallback image if post has none
$fallbackImage = $assetBase . '/images/Overlay+Blur (1).png';
?>

<style>
    :root {
        --brand-green: #3DFF8E;
        --bg-dark: #050509;
        --card-bg: #0e0e10;
        --border-color: #2a2a2e;
        --text-muted: #9ca3af;
    }

    body {
        background-color: var(--bg-dark);
        color: white;
        font-family: 'Inter', sans-serif; /* Ensure you have a font loaded in header */
    }

    /* Page Wrapper with subtle Glow Background */
    .blog-wrapper {
        position: relative;
        min-height: 100vh;
        overflow-x: hidden;
        background-image:
            url(<?php echo $assetBase; ?>/images/Group%201000002307.png),
            radial-gradient(circle at 50% 0%, rgba(61, 255, 142, 0.08), transparent 40%);
        background-size: cover, auto;
        background-repeat: no-repeat;
        background-position: center, center;
    }

    /* Main Container */
    .container-custom {
        max-width: 1200px;
        margin: 0 auto;
        padding: 190px 20px 100px; /* Top padding accounts for fixed header */
        position: relative;
        z-index: 2;
    }

    /* Hero Text Section */
    .blog-hero {
        text-align: center;
        margin-bottom: 60px;
    }

    .blog-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }

    .blog-hero h1 span {
        color: var(--brand-green);
        text-shadow: 0 0 20px rgba(61, 255, 142, 0.4);
    }

    .blog-hero p {
        color: var(--text-muted);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* THE 3-COLUMN GRID SYSTEM */
    .grid-system {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Force 3 columns */
        gap: 30px;
    }

    /* Responsive: 2 columns on tablet, 1 on mobile */
    @media (max-width: 1024px) {
        .grid-system { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .grid-system { grid-template-columns: 1fr; }
        .container-custom { padding-top: 140px; }
    }

    /* Card Styling */
    .post-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .post-card:hover {
        border-color: var(--brand-green);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(61, 255, 142, 0.1);
    }

    .card-image {
        height: 220px;
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .post-card:hover .card-image img {
        transform: scale(1.05);
    }

    .card-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-date {
        font-size: 0.85rem;
        color: var(--brand-green);
        margin-bottom: 10px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 12px;
        line-height: 1.4;
        color: #fff;
    }

    .card-excerpt {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1; /* Pushes button to bottom */
    }

    .btn-read {
        display: inline-block;
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: color 0.2s;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 15px;
        margin-top: auto;
    }
    
    .btn-read span {
        color: var(--brand-green);
        margin-left: 5px;
        transition: margin-left 0.2s;
    }

    .post-card:hover .btn-read span {
        margin-left: 10px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 60px;
    }

    .page-btn {
        padding: 10px 18px;
        border-radius: 8px;
        background: #111;
        border: 1px solid #333;
        color: #fff;
        text-decoration: none;
        transition: 0.2s;
    }

    .page-btn:hover, .page-btn.active {
        background: var(--brand-green);
        color: black;
        border-color: var(--brand-green);
        font-weight: bold;
    }
</style>

<div class="blog-wrapper">
    <div class="container-custom">
        
       

        <div class="grid-system">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): 
                    // Data Prep
                    $img = $post['featured_image'] ? (BASE_URL . '/../' . ltrim($post['featured_image'], '/')) : $fallbackImage;
                    $date = date('M d, Y', strtotime($post['published_at'] ?? $post['created_at']));
                    $link = $publicBase . '/blog/' . escape_html($post['slug']);
                    $excerpt = substr(strip_tags($post['content']), 0, 100) . '...';
                ?>
                
                <article class="post-card">
                    <a href="<?php echo $link; ?>" class="card-image">
                        <img src="<?php echo escape_html($img); ?>" alt="Blog Post Image">
                    </a>
                    <div class="card-content">
                        <div class="card-date"><?php echo $date; ?></div>
                        <h3 class="card-title"><?php echo escape_html($post['title']); ?></h3>
                        <p class="card-excerpt"><?php echo escape_html($excerpt); ?></p>
                        <a href="<?php echo $link; ?>" class="btn-read">
                            Read Article <span>&rarr;</span>
                        </a>
                    </div>
                </article>
                
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; color: #666; padding: 50px;">
                    <h3>No posts found.</h3>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="page-btn <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php
$show_confirmation_toast = false;
require_once __DIR__ . '/../includes/site_footer.php';
?>
