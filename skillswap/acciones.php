<?php
require_once 'db.php';
$accion = $_REQUEST['accion'] ?? '';
$uid = $_SESSION['usuario_id'] ?? 0;

// 1. REGISTRO (Épica 1)
if ($accion === 'registro') {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, correo, password_hash, en_linea) VALUES (?, ?, ?, 1)");
    $stmt->execute([trim($_POST['nombre']), trim($_POST['correo']), $hash]);
    $_SESSION['usuario_id'] = $pdo->lastInsertId();
    $_SESSION['usuario_nombre'] = trim($_POST['nombre']);
    header('Location: index.php?listo=1'); // Pantalla 8: "¡Todo listo!"
    exit;
}

// 2. LOGIN (Épica 1)
if ($accion === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([trim($_POST['correo'])]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'], $u['password_hash'])) {
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['usuario_nombre'] = $u['nombre_completo'];
        header('Location: dashboard.php');
    } else {
        header('Location: index.php?error=1');
    }
    exit;
}

// 3. LOGOUT
if ($accion === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// 4. ACTUALIZAR PERFIL Y HABILIDADES (Épica 2)
if ($accion === 'perfil' && $uid) {
    $pdo->prepare("UPDATE usuarios SET carrera = ?, anio_academico = ?, bio = ? WHERE id = ?")
        ->execute([$_POST['carrera'], $_POST['anio_academico'], $_POST['bio'], $uid]);
    header('Location: perfil.php');
    exit;
}

if ($accion === 'habilidad' && $uid) {
    $hab = trim($_POST['habilidad']);
    $pdo->prepare("INSERT IGNORE INTO habilidades (nombre) VALUES (?)")->execute([$hab]);
    $st = $pdo->prepare("SELECT id FROM habilidades WHERE nombre = ?");
    $st->execute([$hab]);
    $pdo->prepare("INSERT IGNORE INTO usuario_habilidades (usuario_id, habilidad_id, tipo) VALUES (?, ?, ?)")
        ->execute([$uid, $st->fetchColumn(), $_POST['tipo']]);
    header('Location: perfil.php');
    exit;
}

// 5. SOLICITAR, RESPONDER Y EVALUAR INTERCAMBIOS (Épicas 4 y 5)
if ($accion === 'solicitar' && $uid) {
    $pdo->prepare("INSERT INTO intercambios (emisor_id, receptor_id, habilidad_ofrecida, habilidad_solicitada) VALUES (?, ?, ?, ?)")
        ->execute([$uid, $_POST['receptor_id'], $_POST['ofrecida'], $_POST['solicitada']]);
    $pdo->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, descripcion) VALUES (?, 'solicitud', 'Nueva solicitud', ?)")
        ->execute([$_POST['receptor_id'], $_SESSION['usuario_nombre'] . " te envió una solicitud de intercambio."]);
    header('Location: mensajes.php');
    exit;
}

if ($accion === 'responder' && $uid) {
    $pdo->prepare("UPDATE intercambios SET estado = ? WHERE id = ? AND receptor_id = ?")
        ->execute([$_POST['estado'], $_POST['intercambio_id'], $uid]);
    header('Location: mensajes.php');
    exit;
}

if ($accion === 'evaluar' && $uid) {
    $pdo->prepare("INSERT INTO evaluaciones (intercambio_id, evaluador_id, evaluado_id, puntuacion, comentario) VALUES (?, ?, ?, ?, ?)")
        ->execute([$_POST['intercambio_id'], $uid, $_POST['evaluado_id'], $_POST['puntuacion'], $_POST['comentario']]);
    $pdo->prepare("UPDATE intercambios SET estado = 'completado' WHERE id = ?")->execute([$_POST['intercambio_id']]);
    header('Location: mensajes.php');
    exit;
}

// 6. CHAT JSON (Épica 4)
if ($accion === 'chat_leer' && $uid) {
    header('Content-Type: application/json');
    $st = $pdo->prepare("SELECT * FROM mensajes WHERE intercambio_id = ? ORDER BY id ASC");
    $st->execute([(int)$_GET['id']]);
    echo json_encode($st->fetchAll());
    exit;
}

if ($accion === 'chat_enviar' && $uid) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo->prepare("INSERT INTO mensajes (intercambio_id, remitente_id, contenido) VALUES (?, ?, ?)")
        ->execute([(int)$data['intercambio_id'], $uid, trim($data['contenido'])]);
    echo json_encode(['ok' => true]);
    exit;
}