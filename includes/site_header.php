<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

$base = rtrim(BASE_URL, '/');
$assetBase = $base . '/assets';
$publicBase = $base;
if (substr($base, -7) === '/public') {
    $assetBase = $base . '/../assets';
    $publicBase = substr($base, 0, -7);
}

$seo_title = $seo_title ?? 'HypeHunt';
$seo_description = $seo_description ?? 'Hype Hunt helps you track restocks, legit checks, and drop intelligence built for sneakerheads, resellers, and collectors.';
$seo_canonical = $seo_canonical ?? (rtrim(BASE_URL, '/') . $_SERVER['REQUEST_URI']);
$seo_og_title = $seo_og_title ?? $seo_title;
$seo_og_description = $seo_og_description ?? $seo_description;
$defaultOgImage = $assetBase . '/images/Group 1000002315.png';
$seo_og_image = $seo_og_image ?? $defaultOgImage;
$seo_twitter_title = $seo_twitter_title ?? $seo_og_title;
$seo_twitter_description = $seo_twitter_description ?? $seo_og_description;
$seo_twitter_image = $seo_twitter_image ?? $seo_og_image;
$seo_type = $seo_type ?? 'website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape_html($seo_title); ?></title>
    <meta name="description" content="<?php echo escape_html($seo_description); ?>">
    <link rel="canonical" href="<?php echo escape_html($seo_canonical); ?>">
    <meta name="robots" content="index,follow">

    <meta property="og:type" content="<?php echo $seo_type === 'article' ? 'article' : 'website'; ?>">
    <meta property="og:title" content="<?php echo escape_html($seo_og_title); ?>">
    <meta property="og:description" content="<?php echo escape_html($seo_og_description); ?>">
    <meta property="og:url" content="<?php echo escape_html($seo_canonical); ?>">
    <meta property="og:image" content="<?php echo escape_html($seo_og_image); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo escape_html($seo_twitter_title); ?>">
    <meta name="twitter:description" content="<?php echo escape_html($seo_twitter_description); ?>">
    <meta name="twitter:image" content="<?php echo escape_html($seo_twitter_image); ?>">

    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/core.css">
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/media.css">
    <link rel="icon" href="<?php echo $assetBase; ?>/images/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="site-container header-container">
            <div class="header-logo">
                <a href="<?php echo $publicBase; ?>/">
                    <img src="<?php echo $assetBase; ?>/images/site-logo.png" alt="Hype Hunt Logo">
                </a>
                <button id="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <nav class="header-nav">
                <ul class="header-nav-list">
                    <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#What-Is-It" class="header-nav-link" data-target="What-Is-It">What Is It</a></li>
                    <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Who-It-s-For" class="header-nav-link" data-target="Who-It-s-For">Who It's For</a></li>
                    <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Why-Different" class="header-nav-link" data-target="Why-Different">Why Different</a></li>
                    <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Features" class="header-nav-link" data-target="Features">Features</a></li>
                    <li class="header-nav-item"><a href="<?php echo $base; ?>/blog.php" class="header-nav-link">Blog</a></li>
                </ul>
            </nav>

            <div class="header-btn-box">
                <a href="javascript:void(0)" class="Hype-Root1 js-open-early">Get Early Access</a>
            </div>
        </div>
    </header>

    <nav id="slideMenu">
        <button id="closeSlideMenu"><i class="fa-solid fa-xmark"></i></button>
        <ul class="header-nav-list">
            <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#What-Is-It" class="header-nav-link" data-target="What-Is-It">What Is It</a></li>
            <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Who-It-s-For" class="header-nav-link" data-target="Who-It-s-For">Who It's For</a></li>
            <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Why-Different" class="header-nav-link" data-target="Why-Different">Why Different</a></li>
            <li class="header-nav-item"><a href="<?php echo $publicBase; ?>/#Features" class="header-nav-link" data-target="Features">Features</a></li>
            <li class="header-nav-item"><a href="<?php echo $base; ?>/blog.php" class="header-nav-link">Blog</a></li>
        </ul>
    </nav>
