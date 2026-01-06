<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Submissions:
<?= htmlspecialchars($exam['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-0">Submissions: <span class="text-primary">
                <?= htmlspecialchars($exam['title']) ?>
            </span></h3>
        <p class="text-muted small">Review student attempts and assign final marks.</p>
    </div>
    <a href="<?= site_url('teacher/exams') ?>" class="btn btn-light rounded-pill px-4 border">Back to Exams</a>
</div>

<div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3">Student Name</th>
                    <th class="py-3 text-center">Start Time</th>
                    <th class="py-3 text-center">Submission Time</th>
                    <th class="py-3 text-center">Tab Switches</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="py-3 text-center">Final Score</th>
                    <th class="pe-4 py-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($submissions)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No submissions found for this exam.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($submissions as $sub): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($sub['student_name']) ?>&background=random"
                                        class="rounded-circle me-3" width="35" height="35">
                                    <span class="fw-bold">
                                        <?= htmlspecialchars($sub['student_name']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center small">
                                <?= date('M d, H:i', strtotime($sub['start_time'])) ?>
                            </td>
                            <td class="text-center small">
                                <?= $sub['submit_time'] ? date('M d, H:i', strtotime($sub['submit_time'])) : '-' ?>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge <?= $sub['tab_switch_count'] >= $exam['tab_switch_limit'] ? 'bg-danger' : 'bg-warning text-dark' ?> rounded-pill px-3">
                                    <?= $sub['tab_switch_count'] ?> Switches
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($sub['status'] === 'submitted'): ?>
                                    <span
                                        class="badge bg-success-subtle text-success border border-success px-3 rounded-pill">Submitted</span>
                                <?php else: ?>
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary px-3 rounded-pill">
                                        <?= ucfirst($sub['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-5 text-primary"><?= $sub['final_score'] ?> /
                                    <?= $total_possible_marks ?></span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                    <a href="<?= site_url('teacher/exams/marking/' . $sub['id']) ?>"
                                        class="btn btn-sm btn-expert rounded-pill px-4 shadow-sm">
                                        Mark Detail
                                    </a>
                                    <?php if ($sub['status'] === 'submitted' && !$sub['is_released']): ?>
                                        <a href="<?= site_url('teacher/exams/releaseResult/' . $sub['id']) ?>"
                                            class="btn btn-sm btn-light text-success border rounded-pill px-3 shadow-sm"
                                            title="Release Result">
                                            <i class="bi bi-broadcast"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-light text-danger border rounded-pill px-3 shadow-sm"
                                        onclick="confirmDeleteSubmission(<?= $sub['id'] ?>)">
                                        <i class="bi bi-trash"></i>
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

<style>
    .bg-success-subtle {
        background-color: #f0fdf4 !important;
    }

    .bg-secondary-subtle {
        background-color: #f8fafc !important;
    }
</style>

<script>
    function confirmDeleteSubmission(id) {
        Swal.fire({
            title: 'Delete Submission?',
            text: "This will remove the student's attempt entirely, allowing them to retake the exam. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url('teacher/exams/deleteSubmission/') ?>' + id;
            }
        });
    }
</script>
<?= $this->endSection() ?>