<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Exam Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Manage Exams</h3>
        <p class="text-muted small mb-0">Create, configure, and monitor your academic assessments.</p>
    </div>
    <a href="<?= site_url('teacher/exams/create') ?>" class="btn btn-expert px-4 py-2">
        <i class="bi bi-plus-lg me-2"></i> Create New Exam
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Title & Course</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Tab Security</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold fs-6">
                                    <?= htmlspecialchars($exam['title']) ?>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary small border border-primary border-opacity-25 mt-1">
                                    <i class="bi bi-book me-1"></i>
                                    <?= htmlspecialchars($exam['course_name']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock text-muted me-2"></i>
                                    <span>
                                        <?= $exam['duration_minutes'] ?> mins
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if ($exam['status'] === 'active'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
                                <?php elseif ($exam['status'] === 'closed'): ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-2">Closed</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="bi bi-shield-check text-success me-1"></i>
                                    Limit: <strong>
                                        <?= $exam['tab_switch_limit'] ?>
                                    </strong>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-3">
                                    <a href="<?= site_url('teacher/exams/edit/' . $exam['id']) ?>"
                                        class="btn btn-sm btn-light border-0 px-3">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <a href="<?= site_url('teacher/exams/questions/' . $exam['id']) ?>"
                                        class="btn btn-sm btn-light border-0 px-3" title="Questions">
                                        <i class="bi bi-list-check text-success"></i>
                                    </a>
                                    <a href="<?= site_url('teacher/exams/submissions/' . $exam['id']) ?>"
                                        class="btn btn-sm btn-light border-0 px-3" title="Submissions">
                                        <i class="bi bi-people text-info"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0 px-3 dropdown-toggle no-caret"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                            <li><a class="dropdown-item"
                                                    href="<?= site_url('teacher/exams/status/' . $exam['id'] . '/active') ?>"><i
                                                        class="bi bi-play-fill text-success me-2"></i> Activate</a></li>
                                            <li><a class="dropdown-item"
                                                    href="<?= site_url('teacher/exams/status/' . $exam['id'] . '/closed') ?>"><i
                                                        class="bi bi-stop-fill text-danger me-2"></i> Close</a></li>
                                            <li><a class="dropdown-item"
                                                    href="<?= site_url('teacher/exams/status/' . $exam['id'] . '/draft') ?>"><i
                                                        class="bi bi-file-earmark-text text-muted me-2"></i> Set Draft</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><button class="dropdown-item text-danger delete-btn"
                                                    data-url="<?= site_url('teacher/exams/delete/' . $exam['id']) ?>"
                                                    data-name="<?= htmlspecialchars($exam['title']) ?>"><i
                                                        class="bi bi-trash3 me-2"></i> Delete</button></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
    .no-caret::after {
        display: none !important;
    }

    .dropdown-item {
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        color: var(--expert-accent);
    }

    .table thead th {
        border-top: none;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {

        $(document).on('click', '.delete-btn', function () {
            const url = $(this).data('url');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete Exam?',
                text: `WARNING: This will delete "${name}" and all its questions permanently.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete Permanently',
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