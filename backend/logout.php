<?php
session_start();

// Distruggi la sessione
session_destroy();

// Reindirizza al login
header('Location: login.php');
exit;
?>