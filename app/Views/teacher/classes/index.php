<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Class Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Manage Classes</h3>
        <p class="text-muted small mb-0">List of all active student classes and groups.</p>
    </div>
    <button type="button" class="btn btn-expert" data-bs-toggle="modal" data-bs-target="#addClassModal">
        <i class="bi bi-plus-lg me-2"></i> Add New Class
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover datatable align-middle">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Class Name</th>
                        <th width="150" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td class="text-muted small">#<?= $class['id'] ?></td>
                                <td>
                                    <div class="fw-bold fs-6"><?= htmlspecialchars($class['class_name']) ?></div>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success small border border-success border-opacity-25 mt-1">
                                        <i class="bi bi-book-half me-1"></i> <?= $class['course_count'] ?> Courses Assigned
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm rounded-3">
                                        <button class="btn btn-sm btn-light border-0 px-3 edit-class"
                                            data-id="<?= $class['id'] ?>"
                                            data-name="<?= htmlspecialchars($class['class_name']) ?>" data-bs-toggle="modal"
                                            data-bs-target="#editClassModal">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0 px-3 assign-courses-btn"
                                            data-id="<?= $class['id'] ?>"
                                            data-name="<?= htmlspecialchars($class['class_name']) ?>" data-bs-toggle="modal"
                                            data-bs-target="#assignCoursesModal">
                                            <i class="bi bi-book-half text-success"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0 px-3 delete-btn"
                                            data-url="<?= site_url('teacher/classes/delete/' . $class['id']) ?>"
                                            data-name="<?= htmlspecialchars($class['class_name']) ?>">
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

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Create New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/classes/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Class Name</label>
                        <input type="text" name="class_name" class="form-control rounded-3 p-2"
                            placeholder="e.g. Computer Science 2024" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-expert px-4">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/classes/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit-class-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Class Name</label>
                        <input type="text" name="class_name" id="edit-class-name" class="form-control rounded-3 p-2"
                            required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-expert px-4">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Courses Modal -->
<div class="modal fade" id="assignCoursesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered shadow">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-book text-primary me-2"></i> Assign Courses to <span
                        id="assign-class-name" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/classes/saveCourses') ?>" method="post" id="assignCoursesForm">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" id="assign-class-id">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Select the courses that this class will study. Students in this
                        class will automatically be enrolled in these courses.</p>

                    <div class="row row-cols-1 row-cols-md-2 g-3" id="courses-list-container">
                        <?php foreach ($all_courses as $course): ?>
                            <div class="col">
                                <div class="p-3 border rounded-4 course-card hover-lift h-100">
                                    <div class="form-check m-0">
                                        <input class="form-check-input me-3" type="checkbox" name="courses[]"
                                            value="<?= $course['id'] ?>" id="course_<?= $course['id'] ?>">
                                        <label class="form-check-label w-100" for="course_<?= $course['id'] ?>"
                                            style="cursor: pointer;">
                                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($course['course_name']) ?></h6>
                                            <small
                                                class="text-muted"><?= $course['fee'] > 0 ? '$' . $course['fee'] : 'Free' ?></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-expert px-4 shadow">Save Assignments</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .course-card {
        transition: all 0.2s;
        border: 2px solid #f1f5f9 !important;
    }

    .course-card:has(.form-check-input:checked) {
        border-color: #6366f1 !important;
        background: #f5f3ff;
    }

    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Edit Class Modal Trigger
        $(document).on('click', '.edit-class', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#edit-class-id').val(id);
            $('#edit-class-name').val(name);
        });

        // Assign Courses Modal Trigger
        $(document).on('click', '.assign-courses-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#assign-class-id').val(id);
            $('#assign-class-name').text(name);

            // Fetch currently assigned courses
            $.get(`<?= site_url('teacher/classes/getAssignedCourses/') ?>${id}`, function (assignedIds) {
                // Clear all first
                $('#assignCoursesModal input[type="checkbox"]').prop('checked', false);
                // Check those assigned
                assignedIds.forEach(courseId => {
                    $(`#course_${courseId}`).prop('checked', true);
                });
            });
        });

        // Delete Confirmation
        $(document).on('click', '.delete-btn', function () {
            const url = $(this).data('url');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete the class "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
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