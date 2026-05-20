<?php
get_header();
?>
    <?php get_template_part('nav'); ?> 

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title">OUR <?php the_title(); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>"><?php _e('Home', 'alquilercocheswp'); ?></a> / <?php _e('Blog', 'alquilercocheswp'); ?>
            </p>
        </div>
    </section>

    <section class="blog-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12">
                    <?php
                    $featured_args = array(
                        'post_type'      => 'post',
                        'posts_per_page' => 1,
                        'orderby'        => 'date',
                        'order'          => 'DESC'
                    );
                    $featured_query = new WP_Query($featured_args);
                    $featured_post_id = 0;

                    if ( $featured_query->have_posts() ) : 
                        while ( $featured_query->have_posts() ) : $featured_query->the_post(); 
                            $featured_post_id = get_the_ID();
                    ?>
                        <article <?php post_class('blog-card blog-featured'); ?>>
                            <div class="blog-image-placeholder">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('full', array( 'fetchpriority' => 'high' )); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/blog1.png" alt="<?php _e('Featured Post', 'alquilercocheswp'); ?>" fetchpriority="high">
                                <?php endif; ?>
                            </div>
                            <div class="blog-content">
                                <p class="blog-meta">
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(get_option('date_format')); ?></time> • <?php the_category(', '); ?>
                                </p>
                                <h2 class="blog-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="blog-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 40, '...'); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="blog-read-more mt-3 d-inline-block" 
                                   aria-label="<?php echo esc_attr__('Learn more about', 'alquilercocheswp') . ' ' . the_title_attribute(array('echo' => false)); ?>">
                                    <?php _e('Learn More →', 'alquilercocheswp'); ?>
                                </a>
                            </div>
                        </article>
                    <?php 
                        endwhile; 
                        wp_reset_postdata();
                    endif; 
                    ?>
                </div>
            </div>

            <div class="row">
                <?php
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 6,
                    'post__not_in'   => array($featured_post_id)
                );

                $blog_query = new WP_Query($args);

                if ( $blog_query->have_posts() ) : 
                    while ( $blog_query->have_posts() ) : $blog_query->the_post(); 
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article <?php post_class('blog-card h-100'); ?>> <div class="blog-image-placeholder">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium_large', array( 'loading' => 'lazy' )); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/blog-default.png" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                <?php endif; ?>
                            </div>
                            <div class="blog-content">
                                <p class="blog-meta">
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                </p>
                                <h3 class="blog-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="blog-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="blog-read-more" 
                                   aria-label="<?php echo esc_attr__('Learn more about', 'alquilercocheswp') . ' ' . the_title_attribute(array('echo' => false)); ?>">
                                    <?php _e('Learn More →', 'alquilercocheswp'); ?>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php 
                    endwhile; 
                    wp_reset_postdata(); 
                else : 
                ?>
                    <div class="col-12">
                        <p class="text-white"><?php _e('No additional posts found.', 'alquilercocheswp'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section-dark py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="heading-font text-white mb-2" style="text-transform: uppercase; letter-spacing: 0.05em;">
                        <?php _e('Subscribe to Our Newsletter', 'alquilercocheswp'); ?></h3>
                    <p class="body-font" style="color: var(--color-gray-400);">
                        <?php _e('Stay updated with the latest news, tips, and exclusive offers.', 'alquilercocheswp'); ?></p>
                </div>
                <div class="col-lg-6">
                    <form class="d-flex gap-3 flex-wrap flex-lg-nowrap">
                        <input type="email" class="form-control" placeholder="<?php esc_attr_e('Enter your email', 'alquilercocheswp'); ?>" required>
                        <button type="submit" class="btn-cta"><?php _e('Subscribe', 'alquilercocheswp'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>