<?= $this->extend('student/layout') ?>

<?= $this->section('title') ?>My Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-success bg-opacity-10 py-5 position-relative">
                <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n5 text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=10b981&color=fff&size=128"
                        class="rounded-circle border border-4 border-white shadow-sm" width="100">
                </div>
            </div>
            <div class="card-body pt-5 mt-4">
                <h4 class="fw-bold mb-1 text-center"><?= $user['full_name'] ?></h4>
                <p class="text-muted small mb-4 text-uppercase fw-bold text-center">Student Scholar</p>

                <ul class="nav nav-pills nav-justified bg-light rounded-3 p-1 mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-2 fw-bold" id="info-tab" data-bs-toggle="tab"
                            data-bs-target="#info-pane" type="button">
                            <i class="bi bi-info-circle me-2"></i> Information
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-2 fw-bold" id="security-tab" data-bs-toggle="tab"
                            data-bs-target="#security-pane" type="button">
                            <i class="bi bi-shield-lock me-2"></i> Security
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <!-- General Information Tab -->
                    <div class="tab-pane fade show active p-2" id="info-pane" role="tabpanel">
                        <form action="<?= site_url('profile/updateInfo') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="full_name" class="form-control rounded-3 bg-light"
                                    value="<?= htmlspecialchars($user['full_name']) ?>" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control rounded-3 bg-light"
                                    value="<?= htmlspecialchars($user['username']) ?>" readonly>
                            </div>
                            <!-- Update button removed as per security requirements -->
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade p-2" id="security-pane" role="tabpanel">
                        <form action="<?= site_url('profile/updateSecurity') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password</label>
                                <input type="password" name="current_password" class="form-control rounded-3"
                                    placeholder="Verify current password" required>
                            </div>
                            <hr class="my-4 opacity-25">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="new_password" class="form-control rounded-3"
                                    placeholder="Minimum 6 characters" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Repeat New Password</label>
                                <input type="password" name="confirm_password" class="form-control rounded-3"
                                    placeholder="Confirm new password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger py-2 border-0"
                                    style="background: #ef4444;">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>