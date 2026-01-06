<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Student Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Student Registry</h3>
        <p class="text-muted">Manage student profiles, contacts, and class enrollments.</p>
    </div>
    <button class="btn btn-expert px-4 py-2 shadow-sm d-flex align-items-center" data-bs-toggle="modal"
        data-bs-target="#addStudentModal">
        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Enroll New Student
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle custom-datatable w-100">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 text-uppercase small fw-bold">ID</th>
                        <th class="border-0 text-uppercase small fw-bold">Student</th>
                        <th class="border-0 text-uppercase small fw-bold">Class</th>
                        <th class="border-0 text-uppercase small fw-bold">Contact</th>
                        <th class="border-0 text-uppercase small fw-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td class="small text-muted">#
                                <?= $s['id'] ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= $s['photo'] ? base_url('uploads/students/' . $s['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($s['full_name']) . '&background=6366f1&color=fff' ?>"
                                        class="rounded-circle me-3 border shadow-sm" width="40" height="40"
                                        style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            <?= htmlspecialchars($s['full_name']) ?>
                                        </h6>
                                        <small class="text-muted">@
                                            <?= htmlspecialchars($s['username']) ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-3 border">
                                    <i class="bi bi-door-open me-1"></i>
                                    <?= htmlspecialchars($s['class_name'] ?? 'Not Assigned') ?>
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold"><i class="bi bi-telephone text-muted me-1"></i>
                                    <?= htmlspecialchars($s['phone'] ?? 'N/A') ?>
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 150px;"><i
                                        class="bi bi-geo-alt text-muted me-1"></i>
                                    <?= htmlspecialchars($s['address'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm rounded-3">
                                    <button class="btn btn-sm btn-light border-0 px-3" data-bs-toggle="modal"
                                        data-bs-target="#editStudentModal" data-id="<?= $s['id'] ?>"
                                        data-fullname="<?= htmlspecialchars($s['full_name']) ?>"
                                        data-username="<?= htmlspecialchars($s['username']) ?>"
                                        data-phone="<?= htmlspecialchars($s['phone'] ?? '') ?>"
                                        data-address="<?= htmlspecialchars($s['address'] ?? '') ?>"
                                        data-classid="<?= $s['class_id'] ?? '' ?>">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border-0 px-3 delete-btn"
                                        data-url="<?= site_url('teacher/students/delete/' . $s['id']) ?>"
                                        data-name="<?= htmlspecialchars($s['full_name']) ?>">
                                        <i class="bi bi-trash3-fill text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="bi bi-person-plus text-primary me-2"></i> Enroll New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/students/save') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name</label>
                        <input type="text" name="full_name" class="form-control rounded-3"
                            placeholder="Enter student's legal name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" class="form-control rounded-3"
                            placeholder="Preferred login username" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="tel" name="phone" class="form-control rounded-3" placeholder="+123...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assign Class</label>
                            <select name="class_id" class="form-select rounded-3" required>
                                <option value="">Select a class...</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['class_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Address</label>
                        <textarea name="address" class="form-control rounded-3" rows="2"
                            placeholder="Residential address"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Student Photo (Optional)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="alert alert-info mt-3 py-2 border-0 small rounded-3">
                        <i class="bi bi-key me-2"></i> Default password set to: <strong>123456</strong>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-expert rounded-3 px-4">Register Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i> Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teacher/students/save') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="edit-student-id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name</label>
                        <input type="text" name="full_name" id="edit-student-fullname" class="form-control rounded-3"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" id="edit-student-username" class="form-control rounded-3"
                            readonly>
                        <div class="form-text small opacity-75">Username cannot be changed for security.</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone</label>
                            <input type="tel" name="phone" id="edit-student-phone" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Class</label>
                            <select name="class_id" id="edit-student-classid" class="form-select rounded-3" required>
                                <option value="">Select a class...</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['class_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Address</label>
                        <textarea name="address" id="edit-student-address" class="form-control rounded-3"
                            rows="2"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Update Photo (Optional)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        $('.custom-datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search students..."
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        $('#editStudentModal').on('show.bs.modal', function (event) {
            const btn = $(event.relatedTarget);
            const id = btn.data('id');
            const fullname = btn.data('fullname');
            const username = btn.data('username');
            const phone = btn.data('phone');
            const address = btn.data('address');
            const classid = btn.data('classid');

            $(this).find('#edit-student-id').val(id);
            $(this).find('#edit-student-fullname').val(fullname);
            $(this).find('#edit-student-username').val(username);
            $(this).find('#edit-student-phone').val(phone);
            $(this).find('#edit-student-address').val(address);
            $(this).find('#edit-student-classid').val(classid);
        });

        $(document).on('click', '.delete-btn', function () {
            const url = $(this).data('url');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete Student?',
                text: `Removing "${name}" will permanently delete their records.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Yes, remove them',
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