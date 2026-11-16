<?php
require_once 'db.php';
verificarSesion();
$uid = $_SESSION['usuario_id'];

$stHabs = $pdo->prepare("SELECT h.nombre FROM usuario_habilidades uh JOIN habilidades h ON uh.habilidad_id = h.id WHERE uh.usuario_id = ?");
$stHabs->execute([$uid]);
$misHabs = $stHabs->fetchAll();

$stInc = $pdo->prepare("
    SELECT i.*, u1.nombre_completo AS emisor, u2.nombre_completo AS receptor 
    FROM intercambios i
    JOIN usuarios u1 ON i.emisor_id = u1.id
    JOIN usuarios u2 ON i.receptor_id = u2.id
    WHERE i.emisor_id = ? OR i.receptor_id = ?
    ORDER BY i.id DESC LIMIT 4
");
$stInc->execute([$uid, $uid]);
$intercambios = $stInc->fetchAll();

$stNotif = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0");
$stNotif->execute([$uid]);
$numNotif = $stNotif->fetchColumn();

renderInicio('SkillSwap - Inicio');
?>
<h1 style="font-size: 1.9rem; margin-bottom: 4px;">¡Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>!</h1>
<p style="color: var(--text-muted); margin-bottom: 22px;">¿Qué te gustaría hacer hoy?</p>

<form action="buscar.php" method="GET" style="margin-bottom: 26px;">
    <input type="text" name="q" class="form-control search-pill" placeholder="🔍  Buscar estudiantes, habilidades...">
</form>

<div class="grid-3">
    <div class="card stat-card">
        <div class="stat-icon">🛡️</div>
        <div>
            <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Mis habilidades</p>
            <strong style="font-size: 1.15rem;"><?= count($misHabs) ?> registradas</strong>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">🔄</div>
        <div>
            <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Intercambios activos</p>
            <strong style="font-size: 1.15rem;"><?= count($intercambios) ?> en historial</strong>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">🔔</div>
        <div>
            <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Notificaciones</p>
            <strong style="font-size: 1.15rem;"><?= $numNotif ?> nuevas</strong>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3>Intercambios recientes</h3>
            <a href="mensajes.php" style="color: var(--naranja); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Ver todos</a>
        </div>
        <?php if (empty($intercambios)): ?>
            <p style="color: var(--text-muted);">Aún no tienes intercambios registrados.</p>
        <?php endif; ?>
        <?php foreach ($intercambios as $inc): 
            $nombreComp = ($inc['emisor_id'] == $uid) ? $inc['receptor'] : $inc['emisor'];
            $iniComp = strtoupper(substr($nombreComp, 0, 2));
        ?>
            <div style="padding: 14px 0; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="avatar-circle" style="width: 42px; height: 42px; font-size: 0.9rem;"><?= $iniComp ?></div>
                    <div>
                        <strong><?= htmlspecialchars($nombreComp) ?></strong>
                        <p style="font-size: 0.84rem; color: var(--text-muted);">
                            Intercambio de <?= htmlspecialchars($inc['habilidad_ofrecida']) ?> ↔ <?= htmlspecialchars($inc['habilidad_solicitada']) ?>
                        </p>
                    </div>
                </div>
                <span class="status-pill"><?= htmlspecialchars($inc['estado']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 16px;">Tus habilidades destacadas</h3>
        <?php if (empty($misHabs)): ?>
            <p style="color: var(--text-muted);">Agrega habilidades desde <a href="perfil.php" style="color: var(--naranja); font-weight: 700;">Mi perfil</a>.</p>
        <?php endif; ?>
        <div>
            <?php foreach ($misHabs as $h): ?>
                <span class="skill-tag"><?= htmlspecialchars($h['nombre']) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php renderFin(); ?>