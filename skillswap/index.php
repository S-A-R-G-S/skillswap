<?php
require_once 'db.php';
renderInicio('SkillSwap - Acceso', false);

if (isset($_GET['listo'])): // Pantalla 8: "¡Todo listo!"[cite: 1]
?>
<div style="display:flex; justify-content:center; align-items:center; min-height:100vh; text-align:center;">
    <div class="card" style="max-width:440px; padding:40px;">
        <div style="font-size:3.5rem;">🚀</div>
        <h1 style="color:var(--color-naranja); margin:12px 0;">¡Todo listo!</h1>
        <p style="margin-bottom:24px;">Ya has completado tu registro y puedes comenzar a intercambiar habilidades en SkillSwap.</p>
        <a href="dashboard.php" class="btn-primary" style="display:block; margin-bottom:10px;">Ir al inicio</a>
        <a href="buscar.php" class="btn-outline" style="display:block;">Explorar estudiantes</a>
    </div>
</div>
<?php else: $esRegistro = isset($_GET['modo']) && $_GET['modo'] === 'registro'; ?>
<div class="auth-wrapper">
    <div class="auth-banner">
        <h1>🎓 SkillSwap</h1>
        <h2 style="margin:18px 0; font-size:2rem;">Intercambia habilidades, construye tu futuro</h2>
        <p>Conecta con otros estudiantes, aprende nuevas habilidades y crece juntos.</p>
    </div>
    <div class="auth-form-box">
        <h2><?= $esRegistro ? 'Crear cuenta' : 'Iniciar sesión' ?></h2>
        <p style="color:var(--color-texto-suave); margin-bottom:20px;">Tu talento también enseña</p>
        <form action="acciones.php" method="POST">
            <input type="hidden" name="accion" value="<?= $esRegistro ? 'registro' : 'login' ?>">
            <?php if ($esRegistro): ?>
                <div class="form-group"><label>Nombre completo</label><input type="text" name="nombre" class="form-control" required></div>
            <?php endif; ?>
            <div class="form-group"><label>Correo universitario</label><input type="email" name="correo" class="form-control" placeholder="ejemplo@up.ac.pa" required></div>
            <div class="form-group"><label>Contraseña</label><input type="password" name="password" class="form-control" minlength="8" required></div>
            <button type="submit" class="btn-primary" style="width:100%;"><?= $esRegistro ? 'Registrarse' : 'Iniciar sesión' ?></button>
        </form>
        <p style="margin-top:16px; text-align:center;">
            <?= $esRegistro ? '¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a>' : '¿No tienes cuenta? <a href="index.php?modo=registro">Regístrate</a>' ?>
        </p>
    </div>
</div>
<?php endif; renderFin(false); ?>