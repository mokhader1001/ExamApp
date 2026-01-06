<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>
<?= ($exam) ? 'Edit Exam' : 'Create New Exam' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-5 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= site_url('teacher/exams') ?>">Exams</a></li>
                <li class="breadcrumb-item active">
                    <?= ($exam) ? 'Edit' : 'Create' ?>
                </li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">
            <?= ($exam) ? 'Refine Your Assessment' : 'Design Dynamic Exam' ?>
        </h2>
        <p class="text-muted">Build your exam structure and questions in one fluid interface.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('teacher/exams') ?>" class="btn btn-light rounded-pill px-4 border">Discard</a>
        <button type="submit" form="fullExamForm" class="btn btn-expert rounded-pill px-5 shadow-sm">
            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Save Changes
        </button>
    </div>
</div>

<form id="fullExamForm" action="<?= site_url('teacher/exams/saveFull') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="exam_id" value="<?= $exam['id'] ?? '' ?>">
    <input type="hidden" name="status" value="<?= $exam['status'] ?? 'draft' ?>">

    <div class="row g-4">
        <!-- Sidebar Navigation (Sticky) -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase small text-primary mb-3">Form Menu</h6>
                        <ul class="nav flex-column gap-2">
                            <li class="nav-item">
                                <a href="#section-settings"
                                    class="nav-link bg-light rounded-3 p-3 d-flex align-items-center active">
                                    <i class="bi bi-gear-fill me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-bold small">General Settings</div>
                                        <div class="text-muted x-small">Title, Course, Rules</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#section-questions" class="nav-link rounded-3 p-3 d-flex align-items-center">
                                    <i class="bi bi-list-check me-3 text-success"></i>
                                    <div>
                                        <div class="fw-bold small">Question Lab</div>
                                        <div class="text-muted x-small" id="q-count-badge">0 Questions Added</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <hr class="my-4 opacity-50">
                        <button type="button" class="btn btn-outline-primary w-100 rounded-pill py-2"
                            onclick="addQuestion()">
                            <i class="bi bi-plus-lg me-2"></i> Add Question
                        </button>
                    </div>
                </div>

                <div class="card border-0 rounded-4 shadow-sm bg-primary bg-opacity-10">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <span class="fw-bold small">Quick Tip</span>
                        </div>
                        <p class="small text-muted mb-0">Drag and drop questions (coming soon) or use the type selector
                            to change how students respond.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Content -->
        <div class="col-lg-9">
            <!-- Section: Settings -->
            <div id="section-settings"
                class="card border-0 rounded-4 shadow-sm mb-4 bg-white border-start border-primary border-5">
                <div class="card-body p-5">
                    <h4 class="fw-bold mb-4">Exam Configuration</h4>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-uppercase">Exam Title</label>
                            <input type="text" name="title"
                                class="form-control form-control-lg rounded-3 border-0 bg-light"
                                placeholder="Enter Exam Title..." value="<?= htmlspecialchars($exam['title'] ?? '') ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Associated Course</label>
                            <select name="course_id" class="form-select rounded-3 border-0 bg-light" required>
                                <option value="">Select Course...</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course['id'] ?>" <?= (isset($exam['course_id']) && $exam['course_id'] == $course['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($course['course_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Time Limit (Minutes)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light rounded-start-3"><i
                                        class="bi bi-clock"></i></span>
                                <input type="number" name="duration_minutes"
                                    class="form-control rounded-end-3 border-0 bg-light"
                                    value="<?= $exam['duration_minutes'] ?? '60' ?>" required>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="p-4 rounded-4 bg-light bg-opacity-50 border border-dashed">
                                <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-danger"></i> Anti-Cheat
                                    Settings</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Tab Switch Tolerance</label>
                                        <select name="tab_switch_limit" class="form-select rounded-3 border-0">
                                            <option value="1" <?= (isset($exam['tab_switch_limit']) && $exam['tab_switch_limit'] == 1) ? 'selected' : '' ?>>1 Warning / Kick
                                            </option>
                                            <option value="2" <?= (!isset($exam['tab_switch_limit']) || $exam['tab_switch_limit'] == 2) ? 'selected' : 'selected' ?>>2 Warnings /
                                                Kick</option>
                                            <option value="3" <?= (isset($exam['tab_switch_limit']) && $exam['tab_switch_limit'] == 3) ? 'selected' : '' ?>>3 Warnings / Kick
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Enforcement Action</label>
                                        <select name="tab_switch_action" class="form-select rounded-3 border-0">
                                            <option value="warn_then_cancel" <?= (!isset($exam['tab_switch_action']) || $exam['tab_switch_action'] == 'warn_then_cancel') ? 'selected' : '' ?>
                                                >Warn Then Cancel</option>
                                            <option value="cancel_immediately" <?= (isset($exam['tab_switch_action']) && $exam['tab_switch_action'] == 'cancel_immediately') ? 'selected' : '' ?>
                                                >Cancel Immediately</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Questions Container -->
            <div id="section-questions">
                <div id="question-list-container">
                    <!-- Questions will be injected here -->
                </div>

                <!-- Empty State for Questions -->
                <div id="no-questions-state"
                    class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white mb-4 d-none">
                    <div class="py-4">
                        <i class="bi bi-plus-circle text-primary display-3 opacity-25"></i>
                        <h5 class="mt-3 fw-bold">Ready to add questions?</h5>
                        <p class="text-muted">Click the button below to start building your test.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="addQuestion()">Add
                            First Question</button>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Bar -->
            <div class="card border-0 rounded-4 shadow-sm bg-white mt-4">
                <div class="card-body p-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-primary border-0 rounded-pill px-4 mx-2"
                        onclick="addQuestion()">
                        <i class="bi bi-plus-circle me-2"></i> Add Question
                    </button>
                    <div class="vr"></div>
                    <button type="submit" class="btn btn-expert rounded-pill px-5 mx-2 shadow">Save Assessment</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Templates for JS -->
