<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Add Class
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h2 class="h5 mb-0">Add New Class</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('teacher/classes/save') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Class Name</label>
                        <input type="text" name="class_name" class="form-control"
                            placeholder="e.g. Grade 10-A, Computer Science 101" required>
                        <div class="form-text mt-2 text-muted">Give your class a descriptive name that students will
                            recognize.</div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= site_url('teacher/classes') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>