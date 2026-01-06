<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Marking: <?= htmlspecialchars($attempt['student_name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= site_url('teacher/exams') ?>">Exams</a></li>
                <li class="breadcrumb-item"><a
                        href="<?= site_url('teacher/exams/submissions/' . $exam['id']) ?>">Submissions</a></li>
                <li class="breadcrumb-item active">Marking Submission</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0">Grade Attempt: <span
                class="text-primary"><?= htmlspecialchars($attempt['student_name']) ?></span></h3>
        <p class="text-muted small">Review answers, assign marks, and finalize the score.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="card border-0 shadow-sm bg-white rounded- pill px-4 py-2 me-3">
            <span class="text-muted small fw-bold me-2">TOTAL SCORE:</span>
            <span class="fs-4 fw-bold text-primary" id="total-display">0</span>
        </div>
        <button type="submit" form="markingForm" class="btn btn-expert rounded-pill px-5 shadow">
            Save & Finalize Marks
        </button>
    </div>
</div>

<form id="markingForm" action="<?= site_url('teacher/exams/saveMarks') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">
    <input type="hidden" name="final_score" id="final-score-input" value="0">

    <div class="row g-4">
        <div class="col-lg-9">
            <?php foreach ($questions as $index => $q): ?>
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white overflow-hidden animate__animated animate__fadeInUp"
                    style="animation-delay: <?= $index * 0.1 ?>s">
                    <div class="card-header bg-light border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small text-uppercase">Question <?= $index + 1 ?>
                            (<?= $q['question_type'] ?>)</span>
                        <span class="badge bg-white text-muted border px-3 rounded-pill">Max: <?= $q['points'] ?>
                            Points</span>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><?= htmlspecialchars($q['question_text']) ?></h5>

                        <!-- Correct Answer Reference -->
                        <div class="small mb-4 p-3 bg-success bg-opacity-10 rounded-3 border-start border-success border-4">
                            <i class="bi bi-info-circle me-2 text-success"></i>
                            <span class="text-success fw-bold">Correct Solution:</span>
                            <div class="mt-1">
                                <?php if ($q['question_type'] === 'written'): ?>
                                    <span class="text-muted">Subjective Check Required</span>
                                <?php else: ?>
                                    <?php
                                    $correct = [];
                                    foreach ($q['options'] as $o)
                                        if ($o['is_correct'])
                                            $correct[] = $o['option_text'];
                                    echo implode(', ', array_map('htmlspecialchars', $correct));
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Student's Response -->
                        <div class="p-4 bg-light rounded-4 mb-4">
                            <label class="x-small fw-bold text-uppercase text-muted ls-1 mb-2">Student's Response:</label>
                            <?php if (!$q['student_answer']): ?>
                                <p class="text-danger mb-0 fw-bold italic">No answer provided.</p>
                            <?php else: ?>
                                <?php if ($q['question_type'] === 'written'): ?>
                                    <p class="mb-0 fs-5"><?= nl2br(htmlspecialchars($q['student_answer']['written_answer'])) ?></p>
                                <?php else: ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php
                                        foreach ($q['options'] as $o) {
                                            $isSelected = in_array($o['id'], $q['selected_options']);
                                            $class = $isSelected ? ($o['is_correct'] ? 'bg-success text-white' : 'bg-danger text-white') : 'bg-white text-muted border';
                                            echo '<span class="badge ' . $class . ' px-3 py-2 rounded-pill small">' . htmlspecialchars($o['option_text']) . '</span>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Assigning Marks -->
                        <div class="row align-items-center pt-3 border-top border-light">
                            <div class="col-md-8">
                                <span class="text-muted small">Award marks based on the quality and accuracy of the
                                    response.</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span
                                        class="input-group-text border-0 bg-light rounded-start-pill text-primary fw-bold">Marks</span>
                                    <input type="number" name="marks[<?= $q['id'] ?>]"
                                        class="form-control border-0 bg-light rounded-end-pill py-2 mark-input"
                                        value="<?= $q['student_answer'] ? $q['student_answer']['marks_awarded'] : 0 ?>"
                                        max="<?= $q['points'] ?>" min="0" onchange="calculateTotal()">
                                    <span class="ms-2 mt-2 text-muted x-small">/ <?= $q['points'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Sticky Summary Sidebar -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-shield-lock fs-1 text-danger opacity-25 mb-3"></i>
                        <h6 class="fw-bold mb-2">Security Audit</h6>
                        <p class="small text-muted mb-3">Student switched tabs
                            <strong><?= $attempt['tab_switch_count'] ?></strong> times during this exam.
                        </p>
                        <hr class="my-3">
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3 mb-3">
                            <label class="form-label x-small fw-bold text-danger text-uppercase mb-1">Deduction /
                                Penalty</label>
                            <input type="number" id="penalty-input"
                                class="form-control text-center bg-transparent border-0 text-danger fw-bold fs-4 p-0 shadow-none"
                                value="0" onchange="calculateTotal()">
                        </div>

                        <div class="p-3 bg-light rounded-3 text-start mb-3">
                            <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">Teacher's
                                Feedback</label>
                            <textarea name="teacher_comment" class="form-control border-0 bg-white small" rows="4"
                                placeholder="Leave a comment for the student..."><?= htmlspecialchars($attempt['teacher_comment'] ?? '') ?></textarea>
                        </div>

                        <div class="form-check form-switch p-3 bg-white border rounded-3 text-start">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="is_released"
                                id="releaseSwitch" value="1" <?= $attempt['is_released'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-muted small" for="releaseSwitch">Release Result
                                to Student</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 rounded-4 shadow-sm bg-expert text-white shadow-expert">
                    <div class="card-body p-4 text-center">
                        <h4 class="mb-3 fw-bold">Final Verdict</h4>
                        <div class="display-3 fw-bold mb-0" id="final-display">0</div>
                        <p class="opacity-75 small">Automated + Manual Score</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .x-small {
        font-size: 0.72rem;
    }

    .shadow-expert {
        box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4) !important;
    }

    .shadow-expert {
        box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4) !important;
    }
</style>



<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    const studentId = <?= (int) $attempt['student_id'] ?>;
    const teacherId = <?= (int) session()->get('user_id') ?>;

    function calculateTotal() {
        let total = 0;
        $('.mark-input').each(function () {
            total += parseInt($(this).val()) || 0;
        });

        const penalty = parseInt($('#penalty-input').val()) || 0;
        const final = Math.max(0, total - penalty);

        $('#total-display').text(total);
        $('#final-display').text(final);
        $('#final-score-input').val(final);
    }

    $(document).ready(function () {
        calculateTotal();
    });
</script>
<?= $this->endSection() ?>