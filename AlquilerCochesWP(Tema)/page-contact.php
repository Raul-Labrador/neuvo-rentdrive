<?php

get_header();
?>
    <?php get_template_part('nav'); ?>      

    <section class="page-header">
        <div class="container">
            <h1 class="page-header-title">Contact Us</h1>
            <p class="page-header-breadcrumb">
                <a href="<?php echo home_url(); ?>">Home</a> / Contact
            </p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-info-card">
                        <h2 class="contact-info-title">Get In Touch</h2>

                        <div class="contact-info-item">
                            <div class="contact-icon-placeholder" style="font-size: 1.2rem;"><i
                                    class="bi bi-geo-alt-fill"></i></div>
                            <div class="contact-info-text">
                                <h4>Our Location</h4>
                                <p>123 Premium Street, Suite 100<br>New York, NY 10001</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon-placeholder" style="font-size: 1.2rem;"><i
                                    class="bi bi-telephone-fill"></i></div>
                            <div class="contact-info-text">
                                <h4>Phone Number</h4>
                                <p>+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon-placeholder" style="font-size: 1.2rem;"><i
                                    class="bi bi-envelope-fill"></i></div>
                            <div class="contact-info-text">
                                <h4>Email Address</h4>
                                <p>info@neuvo.com<br>support@neuvo.com</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon-placeholder" style="font-size: 1.2rem;"><i
                                    class="bi bi-clock-fill"></i></div>
                            <div class="contact-info-text">
                                <h4>Business Hours</h4>
                                <p>Monday - Friday: 8:00 AM - 8:00 PM<br>Saturday - Sunday: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="heading-font"
                                style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 15px;">
                                Follow Us</h4>
                            <div class="footer-social">
                                <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                                <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="contact-form-card">
                        <h2 class="contact-form-title">Send Us A Message</h2>

                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input type="text" id="firstName" name="firstName"
                                            placeholder="Enter your first name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" id="lastName" name="lastName"
                                            placeholder="Enter your last name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" name="email" placeholder="Enter your email"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="booking">Booking Inquiry</option>
                                    <option value="support">Customer Support</option>
                                    <option value="corporate">Corporate Rental</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <textarea id="message" name="message" placeholder="Tell us how we can help you..."
                                    required></textarea>
                            </div>

                            <button type="submit" class="btn-primary-neuvo w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-0">
        <div class="map-placeholder">
        </div>
    </section>

    <section class="vehicles-section">
        <div class="container">
            <h2 class="section-title section-title-dark">Our Locations</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="trust-card">
                        <div class="trust-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <h3 class="trust-card-title">New York - Downtown</h3>
                        <p class="trust-card-description">
                            123 Premium Street, Suite 100<br>
                            New York, NY 10001<br><br>
                            <strong>Hours:</strong> Mon-Sun 8AM-8PM
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="trust-card">
                        <div class="trust-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <h3 class="trust-card-title">New York - JFK Airport</h3>
                        <p class="trust-card-description">
                            JFK International Airport, Terminal 4<br>
                            Queens, NY 11430<br><br>
                            <strong>Hours:</strong> 24/7 Service
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="trust-card">
                        <div class="trust-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <h3 class="trust-card-title">Los Angeles - Beverly Hills</h3>
                        <p class="trust-card-description">
                            456 Luxury Boulevard<br>
                            Beverly Hills, CA 90210<br><br>
                            <strong>Hours:</strong> Mon-Sun 8AM-8PM
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <<section class="trust-section">
        <div class="container">
            <div class="trust-content">
                <h2 class="section-title section-title-dark">Frequently Asked Questions</h2>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button heading-font" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq1">
                                        What documents do I need to rent a car?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body body-font">
                                        To rent a car from NEUVO, you'll need a valid driver's license, a credit card in
                                        your name, and a valid ID or passport. International customers may need an
                                        International Driving Permit depending on their country of origin.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed heading-font" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                        What is the minimum age to rent a car?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body body-font">
                                        The minimum age to rent a car is 21 years old. However, for luxury and sports
                                        vehicles, the minimum age is 25. Young driver surcharges may apply for drivers
                                        under 25.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed heading-font" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Can I modify or cancel my reservation?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body body-font">
                                        Yes, you can modify or cancel your reservation up to 24 hours before your
                                        scheduled pickup time without any fees. Cancellations made less than 24 hours in
                                        advance may incur a cancellation fee.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed heading-font" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Is insurance included in the rental price?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body body-font">
                                        Basic liability insurance is included in all our rentals. We also offer
                                        comprehensive coverage options including collision damage waiver (CDW) and
                                        personal accident insurance for additional peace of mind.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed heading-font" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq5">
                                        Do you offer airport pickup and drop-off?
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body body-font">
                                        Yes, we offer convenient airport pickup and drop-off services at major airports.
                                        Our representatives will meet you at the designated location for a seamless
                                        experience.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
