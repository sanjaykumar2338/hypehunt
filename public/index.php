<?php
require_once __DIR__ . '/../config/config.php';
$assetBase = BASE_URL . '/assets';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Hunt</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/core.css">
    <link rel="stylesheet" href="<?php echo $assetBase; ?>/css/media.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="<?php echo $assetBase; ?>/images/favicon.png" type="image/png">
    <style>
        .form-message { display: none; margin-bottom: 12px; padding: 10px 12px; border-radius: 10px; font-size: 14px; }
        .form-message.success { background: #e6ffed; color: #0f5132; border: 1px solid #badbcc; }
        .form-message.error { background: #ffe8e6; color: #842029; border: 1px solid #f5c2c7; }
    </style>

</head>

<body>
    <header class="header">
        <div class="site-container header-container">

            <!-- Logo -->
            <div class="header-logo">
                <a href="#">
                    <img src="<?php echo $assetBase; ?>/images/site-logo.png" alt="Hype Hunt Logo">
                </a>
                <!-- Hamburger -->
                <button id="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="header-nav">
                <ul class="header-nav-list">
                    <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="What-Is-It">What Is
                            It</a></li>
                    <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Who-It-s-For">Who It's
                            For</a></li>
                    <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Why-Different">Why
                            Different</a></li>
                    <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Features">Features</a>
                    </li>
                </ul>
            </nav>

            <!-- Header Button -->
            <div class="header-btn-box">
                <a href="javascript:void(0)" class="Hype-Root1 js-open-early">Get Early Access</a>
            </div>

        </div>
    </header>


    <!-- Mobile Slide Menu -->
    <nav id="slideMenu">
        <button id="closeSlideMenu"><i class="fa-solid fa-xmark"></i>
        </button>

        <ul class="header-nav-list">
            <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="What-Is-It">What Is It</a></li>
            <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Who-It-s-For">Who It's For</a>
            </li>
            <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Why-Different">Why
                    Different</a></li>
            <li class="header-nav-item"><a href="#" class="header-nav-link" data-target="Features">Features</a></li>
        </ul>
    </nav>



    <section class="hero-block">
        <div class="site-container">
            <div class="hero-grid">
                <div class="hero-left">


                    <div class="top-tag">
                        <span><img src="<?php echo $assetBase; ?>/images/SVG.png" alt="">Coming Spring 2026</span>
                    </div>
                    <div class="hero-title">
                        <h2>Never Miss a
                            <span> Sneaker Drop </span> Again.
                        </h2>
                    </div>
                    <div class="hero-des-block">
                        <p>Real-time restock alerts, legit checks, and drop intelligence
                            built for sneakerheads, resellers, and collectors.</p>
                        <div class="action-root">

                            <!-- <a href="javascript:void(0)" class="Hype-Root1" id="openModal">
                                Join Early Access
                            </a>
 -->


                            <a href="javascript:void(0)" class="Hype-Root1 js-open-early">
                                Join Early Access
                            </a>



                            <a href="javascript:void(0)" class="Hype-Root2" id="openModal">
                                Get notified at launch
                            </a>

                        </div>
                        <div class="spna-block">

                            <span>Collector</span>
                            <span>Reseller</span>
                            <span>Both</span>
                        </div>
                        <div class="coming-soon
                        ">
                            <p>Hype Hunt is coming soon. Be first in line when we drop.</p>
                        </div>
                    </div>
                    <!-- Left content goes here -->
                </div>

                <div class="hero-right">
                    <img src="<?php echo $assetBase; ?>/images/Group 1000002315.png" alt="">
                </div>
            </div>
        </div>
    </section>
    <!-- Modal Overlay -->
    <div class="custom-modal-overlay" id="customModal">
        <div class="custom-modal">
            <button class="modal-close" id="closeModal"><img src="<?php echo $assetBase; ?>/images/Vector (1).png" alt=""></button>

            <h2>Get Notified</h2>

            <form id="notifyForm" action="<?php echo BASE_URL; ?>/public/submit_notify.php" method="POST" novalidate>
                <div class="form-message" id="notifyMessage" style="display:none;"></div>

                <div class="input-root">
                    <label for="notifyEmail">Enter</label>
                    <input type="email" id="notifyEmail" name="email" placeholder="Enter your email" required>
                </div>


                <div class="input-root">
                    <label for="">Who you are? (Optional)</label>
                    <select name="user_type" id="roleSelect">
                        <option value="" selected>Select collector</option>
                        <option value="collector">Collector</option>
                        <option value="reseller">Reseller</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="root-block">
                    <button class="Hype-Root1" type="submit">Get Notified</button>
                </div>



            </form>
        </div>
    </div>



    <!-- Early Access Modal -->
    <div class="custom-modal-overlay" id="earlyModal">
        <div class="custom-modal custom-modal2">

            <button class="modal-close js-close-early">
                <img src="<?php echo $assetBase; ?>/images/Vector (1).png" alt="Close">
            </button>

            <h2>Join Early Access</h2>

            <form id="earlyAccessForm" action="<?php echo BASE_URL; ?>/public/submit_early_access.php" method="POST" novalidate>
                <div class="Access-form">
                    <div class="form-message" id="earlyMessage" style="display:none;"></div>

                    <div class="input-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" placeholder=" first name" required>
                    </div>

                    <!-- Last Name -->
                    <div class="input-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" placeholder=" last name" required>
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder=" email address" required>
                    </div>

                    <!-- Phone -->
                    <div class="input-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" placeholder=" phone number">
                    </div>

                    <!-- Password -->
                    <div class="input-group Password-Block">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder=" your password" required>
                    </div>

                    <!-- Textarea -->
                    <div class="input-group Message-Block">
                        <label for="message">Message or Comments</label>
                        <textarea id="message" name="comments" rows="4" placeholder="Write your message here..."></textarea>
                    </div>


                    <div class="action-btn-block">
                        <button class="Hype-Root1" type="submit"> Join Early Access</button>
                    </div>
                </div>
            </form>

        </div>
    </div>


















    <section class="hype-hunt-block" id="What-Is-It">
        <div class="site-container">
            <div class="section-head text-center">
                <div class="section-title">
                    <h2>What Is<span> Hype Hunt </span></h2>
                </div>
                <p>Hype Hunt is a sneaker and hype intelligence platform that helps you track restocks, verify
                    authenticity,
                    and <br> stay ahead of hype without bots, guesswork, or wasted time.</p>
            </div>
            <div class="hype-grid">
                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (1).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Instant restock alerts</h2>
                        <p>Get notified the moment your grails are back in stock.</p>
                    </div>
                </div>

                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (2).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Legit check tools</h2>
                        <p>Verify authenticity before you buy or sell.</p>
                    </div>
                </div>

                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (3).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Market & drop insights</h2>
                        <p>Track trends, prices, and upcoming releases.</p>
                    </div>
                </div>

                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (4).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Built for humans, not bots</h2>
                        <p>Fair access for real collectors and resellers.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="for-section" id="Who-It-s-For">
        <div class="site-container">
            <div class="section-title text-center">
                <h2>Who <span> It's For </span></h2>
            </div>


            <div class="for-grid">
                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (5).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Collectors</h2>
                        <p>Secure your personal pairs and
                            avoid taking Ls.</p>
                    </div>
                </div>


                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (6).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Resellers</h2>
                        <p>Spot profit opportunities before
                            everyone else.</p>
                    </div>
                </div>




                <div class="hype-card">
                    <div class="card-icon">
                        <img src="<?php echo $assetBase; ?>/images/SVG (7).png" alt="">
                    </div>
                    <div class="card-content">
                        <h2>Casual Sneaker Fans</h2>
                        <p>Stop missing drops you actually
                            care about.</p>
                    </div>
                </div>




            </div>

            <div class="card-block text-cenetr">
                <p>Whether you're copping one pair or flipping full-size runs <span>Hype Hunt is built for you. </span>
                </p>
            </div>



        </div>
    </section>









    <section class="different-block" id="Why-Different">
        <div class="container">
            <div class="section-head text-center">
                <div class="section-title">
                    <h2>Why <span>Hype Hunt </span> Is Different</h2>
                </div>
            </div>


            <div class="Different-grid">
                <div class="Different-card">
                    <div class="text-block">
                        <h2><img src="<?php echo $assetBase; ?>/images/SVG (8).png" alt="">The Problem</h2>
                    </div>
                    <ul>
                        <li> <span><img src="<?php echo $assetBase; ?>/images/SVG (8).png" alt=""> </span>No Discord clutter or noisy cook groups
                        </li>
                        <li><span><img src="<?php echo $assetBase; ?>/images/SVG (8).png" alt=""> </span>No delayed, low-signal alerts</li>
                        <li><span><img src="<?php echo $assetBase; ?>/images/SVG (8).png" alt=""></span>No overpriced memberships with unused
                            features</li>
                    </ul>


                </div>


                <div class="Different-card">
                    <div class="text-block">
                        <h2><span><img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt=""></span>The Hype Hunt Way</h2>
                    </div>
                    <ul>
                        <li>
                            <span>
                                <img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt=""></span> Clean, focused alerts only for what you
                            care about
                        </li>


                        <li>
                            <span> <img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt=""></span> Legit-first tools to avoid fakes and
                            sketchy sellers
                        </li>


                        <li>
                            <span> <img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt=""> </span>Community-driven intelligence
                        </li>


                        <li>
                            <span> <img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt=""> </span>Tracks more than sneakers (limited
                            items,
                            collectibles)
                        </li>
                        <li>
                            <span> <img src="<?php echo $assetBase; ?>/images/SVG (9).png" alt="">
                            </span>Ready for digital and online collections
                        </li>


                    </ul>


                </div>




            </div>
            <div class="section-end-block">
                <p>Built by sneaker culture for collectors of everything hype.</p>
            </div>
        </div>
    </section>

    <section class="features-block" id="Features">
        <div class="container">
            <div class="section-head text-center">
                <div class="section-sub-title">
                    <span class="dot"></span>
                    <span>Sneak Peek</span>
                </div>
                <div class="section-title">
                    <h2> <span>Features </span> Preview</h2>
                </div>
                <p>Everything you need to stay ahead of hype—in one platform.</p>
            </div>


            <div class="root-grid-block">

                <div class="top-grid">
                    <div class="card-bx">
                        <div class="icon">
                            <img src="<?php echo $assetBase; ?>/images/1👟.png" alt="">
                        </div>
                        <div class="text-block">
                            <h3>Sneakers</h3>
                            <p>Tracked & monitored</p>
                        </div>
                    </div>


                    <div class="card-bx">
                        <div class="icon">
                            <img src="<?php echo $assetBase; ?>/images/2👕.png" alt="">

                        </div>
                        <div class="text-block">
                            <h3>Streetwear</h3>
                            <p>Tracked & monitored</p>
                        </div>
                    </div>



                    <div class="card-bx">
                        <div class="icon">
                            <img src="<?php echo $assetBase; ?>/images/3🎮.png" alt="">

                        </div>
                        <div class="text-block">
                            <h3>Digital Collectibles</h3>
                            <p>Tracked & monitored</p>
                        </div>
                    </div>




                </div>


                <ul class="end-grid">
                    <li><span><img src="<?php echo $assetBase; ?>/images/SVG (10).png" alt=""></span>
                         Live restock tracking for hype sneakers and other sold‑out <br> or limited items.
                    </li>




                    <li><span><img src="<?php echo $assetBase; ?>/images/SVG (11).png" alt=""></span>
                        Retailer monitoring across major sites so you see when heat <br> quietly comes back in stock.
                    </li>



                    <li><span><img src="<?php echo $assetBase; ?>/images/SVG (12).png" alt=""></span>
                        Mobile‑first alerts so you never miss on the go whether it’s a <br> pair, a collectible, or a
                        capsule
                        drop.
                    </li>






                    <li><span><img src="<?php echo $assetBase; ?>/images/Vector.png" alt=""></span>
                        Dashboard with heat & demand signals for your favorite <br> sneakers, collectibles, and digital
                        items.
                    </li>





                    <li><span><img src="<?php echo $assetBase; ?>/images/SVG (13).png" alt=""></span>
                        Authentication assistance and legit‑check resources to <br> reduce the risk of buying fakes.
                    </li>





                    <li><span><img src="<?php echo $assetBase; ?>/images/SVG (14).png" alt=""></span>
                        Tools to organize your online and digital collection in one <br> **place**, from physical pairs
                        to
                        virtual assets.
                    </li>








                </ul>
                <div class="section-root-block">
                    <p>Features roll out in phases—<span>early users help decide what we build next. </span></p>
                </div>
            </div>

        </div>
    </section>


    <section class="rewarded-block">
        <div class="site-container rewarded-container">
            <div class="section-head text-center">
                <div class="head-span"> <span>🎁 Early Access Benefits</span>
                </div>
                <div class="section-title">
                    <h2>Join Now, <span> Get Rewarded </span></h2>
                </div>
            </div>


            <form action="#">
                <div class="form-grid">
                    <div class="input-group">
                        <span> <img src="<?php echo $assetBase; ?>/images/SVG (16).png" alt=""> </span>
                        <input type="text" placeholder="Early access to the platform">
                    </div>


                    <div class="input-group">
                        <span> <img src="<?php echo $assetBase; ?>/images/SVG (17).png" alt=""> </span>
                        <input type="text" placeholder="Feature voting privileges">
                    </div>



                    <div class="input-group">
                        <span> <img src="<?php echo $assetBase; ?>/images/SVG (18).png" alt=""> </span>
                        <input type="text" placeholder="Free credits & legit checks">
                    </div>

                    <div class="input-group">
                        <span> <img src="<?php echo $assetBase; ?>/images/SVG (21).png" alt=""> </span>
                        <input type="text" placeholder="Free credits & legit checks">
                    </div>
                    <div class="form-action-btn">
                        <button class="Hype-Root1">Lock In Early Access</button>
                    </div>



                </div>

            </form>

        </div>
    </section>




    <section class="launching-soon">
        <div class="container">

            <div class="text-root">
                <h3>Built by lifelong sneakerheads. <br> Designed with resellers in mind. <span> Launching with early
                        testers soon. </span></h3>
            </div>

        </div>
    </section>



    <section class="spring-block">
        <div class="container spring-container
        ">
            <div class="section-top-root">
                <span><img src="<?php echo $assetBase; ?>/images/SVG (22).png" alt="">Launch Target</span>
            </div>
            <div class="main-title">
                <h3>Spring <span> 2026 </span></h3>
            </div>
            <p>Early access invites sent in waves.</p>
            <strong> <img src="<?php echo $assetBase; ?>/images/SVG (23).png" alt="">Be first. Be ready. Be notified.</strong>
        </div>
    </section>


    <div class="site-footer">
        <div class="footer-wrapper">

            <div class="footer-brand">
                <a href="#">
                    <img src="<?php echo $assetBase; ?>/images/Frame (2).png" alt="Hype Hunt Logo">
                </a>
            </div>

            <div class="footer-socials">
                <a href="#"><img src="<?php echo $assetBase; ?>/images/SVG (24).png" alt="Instagram"></a>
                <a href="#"><img src="<?php echo $assetBase; ?>/images/SVG (25).png" alt="Twitter"></a>
                <a href="#"><img src="<?php echo $assetBase; ?>/images/SVG (26).png" alt="Message"></a>
                <a href="#"><img src="<?php echo $assetBase; ?>/images/SVG (27).png" alt="Email"></a>
            </div>

            <ul class="footer-links">
                <li class="footer-link"><a href="#">Privacy Policy</a></li>
                <li class="footer-link"><a href="#">Terms of Use</a></li>
                <li class="footer-copy">© Hype Hunt, 2026</li>
            </ul>

        </div>
    </div>

    <!-- JS Files -->
    <script src="<?php echo $assetBase; ?>/js/jquery-3.7.1.min.js"></script>
    <script src="<?php echo $assetBase; ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $assetBase; ?>/js/core.js"></script>
</body>

</html>
