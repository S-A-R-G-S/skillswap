<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$host = "127.0.0.1";
$port = "3307";
$db   = "skillswap";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }
}

function renderInicio($titulo = 'SkillSwap', $conMenu = true) {
    global $pdo;
    $actual = basename($_SERVER['PHP_SELF']);
    $notifs = 0;
    if ($conMenu && isset($_SESSION['usuario_id'])) {
        $st = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0");
        $st->execute([$_SESSION['usuario_id']]);
        $notifs = $st->fetchColumn();
    }
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>$titulo</title><link rel='stylesheet' href='styles.css'></head><body>";
    if ($conMenu) {
        $iniciales = strtoupper(substr($_SESSION['usuario_nombre'] ?? 'US', 0, 2));
        echo "<div class='app-container'>
        <aside class='sidebar'>
            <div class='sidebar-brand'><div class='brand-logo'>🎓 Skill<span>Swap</span></div></div>
            <nav class='sidebar-nav'>
                <a href='dashboard.php' class='" . ($actual=='dashboard.php'?'active':'') . "'>🏠 Inicio</a>
                <a href='buscar.php' class='" . ($actual=='buscar.php'?'active':'') . "'>🔍 Buscar</a>
                <a href='mensajes.php' class='" . ($actual=='mensajes.php'?'active':'') . "'>🔄 Mis intercambios <span class='badge-notif'>$notifs</span></a>
                <a href='perfil.php' class='" . ($actual=='perfil.php'?'active':'') . "'>👤 Mi perfil</a>
            </nav>
            <div class='sidebar-footer'><a href='acciones.php?accion=logout'>🚪 Cerrar sesión</a></div>
        </aside>
        <main class='main-content'>
            <div style='display:flex; justify-content:flex-end; align-items:center; gap:14px; margin-bottom:14px;'>
                <a href='mensajes.php' style='text-decoration:none; font-size:1.3rem;'>🔔</a>
                <div class='avatar-circle' style='width:42px; height:42px; font-size:0.9rem;'>$iniciales</div>
            </div>";
    }
}

function renderFin($conMenu = true) {
    if ($conMenu) echo "</main></div>";
    echo "<script src='app.js'></script></body></html>";
}