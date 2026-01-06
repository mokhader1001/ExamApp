<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Course Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Expert Courses</h3>
        <p class="text-muted small mb-0">Review and manage your curriculum catalog.</p>
    </div>
    <button type="button" class="btn btn-expert" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="bi bi-plus-lg me-2"></i> Create New Course
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover datatable align-middle">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Course Details</th>
                        <th>Registration Fee</th>
                        <th width="150" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="text-muted small">#<?= $course['id'] ?></td>
                                <td>
                                    <div class="fw-semibold text-dark mb-1"><?= $course['course_name'] ?></div>
                                    <div class="text-muted small">
                                        <?= strlen($course['description']) > 60 ? substr($course['description'], 0, 60) . '...' : $course['description'] ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success fw-bold px-3 py-2">
                                        $<?= number_format($course['fee'], 2) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border-0 rounded-3 me-1 px-3 edit-course"
                                            data-id="<?= $course['id'] ?>"
                                            data-name="<?= htmlspecialchars($course['course_name']) ?>"
                                            data-fee="<?= $course['fee'] ?>"
                                            data-desc="<?= htmlspecialchars($course['description']) ?>" data-bs-toggle="modal"
                                            data-bs-target="#editCourseModal">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0 rounded-3 px-3 delete-btn"
                                            data-url="<?= site_url('teacher/courses/delete/' . $course['id']) ?>"
                                            data-name="<?= htmlspecialchars($course['course_name']) ?>">
                                            <i class="bi bi-trash3-fill text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Design New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/courses/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Name</label>
                            <input type="text" name="course_name" class="form-control rounded-3 p-2"
                                placeholder="e.g. Advanced Physics for Engineers" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Fee ($)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-3">$</span>
                                <input type="number" step="0.01" name="fee" class="form-control rounded-end-3 p-2"
                                    placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Course Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="4"
                                placeholder="Highlight key learning objectives..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-expert px-4">Publish Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/courses/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit-course-id">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Name</label>
                            <input type="text" name="course_name" id="edit-course-name"
                                class="form-control rounded-3 p-2" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Fee ($)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-3">$</span>
                                <input type="number" step="0.01" name="fee" id="edit-course-fee"
                                    class="form-control rounded-end-3 p-2" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Course Description</label>
                            <textarea name="description" id="edit-course-desc" class="form-control rounded-3"
                                rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-expert px-4">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        $(document).on('show.bs.modal', '#editCourseModal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const fee = button.data('fee');
            const desc = button.data('desc');

            $(this).find('#edit-course-id').val(id);
            $(this).find('#edit-course-name').val(name);
            $(this).find('#edit-course-fee').val(fee);
            $(this).find('#edit-course-desc').val(desc);
        });

        $(document).on('click', '.delete-btn', function () {
            const url = $(this).data('url');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete Course?',
                text: `This will permanently remove the course "${name}".`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Confirm Delete',
                cancelButtonText: 'Keep Course',
                customClass: {
                    confirmButton: 'btn btn-danger px-4 rounded-3',
                    cancelButton: 'btn btn-light px-4 rounded-3 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>