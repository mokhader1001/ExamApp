<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Assign Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= site_url('teacher/classes') ?>" class="btn btn-light rounded-pill px-3 py-2 small mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Classes
    </a>
    <h3 class="fw-bold">Course Assignments: <span class="text-primary"><?= htmlspecialchars($class['class_name']) ?></span></h3>
    <p class="text-muted">Select the courses that belong to this class.</p>
</div>

<form action="<?= site_url('teacher/classes/saveCourses') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        <?php foreach ($all_courses as $course): ?>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4 d-flex align-items-start">
                    <div class="form-check p-0 m-0 w-100">
                        <input class="form-check-input d-none" type="checkbox" name="courses[]" value="<?= $course['id'] ?>" 
                               id="course_<?= $course['id'] ?>" <?= in_array($course['id'], $assigned_course_ids) ? 'checked' : '' ?>>
                        <label class="form-check-label w-100 d-flex align-items-center" for="course_<?= $course['id'] ?>" style="cursor: pointer;">
                            <div class="course-check-btn border rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                <i class="bi bi-check-lg text-white small"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($course['course_name']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($course['fee'] > 0 ? '$' . $course['fee'] : 'Free') ?></small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="sticky-bottom bg-white py-4 border-top">
        <button type="submit" class="btn btn-expert px-5 py-3 shadow w-100 w-md-auto">
            <i class="bi bi-save2-fill me-2"></i> Save Course Assignments
        </button>
    </div>
</form>

<style>
    .course-check-btn { transition: all 0.2s; border: 2px solid #e2e8f0; }
    .form-check-input:checked + .form-check-label .course-check-btn {
        background: #6366f1;
        border-color: #6366f1;
    }
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important; }
</style>
<?= $this->endSection() ?>
