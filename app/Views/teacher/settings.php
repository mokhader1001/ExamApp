<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>System Settings
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <form action="<?= site_url('settings/save') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Maintenance & Identity Group -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="fw-bold mb-0 text-primary">Portal Identity</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Site Project Name</label>
                                <input type="text" name="settings[site_name]" class="form-control rounded-3"
                                    value="<?= htmlspecialchars(get_setting('site_name', 'SalaamAcademy')) ?>">
                                <div class="form-text small opacity-75">Visible in navigation and titles.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Global Exam Rules</label>
                                <textarea name="settings[exam_rules]" class="form-control rounded-3"
                                    rows="3"><?= htmlspecialchars(get_setting('exam_rules')) ?></textarea>
                                <div class="form-text small opacity-75">Default instructions for students.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-danger">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="fw-bold mb-0 text-danger">Maintenance Lock</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">System Status</label>
                                <select name="settings[maintenance_mode]" class="form-select rounded-3 fw-bold">
                                    <option value="on" <?= get_setting('maintenance_mode') === 'on' ? 'selected' : '' ?>>ON
                                        (LOCKED)</option>
                                    <option value="off" <?= get_setting('maintenance_mode') === 'off' ? 'selected' : '' ?>>
                                        OFF (ACTIVE)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lock Reason (Shown to Students)</label>
                                <textarea name="settings[maintenance_reason]" class="form-control rounded-3" rows="3"
                                    placeholder="e.g. Updating Exam Questions..."><?= htmlspecialchars(get_setting('maintenance_reason')) ?></textarea>
                                <div class="form-text small opacity-75">Explain why students are blocked.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Switching Security Group -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-primary">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Tab Switching Detection Control</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Max Tab Switches Allowed</label>
                            <select name="settings[tab_switch_limit]" class="form-select rounded-3">
                                <option value="1" <?= get_setting('tab_switch_limit') == '1' ? 'selected' : '' ?>>1 Switch
                                </option>
                                <option value="2" <?= get_setting('tab_switch_limit') == '2' ? 'selected' : '' ?>>2
                                    Switches</option>
                                <option value="3" <?= get_setting('tab_switch_limit') == '3' ? 'selected' : '' ?>>3
                                    Switches (Max)</option>
                            </select>
                            <div class="form-text small opacity-75">Automatic kick if exceeded.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warning Alert Message</label>
                            <textarea name="settings[tab_switch_warning]" class="form-control rounded-3"
                                rows="2"><?= htmlspecialchars(get_setting('tab_switch_warning')) ?></textarea>
                            <div class="form-text small opacity-75">Shown when they switch once.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Final Kick Message</label>
                            <textarea name="settings[tab_switch_kick]" class="form-control rounded-3"
                                rows="2"><?= htmlspecialchars(get_setting('tab_switch_kick')) ?></textarea>
                            <div class="form-text small opacity-75">Shown when exam is canceled.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn btn-expert px-5 py-3 shadow">
                    <i class="bi bi-shield-check me-2"></i> Apply All System Settings
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>