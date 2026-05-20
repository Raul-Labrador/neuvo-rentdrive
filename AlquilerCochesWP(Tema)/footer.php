
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="<?php echo home_url(); ?>" class="footer-brand brand-font">NEUVO</a>
                    <p class="footer-description">
                        Premium car rental service providing exceptional vehicles and outstanding customer experience
                        since 2015.
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo home_url(); ?>">Home</a></li>
                        <li><a href="<?php echo home_url('/Cars'); ?>">Our Cars</a></li>
                        <li><a href="<?php echo home_url('/about'); ?>">About</a></li>
                        <li><a href="<?php echo home_url('/blog'); ?>">Blog</a></li>
                        <li><a href="<?php echo home_url('/contact'); ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-links">
                        <li><a href="#">Car Rental</a></li>
                        <li><a href="#">Long Term Lease</a></li>
                        <li><a href="#">Airport Transfer</a></li>
                        <li><a href="#">Chauffeur Service</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h4 class="footer-title">Contact Us</h4>
                    <ul class="footer-links">
                        <li>123 Premium Street, Suite 100</li>
                        <li>New York, NY 10001</li>
                        <li>+1 (555) 123-4567</li>
                        <li>info@neuvo.com</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">© <?php echo date("Y"); ?> NEUVO. All rights reserved. | <a href="<?php echo home_url("/legal"); ?>" class="footer-legal-link">Terms & Privacy Policy</a></p>
                <div class="footer-social">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <button id="back-to-top" class="back-to-top-btn" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <?php wp_footer(); ?>
</body>
</html>
