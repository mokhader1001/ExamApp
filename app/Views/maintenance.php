<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode -
        <?= get_setting('site_name', 'ExamApp') ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0fdf4;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #064e3b;
        }

        .maintenance-card {
            background: white;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(16, 185, 129, 0.1);
            max-width: 500px;
            text-align: center;
        }

        .icon-box {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 30px;
        }
    </style>
</head>

<body>
    <div class="maintenance-card">
        <div class="icon-box">
            <i class="bi bi-tools"></i>
        </div>
        <h2 class="fw-bold mb-3">System Maintenance</h2>
        <p class="text-muted mb-4">
            <?= get_setting('maintenance_reason', 'We are currently updating the platform to provide a better experience. The student portal is temporarily closed. Please check back shortly!') ?>
        </p>
        <a href="<?= site_url('logout') ?>" class="btn btn-success px-5 py-3 rounded-pill fw-bold border-0 shadow-sm"
            style="background: #10b981;">
            Sign Out
        </a>
    </div>
</body>

</html>