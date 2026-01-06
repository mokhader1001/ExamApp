<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Add Course
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h2 class="h5 mb-0">Create New Course</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('teacher/courses/save') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Course Name</label>
                        <input type="text" name="course_name" class="form-control"
                            placeholder="e.g. Introduction to Mathematics" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Course Fee ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">$</span>
                            <input type="number" step="0.01" name="fee" class="form-control border-start-0"
                                placeholder="0.00" required>
                        </div>
                        <div class="form-text mt-2">Enter the registration fee for this course.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"
                            placeholder="Briefly describe what this course covers..."></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                        <a href="<?= site_url('teacher/courses') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>