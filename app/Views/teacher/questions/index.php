<?= $this->extend('teacher/layout') ?>

<?= $this->section('title') ?>Question Lab: <?= htmlspecialchars($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= site_url('teacher/exams') ?>">Exams</a></li>
                <li class="breadcrumb-item active">Question Lab</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0">Question Lab: <span class="text-primary"><?= htmlspecialchars($exam['title']) ?></span>
        </h3>
        <p class="text-muted small">Design your questions directly on the page. All changes are saved instantly or via
            the main save button.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('teacher/exams') ?>" class="btn btn-light rounded-pill px-4 border">Back to Exams</a>
        <button type="submit" form="questionLabForm" class="btn btn-expert rounded-pill px-5 shadow">
            <i class="bi bi-cloud-check-fill me-2"></i> Save All Questions
        </button>
    </div>
</div>

<form id="questionLabForm" action="<?= site_url('teacher/exams/saveFull') ?>" method="post">
    <?= csrf_field() ?>
    <!-- Pass exam details to keep them during save if we use saveFull -->
    <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
    <input type="hidden" name="title" value="<?= htmlspecialchars($exam['title']) ?>">
    <input type="hidden" name="course_id" value="<?= $exam['course_id'] ?>">
    <input type="hidden" name="duration_minutes" value="<?= $exam['duration_minutes'] ?>">
    <input type="hidden" name="tab_switch_limit" value="<?= $exam['tab_switch_limit'] ?>">
    <input type="hidden" name="tab_switch_action" value="<?= $exam['tab_switch_action'] ?>">
    <input type="hidden" name="status" value="<?= $exam['status'] ?>">

    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div id="question-list-container">
                <!-- Questions will be dynamically loaded here -->
            </div>

            <!-- Add Question Button Card -->
            <div class="card border-0 rounded-4 shadow-sm bg-white mb-5 animate__animated animate__fadeInUp">
                <div class="card-body p-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-5 py-2 border-dashed border-2"
                        onclick="addQuestion()">
                        <i class="bi bi-plus-circle me-2 fs-5"></i> Add New Question
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Templates for JS -->
<template id="question-card-template">
    <div class="question-card card border-0 rounded-4 shadow-sm mb-4 bg-white overflow-hidden animate__animated animate__fadeInUp"
        data-q-index="{INDEX}">
        <!-- Top bar for drag (future) and delete -->
        <div class="bg-light p-2 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
            <div class="ms-3 d-flex align-items-center">
                <span class="badge bg-primary rounded-pill me-2 px-3">Question {DISPLAY_INDEX}</span>
                <span class="text-muted small fw-bold text-uppercase q-type-badge">{TYPE_LABEL}</span>
            </div>
            <button type="button" class="btn btn-link link-danger p-2 me-2" onclick="removeQuestion(this)">
                <i class="bi bi-trash3 fs-5"></i>
            </button>
        </div>

        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-9">
                    <label class="form-label x-small fw-bold text-uppercase text-muted ls-1">Question Prompt</label>
                    <textarea name="questions[{INDEX}][text]"
                        class="form-control form-control-lg border-0 bg-light rounded-4 px-4 py-3" rows="2"
                        placeholder="What is the capital of..." required>{TEXT}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label x-small fw-bold text-uppercase text-muted ls-1">Points</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light rounded-start-4 text-primary"><i
                                class="bi bi-award"></i></span>
                        <input type="number" name="questions[{INDEX}][points]"
                            class="form-control form-control-lg border-0 bg-light rounded-end-4" value="{POINTS}"
                            min="1" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="d-flex align-items-center gap-4 py-2 border-top border-bottom border-light mt-3">
                        <span class="x-small fw-bold text-uppercase text-muted">Response Type:</span>
                        <div class="form-check custom-radio">
                            <input class="form-check-input q-type-select" type="radio" name="questions[{INDEX}][type]"
                                value="mcq" id="mcq_{INDEX}" {MCQ_CHECKED} onchange="onTypeChange(this)">
                            <label class="form-check-label small" for="mcq_{INDEX}">Multiple Choice</label>
                        </div>
                        <div class="form-check custom-radio">
                            <input class="form-check-input q-type-select" type="radio" name="questions[{INDEX}][type]"
                                value="checkbox" id="chk_{INDEX}" {CHECKBOX_CHECKED} onchange="onTypeChange(this)">
                            <label class="form-check-label small" for="chk_{INDEX}">Checkboxes</label>
                        </div>
                        <div class="form-check custom-radio">
                            <input class="form-check-input q-type-select" type="radio" name="questions[{INDEX}][type]"
                                value="written" id="wrt_{INDEX}" {WRITTEN_CHECKED} onchange="onTypeChange(this)">
                            <label class="form-check-label small" for="wrt_{INDEX}">Written Answer</label>
                        </div>
                    </div>
                </div>

                <!-- Options List Area -->
                <div class="options-section {OPTIONS_HIDDEN} mt-4" data-q-index="{INDEX}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 small text-uppercase ls-1">Answers & Options</h6>
                        <span class="text-muted x-small">Mark the correct answer(s) using the indicator.</span>
                    </div>
                    <div class="options-list d-flex flex-column gap-2 mb-3">
                        <!-- Options injected here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 d-flex align-items-center"
                        onclick="addOptionRow(this)">
                        <i class="bi bi-plus-circle-fill me-2"></i> Add Choice
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="option-row-template">
    <div class="option-row d-flex align-items-center gap-3 animate__animated animate__fadeIn">
        <div class="indicator-box">
            <input type="{TYPE}" name="questions[{Q_INDEX}][correct]{SUFFIX}" value="{OPT_INDEX}"
                class="form-check-input custom-check mt-0" {CHECKED} title="Mark as correct">
        </div>
        <div class="flex-grow-1 position-relative">
            <input type="text" name="questions[{Q_INDEX}][options][{OPT_INDEX}]"
                class="form-control border-0 bg-light rounded-pill px-4" placeholder="Enter option text..."
                value="{TEXT}" required>
        </div>
        <button type="button" class="btn btn-link link-secondary p-1" onclick="this.closest('.option-row').remove()">
            <i class="bi bi-x-circle"></i>
        </button>
    </div>
</template>

<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .x-small {
        font-size: 0.72rem;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .animate__animated {
        --animate-duration: 0.35s;
    }

    .question-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 6px solid transparent !important;
    }

    .question-card:hover {
        transform: scale(1.005);
        border-left-color: #e2e8f0 !important;
    }

    .question-card:focus-within {
        border-left-color: var(--expert-accent) !important;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1) !important;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .indicator-box {
        width: 30px;
        display: flex;
        justify-content: center;
    }

    .custom-check {
        width: 1.3rem;
        height: 1.3rem;
        cursor: pointer;
        border-color: #cbd5e1;
    }

    .custom-check:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .custom-radio .form-check-input:checked {
        background-color: var(--expert-accent);
        border-color: var(--expert-accent);
    }

    .nav-breadcrumb {
        font-size: 0.85rem;
    }

    /* Google Forms Header Look */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: var(--primary-gradient);
        z-index: 10000;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    let qCounter = 0;

    function renderQuestion(data = {}) {
        const template = document.getElementById('question-card-template').innerHTML;
        const index = qCounter++;
        const type = data.question_type || 'mcq';

        const typeLabels = {
            mcq: 'Multiple Choice',
            checkbox: 'Checkboxes',
            written: 'Written Answer'
        };

        let html = template
            .replace(/{INDEX}/g, index)
            .replace(/{DISPLAY_INDEX}/g, qCounter)
            .replace(/{TEXT}/g, data.question_text || '')
            .replace(/{POINTS}/g, data.points || '1')
            .replace(/{TYPE_LABEL}/g, typeLabels[type])
            .replace(/{MCQ_CHECKED}/g, type === 'mcq' ? 'checked' : '')
            .replace(/{CHECKBOX_CHECKED}/g, type === 'checkbox' ? 'checked' : '')
            .replace(/{WRITTEN_CHECKED}/g, type === 'written' ? 'checked' : '')
            .replace(/{OPTIONS_HIDDEN}/g, type === 'written' ? 'd-none' : '');

        const container = $('#question-list-container');
        container.append(html);

        const qCard = container.find(`.question-card[data-q-index="${index}"]`);

        // Add existing options if any
        if (data.options && data.options.length > 0) {
            data.options.forEach((opt, optIdx) => {
                renderOption(qCard, index, type, optIdx, opt.option_text, opt.is_correct == 1);
            });
        } else if (type !== 'written') {
            // Default choices for new questions
            renderOption(qCard, index, type, 0, 'Option 1', true);
            renderOption(qCard, index, type, 1, 'Option 2', false);
        }
    }

    function renderOption(qCard, qIndex, qType, optIdx, text = '', checked = false) {
        const template = document.getElementById('option-row-template').innerHTML;
        const suffix = qType === 'checkbox' ? '[]' : '';
        const type = qType === 'checkbox' ? 'checkbox' : 'radio';

        let html = template
            .replace(/{Q_INDEX}/g, qIndex)
            .replace(/{OPT_INDEX}/g, optIdx)
            .replace(/{TYPE}/g, type)
            .replace(/{SUFFIX}/g, suffix)
            .replace(/{TEXT}/g, text)
            .replace(/{CHECKED}/g, checked ? 'checked' : '');

        qCard.find('.options-list').append(html);
    }

    function addQuestion() {
        renderQuestion({ question_type: 'mcq', points: 1 });
        // Smooth scroll to new question
        const newCard = $('#question-list-container .question-card').last();
        $('html, body').animate({
            scrollTop: newCard.offset().top - 100
        }, 500);
    }

    function removeQuestion(btn) {
        Swal.fire({
            title: 'Remove Question?',
            text: 'This will remove the question from this build.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Remove'
        }).then((result) => {
            if (result.isConfirmed) {
                $(btn).closest('.question-card').addClass('animate__fadeOutLeft');
                setTimeout(() => {
                    $(btn).closest('.question-card').remove();
                    reindexQuestions();
                }, 350);
            }
        });
    }

    function reindexQuestions() {
        qCounter = 0;
        $('.question-card').each(function () {
            qCounter++;
            $(this).find('.badge').text(`Question ${qCounter}`);
        });
    }

    function addOptionRow(btn) {
        const qCard = $(btn).closest('.question-card');
        const qIndex = qCard.data('q-index');
        const qType = qCard.find('.q-type-select:checked').val();
        const optList = qCard.find('.options-list');
        const optIdx = optList.find('.option-row').length;

        renderOption(qCard, qIndex, qType, optIdx, '', false);
    }

    function onTypeChange(radio) {
        const type = $(radio).val();
        const qCard = $(radio).closest('.question-card');
        const section = qCard.find('.options-section');
        const badge = qCard.find('.q-type-badge');
        const qIndex = qCard.data('q-index');

        const typeLabels = {
            mcq: 'Multiple Choice',
            checkbox: 'Checkboxes',
            written: 'Written Answer'
        };
        badge.text(typeLabels[type]);

        if (type === 'written') {
            section.addClass('d-none');
        } else {
            section.removeClass('d-none');
            // Update indicators
            const inputType = type === 'checkbox' ? 'checkbox' : 'radio';
            const suffix = type === 'checkbox' ? '[]' : '';

            qCard.find('.custom-check').each(function () {
                $(this).attr('type', inputType).attr('name', `questions[${qIndex}][correct]${suffix}`);
            });
        }
    }

    $(document).ready(function () {
        // Load existing questions
        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
                renderQuestion(<?= json_encode($q) ?>);
            <?php endforeach; ?>
        <?php else: ?>
            // Add first question automatically if empty
            addQuestion();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>