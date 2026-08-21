<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_contact'); ?></h1>
        <p class="text-white-50 mb-0">Reach out to Kamadenu Goushala for visits, donations, cow adoption, or volunteering.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4">Send Us a Message</h3>
                    <form action="/Kamadenu/message-sent.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 Phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-ui small fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Subject">
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-ui small fw-bold">Message</label>
                            <textarea name="message" rows="4" class="form-control" required placeholder="Write your inquiry or message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="kamadenu-card p-4 p-md-5 h-100">
                    <h3 class="font-heading mb-4">Goushala Information</h3>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-warning text-dark p-3 fs-4"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h5 class="font-heading mb-1">Sanctuary Address</h5>
                            <p class="text-muted mb-0">Kamadenu Sacred Grove, Nelamangala Road, Bengaluru Rural, Karnataka 562123</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-warning text-dark p-3 fs-4"><i class="fas fa-phone"></i></div>
                        <div>
                            <h5 class="font-heading mb-1">Helpline & WhatsApp</h5>
                            <p class="text-muted mb-0">+91 98800 12345 / +91 98450 67890</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-warning text-dark p-3 fs-4"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h5 class="font-heading mb-1">Email Inquiry</h5>
                            <p class="text-muted mb-0">info@kamadenugoushala.org</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-warning text-dark p-3 fs-4"><i class="fas fa-clock"></i></div>
                        <div>
                            <h5 class="font-heading mb-1">Visiting Hours</h5>
                            <p class="text-muted mb-0">Daily Morning: 6:00 AM – 12:00 PM<br>Evening: 4:00 PM – 7:30 PM (Evening Aarti at 6:30 PM)</p>
                        </div>
                    </div>

                    <div class="devotional-phrase fs-3 text-warning mt-5 text-center">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
