<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->renderSection('title') ?> - Student Portal
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $this->renderSection('css') ?>

    <style>
        :root {
            --student-primary: #10b981;
            --student-dark: #064e3b;
            --glass-bg: rgba(255, 255, 255, 0.9);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f0fdf4;
            color: #064e3b;
        }

        .navbar {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.05);
        }

        .btn-student {
            background: var(--student-primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
        }

        .btn-student:hover {
            background: var(--student-dark);
            color: white;
            transform: translateY(-2px);
        }

        /* Toast Styling */
        .toast-container {
            z-index: 2000;
        }

        .toast {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }

            main.container {
                py-3 !important;
            }

            h2 {
                font-size: 1.5rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success d-flex align-items-center"
                href="<?= site_url('student/dashboard') ?>">
                <i class="bi bi-mortarboard-fill fs-2 me-2"></i> <?= get_setting('site_name', 'ExamExpert') ?>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navStudent">
                <i class="bi bi-list fs-2 text-success"></i>
            </button>
            <div class="collapse navbar-collapse" id="navStudent">
                <ul class="navbar-nav ms-auto align-items-center bg-white rounded-4 p-2 p-lg-0 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link px-3 active fw-semibold"
                            href="<?= site_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= (url_is('student/exams*')) ? 'active' : '' ?>"
                            href="<?= site_url('student/exams') ?>">My Exams</a>
                    </li>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                            data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?= session()->get('full_name') ?>&background=10b981&color=fff"
                                class="rounded-circle me-2" width="35" height="35">
                            <span class="d-lg-none">
                                <?= session()->get('full_name') ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                            <li><a class="dropdown-item" href="<?= site_url('profile') ?>"><i
                                        class="bi bi-person me-2"></i> My Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>"><i
                                        class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/chat_widget') ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-4">
        <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header border-0 bg-transparent py-3 px-3">
                <i class="bi bi-info-circle-fill me-2 fs-5" id="toastIcon"></i>
                <strong class="me-auto fs-6" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body bg-white py-3 px-3 rounded-bottom-4" id="toastMessage">
                <!-- Message injected here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showToast(message, type = 'success') {
            const toastElement = document.getElementById('liveToast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            if (type === 'success') {
                toastIcon.className = 'bi bi-check-circle-fill me-2 fs-5 text-success';
                toastTitle.innerText = 'Success';
                toastElement.className = 'toast show bg-success-subtle';
            } else {
                toastIcon.className = 'bi bi-exclamation-triangle-fill me-2 fs-5 text-danger';
                toastTitle.innerText = 'Error';
                toastElement.className = 'toast show bg-danger-subtle';
            }

            toastMessage.innerText = message;

            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        $(document).ready(function () {
            // Handle Flash Data
            <?php if (session()->getFlashdata('success')): ?>
                showToast("<?= session()->getFlashdata('success') ?>", 'success');
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                showToast("<?= session()->getFlashdata('error') ?>", 'error');
            <?php endif; ?>
        });
    </script>
    <?= $this->renderSection('js') ?>
</body>

</html>