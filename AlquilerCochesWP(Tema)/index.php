<?php get_header(); ?>

    <?php get_template_part('nav'); ?> 

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title"><?php single_post_title(); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / Blog
            </p>
        </div>
    </section>

    <section class="blog-section">
        <div class="container">
            <div class="row">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="blog-card">
                        <div class="blog-image-placeholder">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail('medium', ['class' => 'img-fluid']); ?>
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/blog2.png" alt="">
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <p class="blog-meta"><?php echo get_the_date(); ?> • <?php the_category(', '); ?></p>
                            <h3 class="blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="blog-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="blog-read-more">Read More →</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; else : ?>
                    <p>No posts found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
