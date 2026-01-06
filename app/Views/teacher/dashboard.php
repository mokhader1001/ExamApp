<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800">Welcome, Instructor!</h1>
    </div>
</div>

<div class="row">
    <!-- Classes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                            style="font-size: 0.8rem;">Total Classes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $classCount ?? 0 ?></div>
                    </div>
                    <div class="col-auto text-primary opacity-25">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1"
                            style="font-size: 0.8rem;">Active Courses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $courseCount ?? 0 ?></div>
                    </div>
                    <div class="col-auto text-success opacity-25">
                        <i class="bi bi-book-half fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body p-4">
                <h5 class="fw-bold">Set up your Exam</h5>
                <p class="text-white-50 small">Configure rules, questions, tab-switching limits, and timing for your
                    students.</p>
                <a href="#" class="btn btn-sm btn-light mt-2 fw-bold text-primary px-3">Start Now</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>