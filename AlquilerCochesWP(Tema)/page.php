<?php get_header(); ?>
    <?php get_template_part('nav'); ?> 

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title"><?php the_title(); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / <?php the_title(); ?>
            </p>
        </div>
    </section>

    <section class="section-padding" style="padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
