<?php
/**
 * Middleware per autenticazione 2FA
 * Da includere all'inizio di ogni pagina protetta
 */

session_start();
require_once __DIR__ . '/../config/database.php';

function getAuthDb() {
    static $db = null;
    if ($db === null) {
        $database = new Database();
        $db = $database->connect();
    }
    return $db;
}

function isAuthenticated() {
    return isset($_SESSION['logged_in']) && 
           $_SESSION['logged_in'] === true && 
           isset($_SESSION['user_id']) && 
           isset($_SESSION['login_time']);
}

function isSessionExpired() {
    if (!isset($_SESSION['login_time'])) {
        return true;
    }
    
    $session_lifetime = 8 * 60 * 60; // 8 ore in secondi
    return (time() - $_SESSION['login_time']) > $session_lifetime;
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login_2fa.php');
        exit;
    }
    
    if (isSessionExpired()) {
        session_destroy();
        header('Location: login_2fa.php?expired=1');
        exit;
    }
    
    $_SESSION['login_time'] = time();
}

function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

function logout() {
    if (isset($_SESSION['user_email'])) {
        try {
            $db = getAuthDb();
            $stmt = $db->prepare("INSERT INTO auth_logs (email, ip_address, user_agent, action, details) VALUES (?, ?, ?, 'LOGOUT', ?)");
            $stmt->execute([$_SESSION['user_email'], $_SERVER['REMOTE_ADDR'] ?? 'CLI', $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN', 'Logout manuale']);
        } catch (Exception $e) {
            // ignora errori di log
        }
    }
    
    session_destroy();
    
    header('Location: login_2fa.php');
    exit;
}

function isAdmin() {
    if (!isAuthenticated()) {
        return false;
    }
    
    return true;
}

function requireAdmin() {
    requireAuth();
    
    if (!isAdmin()) {
        header('Location: dashboard.php?error=access_denied');
        exit;
    }
}

function logAction($action, $details = '') {
    if (!isAuthenticated()) {
        return;
    }
    
    try {
        $db = getAuthDb();
        $stmt = $db->prepare("INSERT INTO auth_logs (email, ip_address, user_agent, action, details) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_email'], $_SERVER['REMOTE_ADDR'] ?? 'CLI', $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN', $action, $details]);
    } catch (Exception $e) {
        // Silenzioso fallimento per non interrompere il flusso principale
    }
}
?>
