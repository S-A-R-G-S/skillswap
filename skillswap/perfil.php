<?php
require_once 'db.php';
verificarSesion();
$uid = $_SESSION['usuario_id'];

$stUser = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stUser->execute([$uid]);
$usuario = $stUser->fetch();

$stHabs = $pdo->prepare("
    SELECT h.nombre, uh.tipo FROM usuario_habilidades uh
    JOIN habilidades h ON uh.habilidad_id = h.id
    WHERE uh.usuario_id = ?
");
$stHabs->execute([$uid]);
$habilidades = $stHabs->fetchAll();

$iniPerfil = strtoupper(substr($usuario['nombre_completo'], 0, 2));

renderInicio('SkillSwap - Mi Perfil');
?>
<div class="card" style="display: flex; align-items: center; gap: 20px;">
    <div class="avatar-circle" style="width: 76px; height: 76px; font-size: 1.7rem;"><?= $iniPerfil ?></div>
    <div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <h2 style="font-size: 1.5rem;"><?= htmlspecialchars($usuario['nombre_completo']) ?></h2>
            <span style="color: #2E7D32; font-size: 0.85rem; font-weight: 700;">● En línea</span>
        </div>
        <p style="color: var(--text-muted); margin-top: 4px;">Universidad de Panamá · <?= htmlspecialchars($usuario['carrera']) ?> (<?= htmlspecialchars($usuario['anio_academico']) ?>)</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3 style="margin-bottom: 14px;">Sobre mí</h3>
        <form action="acciones.php" method="POST">
            <input type="hidden" name="accion" value="perfil">
            <div class="form-group">
                <label>Carrera</label>
                <input type="text" name="carrera" class="form-control" value="<?= htmlspecialchars($usuario['carrera']) ?>">
            </div>
            <div class="form-group">
                <label>Año académico</label>
                <input type="text" name="anio_academico" class="form-control" value="<?= htmlspecialchars($usuario['anio_academico']) ?>">
            </div>
            <div class="form-group">
                <label>Descripción personal</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="Cuéntales a otros estudiantes qué te apasiona aprender y enseñar..."><?= htmlspecialchars($usuario['bio'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-primary">Guardar cambios</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 14px;">Mis habilidades</h3>
        <p style="font-weight: 700; font-size: 0.9rem; margin-bottom: 6px;">Ofrezco:</p>
        <div style="margin-bottom: 16px;">
            <?php foreach ($habilidades as $h) if ($h['tipo'] === 'ofrezco'): ?>
                <span class="skill-tag"><?= htmlspecialchars($h['nombre']) ?></span>
            <?php endif; ?>
        </div>

        <p style="font-weight: 700; font-size: 0.9rem; margin-bottom: 6px;">Quiero aprender:</p>
        <div style="margin-bottom: 20px;">
            <?php foreach ($habilidades as $h) if ($h['tipo'] === 'aprender'): ?>
                <span class="skill-tag learn"><?= htmlspecialchars($h['nombre']) ?></span>
            <?php endif; ?>
        </div>

        <form action="acciones.php" method="POST" style="border-top: 1px solid var(--border-soft); padding-top: 18px;">
            <input type="hidden" name="accion" value="habilidad">
            <div class="form-group">
                <label>Agregar nueva habilidad</label>
                <input type="text" name="habilidad" class="form-control" placeholder="Ej. Python, Inglés, Diseño gráfico" required>
            </div>
            <div class="form-group">
                <select name="tipo" class="form-control">
                    <option value="ofrezco">La ofrezco (Sé enseñarla)</option>
                    <option value="aprender">Quiero aprenderla</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">+ Agregar habilidad</button>
        </form>
    </div>
</div>
<?php renderFin(); ?>