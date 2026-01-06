<?= $this->extend('student/layout') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 mb-md-5 text-center text-md-start">
    <div class="col-12">
        <h2 class="fw-bold mb-1">Welcome back, <?= session()->get('full_name') ?>! 👋</h2>
        <p class="text-muted small mb-0">
            <?php if ($class): ?>
                Enrolled in: <strong class="text-primary"><?= htmlspecialchars($class['class_name']) ?></strong>
            <?php else: ?>
                <span class="text-danger">Not enrolled in any class.</span>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Assigned Courses Card -->
    <div class="col-md-8">
        <div class="card p-4 h-100 shadow-sm border-0 bg-white">
            <h5 class="fw-bold mb-4">My Courses</h5>
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">No courses assigned to your class yet.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <?php foreach ($courses as $course): ?>
                        <div class="col">
                            <div class="p-3 border rounded-4 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                                    <i class="bi bi-mortarboard-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold small"><?= htmlspecialchars($course['course_name']) ?></h6>
                                    <small class="text-muted">ID: #<?= $course['id'] ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats/Profile Card -->
    <div class="col-md-4">
        <div class="card p-4 h-100 shadow-sm border-0 bg-white">
            <h5 class="fw-bold mb-4">My Progress</h5>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3">
                    <i class="bi bi-trophy-fill text-success fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">0</h4>
                    <span class="text-muted small">Exams Completed</span>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                    <i class="bi bi-clock-history text-primary fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">0</h4>
                    <span class="text-muted small">Total Study Hours</span>
                </div>
            </div>
            <hr class="opacity-25 my-4">
            <button class="btn btn-student w-100">Browse Courses</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>