<template id="question-template">
    <div class="question-card card border-0 rounded-4 shadow-sm mb-4 bg-white overflow-hidden animate__animated animate__fadeInUp"
        data-q-index="{INDEX}">
        <div
            class="bg-primary bg-opacity-10 p-2 d-flex justify-content-between align-items-center border-bottom border-primary border-opacity-10">
            <span class="badge bg-primary rounded-pill px-3 py-1 ms-3">Question {DISPLAY_INDEX}</span>
            <div class="d-flex align-items-center me-2">
                <button type="button" class="btn btn-link link-danger p-2" onclick="removeQuestion(this)"><i
                        class="bi bi-trash3"></i></button>
            </div>
        </div>
        <div class="card-body p-4 pt-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <textarea name="questions[{INDEX}][text]" class="form-control bg-light rounded-3 border-0 py-3"
                        placeholder="Type your question prompt here..." rows="2" required>{TEXT}</textarea>
                </div>
                <div class="col-md-2">
                    <select name="questions[{INDEX}][type]"
                        class="form-select bg-light rounded-3 border-0 py-3 q-type-select"
                        onchange="toggleOptionsArea(this)">
                        <option value="mcq" {MCQ_SELECTED}>Multiple Choice</option>
                        <option value="checkbox" {CHECKBOX_SELECTED}>Checkboxes</option>
                        <option value="written" {WRITTEN_SELECTED}>Written Answer</option>
                        <option value="dropdown" {DROPDOWN_SELECTED}>Dropdown Menu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="questions[{INDEX}][points]"
                        class="form-control bg-light rounded-3 border-0 py-3" placeholder="Points" value="{POINTS}"
                        min="1" required>
                </div>

                <!-- Options Area -->
                <div class="options-container {OPTIONS_HIDDEN} mt-4 px-3" data-q-index="{INDEX}">
                    <div class="option-list d-flex flex-column gap-2 mb-3">
                        <!-- Options injected here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 d-flex align-items-center"
                        onclick="addOptionRow(this)">
                        <i class="bi bi-plus-circle-dotted fs-5 me-2"></i> Add Option
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="option-template">
    <div class="option-row d-flex align-items-center gap-3 animate__animated animate__fadeIn">
        <div class="indicator-wrapper">
            <input type="{TYPE}" name="questions[{Q_INDEX}][correct]{SUFFIX}" value="{OPT_INDEX}"
                class="form-check-input mt-0 correct-indicator" {CHECKED}>
        </div>
        <input type="text" name="questions[{Q_INDEX}][options][{OPT_INDEX}]"
            class="form-control bg-light rounded-pill border-0 px-4" placeholder="Enter option text..." value="{TEXT}"
            required>
        <button type="button" class="btn btn-link text-muted p-1" onclick="this.closest('.option-row').remove()"><i
                class="bi bi-x-lg"></i></button>
    </div>
</template>

