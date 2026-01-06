<?= $this->extend('student/layout') ?>

<?= $this->section('title') ?>Exam Result:
<?= htmlspecialchars($exam['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-5 d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
    <div>
        <h3 class="fw-bold mb-1">Result: <span class="text-success">
                <?= htmlspecialchars($exam['title']) ?>
            </span></h3>
        <p class="text-muted small">Released by your instructor.</p>
    </div>
    <a href="<?= site_url('student/exams') ?>" class="btn btn-outline-success rounded-pill px-4">Back to My Exams</a>
</div>

<div class="row g-4">
    <!-- Score Result Card -->
    <div class="col-lg-4 animate__animated animate__fadeInLeft">
        <div class="card border-0 rounded-4 shadow-sm bg-success text-white overflow-hidden mb-4">
            <div class="card-body p-5 text-center position-relative">
                <div class="position-absolute top-0 end-0 p-4 opacity-10">
                    <i class="bi bi-trophy-fill display-1"></i>
                </div>
                <h6 class="text-uppercase fw-bold opacity-75 mb-4 ls-1">Final Score</h6>
                <h1 class="display-1 fw-bold mb-0">
                    <?= $attempt['final_score'] ?> <small class="fs-4 opacity-75">/ <?= $total_possible_marks ?></small>
                </h1>
                <p class="mb-4 opacity-75">Points Awarded</p>
                <hr class="opacity-25">
                <div class="row pt-2">
                    <div class="col">
                        <small class="d-block opacity-75">Status</small>
                        <span class="fw-bold">Graded</span>
                    </div>
                    <div class="col">
                        <small class="d-block opacity-75">Switches</small>
                        <span class="fw-bold">
                            <?= $attempt['tab_switch_count'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm bg-white p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-success"></i>Performance Note</h6>
            <p class="small text-muted mb-0">
                Your final score includes automatic points for multiple-choice questions and manual marks for written
                responses assigned by your teacher.
            </p>
            <?php if (!empty($attempt['teacher_comment'])): ?>
                <div class="mt-4 p-3 bg-light rounded-3 border-start border-success border-4">
                    <label class="x-small fw-bold text-uppercase text-muted ls-1 mb-2">Teacher's Feedback:</label>
                    <p class="small mb-0 italic text-dark"><?= nl2br(htmlspecialchars($attempt['teacher_comment'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Question Detail -->
    <div class="col-lg-8 animate__animated animate__fadeInRight">
        <?php foreach ($questions as $index => $q): ?>
            <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white overflow-hidden">
                <div class="card-header bg-light border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-muted small text-uppercase">Question
                        <?= $index + 1 ?>
                    </span>
                    <span class="badge bg-white text-muted border px-3 rounded-pill">
                        <?= $q['student_answer'] ? $q['student_answer']['marks_awarded'] : 0 ?> /
                        <?= $q['points'] ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <?= htmlspecialchars($q['question_text']) ?>
                    </h5>

                    <div class="p-4 bg-light rounded-4">
                        <label class="x-small fw-bold text-uppercase text-muted ls-1 mb-2">Your Answer:</label>
                        <?php if (!$q['student_answer']): ?>
                            <p class="text-danger mb-0 italic">No answer submitted.</p>
                        <?php else: ?>
                            <?php if ($q['question_type'] === 'written'): ?>
                                <p class="mb-0 fs-5">
                                    <?= nl2br(htmlspecialchars($q['student_answer']['written_answer'])) ?>
                                </p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php
                                    foreach ($q['options'] as $o) {
                                        $isSelected = in_array($o['id'], $q['selected_options']);
                                        if ($isSelected) {
                                            $class = $o['is_correct'] ? 'bg-success text-white' : 'bg-danger text-white';
                                            echo '<span class="badge ' . $class . ' px-3 py-2 rounded-pill small">' . htmlspecialchars($o['option_text']) . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                                <?php if ($q['question_type'] !== 'written'): ?>
                                    <div class="mt-3 small text-muted">
                                        <i class="bi bi-info-circle me-1"></i> Correct was:
                                        <?php
                                        $correct = [];
                                        foreach ($q['options'] as $o)
                                            if ($o['is_correct'])
                                                $correct[] = $o['option_text'];
                                        echo '<strong>' . htmlspecialchars(implode(', ', $correct)) . '</strong>';
                                        ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .x-small {
        font-size: 0.72rem;
    }
</style>
<?= $this->endSection() ?>