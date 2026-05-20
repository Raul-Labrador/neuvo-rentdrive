<?php get_header(); ?>
    <!-- Navigation -->
    <?php get_template_part('nav'); ?> 

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title"><?php the_title(); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / <a href="<?php echo home_url('/blog'); ?>">Blog</a> / <?php the_title(); ?>
            </p>
        </div>
    </section>

    <!-- Post Content -->
    <section class="section-padding" style="padding: 60px 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="mb-4">
                                    <?php the_post_thumbnail('large', ['class' => 'img-fluid rounded']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="blog-meta mb-3">
                                <?php echo get_the_date(); ?> • <?php the_category(', '); ?>
                            </div>

                            <div class="entry-content body-font">
                                <?php the_content(); ?>
                            </div>

                            <div class="mt-5">
                                <?php the_tags('<span class="badge bg-secondary me-1">', '</span><span class="badge bg-secondary me-1">', '</span>'); ?>
                            </div>
                        </article>

                        <div class="mt-5 pt-5 border-top">
                            <?php 
                            if ( comments_open() || get_comments_number() ) :
                                comments_template();
                            endif;
                            ?>
                        </div>

                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
