<?php
session_start();

require_once '../config/database.php';
require_once '../classes/Totp.php';
require_once '../vendor/autoload.php';

use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCode;

$database = new Database();
$db = $database->connect();

$error = '';
$success = '';
$qrCodeDataUri = null;
$provisioningUri = null;
$step = 'email'; // email, setup, token

if (isset($_GET['reset'])) {
    reset2faSession();
}

if (isset($_SESSION['2fa_step'])) {
    $step = $_SESSION['2fa_step'];
} elseif (isset($_SESSION['2fa_email'])) {
    $step = !empty($_SESSION['totp_enabled']) ? 'token' : 'setup';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email non valida.';
        reset2faSession();
        $step = 'email';
    } else {
        try {
            $stmt = $db->prepare("
                SELECT id, username, nome, cognome, ruolo, totp_secret, totp_enabled 
                FROM utenti_admin 
                WHERE email = ? AND attivo = 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                $error = 'Utente amministratore non trovato o disattivato.';
                reset2faSession();
                $step = 'email';
            } else {
                $secret = $user['totp_secret'];
                if (empty($secret)) {
                    $secret = Totp::generateSecret();
                    $update = $db->prepare("UPDATE utenti_admin SET totp_secret = :secret WHERE id = :id");
                    $update->execute([
                        ':secret' => $secret,
                        ':id' => $user['id']
                    ]);
                }
                
                $_SESSION['2fa_email'] = $email;
                $_SESSION['2fa_user_id'] = $user['id'];
                $_SESSION['2fa_user_name'] = trim(($user['nome'] ?? '') . ' ' . ($user['cognome'] ?? '')) ?: $user['username'];
                $_SESSION['2fa_username'] = $user['username'];
                $_SESSION['2fa_ruolo'] = $user['ruolo'];
                $_SESSION['totp_secret'] = $secret;
                $_SESSION['totp_enabled'] = (bool)$user['totp_enabled'];
                
                if ($user['totp_enabled']) {
                    $step = 'token';
                    $success = 'Inserisci il codice generato dalla tua app di autenticazione.';
                } else {
                    $step = 'setup';
                    $success = 'Configura la tua app scannerizzando il QR code e inserendo il codice per completare l’attivazione.';
                }
                
                $_SESSION['2fa_step'] = $step;
                logAuthEvent($db, $email, 'LOGIN_ATTEMPT', 'Avvio login 2FA via TOTP');
            }
        } catch (Exception $e) {
            $error = 'Errore interno durante la verifica. Riprova più tardi.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_token'])) {
    if (!isset($_SESSION['2fa_email'], $_SESSION['totp_secret'])) {
        $error = 'Sessione non valida. Ripeti il login.';
        reset2faSession();
        $step = 'email';
    } else {
        $code = trim($_POST['setup_token']);
        $secret = $_SESSION['totp_secret'];
        $email = $_SESSION['2fa_email'];

        if (Totp::verifyCode($secret, $code)) {
            try {
                $stmt = $db->prepare("UPDATE utenti_admin SET totp_enabled = 1 WHERE id = ?");
                $stmt->execute([$_SESSION['2fa_user_id']]);
            } catch (Exception $e) {
                $error = 'Impossibile salvare la configurazione. Riprova.';
                $step = 'setup';
            }

            if (empty($error)) {
                $_SESSION['totp_enabled'] = true;
                $step = 'token';
                $_SESSION['2fa_step'] = 'token';
                $success = 'Dispositivo configurato! Inserisci un nuovo codice generato dall’app per accedere.';
                logAuthEvent($db, $email, 'TOKEN_VERIFIED', 'TOTP attivato');
            }
        } else {
            $error = 'Codice non valido. Assicurati che l’orologio del dispositivo sia aggiornato.';
            $step = 'setup';
            $_SESSION['2fa_step'] = 'setup';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    if (!isset($_SESSION['2fa_email'], $_SESSION['totp_secret']) || empty($_SESSION['totp_enabled'])) {
        $error = 'Sessione non valida o TOTP non configurato.';
        reset2faSession();
        $step = 'email';
    } else {
        $email = $_SESSION['2fa_email'];
        $token = trim($_POST['token']);
        $secret = $_SESSION['totp_secret'];

        if (Totp::verifyCode($secret, $token)) {
            $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
            $_SESSION['user_name'] = $_SESSION['2fa_user_name'];
            $_SESSION['user_email'] = $email;
            $_SESSION['admin_id'] = $_SESSION['2fa_user_id'];
            $_SESSION['admin_username'] = $_SESSION['2fa_username'];
            $_SESSION['admin_nome'] = $_SESSION['2fa_user_name'];
            $_SESSION['admin_ruolo'] = $_SESSION['2fa_ruolo'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            logAuthEvent($db, $email, 'LOGIN_SUCCESS', 'Login 2FA completato via TOTP');

            reset2faSession();

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Codice TOTP non valido. Riprova.';
            $_SESSION['2fa_step'] = 'token';
            logAuthEvent($db, $email, 'LOGIN_FAILED', 'Codice TOTP errato');
            $step = 'token';
        }
    }
}

if ($step === 'setup' && isset($_SESSION['totp_secret'], $_SESSION['2fa_email'])) {
    $provisioningUri = Totp::getProvisioningUri($_SESSION['2fa_email'], $_SESSION['totp_secret']);
    $qrOptions = new QROptions([
        'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'     => QRCode::ECC_M,
        'scale'        => 6,
        'imageBase64'  => true,
        'imageTransparent' => false,
    ]);
    $qrCodeDataUri = (new QRCode($qrOptions))->render($provisioningUri);
}

function reset2faSession(): void {
    unset(
        $_SESSION['2fa_email'],
        $_SESSION['2fa_user_id'],
        $_SESSION['2fa_user_name'],
        $_SESSION['2fa_username'],
        $_SESSION['2fa_ruolo'],
        $_SESSION['totp_secret'],
        $_SESSION['totp_enabled'],
        $_SESSION['2fa_step']
    );
}

function logAuthEvent($db, string $email, string $action, string $details = ''): void {
    try {
        $stmt = $db->prepare("
            INSERT INTO auth_logs (email, ip_address, user_agent, action, details)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $email,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
            $action,
            $details
        ]);
    } catch (Exception $e) {
        // Silenzia errori di logging
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login 2FA - Sistema Professionisti ZPeC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/style.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .token-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>🔐 Accesso Sicuro</h2>
                <p>Sistema Professionisti ZPeC</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <?php if ($step === 'email'): ?>
                    <h4 class="mb-4">Inserisci la tua email</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continua</button>
                    </form>
                
                <?php elseif ($step === 'setup' && isset($_SESSION['2fa_email'])): ?>
                    <h4 class="mb-3">Configura l'app di autenticazione</h4>
                    <p class="text-muted">
                        Scansiona il QR code con Google Authenticator, Microsoft Authenticator o qualsiasi app TOTP.
                        In alternativa inserisci manualmente il codice segreto indicato sotto.
                    </p>
                    
                    <?php if ($qrCodeDataUri): ?>
                        <div class="text-center my-4">
                            <img src="<?= $qrCodeDataUri ?>" alt="QR Code TOTP" class="img-fluid">
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-secondary">
                        <strong>Codice segreto:</strong><br>
                        <code class="fs-5"><?= htmlspecialchars(chunk_split($_SESSION['totp_secret'], 4, ' ')) ?></code>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="setup_token" class="form-label">Inserisci il codice generato (6 cifre)</label>
                            <input type="text" class="form-control token-input" id="setup_token" name="setup_token" 
                                   maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Conferma configurazione</button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <a href="login_2fa.php?reset=1" class="text-decoration-none">← Usa un'altra email</a>
                    </div>
                
                <?php elseif ($step === 'token' && isset($_SESSION['2fa_email'])): ?>
                    <h4 class="mb-4">Verifica Codice</h4>
                    <p class="text-muted">
                        Inserisci il codice di 6 cifre generato dall'app collegata a:<br>
                        <strong><?= htmlspecialchars($_SESSION['2fa_email']) ?></strong>
                    </p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="token" class="form-label">Codice di Verifica</label>
                            <input type="text" class="form-control token-input" id="token" name="token" 
                                   maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verifica Codice</button>
                    </form>
                    
                    <div class="mt-4">
                        <a href="login_2fa.php?reset=1" class="text-decoration-none">← Torna al login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ['token', 'setup_token'].forEach((id) => {
                const input = document.getElementById(id);
                if (!input) {
                    return;
                }
                input.focus();
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                    if (this.value.length === 6 && id === 'token') {
                        this.form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>

