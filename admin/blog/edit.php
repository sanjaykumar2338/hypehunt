<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/header.php';

$base = rtrim(BASE_URL, '/');
$adminBase = $base;
if (substr($base, -7) === '/public') {
    $adminBase = substr($base, 0, -7);
}

$id = (int) ($_GET['id'] ?? 0);
$post = $id ? get_blog_post_by_id($id) : null;
if (!$post) {
    echo '<div class="alert alert-danger">Post not found.</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$errors = [];
$csrf = generate_csrf_token();
$defaultAuthor = 'HypeHunt Team';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }

    $title = sanitize_text($_POST['title'] ?? '');
    $slugInput = sanitize_text($_POST['slug'] ?? '');
    $slugBase = slugify($slugInput !== '' ? $slugInput : $title);
    $slug = unique_slug($slugBase, $post['id']);
    $excerpt = sanitize_text($_POST['excerpt'] ?? '');
    $content = sanitize_blog_content($_POST['content'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft';
    $published_at = ($_POST['published_at'] ?? '') ?: null;
    $author = sanitize_text($_POST['author'] ?? $defaultAuthor);
    $tags = sanitize_text($_POST['tags'] ?? '');

    $meta_title = sanitize_text($_POST['meta_title'] ?? '');
    $meta_description = sanitize_text($_POST['meta_description'] ?? '');
    $canonical_url = sanitize_text($_POST['canonical_url'] ?? '');
    $og_title = sanitize_text($_POST['og_title'] ?? '');
    $og_description = sanitize_text($_POST['og_description'] ?? '');
    $og_image = sanitize_text($_POST['og_image'] ?? '');
    $twitter_title = sanitize_text($_POST['twitter_title'] ?? '');
    $twitter_description = sanitize_text($_POST['twitter_description'] ?? '');
    $twitter_image = sanitize_text($_POST['twitter_image'] ?? '');

    if ($title === '' || $content === '') {
        $errors[] = 'Title and content are required.';
    }

    $featuredImage = $post['featured_image'];
    if (!empty($_FILES['featured_image']['name'])) {
        $file = $_FILES['featured_image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Invalid image type. Only jpg, png, webp allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Image too large (max 2MB).';
            } else {
                $safeSlug = preg_replace('/[^a-z0-9-]+/i', '-', $slug);
                $uniqueName = time() . '_' . $safeSlug . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $targetDir = __DIR__ . '/../../assets/uploads/blog/';
                if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
                    $errors[] = 'Unable to create upload directory.';
                }
                if (empty($errors) && !is_writable($targetDir)) {
                    @chmod($targetDir, 0775);
                }
                if (empty($errors) && is_writable($targetDir)) {
                    $targetPath = $targetDir . $uniqueName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $featuredImage = 'assets/uploads/blog/' . $uniqueName;
                    } else {
                        $errors[] = 'Error saving uploaded image.';
                    }
                } elseif (empty($errors)) {
                    $errors[] = 'Upload directory is not writable.';
                }
            }
        } else {
            $errors[] = 'Error uploading image.';
        }
    }

    if (empty($errors)) {
        if ($status === 'published' && empty($published_at)) {
            $published_at = date('Y-m-d H:i:s');
        }

        $pdo = get_pdo();
        $stmt = $pdo->prepare(
            'UPDATE blog_posts SET
                title=:title, slug=:slug, excerpt=:excerpt, content=:content, featured_image=:featured_image,
                status=:status, published_at=:published_at, meta_title=:meta_title, meta_description=:meta_description,
                canonical_url=:canonical_url, og_title=:og_title, og_description=:og_description, og_image=:og_image,
                twitter_title=:twitter_title, twitter_description=:twitter_description, twitter_image=:twitter_image,
                tags=:tags, author=:author
             WHERE id=:id'
        );
        $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image' => $featuredImage,
            'status' => $status,
            'published_at' => $published_at,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'canonical_url' => $canonical_url,
            'og_title' => $og_title,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'twitter_title' => $twitter_title,
            'twitter_description' => $twitter_description,
            'twitter_image' => $twitter_image,
            'tags' => $tags,
            'author' => $author,
            'id' => $post['id'],
        ]);

        header('Location: ' . $adminBase . '/admin/blog/index.php');
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Edit Blog Post</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo $adminBase; ?>/admin/blog/index.php" class="btn btn-outline-secondary">Back</a>
        <?php
            $previewLink = BASE_URL . '/blog/' . $post['slug'];
            if (($post['status'] ?? '') !== 'published') {
                $previewLink .= '?preview=1';
            }
        ?>
        <a href="<?php echo escape_html($previewLink); ?>" class="btn btn-outline-primary" target="_blank">Preview</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?php echo escape_html($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required value="<?php echo escape_html($_POST['title'] ?? $post['title']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="<?php echo escape_html($_POST['slug'] ?? $post['slug']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="3"><?php echo escape_html($_POST['excerpt'] ?? $post['excerpt']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Content</label>
                <div id="editor-container" style="height:320px; background:#fff; color:#111; border:1px solid #d1d5db; border-radius:12px;"></div>
                <textarea name="content" id="contentInput" class="form-control" rows="10" style="display:none;"><?php echo escape_html($_POST['content'] ?? $post['content']); ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo ($_POST['status'] ?? $post['status']) === 'published' ? '' : 'selected'; ?>>Draft</option>
                        <option value="published" <?php echo ($_POST['status'] ?? $post['status']) === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Published At</label>
                    <input type="datetime-local" name="published_at" class="form-control" value="<?php echo escape_html($_POST['published_at'] ?? ($post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '')); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-control" value="<?php echo escape_html($_POST['author'] ?? ($post['author'] ?: $defaultAuthor)); ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Tags (comma separated)</label>
                <input type="text" name="tags" class="form-control" value="<?php echo escape_html($_POST['tags'] ?? $post['tags']); ?>">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Featured Image</label>
                <?php if (!empty($post['featured_image'])): ?>
                    <div class="mb-2"><img src="<?php echo BASE_URL . '/../' . escape_html($post['featured_image']); ?>" alt="Featured" style="max-width:100%; height:auto;"></div>
                <?php endif; ?>
                <input type="file" name="featured_image" accept="image/*" class="form-control">
                <small class="text-muted">Max 2MB. jpg/png/webp.</small>
            </div>
            <h6 class="mt-4">SEO</h6>
            <div class="mb-2"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?php echo escape_html($_POST['meta_title'] ?? $post['meta_title']); ?>"></div>
            <div class="mb-2"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2"><?php echo escape_html($_POST['meta_description'] ?? $post['meta_description']); ?></textarea></div>
            <div class="mb-2"><label class="form-label">Canonical URL</label><input type="text" name="canonical_url" class="form-control" value="<?php echo escape_html($_POST['canonical_url'] ?? $post['canonical_url']); ?>"></div>
            <div class="mb-2"><label class="form-label">OG Title</label><input type="text" name="og_title" class="form-control" value="<?php echo escape_html($_POST['og_title'] ?? $post['og_title']); ?>"></div>
            <div class="mb-2"><label class="form-label">OG Description</label><textarea name="og_description" class="form-control" rows="2"><?php echo escape_html($_POST['og_description'] ?? $post['og_description']); ?></textarea></div>
            <div class="mb-2"><label class="form-label">OG Image URL</label><input type="text" name="og_image" class="form-control" value="<?php echo escape_html($_POST['og_image'] ?? $post['og_image']); ?>"></div>
            <div class="mb-2"><label class="form-label">Twitter Title</label><input type="text" name="twitter_title" class="form-control" value="<?php echo escape_html($_POST['twitter_title'] ?? $post['twitter_title']); ?>"></div>
            <div class="mb-2"><label class="form-label">Twitter Description</label><textarea name="twitter_description" class="form-control" rows="2"><?php echo escape_html($_POST['twitter_description'] ?? $post['twitter_description']); ?></textarea></div>
            <div class="mb-2"><label class="form-label">Twitter Image URL</label><input type="text" name="twitter_image" class="form-control" value="<?php echo escape_html($_POST['twitter_image'] ?? $post['twitter_image']); ?>"></div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="<?php echo $adminBase; ?>/admin/blog/index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow { border: 1px solid #d1d5db; border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .ql-container.ql-snow { border: 1px solid #d1d5db; border-top: none; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
    .ql-editor { min-height: 260px; }
</style>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Write your post...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'blockquote', 'code-block'],
                ['clean']
            ]
        }
    });
    const contentInput = document.getElementById('contentInput');
    if (contentInput && contentInput.value) {
        quill.clipboard.dangerouslyPasteHTML(contentInput.value);
    }
    const form = document.querySelector('form');
    form?.addEventListener('submit', () => {
        if (contentInput) {
            contentInput.value = quill.root.innerHTML;
        }
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
