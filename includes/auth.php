<?php
session_start();

function is_logged() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged()) {
        header('Location: /Barber%20System/index.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $role) {
        if (is_array($role) && in_array($_SESSION['rol'], $role)) return;
        http_response_code(403);
        echo "Acceso denegado.";
        exit;
    }
}
