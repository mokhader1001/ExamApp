<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Exam App Expert</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.95);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --sidebar-width: 260px;
            --expert-blue: #1e293b;
            --expert-accent: #6366f1;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Navigation */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--expert-blue);
            color: white;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 14px 25px;
            margin: 5px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        #sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 15px;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-link.active {
            background: var(--expert-accent);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        /* Header / Navbar */
        .top-nav {
            margin-left: var(--sidebar-width);
            height: 75px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            min-height: calc(100vh - 75px);
            transition: all 0.3s;
        }

        /* Cards and Components */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background: white;
            transition: transform 0.3s;
        }

        .btn-expert {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            transition: all 0.3s;
        }

        .btn-expert:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            #sidebar {
                left: -var(--sidebar-width);
            }

            #sidebar.show {
                left: 0;
            }

            .top-nav,
            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        /* DataTables Custom Styling */
        .table thead th {
            background: #f1f5f9;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border: none;
            padding: 15px 20px;
        }

        .table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Profile Dropdown */
        .profile-btn {
            display: flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 30px;
            background: #f1f5f9;
            border: none;
            transition: all 0.3s;
        }

        .profile-btn:hover {
            background: #e2e8f0;
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
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="p-4 mb-3 d-flex align-items-center">
            <i class="bi bi-mortarboard-fill fs-2 text-primary me-3"></i>
            <h4 class="mb-0 fw-bold"><?= get_setting('site_name', 'ExamExpert') ?></h4>
        </div>

        <nav class="nav flex-column">
            <a href="<?= site_url('teacher/dashboard') ?>"
                class="nav-link <?= strpos(current_url(), 'dashboard') !== false ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <p class="px-4 mt-4 mb-2 text-uppercase small opacity-50 fw-bold">Management</p>
            <a href="<?= site_url('teacher/classes') ?>"
                class="nav-link <?= strpos(current_url(), 'classes') !== false ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Classes
            </a>
            <a class="nav-link rounded-3 mb-2 <?= (url_is('teacher/courses*')) ? 'active' : '' ?>"
                href="<?= site_url('teacher/courses') ?>">
                <i class="bi bi-book me-3"></i> Courses
            </a>
            <a class="nav-link rounded-3 mb-2 <?= (url_is('teacher/students*')) ? 'active' : '' ?>"
                href="<?= site_url('teacher/students') ?>">
                <i class="bi bi-people me-3"></i> Enroll Students
            </a>
            <a href="<?= site_url('teacher/exams') ?>"
                class="nav-link <?= strpos(current_url(), 'exams') !== false ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text-fill"></i> Exams
            </a>

            <p class="px-4 mt-4 mb-2 text-uppercase small opacity-50 fw-bold">Settings</p>
            <a href="<?= site_url('profile') ?>"
                class="nav-link <?= strpos(current_url(), 'profile') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-fill-gear"></i> Profile
            </a>
            <a href="<?= site_url('settings') ?>"
                class="nav-link <?= strpos(current_url(), 'settings') !== false ? 'active' : '' ?>">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
            <a href="<?= site_url('logout') ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Top Navigation -->
    <header class="top-nav">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <h5 class="mb-0 fw-bold d-none d-md-block"><?= $this->renderSection('title') ?></h5>

        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?= session()->get('full_name') ?>&background=6366f1&color=fff"
                        class="rounded-circle me-2" width="32" height="32">
                    <span class="fw-semibold d-none d-sm-inline"><?= session()->get('full_name') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="border-radius: 12px;">
                    <li><a class="dropdown-item p-2 rounded-2" href="<?= site_url('profile') ?>"><i
                                class="bi bi-person me-2"></i> Profile</a>
                    </li>
                    <li><a class="dropdown-item p-2 rounded-2" href="<?= site_url('settings') ?>"><i
                                class="bi bi-gear me-2"></i> Settings</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item p-2 rounded-2 text-danger" href="<?= site_url('logout') ?>"><i
                                class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
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

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        function toggleSidebar() {
            $('#sidebar').toggleClass('show');
        }

        function showToast(message, type = 'success') {
            const toastElement = document.getElementById('liveToast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            // Set styles based on type
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
            // Initialize DataTables
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                }
            });

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