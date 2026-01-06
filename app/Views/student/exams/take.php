<?= $this->extend('student/layout') ?>

<?= $this->section('title') ?>Portal: <?= htmlspecialchars($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="exam-arena">
    <!-- Exam Header -->
    <!-- Mobile Header (Visible on Mobile Only) -->
    <div class="fixed-top bg-white shadow-sm d-lg-none px-3 py-2 d-flex justify-content-between align-items-center" style="z-index: 1030; height: 60px;">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light rounded-circle border shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavigator">
                <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
            </button>
            <div class="d-flex flex-column">
                <span class="text-muted" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px;">REMAINING</span>
                <span id="exam-timer-mobile" class="fw-bold text-primary font-monospace" style="font-size: 1.1rem; line-height: 1;">00:00:00</span>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-expert rounded-pill px-3" onclick="confirmSubmission()">
            Submit
        </button>
    </div>

    <!-- Desktop Exam Header (Hidden on Mobile) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white sticky-top shadow-lg d-none d-lg-block" style="top: 80px; z-index: 1020;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-primary bg-opacity-10 rounded-circle me-3">
                    <i class="bi bi-journal-text fs-4 text-primary"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($exam['title']) ?></h5>
                    <span class="text-muted small">Course: <?= htmlspecialchars($exam['course_name'] ?? 'N/A') ?></span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <div class="text-center">
                    <div class="text-muted small fw-bold text-uppercase ls-1">Time Remaining</div>
                    <div id="exam-timer" class="fs-4 fw-bold text-primary font-monospace">00:00:00</div>
                </div>
                <div class="vr"></div>
                <button type="button" class="btn btn-expert rounded-pill px-4" onclick="confirmSubmission()">
                    Submit Final Exam
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Question Navigation -->
        <!-- Question Navigation (Desktop) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-top" style="top: 180px;">
                <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Question Navigator</h6>
                        <div class="d-flex flex-wrap gap-2" id="q-navigator">
                            <?php foreach ($questions as $index => $q): ?>
                                <button type="button" class="btn btn-light rounded-3 border q-nav-btn p-0" 
                                    style="width: 40px; height: 40px;"
                                    onclick="jumpToQuestion(<?= $index ?>)" 
                                    data-q-index="<?= $index ?>">
                                    <?= $index + 1 ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <hr class="my-4 opacity-50">
                        <div class="small">
                            <div class="mb-2 d-flex align-items-center">
                                <span class="badge bg-expert rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                                <span class="text-muted">Answered</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light border rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                                <span class="text-muted">Remaining</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anti-Cheat Status -->
                <div class="card border-0 rounded-4 shadow-sm bg-danger bg-opacity-10 border-start border-danger border-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-shield-lock me-2"></i> Security Active</h6>
                        <p class="small text-danger opacity-75 mb-0">Tab switching is strictly monitored. Switching tabs will trigger an automatic log and potential cancellation.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Area -->
        <div class="col-lg-9">
            <form id="takingExamForm" action="<?= site_url('student/exams/submit') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">
                
                <div id="questions-viewport">
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="question-container <?= $index === 0 ? 'active' : 'd-none' ?>" data-index="<?= $index ?>">
                            <div class="card border-0 rounded-4 shadow-sm bg-white mb-4 overflow-hidden">
                                <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-primary mb-0">Question <?= $index + 1 ?> of <?= count($questions) ?></h6>
                                    <span class="badge bg-light text-muted border px-3 rounded-pill"><?= $q['points'] ?> Points</span>
                                </div>
                                <div class="card-body p-4">
                                    <h4 class="fw-bold mb-4"><?= htmlspecialchars($q['question_text']) ?></h4>
                                    
                                    <div class="p-3">
                                        <?php if ($q['question_type'] === 'written'): ?>
                                            <textarea name="answers[<?= $q['id'] ?>]" class="form-control rounded-4 border-0 bg-light p-4" 
                                                rows="5" placeholder="Type your answer here..." onchange="markAnswered(<?= $index ?>); saveAnswer(<?= $q['id'] ?>, this.value)"><?= htmlspecialchars($q['saved_answer']['written_answer'] ?? '') ?></textarea>
                                        <?php elseif (in_array($q['question_type'], ['mcq', 'dropdown'])): ?>
                                            <div class="d-flex flex-column gap-3">
                                                <?php foreach ($q['options'] as $opt): ?>
                                                    <?php $isChecked = in_array($opt['id'], $q['saved_options'] ?? []); ?>
                                                    <label class="p-3 border rounded-4 d-flex align-items-center gap-3 option-label cursor-pointer transition">
                                                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" 
                                                            class="form-check-input" onchange="markAnswered(<?= $index ?>); saveAnswer(<?= $q['id'] ?>, this.value)" <?= $isChecked ? 'checked' : '' ?>>
                                                        <span class="<?= $isChecked ? 'text-white' : '' ?>"><?= htmlspecialchars($opt['option_text']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif ($q['question_type'] === 'checkbox'): ?>
                                            <div class="d-flex flex-column gap-3">
                                                <?php foreach ($q['options'] as $opt): ?>
                                                    <?php $isChecked = in_array($opt['id'], $q['saved_options'] ?? []); ?>
                                                    <label class="p-3 border rounded-4 d-flex align-items-center gap-3 option-label cursor-pointer transition">
                                                        <input type="checkbox" name="answers[<?= $q['id'] ?>][]" value="<?= $opt['id'] ?>" 
                                                            class="form-check-input" onchange="markAnswered(<?= $index ?>); saveAnswer(<?= $q['id'] ?>, getCheckboxValues(<?= $q['id'] ?>))" <?= $isChecked ? 'checked' : '' ?>>
                                                        <span class="<?= $isChecked ? 'text-white' : '' ?>"><?= htmlspecialchars($opt['option_text']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Controls -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 py-2 border shadow-sm" id="prevBtn" onclick="prevQuestion()" disabled>
                        <i class="bi bi-arrow-left me-2"></i> Previous Question
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2 shadow" id="nextBtn" onclick="nextQuestion()">
                        Next Question <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <!-- Mobile Offcanvas Navigator -->
    <div class="offcanvas offcanvas-start rounded-end-4" tabindex="-1" id="mobileNavigator" style="max-width: 80%;">
        <div class="offcanvas-header border-bottom">
            <div>
                <h5 class="offcanvas-title fw-bold">Question Map</h5>
                <span class="text-muted small"><?= count($questions) ?> Questions Total</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-light">
            <div class="d-flex flex-wrap gap-2 mb-4 justify-content-center">
                <?php foreach ($questions as $index => $q): ?>
                    <button type="button" class="btn btn-light rounded-3 border q-nav-btn p-0 shadow-sm" 
                        style="width: 45px; height: 45px; font-weight: bold;"
                        onclick="jumpToQuestion(<?= $index ?>); bootstrap.Offcanvas.getInstance('#mobileNavigator').hide();" 
                        data-q-index="<?= $index ?>">
                        <?= $index + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                     <div class="small">
                        <div class="mb-2 d-flex align-items-center">
                            <span class="badge bg-expert rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                            <span class="text-muted">Answered</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-light border rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                            <span class="text-muted">Remaining</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Security Info -->
            <div class="card border-0 rounded-4 shadow-sm bg-danger bg-opacity-10 border-start border-danger border-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-danger mb-2 small"><i class="bi bi-shield-lock me-2"></i> Security Active</h6>
                    <p class="small text-danger opacity-75 mb-0" style="font-size: 0.75rem;">Tab switching is strictly monitored.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .cursor-pointer { cursor: pointer; }
    .transition { transition: all 0.2s; }
    .option-label:hover { background: #f8fafc; border-color: var(--expert-accent) !important; }
    /* Fix text visibility when option is selected */
    .option-label:has(input:checked) span {
        color: #ffffff !important;
        font-weight: 600;
        z-index: 2; /* Ensure text is above background */
        position: relative;
    }
    .option-label:has(input:checked) { 
        background-color: var(--primary-gradient, #4f46e5); /* Fallback to indigo if var missing */
        color: white; 
        border-color: transparent !important;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
    }
    .option-label:has(input:checked) .form-check-input {
        background-color: white;
        border-color: white;
        background-image: none; /* Remove default checkmark to avoid visual clutter if desired, or keep it */
    }
    .exam-arena { padding-top: 20px; }

    .exam-arena { padding-top: 20px; }
    
    @media (max-width: 991.98px) {
        .exam-arena { padding-top: 50px; } /* Push down for mobile header */
        .option-label { padding: 12px !important; }
        .question-container textarea { font-size: 16px; } /* Prevent iOS zoom */
    }
</style>



<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    let currentQ = 0;
    const totalQ = <?= count($questions) ?>;
    const durationMins = <?= $exam['duration_minutes'] ?>;
    const startTimeStr = '<?= str_replace(' ', 'T', $attempt['start_time']) ?>';
    const tabLimit = <?= (int)($exam['tab_switch_limit'] ?? 0) ?>;
    const tabAction = '<?= $exam['tab_switch_action'] ?? 'warn_then_cancel' ?>';
    const attemptId = <?= (int)($attempt['id'] ?? 0) ?>;
    const teacherId = <?= (int)($exam['teacher_id'] ?? 0) ?>;
    const studentId = <?= (int)session()->get('user_id') ?>;
    let switchCountAvailable = <?= (int)($attempt['tab_switch_count'] ?? 0) ?>;

    // Timer Logic
    let remainingSecs = <?= (int)$remaining_seconds ?>;
    
    function startTimer() {
        const x = setInterval(function() {
            if (remainingSecs <= 0) {
                clearInterval(x);
                $('#exam-timer').text('00:00:00');
                Swal.fire({
                    title: 'Time is Up!',
                    text: 'Your exam is being automatically submitted.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then(() => $('#takingExamForm').submit());
                return;
            }
            
            const hours = Math.floor(remainingSecs / 3600);
            const minutes = Math.floor((remainingSecs % 3600) / 60);
            const seconds = remainingSecs % 60;
            const timeStr = (hours < 10 ? "0" + hours : hours) + ":" + 
                            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                            (seconds < 10 ? "0" + seconds : seconds);
            
            $('#exam-timer').text(timeStr);
            $('#exam-timer-mobile').text(timeStr);
            
            remainingSecs--;
        }, 1000);
    }

    // Question Navigation
    function updateLayout() {
        $('.question-container').addClass('d-none').removeClass('active');
        $(`.question-container[data-index="${currentQ}"]`).removeClass('d-none').addClass('active');
        
        $('.q-nav-btn').removeClass('btn-primary').addClass('btn-light');
        $(`.q-nav-btn[data-q-index="${currentQ}"]`).addClass('btn-primary').removeClass('btn-light');
        
        $('#prevBtn').prop('disabled', currentQ === 0);
        if (currentQ === totalQ - 1) {
            $('#nextBtn').text('Finish Assessment').removeClass('btn-primary').addClass('btn-expert');
        } else {
            $('#nextBtn').html('Next Question <i class="bi bi-arrow-right ms-2"></i>').addClass('btn-primary').removeClass('btn-expert');
        }
    }

    function nextQuestion() {
        if (currentQ < totalQ - 1) {
            currentQ++;
            updateLayout();
        } else {
            confirmSubmission();
        }
    }

    function prevQuestion() {
        if (currentQ > 0) {
            currentQ--;
            updateLayout();
        }
    }

    function jumpToQuestion(idx) {
        currentQ = idx;
        updateLayout();
    }

    function markAnswered(idx) {
        $(`.q-nav-btn[data-q-index="${idx}"]`).addClass('bg-success text-white border-0');
    }

    function confirmSubmission() {
        Swal.fire({
            title: 'Submit Exam?',
            text: 'Are you sure you want to finalize your answers?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit Now',
            confirmButtonColor: '#6366f1'
        }).then((result) => {
            if (result.isConfirmed) $('#takingExamForm').submit();
        });
    }

    let switchLogged = false;
    
    $(window).on('focus', function() {
        switchLogged = false; // Allow logging again when coming back
    });

    $(document).on('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            console.log("Visibility State: Hidden");
            handleLog();
        }
    });

    $(window).on('blur', function() {
        console.log("Window Blur Detected");
        handleLog();
    });

    function handleLog() {
        if (!switchLogged) {
            switchLogged = true;
            recordTabSwitch();
            // We'll reset switchLogged on focus to ensure we capture the NEXT switch
        }
    }

    function recordTabSwitch() {
        if (!attemptId) return;
        
        $.post('<?= site_url('student/exams/recordTabSwitch/') ?>' + attemptId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if(res.status === 'success') {
                switchCountAvailable = res.count;
                handleWarning();
            }
        }).fail(function() {
            console.error("Failed to log tab switch");
        });
    }

    function handleWarning() {
        const remaining = tabLimit - switchCountAvailable;
        
        if (remaining <= 0) {
            // Force Submit instead of just redirecting
            $.post('<?= site_url('student/exams/forceSubmit') ?>', $('#takingExamForm').serialize(), function(res) {
                Swal.fire({
                    title: 'Security Violation!',
                    text: '<?= addslashes($switch_kick_msg) ?> Your exam has been automatically submitted.',
                    icon: 'error',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = '<?= site_url('student/dashboard') ?>';
                });
            });
        } else {
            Swal.fire({
                title: 'Security Warning!',
                text: '<?= addslashes($switch_warning_msg) ?> (Remaining: ' + remaining + ')',
                icon: 'warning'
            });
        }
    }

    $(document).ready(function() {
        startTimer();
        updateLayout();
        
        // Restore answered state for navigation buttons
        <?php foreach ($questions as $idx => $q): ?>
            <?php if (!empty($q['saved_answer'])): ?>
                markAnswered(<?= $idx ?>);
            <?php endif; ?>
        <?php endforeach; ?>
        
        // Handle unexpected close/navigation
        window.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden' && !$('#takingExamForm').data('submitted')) {
                // We don't necessarily want to force submit on EVERY hide, 
                // but the user said "if closes the exam resubmit by force".
                // 'beforeunload' is better for "closing", but 'visibilitychange' is more reliable on mobile.
                // However, the user specifically mentioned "closes the app".
            }
        });

        window.addEventListener('beforeunload', function (e) {
            if (!$('#takingExamForm').data('submitted')) {
                const formData = new FormData(document.getElementById('takingExamForm'));
                navigator.sendBeacon('<?= site_url('student/exams/forceSubmit') ?>', formData);
            }
        });
        
        $('#takingExamForm').on('submit', function() {
            $(this).data('submitted', true);
        });
    });

    function json_encode(obj) {
        return JSON.stringify(obj);
    }

    function saveAnswer(qId, val) {
        $.post('<?= site_url('student/exams/saveProgress') ?>', {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            attempt_id: attemptId,
            question_id: qId,
            answer: val
        }).done(function(res) {
            console.log("Saved", res);
        });
    }

    function getCheckboxValues(qId) {
        let vals = [];
        $(`input[name="answers[${qId}][]"]:checked`).each(function() {
            vals.push($(this).val());
        });
        return vals;
    }
</script>
<?= $this->endSection() ?>
