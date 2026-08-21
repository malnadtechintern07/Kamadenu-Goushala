<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-4 bg-dark text-white border-bottom border-warning">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="font-heading text-warning mb-1"><?php echo __t('nav_volunteer'); ?></h1>
                <p class="text-white-50 mb-0">Offer your time, skills, and love towards daily cow care, organic farming, and Goushala events.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="devotional-phrase fs-4">ಗೋ ಮಾತಾ ಕಿ ಜೈ</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="kamadenu-card p-4 p-md-5">
                    <h3 class="font-heading mb-4 text-center">Join Our Selfless Gouseva Volunteer Team</h3>

                    <form id="volunteer-form">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Your Name" value="<?php echo $user ? e($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Email Address" value="<?php echo $user ? e($user['email']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 Phone Number" value="<?php echo $user ? e($user['phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Availability</label>
                                <select name="availability" class="form-select">
                                    <option value="Weekends Only">Weekends Only</option>
                                    <option value="Full Time">Full Time</option>
                                    <option value="Part Time (Mornings)">Part Time (Mornings)</option>
                                    <option value="Special Events & Campaigns">Special Events & Rescue Campaigns</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Primary Area of Interest</label>
                                <select name="interest_area" class="form-select">
                                    <option value="Daily Fodder & Grooming">Daily Fodder & Grooming</option>
                                    <option value="Veterinary Assistance">Veterinary Medical Assistance</option>
                                    <option value="Organic Farming & Bio-Inputs">Organic Farming & Bio-Inputs</option>
                                    <option value="Digital Media & Photography">Digital Media & Photography</option>
                                    <option value="Event Coordination">Event Coordination & Gou Pooja</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-ui small fw-bold">Relevant Skills</label>
                                <input type="text" name="skills" class="form-control" placeholder="e.g. Animal Care, IT, Photography, Agriculture">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-ui small fw-bold">Why do you wish to join Kamadenu Goushala?</label>
                                <textarea name="message" rows="4" class="form-control" placeholder="Share your motivation and past experience..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kamadenu-primary w-100 py-3 font-ui fw-bold fs-5 shadow">
                            <i class="fas fa-paper-plane me-2"></i> Submit Volunteer Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('volunteer-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    const data = {
        name: this.name.value,
        email: this.email.value,
        phone: this.phone.value,
        availability: this.availability.value,
        interest_area: this.interest_area.value,
        skills: this.skills.value,
        message: this.message.value
    };

    fetch('/Kamadenu/api/volunteers.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        showToast(res.message, res.success ? 'success' : 'danger');
        if (res.success) {
            this.reset();
        }
        btn.disabled = false;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
