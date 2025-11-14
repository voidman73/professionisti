<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>ZPeC Professionisti</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- DataTables -->
    <?php if (isset($include_datatables) && $include_datatables): ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <?php endif; ?>
    
    <!-- Custom ZPeC Styles -->
    <link rel="stylesheet" href="<?= isset($css_path) ? $css_path : '../assets/style.css' ?>">
    
    <!-- Custom page styles -->
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">
    <?php if (function_exists('getCurrentUser') && getCurrentUser()): ?>
    <!-- Header con info utente -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-users-cog"></i> ZPeC Admin
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars(getCurrentUser()['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted">
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars(getCurrentUser()['email']) ?>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout_2fa.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>