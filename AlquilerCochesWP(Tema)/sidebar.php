<div class="col-lg-3 col-xxl-2 mb-5 mb-lg-0">
    <div class="collapse d-lg-block" id="sidebarCollapse">
        <div class="sidebar-wrapper">
            <form action="<?php echo home_url('/Cars'); ?>" method="GET" id="filter-form">
                <div class="sidebar-header">
                    <h3 class="sidebar-title">Filters</h3>
                    <a href="<?php echo home_url('/Cars'); ?>" class="reset-filters-btn" style="text-decoration:none;">Reset All</a>
                </div>

                <div class="search-input-wrapper">
                    <input type="text" name="search_car" class="search-input" placeholder="Search cars..." value="<?php echo esc_attr($_GET['search_car'] ?? ''); ?>">
                    <i class="bi bi-search search-icon"></i>
                </div>

                <div class="filter-group">
                    <div class="filter-group-title">
                        <span>Sort By</span>
                        <i class="bi bi-sort-down"></i>
                    </div>
                    <div class="sort-select-wrapper">
                        <select name="orderby" class="sort-select" onchange="document.getElementById('filter-form').submit();">
                            <option value="date" <?php selected($_GET['orderby'] ?? '', 'date'); ?>>Newest Arrivals</option>
                            <option value="price_low" <?php selected($_GET['orderby'] ?? '', 'price_low'); ?>>Price: Low to High</option>
                            <option value="price_high" <?php selected($_GET['orderby'] ?? '', 'price_high'); ?>>Price: High to Low</option>
                        </select>
                        <i class="bi bi-chevron-down sort-icon"></i>
                    </div>
                </div>

                <div class="filter-group">
                    <div class="filter-group-title">
                        <span>Category</span>
                        <i class="bi bi-car-front"></i>
                    </div>
                    <div class="checkbox-list">
                        <label class="custom-checkbox">
                            <input type="checkbox" <?php echo !isset($_GET['body']) ? 'checked' : ''; ?> onchange="window.location.href='<?php echo home_url('/Cars'); ?>'">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">All Categories</span>
                        </label>
                        <?php
                        global $wpdb;
                        $bodies = $wpdb->get_results("SELECT meta_value, COUNT(post_id) as count FROM $wpdb->postmeta WHERE meta_key = 'rlp_car_body' AND meta_value != '' GROUP BY meta_value");
                        foreach ($bodies as $b) : 
                            $checked = (isset($_GET['body']) && in_array($b->meta_value, $_GET['body'])) ? 'checked' : '';
                        ?>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="body[]" value="<?php echo esc_attr($b->meta_value); ?>" <?php echo $checked; ?>>
                                <span class="checkmark"></span>
                                <span class="checkbox-label"><?php echo esc_html(ucfirst($b->meta_value)); ?></span>
                                <span class="count-badge"><?php echo $b->count; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <div class="filter-group-title">
                        <span>Price Range</span>
                        <i class="bi bi-cash"></i>
                    </div>
                    <div class="range-slider-container">
                        <?php $max_price = $_GET['max_price'] ?? '500'; ?>
                        <input type="range" name="max_price" class="range-slider" min="50" max="1000" value="<?php echo esc_attr($max_price); ?>" 
                               oninput="document.getElementById('price-val').innerText = '$' + this.value;">
                        <div class="price-values-display">
                            <span class="price-tag">$50</span>
                            <span class="price-tag" id="price-val">$<?php echo esc_html($max_price); ?></span>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <div class="filter-group-title">
                        <span>Transmission</span>
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="checkbox-list">
                        <?php 
                        $transmissions = ['Automatic', 'Manual'];
                        foreach ($transmissions as $t) : 
                            $checked = (isset($_GET['trans']) && in_array($t, $_GET['trans'])) ? 'checked' : '';
                            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key = 'rlp_car_transmission' AND meta_value = %s", $t));
                        ?>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="trans[]" value="<?php echo $t; ?>" <?php echo $checked; ?>>
                                <span class="checkmark"></span>
                                <span class="checkbox-label"><?php echo $t; ?></span>
                                <span class="count-badge"><?php echo (int)$count; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn-cta w-100 mt-4 py-3">Apply Filters</button>
            </form>
        </div>
    </div>
</div>