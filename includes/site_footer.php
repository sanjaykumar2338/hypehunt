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

    <?php if (!isset($show_confirmation_toast) || $show_confirmation_toast): ?>
    <div class="confirmation-toast" id="confirmationToast">
        <div class="toast-content">
            <h4>You're in.</h4>
            <p id="confirmationText">Thanks for joining the HypeHunt early access waitlist!</p>
            <p class="toast-sub">Get ready for exclusive tools to hunt grails, track drops, and dominate the sneaker game.</p>
            <p class="toast-sub">We'll email you with access details and updates as we get closer to launch.</p>
        </div>
    </div>
    <?php endif; ?>

    <script src="<?php echo $assetBase; ?>/js/jquery-3.7.1.min.js"></script>
    <script src="<?php echo $assetBase; ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $assetBase; ?>/js/core.js"></script>
</body>
</html>
