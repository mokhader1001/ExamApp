<?= $this->extend('student/layout') ?>

<?= $this->section('title') ?>My Exams
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold mb-1">My Assessments</h2>
    <p class="text-muted">View your active exams and previous results.</p>
</div>

<div class="row g-4">
    <?php if (empty($exams)): ?>
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                <div class="py-5">
                    <i class="bi bi-journal-x text-muted display-1 opacity-25"></i>
                    <h4 class="mt-4 fw-bold">No exams available</h4>
                    <p class="text-muted">You have no active exams assigned to your class at this moment.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($exams as $exam): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small">
                                <?= htmlspecialchars($exam['course_name']) ?>
                            </span>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i>
                                <?= $exam['duration_minutes'] ?>m
                            </div>
                        </div>
                        <h5 class="fw-bold mb-3">
                            <?= htmlspecialchars($exam['title']) ?>
                        </h5>

                        <div class="p-3 bg-light rounded-3 mb-4">
                            <?php if (!$exam['attempt']): ?>
                                <div class="small text-success d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2"></i> Ready to Start
                                </div>
                            <?php elseif ($exam['attempt']['status'] === 'in_progress'): ?>
                                <div class="small text-warning d-flex align-items-center">
                                    <i class="bi bi-pause-circle-fill me-2"></i> In Progress
                                </div>
                            <?php else: ?>
                                <div class="small text-muted d-flex align-items-center">
                                    <?php if ($exam['attempt']['is_released']): ?>
                                        <i class="bi bi-check-circle-fill me-2"></i> Graded
                                    <?php else: ?>
                                        <i class="bi bi-clock-history me-2"></i> Result Pending
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$exam['attempt'] || $exam['attempt']['status'] === 'in_progress'): ?>
                            <a href="<?= site_url('student/exams/enter/' . $exam['id']) ?>"
                                class="btn <?= ($exam['attempt']) ? 'btn-warning' : 'btn-expert' ?> w-100 rounded-pill py-2 shadow-sm">
                                <?= ($exam['attempt']) ? 'Resume Exam' : 'Enter Exam' ?>
                            </a>
                        <?php elseif ($exam['attempt']['status'] === 'submitted'): ?>
                            <?php if ($exam['attempt']['is_released']): ?>
                                <a href="<?= site_url('student/exams/result/' . $exam['attempt']['id']) ?>"
                                    class="btn btn-outline-success w-100 rounded-pill py-2 fw-bold">
                                    <i class="bi bi-eye me-2"></i> View Result
                                </a>
                            <?php else: ?>
                                <button class="btn btn-light w-100 rounded-pill py-2 border shadow-none opacity-75" disabled>
                                    <i class="bi bi-hourglass-split me-2"></i> Marks Not Released
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-light w-100 rounded-pill py-2 border disabled">
                                Exam <?= ucfirst($exam['attempt']['status']) ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>