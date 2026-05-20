<?php

get_header();

// Recogemos y SANEAMOS los filtros de la URL
$filter_body  = isset($_GET['body']) ? array_map('sanitize_text_field', (array)$_GET['body']) : [];
$filter_trans = isset($_GET['trans']) ? array_map('sanitize_text_field', (array)$_GET['trans']) : [];
$search_query = isset($_GET['search_car']) ? sanitize_text_field($_GET['search_car']) : '';
$max_price    = isset($_GET['max_price']) ? intval($_GET['max_price']) : 500;
$orderby      = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';

get_template_part('nav'); 
?>

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title"><?php _e('Our Fleet', 'alquilercocheswp'); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'alquilercocheswp'); ?></a> / <?php _e('Cars', 'alquilercocheswp'); ?>
            </p>
        </div>
    </section>

    <section class="cars-grid-section">
        <div class="container-wide">
            <button class="mobile-filter-toggle d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#sidebarCollapse" aria-expanded="false" aria-controls="sidebarCollapse">
                <i class="bi bi-funnel"></i> <?php _e('Filter & Search', 'alquilercocheswp'); ?>
            </button>

            <div class="row">
                <?php get_sidebar(); ?>

                <div class="col-lg-9 col-xxl-10">
                    <div class="row">
                        <?php
                        // Construimos la Meta Query dinámicamente
                        $meta_query = array('relation' => 'AND');

                        if (!empty($filter_body)) {
                            $meta_query[] = array(
                                'key'     => 'rlp_car_body',
                                'value'   => $filter_body,
                                'compare' => 'IN',
                            );
                        }

                        if (!empty($filter_trans)) {
                            $meta_query[] = array(
                                'key'     => 'rlp_car_transmission',
                                'value'   => $filter_trans,
                                'compare' => 'IN',
                            );
                        }

                        // Filtro de precio
                        $meta_query[] = array(
                            'key'     => 'rlp_car_price',
                            'value'   => array(0, $max_price),
                            'type'    => 'numeric',
                            'compare' => 'BETWEEN',
                        );

                        // Ordenación
                        $query_orderby = 'date';
                        $query_order   = 'DESC';
                        $meta_key      = '';

                        if ($orderby === 'price_low') {
                            $query_orderby = 'meta_value_num';
                            $query_order   = 'ASC';
                            $meta_key      = 'rlp_car_price';
                        } elseif ($orderby === 'price_high') {
                            $query_orderby = 'meta_value_num';
                            $query_order   = 'DESC';
                            $meta_key      = 'rlp_car_price';
                        }

                        // Argumentos
                        $args = array(
                            'post_type'      => 'cars',
                            'posts_per_page' => 9, 
                            's'              => $search_query,
                            'meta_query'     => $meta_query,
                            'orderby'        => $query_orderby,
                            'order'          => $query_order,
                            'meta_key'       => $meta_key,
                        );

                        $cars_query = new WP_Query($args);

                        if ($cars_query->have_posts()):
                            while ($cars_query->have_posts()): $cars_query->the_post();

                                // Harvesting (Sacamos los datos una sola vez)
                                $price = get_post_meta(get_the_ID(), 'rlp_car_price', true);
                                $fuel  = get_post_meta(get_the_ID(), 'rlp_car_fuel', true);
                                $seats = get_post_meta(get_the_ID(), 'rlp_car_seats', true);
                                $trans = get_post_meta(get_the_ID(), 'rlp_car_transmission', true);
                        ?>
                            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 mb-4">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none d-block">
                                    <div class="premium-car-card">
                                        <div class="premium-card-header">
                                            <h3 class="premium-car-title"><?php the_title(); ?></h3>
                                        </div>
                                        
                                        <div class="premium-stats-container">
                                            <div class="premium-stat-badge premium-glass-card">
                                                <i class="bi bi-people-fill"></i>
                                                <span class="premium-stat-value"><?php echo esc_html($seats); ?></span>
                                            </div>
                                            <div class="premium-stat-badge premium-glass-card">
                                                <i class="bi bi-gear-fill"></i>
                                                <span class="premium-stat-value"><?php echo ($trans == 'Automatic') ? __('Auto', 'alquilercocheswp') : __('Manual', 'alquilercocheswp'); ?></span>
                                            </div>
                                            <div class="premium-stat-badge premium-glass-card">
                                                <i class="bi bi-fuel-pump-fill"></i>
                                                <span class="premium-stat-text"><?php echo esc_html($fuel); ?></span>
                                            </div>
                                        </div>

                                        <div class="premium-image-container">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('full', ['alt' => get_the_title()]); ?>
                                            <?php else: ?>
                                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/coche-default.png" alt="<?php _e('Default Car', 'alquilercocheswp'); ?>">
                                            <?php endif; ?>
                                        </div>

                                        <div class="premium-price-container">
                                            <h2 class="premium-price">$<?php echo esc_html($price); ?><span>/<?php _e('Day', 'alquilercocheswp'); ?></span></h2>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else: ?>
                            <div class="col-12">
                                <p class="text-white"><?php _e('No cars found with these criteria.', 'alquilercocheswp'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pagination-wrapper mt-5">
                        <?php 
                        echo paginate_links(array(
                            'total' => $cars_query->max_num_pages,
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>