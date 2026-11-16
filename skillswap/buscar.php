<?php
require_once 'db.php';
verificarSesion();
$uid = $_SESSION['usuario_id'];

$q         = trim($_GET['q'] ?? '');
$carrera   = $_GET['carrera'] ?? '';
$habilidad = $_GET['habilidad'] ?? '';
$orden     = $_GET['orden'] ?? 'coincidencia';

$carreras = $pdo->query("SELECT DISTINCT carrera FROM usuarios WHERE carrera != ''")->fetchAll(PDO::FETCH_COLUMN);
$listaHabilidades = $pdo->query("SELECT id, nombre FROM habilidades ORDER BY nombre ASC")->fetchAll();

$stOf = $pdo->prepare("SELECT h.id, h.nombre FROM usuario_habilidades uh JOIN habilidades h ON uh.habilidad_id = h.id WHERE uh.usuario_id = ? AND uh.tipo = 'ofrezco'");
$stOf->execute([$uid]);
$misOfrezco = $stOf->fetchAll(PDO::FETCH_KEY_PAIR);

$stAp = $pdo->prepare("SELECT habilidad_id FROM usuario_habilidades WHERE usuario_id = ? AND tipo = 'aprender'");
$stAp->execute([$uid]);
$misAprender = $stAp->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT DISTINCT u.* FROM usuarios u
        LEFT JOIN usuario_habilidades uh ON u.id = uh.usuario_id
        LEFT JOIN habilidades h ON uh.habilidad_id = h.id
        WHERE u.id != :uid";
$params = ['uid' => $uid];

if ($q !== '') {
    $sql .= " AND (u.nombre_completo LIKE :q OR u.carrera LIKE :q OR h.nombre LIKE :q)";
    $params['q'] = "%$q%";
}
if ($carrera !== '') {
    $sql .= " AND u.carrera = :carrera";
    $params['carrera'] = $carrera;
}
if ($habilidad !== '') {
    $sql .= " AND h.id = :habilidad";
    $params['habilidad'] = (int)$habilidad;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidatos = $stmt->fetchAll();

$resultados = [];
foreach ($candidatos as $cand) {
    $stCand = $pdo->prepare("SELECT h.id, h.nombre, uh.tipo FROM usuario_habilidades uh JOIN habilidades h ON uh.habilidad_id = h.id WHERE uh.usuario_id = ?");
    $stCand->execute([$cand['id']]);
    $habsCand = $stCand->fetchAll();

    $matches = 0;
    $habSolicitada = 'Habilidad general';
    $habOfrecida = !empty($misOfrezco) ? reset($misOfrezco) : 'Mi habilidad';

    foreach ($habsCand as $hc) {
        if ($hc['tipo'] === 'ofrezco') {
            if ($habSolicitada === 'Habilidad general') $habSolicitada = $hc['nombre'];
            if (in_array($hc['id'], $misAprender)) {
                $matches++;
                $habSolicitada = $hc['nombre'];
            }
        }
        if ($hc['tipo'] === 'aprender' && isset($misOfrezco[$hc['id']])) {
            $matches++;
            $habOfrecida = $misOfrezco[$hc['id']];
        }
    }
    $cand['porcentaje'] = min(98, 50 + ($matches * 22));
    $cand['habilidades'] = $habsCand;
    $cand['hab_ofrecida'] = $habOfrecida;
    $cand['hab_solicitada'] = $habSolicitada;
    $resultados[] = $cand;
}

if ($orden === 'coincidencia') {
    usort($resultados, fn($a, $b) => $b['porcentaje'] <=> $a['porcentaje']);
} else {
    usort($resultados, fn($a, $b) => strcmp($a['nombre_completo'], $b['nombre_completo']));
}

renderInicio('SkillSwap - Buscar');
?>
<h2 style="font-size: 1.7rem; margin-bottom: 16px;">Buscar estudiantes</h2>

<form method="GET" class="card" style="padding: 20px;">
    <div style="display: flex; gap: 12px; margin-bottom: 14px;">
        <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($q) ?>" placeholder="🔍 Buscar por habilidad, nombre o carrera...">
        <button type="submit" class="btn-primary">Buscar</button>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <select name="carrera" class="form-control">
            <option value="">Todas las carreras</option>
            <?php foreach ($carreras as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $carrera === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="habilidad" class="form-control">
            <option value="">Todas las habilidades</option>
            <?php foreach ($listaHabilidades as $hab): ?>
                <option value="<?= $hab['id'] ?>" <?= $habilidad == $hab['id'] ? 'selected' : '' ?>><?= htmlspecialchars($hab['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="orden" class="form-control" onchange="this.form.submit()">
            <option value="coincidencia" <?= $orden === 'coincidencia' ? 'selected' : '' ?>>Ordenar por: Mejor coincidencia</option>
            <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Ordenar por: Nombre (A-Z)</option>
        </select>
    </div>
</form>

<?php foreach ($resultados as $cand): 
    $iniCand = strtoupper(substr($cand['nombre_completo'], 0, 2));
?>
<div class="card" style="display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="avatar-circle" style="width: 56px; height: 56px; font-size: 1.2rem;"><?= $iniCand ?></div>
        <div>
            <h3 style="font-size: 1.15rem;"><?= htmlspecialchars($cand['nombre_completo']) ?></h3>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 8px;">
                <?= htmlspecialchars($cand['carrera']) ?> · <?= htmlspecialchars($cand['anio_academico']) ?>
            </p>
            <div>
                <?php foreach ($cand['habilidades'] as $hc) if ($hc['tipo'] === 'ofrezco'): ?>
                    <span class="skill-tag"><?= htmlspecialchars($hc['nombre']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div style="text-align: right;">
        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 10px;">
            Coincidencia <strong style="color: var(--naranja); font-size: 1.35rem;"><?= $cand['porcentaje'] ?>%</strong>
        </p>
        <form action="acciones.php" method="POST">
            <input type="hidden" name="accion" value="solicitar">
            <input type="hidden" name="receptor_id" value="<?= $cand['id'] ?>">
            <input type="hidden" name="ofrecida" value="<?= htmlspecialchars($cand['hab_ofrecida']) ?>">
            <input type="hidden" name="solicitada" value="<?= htmlspecialchars($cand['hab_solicitada']) ?>">
            <button type="submit" class="btn-primary">Solicitar intercambio</button>
        </form>
    </div>
</div>
<?php endforeach; ?>
<?php renderFin(); ?>