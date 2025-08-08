<?php
session_start();
require_once '../classes/Admin.php';

$admin = new Admin();
$errore = '';

// Se già loggato, reindirizza alla dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Gestione login
if ($_POST) {
    try {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            throw new Exception("Username e password sono obbligatori");
        }

        $utente = $admin->autenticaUtente($username, $password);
        
        if ($utente) {
            $_SESSION['admin_id'] = $utente['id'];
            $_SESSION['admin_username'] = $utente['username'];
            $_SESSION['admin_nome'] = $utente['nome'] . ' ' . $utente['cognome'];
            $_SESSION['admin_ruolo'] = $utente['ruolo'];
            
            // Aggiorna ultimo accesso
            $admin->aggiornaUltimoAccesso($utente['id']);
            
            header('Location: dashboard.php');
            exit;
        } else {
            throw new Exception("Credenziali non valide");
        }
    } catch (Exception $e) {
        $errore = $e->getMessage();
    }
}

$page_title = 'Login Amministrazione - Professionisti';
$css_path = '../assets/style.css';
$extra_css = [];
$inline_scripts = '';
include '../includes/header.php'; 
?>

<style>
    body {
        background: linear-gradient(135deg, var(--zpec-primary-dark) 0%, var(--zpec-primary) 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    .login-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .login-header {
        background: linear-gradient(135deg, var(--zpec-primary-dark) 0%, var(--zpec-primary) 100%);
        color: white;
        padding: 40px 30px;
        text-align: center;
    }
    .login-body {
        padding: 40px;
    }
    .form-control:focus {
        border-color: var(--zpec-primary);
        box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.25);
    }
    .btn-login {
        background: linear-gradient(135deg, var(--zpec-primary-dark) 0%, var(--zpec-primary) 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 0, 0, 0.3);
    }
</style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h3>Area Amministrazione</h3>
                        <p class="mb-0">Accesso riservato agli amministratori</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if ($errore): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($errore) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" class="form-control form-control-lg" name="username" 
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" class="form-control form-control-lg" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Accedi
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Per assistenza contattare l'amministratore di sistema
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>