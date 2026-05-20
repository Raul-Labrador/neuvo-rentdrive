<?php
get_header(); ?>

    <?php get_template_part('nav'); ?> 


    <?php 
        if ( have_posts() ) :
            while ( have_posts() ) : the_post(); 
                // Recuperamos todos los metadatos del plugin
                $brand     = get_post_meta( get_the_ID(), 'rlp_car_brand', true );
                $model     = get_post_meta( get_the_ID(), 'rlp_car_model', true );
                $year      = get_post_meta( get_the_ID(), 'rlp_car_year', true );
                $price     = get_post_meta( get_the_ID(), 'rlp_car_price', true );
                $fuel      = get_post_meta( get_the_ID(), 'rlp_car_fuel', true );
                $km        = get_post_meta( get_the_ID(), 'rlp_car_km', true );
                $trans     = get_post_meta( get_the_ID(), 'rlp_car_transmission', true );
                $engine    = get_post_meta( get_the_ID(), 'rlp_car_ed', true );
                $hp        = get_post_meta( get_the_ID(), 'rlp_car_horsepower', true );
                $emissions = get_post_meta( get_the_ID(), 'rlp_car_emissions', true );
                $doors     = get_post_meta( get_the_ID(), 'rlp_car_doors', true );
                $seats     = get_post_meta( get_the_ID(), 'rlp_car_seats', true );
                $body      = get_post_meta( get_the_ID(), 'rlp_car_body', true );
                $trunk     = get_post_meta( get_the_ID(), 'rlp_car_trunk', true );
                $color     = get_post_meta( get_the_ID(), 'rlp_car_color', true );
    ?>

    <section class="page-header page-header-detail">
        <div class="container">
            <h1 class="page-header-title"><?php the_title(); ?></h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / 
                <a href="<?php echo home_url('/cars'); ?>">Cars</a> / 
                <span><?php the_title(); ?></span>
            </p>
        </div>
    </section>

    <section class="car-detail-main py-5 section-light">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    
                    <div class="detail-image-wrapper rounded-4 mb-5 overflow-hidden shadow-sm" style="background: radial-gradient(circle, rgba(40, 70, 90, 1) 0%, rgba(1, 2, 4, 1) 100%);">
                        <?php 
                        // Array para guardar los IDs de imágenes sin repetir
                        $image_ids = array();
                        
                        // Añadimos siempre primero la Imagen Destacada (Featured Image)
                        if ( has_post_thumbnail() ) {
                            $image_ids[] = get_post_thumbnail_id();
                        }
                        
                        // Obtenemos todas las imágenes adjuntas subidas a este post
                        $attached_images = get_attached_media('image', get_the_ID());
                        foreach( $attached_images as $attachment ) {
                            if ( !in_array( $attachment->ID, $image_ids ) ) {
                                $image_ids[] = $attachment->ID;
                            }
                        }

                        // Si hay más de 1 foto, mostramos el Carrusel
                        if ( count($image_ids) > 1 ) : 
                            $carousel_id = 'carSlider_' . get_the_ID();
                        ?>
                            <div id="<?php echo esc_attr($carousel_id); ?>" class="carousel slide w-100" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <?php for($i = 0; $i < count($image_ids); $i++) : ?>
                                        <button type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo ($i === 0) ? 'active' : ''; ?>" aria-current="<?php echo ($i === 0) ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
                                    <?php endfor; ?>
                                </div>
                                <div class="carousel-inner w-100">
                                    <?php 
                                    $i = 0;
                                    foreach($image_ids as $img_id) : 
                                        $img_url = wp_get_attachment_image_url($img_id, 'full');
                                        $bg_size = ($i === 0) ? 'contain' : 'cover';
                                    ?>
                                        <div class="carousel-item <?php echo ($i === 0) ? 'active' : ''; ?> w-100">
                                            <div class="w-100" style="height: 550px; background-image: url('<?php echo esc_url($img_url); ?>'); background-size: <?php echo $bg_size; ?>; background-position: center; background-repeat: no-repeat;"></div>
                                        </div>
                                    <?php 
                                        $i++;
                                    endforeach; 
                                    ?>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon shadow-sm" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.8));"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon shadow-sm" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.8));"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>

                        <?php 
                        // Si solo hay 1 foto (la destacada)
                        elseif ( count($image_ids) === 1 ) : 
                            $img_url = wp_get_attachment_image_url($image_ids[0], 'full');
                        ?>
                            <div class="w-100" style="height: 550px; background-image: url('<?php echo esc_url($img_url); ?>'); background-size: 80%; background-position: center; background-repeat: no-repeat;"></div>

                        <?php 
                        // No hay fotos
                        else : ?>
                            <div class="w-100" style="height: 550px; background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/img/placeholder.png'); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="heading-font mb-4 text-uppercase fw-bold detail-section-title">Overview</h3>
                        <div class="body-font text-muted lh-lg detail-description-text">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    <div class="mb-5">
                        <h3 class="heading-font mb-4 text-uppercase fw-bold detail-section-title">Key Features</h3>
                        <div class="row g-3" id="car-features">
                            <?php
                            $services_data = get_post_meta(get_the_ID(), 'rlp_services', true);

                            if (!empty($services_data) && is_array($services_data)) :
                                foreach ($services_data as $row) : 
                                    if (empty($row['service'])) continue; ?>
                                    <div class="col-12 col-md-6">
                                        <div class="feature-badge bg-white shadow-sm border rounded-pill px-4 py-3 d-flex align-items-center h-100">
                                            <i class="bi bi-star-fill text-dark me-2 flex-shrink-0"></i> 
                                            <span class="fw-medium text-dark"><?php echo esc_html($row['service']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; 
                            else : ?>
                                <div class="col-12"><p class="text-muted italic">Standard equipment included.</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="position-sticky sticky-sidebar">
                        
                        <div class="price-action-card p-5 rounded-4 mb-4">
                            <div class="d-flex align-items-baseline mb-3">
                                <h2 class="heading-font m-0 price-action-price">$<?php echo esc_html($price); ?></h2>
                                <span class="ms-2 body-font fw-medium price-action-period">/ Day</span>
                            </div>
                            <p class="body-font small mb-4 lh-base price-action-disclaimer">Prices may vary depending on rental duration and season. Standard insurance included.</p>
                            <?php 
                                $laravel_car_id = get_post_meta( get_the_ID(), 'laravel_car_id', true ); 
                                if ( empty( $laravel_car_id ) ) :
                            ?>
                                <button type="button" class="btn w-100 py-3 text-uppercase fw-bold rounded-3 body-font btn-secondary" disabled title="No sincronizado con la base de datos central">Not Available</button>
                            <?php else : ?>
                                <button type="button" class="book-car-btn btn w-100 py-3 text-uppercase fw-bold rounded-3 body-font btn-book-action" data-car-id="<?php echo esc_attr( $laravel_car_id ); ?>">Book Now</button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="specs-card p-4 rounded-4">
                            <h4 class="heading-font fs-5 text-uppercase fw-bold border-bottom pb-3 mb-3 m-0 specs-title">Technical Specs</h4>
                            
                            <ul class="list-unstyled body-font m-0">
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-tag me-3 text-secondary"></i> Brand</span>
                                    <span class="fw-bold text-dark"><?php echo ($brand); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-car-front me-3 text-secondary"></i> Model</span>
                                    <span class="fw-bold text-dark"><?php echo ($model); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-calendar me-3 text-secondary"></i> Year</span>
                                    <span class="fw-bold text-dark"><?php echo ($year); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-fuel-pump me-3 text-secondary"></i> Fuel</span>
                                    <span class="fw-bold text-dark"><?php echo ($fuel); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-speedometer2 me-3 text-secondary"></i> Mileage</span>
                                    <span class="fw-bold text-dark"><?php echo ($km); ?> km</span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-gear me-3 text-secondary"></i> Transmission</span>
                                    <span class="fw-bold text-dark"><?php echo ($trans); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-box me-3 text-secondary"></i> Engine Disp.</span>
                                    <span class="fw-bold text-dark"><?php echo ($engine); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-lightning me-3 text-secondary"></i> Horsepower</span>
                                    <span class="fw-bold text-dark"><?php echo ($hp); ?> hp</span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-cloud-arrow-up me-3 text-secondary"></i> Emissions</span>
                                    <span class="fw-bold text-dark"><?php echo ($emissions); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-door-closed me-3 text-secondary"></i> Doors</span>
                                    <span class="fw-bold text-dark"><?php echo ($doors); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-people me-3 text-secondary"></i> Seats</span>
                                    <span class="fw-bold text-dark"><?php echo ($seats); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-bounding-box me-3 text-secondary"></i> Body Type</span>
                                    <span class="fw-bold text-dark"><?php echo ($body); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom spec-list-item">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-suitcase me-3 text-secondary"></i> Trunk Vol.</span>
                                    <span class="fw-bold text-dark"><?php echo ($trunk); ?></span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-2">
                                    <span class="text-muted d-flex align-items-center"><i class="bi bi-palette me-3 text-secondary"></i> Color</span>
                                    <span class="fw-bold text-dark"><?php echo ($color); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <?php endwhile; 
        endif;?>

<?php get_footer(); ?>