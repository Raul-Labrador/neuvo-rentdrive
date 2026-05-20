<?php

get_header();
?>
    <?php get_template_part('nav'); ?> 

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title">About Us</h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / About
            </p>
        </div>
    </section>

    <section class="about-story-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="about-story-label">Our Story</span>
                    <h2 class="about-story-title">Redefining The Car Rental Experience Since 2015</h2>
                    <p class="about-story-text">
                        NEUVO was born from a simple yet powerful idea: that renting a car should feel as exciting and
                        premium as the vehicles themselves. Founded in 2015 in the heart of New York City, what started
                        as a small fleet of handpicked luxury vehicles has grown into one of the most trusted premium
                        car
                        rental services in the country.
                    </p>
                    <p class="about-story-text">
                        Our founders, passionate automotive enthusiasts, noticed a gap in the market — rental
                        experiences
                        that felt impersonal and outdated. They set out to build something different: a service where
                        every interaction, from booking to return, would feel seamless, modern, and truly premium.
                    </p>
                    <p class="about-story-text">
                        Today, NEUVO operates across multiple locations with a fleet of over 500 vehicles, ranging from
                        elegant sedans and powerful sports cars to eco-friendly electric vehicles. But our core mission
                        remains the same — to provide an unparalleled driving experience backed by world-class service.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="about-story-image">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about-story.jpg" alt="NEUVO Story">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-timeline-section">
        <div class="container">
            <h2 class="section-title section-title-light">Our Journey</h2>

            <div class="timeline-container">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2015 — The Beginning</div>
                        <p class="timeline-text">
                            NEUVO launches in New York City with a curated fleet of 25 premium vehicles, bringing
                            a fresh approach to car rentals with a focus on quality and customer experience.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2017 — Expanding Horizons</div>
                        <p class="timeline-text">
                            After rapid growth and strong customer demand, NEUVO expands to Los Angeles and Miami,
                            growing the fleet to over 150 vehicles and introducing luxury SUV and convertible
                            categories.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2019 — Going Green</div>
                        <p class="timeline-text">
                            Committed to sustainability, NEUVO introduces its first electric vehicle collection,
                            partnering with Tesla, BMW, and Audi to offer a fully electric rental option across
                            all locations.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2021 — Digital Transformation</div>
                        <p class="timeline-text">
                            NEUVO launches its fully digital platform, enabling contactless booking, keyless vehicle
                            access, and a seamless online experience that sets a new industry standard.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2023 — 500+ Vehicles Strong</div>
                        <p class="timeline-text">
                            With over 500 vehicles across 6 locations, NEUVO becomes a nationally recognized brand,
                            earning multiple industry awards for customer satisfaction and service excellence.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="timeline-year">2025 — The Future Is Now</div>
                        <p class="timeline-text">
                            NEUVO continues to innovate, exploring partnerships with autonomous driving companies and
                            introducing subscription-based rental plans for the modern commuter.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-values-section">
        <div class="container mb-5 text-center">
            <h2 class="section-title section-title-dark section-title-white mb-0">What Drives Us</h2>
        </div>

        <div class="drives-alternating-layout">
            <div class="drives-row">
                <div class="drives-content">
                    <span class="drives-number">01</span>
                    <div class="drives-text-block">
                        <span class="drives-label">Our Mission</span>
                        <h3 class="drives-title">Delivering Unforgettable Journeys</h3>
                        <p class="drives-desc">
                            To deliver the most seamless, premium, and enjoyable car rental experience in the
                            industry, making every journey memorable from the moment you book to the moment you
                            return. We believe every mile should feel extraordinary.
                        </p>
                    </div>
                </div>
                <div class="drives-visual">
                    <div class="drives-visual-inner">
                        <i class="bi bi-bullseye drives-large-icon"></i>
                    </div>
                </div>
            </div>

            <div class="drives-row reverse">
                <div class="drives-content">
                    <span class="drives-number">02</span>
                    <div class="drives-text-block">
                        <span class="drives-label">Our Vision</span>
                        <h3 class="drives-title">Setting The Global Standard</h3>
                        <p class="drives-desc">
                            To become the global benchmark for premium car rental services, setting the standard for
                            quality, technology, and sustainability in the mobility industry. We aim to redefine
                            what it means to rent a car.
                        </p>
                    </div>
                </div>
                <div class="drives-visual">
                    <div class="drives-visual-inner">
                        <i class="bi bi-eye-fill drives-large-icon"></i>
                    </div>
                </div>
            </div>

            <div class="drives-row">
                <div class="drives-content">
                    <span class="drives-number">03</span>
                    <div class="drives-text-block">
                        <span class="drives-label">Innovation</span>
                        <h3 class="drives-title">Pushing Boundaries Forward</h3>
                        <p class="drives-desc">
                            We constantly push boundaries, embracing the latest technology and trends to provide
                            cutting-edge solutions. From keyless access to AI-driven recommendations, we ensure
                            our customers always stay ahead of the curve.
                        </p>
                    </div>
                </div>
                <div class="drives-visual">
                    <div class="drives-visual-inner">
                        <i class="bi bi-lightning-charge-fill drives-large-icon"></i>
                    </div>
                </div>
            </div>

            <div class="drives-row reverse">
                <div class="drives-content">
                    <span class="drives-number">04</span>
                    <div class="drives-text-block">
                        <span class="drives-label">Our Values</span>
                        <h3 class="drives-title">Quality, Sustainability & People</h3>
                        <p class="drives-desc">
                            Every vehicle is meticulously maintained. We're committed to reducing our environmental
                            footprint through our growing electric fleet. And above all, our customers are at the
                            heart of everything we do — we listen, adapt, and exceed expectations.
                        </p>
                    </div>
                </div>
                <div class="drives-visual">
                    <div class="drives-visual-inner">
                        <i class="bi bi-heart-fill drives-large-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="about-stat-item">
                        <div class="about-stat-number hero-stat-number-small">500+</div>
                        <div class="about-stat-label">Premium Vehicles</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-stat-item">
                        <div class="about-stat-number hero-stat-number-small">50000+</div>
                        <div class="about-stat-label">Happy Customers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-stat-item">
                        <div class="about-stat-number hero-stat-number-small">6</div>
                        <div class="about-stat-label">Locations Nationwide</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-stat-item">
                        <div class="about-stat-number hero-stat-number-small">10+</div>
                        <div class="about-stat-label">Years Of Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-team-section">
        <div class="container-fluid px-4 px-xl-5">
            <h2 class="section-title section-title-dark">Meet The Team</h2>
            <p class="trust-subtitle trust-subtitle-team">
                The passionate people behind NEUVO who work tirelessly to deliver an exceptional experience.
            </p>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-ceo.png" alt="Alexander Mitchell">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">Alexander Mitchell</h3>
                            <p class="team-role">Co-Founder & CEO</p>
                            <p class="team-bio">
                                A visionary leader with over 15 years of experience in the automotive and hospitality
                                industries. Alexander's passion for cars and innovation drives NEUVO's strategic vision.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-coo.png" alt="Sofia Martínez">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">Sofia Martínez</h3>
                            <p class="team-role">Co-Founder & COO</p>
                            <p class="team-bio">
                                Operations expert who ensures every branch runs flawlessly. Sofia's attention to detail
                                and customer-first mindset have shaped NEUVO's reputation for excellence.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-cto.png" alt="James Chen">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">James Chen</h3>
                            <p class="team-role">Chief Technology Officer</p>
                            <p class="team-bio">
                                The tech mind behind NEUVO's digital platform. James leads a team of engineers building
                                the next generation of seamless rental technology, from keyless access to AI-driven
                                recommendations.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-fleet.png" alt="Marcus Thompson">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">Marcus Thompson</h3>
                            <p class="team-role">Fleet Director</p>
                            <p class="team-bio">
                                With a deep love for cars and 12 years of fleet management experience, Marcus handpicks
                                every vehicle in the NEUVO collection, ensuring only the best make it to our lineup.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-marketing.png" alt="Elena Rodriguez">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">Elena Rodriguez</h3>
                            <p class="team-role">Marketing Director</p>
                            <p class="team-bio">
                                Creative strategist responsible for NEUVO's brand identity and global marketing
                                campaigns. Elena brings 10 years of luxury brand experience to the table.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/team-cx.png" alt="David Park">
                        </div>
                        <div class="team-info">
                            <h3 class="team-name">David Park</h3>
                            <p class="team-role">Customer Experience Manager</p>
                            <p class="team-bio">
                                The voice of the customer at NEUVO. David ensures every touchpoint delivers a memorable
                                experience, leading our 24/7 support team and loyalty programs.
                            </p>
                            <div class="team-social">
                                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-team-photo-section">
        <div class="container">
            <div class="team-photo-interactive">
                <div class="team-photo-placeholder-text">TU FOTO DE EQUIPO AQUÍ<br><span
                        class="team-photo-placeholder-subtext">(Sustituir la imagen en el código HTML)</span></div>

                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/foto-equipo1.jpg" alt="The NEUVO Family" onerror="this.style.opacity='0'">

                <div class="team-photo-overlay">
                    <div class="team-photo-text-content">
                        <h3 class="team-photo-title brand-font">The NEUVO Family</h3>
                        <p class="team-photo-desc">Behind every perfect vehicle and premium experience, there is a
                            dedicated team united by the same passion. Together, we make your journey unforgettable.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-cta-section">
        <div class="container">
            <h2 class="about-cta-title">Ready To Experience<br>The NEUVO Difference?</h2>
            <p class="about-cta-text">
                Whether it's a weekend getaway, a business trip, or a special occasion — we have the perfect car
                waiting for you. Explore our fleet and book your ride today.
            </p>
            <a href="<?php echo home_url('/cars'); ?>" class="btn-cta">Explore Our Fleet</a>
        </div>
    </section>

<?php get_footer(); ?>