<style>
    .x-small {
        font-size: 0.7rem;
    }

    .animate__animated {
        --animate-duration: 0.4s;
    }

    .question-card {
        transition: transform 0.3s;
        border-left: 5px solid transparent !important;
    }

    .question-card:focus-within {
        border-left: 5px solid var(--expert-accent) !important;
        transform: translateX(5px);
    }

    .indicator-wrapper {
        width: 40px;
        text-align: center;
    }

    .nav-link.active {
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.15);
    }

    .btn-expert {
        background: var(--primary-gradient);
        color: white;
        border: none;
    }

    .btn-expert:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        color: white;
    }

    /* Google Forms Header Effect */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: var(--primary-gradient);
        z-index: 9999;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    let questionCounter = 0;

    function renderQuestion(data = {}) {
        const template = document.getElementById('question-template').innerHTML;
        const qIndex = questionCounter++;

        let html = template
            .replace(/{INDEX}/g, qIndex)
            .replace(/{DISPLAY_INDEX}/g, questionCounter)
            .replace(/{TEXT}/g, data.question_text || '')
            .replace(/{POINTS}/g, data.points || '1')
            .replace(/{MCQ_SELECTED}/g, data.question_type === 'mcq' ? 'selected' : '')
            .replace(/{CHECKBOX_SELECTED}/g, data.question_type === 'checkbox' ? 'selected' : '')
            .replace(/{WRITTEN_SELECTED}/g, data.question_type === 'written' ? 'selected' : '')
            .replace(/{DROPDOWN_SELECTED}/g, data.question_type === 'dropdown' ? 'selected' : '')
            .replace(/{OPTIONS_HIDDEN}/g, data.question_type === 'written' ? 'd-none' : '');

        const container = $('#question-list-container');
        container.append(html);

        const qRow = container.find(`.question-card[data-q-index="${qIndex}"]`);

        // Add existing options if any
        if (data.options && data.options.length > 0) {
            data.options.forEach((opt, optIndex) => {
                renderOption(qRow, qIndex, data.question_type, optIndex, opt.option_text, opt.is_correct == 1);
            });
        } else if (data.question_type !== 'written') {
            // Default 2 options for new questions
            renderOption(qRow, qIndex, data.question_type, 0, 'Option 1', true);
            renderOption(qRow, qIndex, data.question_type, 1, 'Option 2', false);
        }

        updateStats();
    }

    function renderOption(qRow, qIndex, qType, optIndex, text = '', checked = false) {
        const template = document.getElementById('option-template').innerHTML;
        const suffix = qType === 'checkbox' ? '[]' : '';
        const inputType = qType === 'checkbox' ? 'checkbox' : 'radio';

        let html = template
            .replace(/{Q_INDEX}/g, qIndex)
            .replace(/{OPT_INDEX}/g, optIndex)
            .replace(/{TYPE}/g, inputType)
            .replace(/{SUFFIX}/g, suffix)
            .replace(/{TEXT}/g, text)
            .replace(/{CHECKED}/g, checked ? 'checked' : '');

        qRow.find('.option-list').append(html);
    }

    function addQuestion() {
        renderQuestion({ question_type: 'mcq', points: 1 });
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    function removeQuestion(btn) {
        $(btn).closest('.question-card').remove();
        updateStats();
    }

    function addOptionRow(btn) {
        const qContainer = $(btn).closest('.options-container');
        const qIndex = qContainer.data('q-index');
        const qType = qContainer.closest('.question-card').find('.q-type-select').val();
        const optList = qContainer.find('.option-list');
        const optIndex = optList.find('.option-row').length;

        renderOption(qContainer.closest('.question-card'), qIndex, qType, optIndex, '', false);
    }

    function toggleOptionsArea(select) {
        const type = $(select).val();
        const qCard = $(select).closest('.question-card');
        const optionsArea = qCard.find('.options-container');
        const indicators = qCard.find('.correct-indicator');
        const qIndex = qCard.data('q-index');

        if (type === 'written') {
            optionsArea.addClass('d-none');
        } else {
            optionsArea.removeClass('d-none');
            const inputType = type === 'checkbox' ? 'checkbox' : 'radio';
            const suffix = type === 'checkbox' ? '[]' : '';

            indicators.each(function () {
                $(this).attr('type', inputType).attr('name', `questions[${qIndex}][correct]${suffix}`);
            });
        }
    }

    function updateStats() {
        const count = $('.question-card').length;
        $('#q-count-badge').text(`${count} Question${count === 1 ? '' : 's'} Added`);

        if (count === 0) {
            $('#no-questions-state').removeClass('d-none');
        } else {
            $('#no-questions-state').addClass('d-none');
        }
    }

    $(document).ready(function () {
        // Load initial data
        <?php if (isset($questions) && !empty($questions)): ?>
                <?php foreach ($questions as $q): ?>
                    renderQuestion(<?= json_encode($q) ?>);
                <?php endforeach; ?>
        <?php else: ?>
                // Don't auto-add if empty, show state
                updateStats();
        <?php endif; ?>

            // Smooth Scroll for Sidebar
            $('a[href^="#"]').on('click', function (e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    window.scrollTo({
                        top: target.offset().top - 120,
                        behavior: 'smooth'
                    });
                }
            });
    });
</script>
<?= $this->endSection() ?>