<nav class="navbar navbar-expand-lg navbar-neuvo w-100 top-0 start-0 position-absolute">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand brand-font" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php bloginfo( 'name' ); ?> </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="<?php esc_attr_e( 'Toggle navigation', 'alquilercocheswp' ); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>" 
                       href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php _e( 'Home', 'alquilercocheswp' ); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_page('about') ? 'active' : ''; ?>" 
                       href="<?php echo esc_url( home_url( '/about' ) ); ?>">
                        <?php _e( 'About', 'alquilercocheswp' ); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (is_post_type_archive('cars') || is_singular('cars')) ? 'active' : ''; ?>" 
                       href="<?php echo esc_url( home_url( '/cars' ) ); ?>">
                        <?php _e( 'Cars', 'alquilercocheswp' ); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (is_home() || is_singular('post')) ? 'active' : ''; ?>" 
                       href="<?php echo esc_url( home_url( '/blog' ) ); ?>">
                        <?php _e( 'Blog', 'alquilercocheswp' ); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_page('contact') ? 'active' : ''; ?>" 
                       href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
                        <?php _e( 'Contact', 'alquilercocheswp' ); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="d-none d-lg-flex align-items-center gap-2">
            <?php if ( function_exists( 'alquilercocheswp_login_cta' ) ) : ?>
                <?php alquilercocheswp_login_cta(); ?>
            <?php endif; ?>
            
            <a href="<?php echo esc_url( home_url( '/cars' ) ); ?>" class="btn-book">
                <?php _e( 'Book Now', 'alquilercocheswp' ); ?>
            </a>
        </div>
    </div>
</nav>