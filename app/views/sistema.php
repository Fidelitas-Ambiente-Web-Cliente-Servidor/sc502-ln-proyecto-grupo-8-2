<?php
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$esEmpresa = $usuario['tipo'] === 'empresa';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkMatch | <?= $esEmpresa ? 'Empresa' : 'Candidato' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-briefcase-fill"></i> WorkMatch</a>
    <div class="text-white d-flex align-items-center gap-3">
        <span class="user-chip"><i class="bi bi-person-circle"></i> <?= e($usuario['nombre']) ?></span>
        <a class="btn btn-outline-light btn-sm" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a>
    </div>
</nav>

<div class="app-layout">
    <aside class="sidebar">
        <h5 class="mb-4"><?= $esEmpresa ? 'Empresa' : 'Candidato' ?></h5>
        <a class="<?= $seccion === 'panel' ? 'active' : '' ?>" href="index.php?seccion=panel"><i class="bi bi-speedometer2"></i> Panel Principal</a>
        <a class="<?= $seccion === 'perfil' ? 'active' : '' ?>" href="index.php?seccion=perfil"><i class="bi bi-person-vcard"></i> <?= $esEmpresa ? 'Perfil Empresarial' : 'Perfil Profesional' ?></a>
        <a class="<?= $seccion === 'vacantes' ? 'active' : '' ?>" href="index.php?seccion=vacantes"><i class="bi bi-briefcase"></i> <?= $esEmpresa ? 'Vacantes' : 'Buscar Vacantes' ?></a>
        <?php if ($esEmpresa): ?>
            <a class="<?= $seccion === 'candidatos' ? 'active' : '' ?>" href="index.php?seccion=candidatos"><i class="bi bi-people"></i> Candidatos</a>
        <?php else: ?>
            <a class="<?= $seccion === 'postulaciones' ? 'active' : '' ?>" href="index.php?seccion=postulaciones"><i class="bi bi-file-earmark-check"></i> Postulaciones</a>
        <?php endif; ?>
    </aside>

    <main class="content-area">
        <div class="page-topbar">
            <div>
                <span class="eyebrow text-success"><?= $esEmpresa ? 'Cuenta empresarial' : 'Cuenta de candidato' ?></span>
            </div>
            <div class="live-clock" id="liveClock"><i class="bi bi-clock"></i> <span></span></div>
        </div>
        <?php if ($mensajeOk): ?>
            <div class="alert alert-success"><?= e($mensajeOk) ?></div>
        <?php endif; ?>

        <?php if ($seccion === 'panel'): ?>
            <div class="hero-panel">
                <div>
                    <span class="hero-kicker">Panel Principal</span>
                    <h2 class="fw-bold mb-2">Hola, <?= e($usuario['nombre']) ?></h2>
                    <p class="mb-0"><?= $esEmpresa ? 'Administra tus vacantes y revisa candidatos desde un solo lugar.' : 'Encuentra oportunidades y lleva el control de tus postulaciones.' ?></p>
                </div>
                <div class="hero-icon"><i class="bi <?= $esEmpresa ? 'bi-building' : 'bi-person-workspace' ?>"></i></div>
            </div>

            <div class="row g-4 mt-2">
                <?php if ($esEmpresa): ?>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center"><div><h5>Vacantes</h5><h2 class="stat-number" data-count="<?= count($vacantes) ?>">0</h2></div><div class="stat-icon"><i class="bi bi-briefcase"></i></div></div></div></div>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center"><div><h5>Candidatos</h5><h2 class="stat-number" data-count="<?= count($candidatos) ?>">0</h2></div><div class="stat-icon"><i class="bi bi-people"></i></div></div></div></div>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body"><h5>Perfil</h5><p class="mb-0"><?= e($perfil['sector'] ?: 'Pendiente de completar') ?></p></div></div></div>
                <?php else: ?>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center"><div><h5>Vacantes disponibles</h5><h2 class="stat-number" data-count="<?= count($vacantes) ?>">0</h2></div><div class="stat-icon"><i class="bi bi-briefcase"></i></div></div></div></div>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center"><div><h5>Postulaciones</h5><h2 class="stat-number" data-count="<?= count($postulaciones) ?>">0</h2></div><div class="stat-icon"><i class="bi bi-file-earmark-check"></i></div></div></div></div>
                    <div class="col-md-4"><div class="card stat-card"><div class="card-body"><h5>Profesión</h5><p class="mb-0"><?= e($perfil['profesion'] ?: 'Pendiente de completar') ?></p></div></div></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($seccion === 'perfil'): ?>
            <h2 class="fw-bold"><?= $esEmpresa ? 'Perfil Empresarial' : 'Perfil Profesional' ?></h2>
            <p class="text-muted">Actualiza la información de tu perfil.</p>

            <div class="card mt-4 reveal">
                <div class="card-body p-4">
                    <form method="post" action="index.php?seccion=perfil">
                        <input type="hidden" name="action" value="actualizar_perfil">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= $esEmpresa ? 'Nombre de Empresa' : 'Nombre Completo' ?></label>
                                <input type="text" name="nombre" class="form-control" value="<?= e($perfil['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" value="<?= e($perfil['correo']) ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?= e($perfil['telefono']) ?>">
                            </div>

                            <?php if ($esEmpresa): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sitio Web</label>
                                    <input type="url" name="sitio_web" class="form-control" value="<?= e($perfil['sitio_web']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sector</label>
                                    <input type="text" name="sector" class="form-control" value="<?= e($perfil['sector']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" value="<?= e($perfil['direccion']) ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción de la Empresa</label>
                                    <textarea name="descripcion" class="form-control" rows="4"><?= e($perfil['descripcion']) ?></textarea>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Profesión</label>
                                    <input type="text" name="profesion" class="form-control" value="<?= e($perfil['profesion']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ubicación</label>
                                    <input type="text" name="ubicacion" class="form-control" value="<?= e($perfil['ubicacion']) ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Formación Académica</label>
                                    <textarea name="formacion" class="form-control" rows="3"><?= e($perfil['formacion']) ?></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Experiencia Laboral</label>
                                    <textarea name="experiencia" class="form-control" rows="3"><?= e($perfil['experiencia']) ?></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Habilidades</label>
                                    <textarea name="habilidades" class="form-control" rows="3"><?= e($perfil['habilidades']) ?></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-success" type="submit">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($seccion === 'vacantes'): ?>
            <h2 class="fw-bold"><?= $esEmpresa ? 'Gestión de Vacantes' : 'Buscar Vacantes' ?></h2>

            <?php if ($esEmpresa): ?>
                <div class="card my-4">
                    <div class="card-body p-4">
                        <h5>Nueva Vacante</h5>
                        <form method="post" action="index.php?seccion=vacantes">
                            <input type="hidden" name="action" value="crear_vacante">
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Puesto</label><input type="text" name="puesto" class="form-control" required></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Área</label><input type="text" name="area" class="form-control" required></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Ubicación</label><input type="text" name="ubicacion" class="form-control" required></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Salario</label><input type="number" step="0.01" name="salario" class="form-control"></div>
                                <div class="col-12 mb-3"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="3" required></textarea></div>
                            </div>
                            <button class="btn btn-success" type="submit">Crear Vacante</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="vacancy-tools mt-4 mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="vacancySearch" class="form-control" placeholder="Buscar por puesto, área, empresa o ubicación...">
                </div>
                <span class="result-count"><strong id="vacancyCount"><?= count($vacantes) ?></strong> resultados</span>
            </div>

            <div class="row g-4" id="vacancyGrid">
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="col-lg-6 vacancy-item">
                        <div class="card h-100 vacancy-card" data-search="<?= e(strtolower(($vacante['puesto'] ?? '') . ' ' . ($vacante['area'] ?? '') . ' ' . ($vacante['ubicacion'] ?? '') . ' ' . ($vacante['empresa'] ?? ''))) ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <h5><?= e($vacante['puesto']) ?></h5>
                                        <?php if (!$esEmpresa): ?><p class="text-success fw-semibold mb-1"><?= e($vacante['empresa']) ?></p><?php endif; ?>
                                    </div>
                                    <span class="badge <?= ($vacante['estado'] ?? 'Activa') === 'Activa' ? 'text-bg-success' : 'text-bg-secondary' ?> align-self-start"><?= e($vacante['estado'] ?? 'Activa') ?></span>
                                </div>
                                <p class="mb-1 vacancy-meta"><i class="bi bi-grid"></i><strong>Área:</strong> <?= e($vacante['area']) ?></p>
                                <p class="mb-1 vacancy-meta"><i class="bi bi-geo-alt"></i><strong>Ubicación:</strong> <?= e($vacante['ubicacion']) ?></p>
                                <p><?= e($vacante['descripcion']) ?></p>

                                <?php if ($esEmpresa): ?>
                                    <form method="post" action="index.php?seccion=vacantes">
                                        <input type="hidden" name="action" value="estado_vacante">
                                        <input type="hidden" name="vacante_id" value="<?= (int) $vacante['id'] ?>">
                                        <button class="btn btn-outline-dark btn-sm confirm-action" data-confirm="¿Deseas cambiar el estado de esta vacante?" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Cambiar Estado</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="index.php?seccion=vacantes">
                                        <input type="hidden" name="action" value="postular">
                                        <input type="hidden" name="vacante_id" value="<?= (int) $vacante['id'] ?>">
                                        <button class="btn btn-success btn-sm confirm-action" data-confirm="¿Deseas postularte a esta vacante?" type="submit"><i class="bi bi-send me-1"></i>Postularme</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$vacantes): ?>
                    <p class="text-muted">No hay vacantes registradas.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$esEmpresa && $seccion === 'postulaciones'): ?>
            <h2 class="fw-bold">Mis Postulaciones</h2>
            <div class="table-responsive mt-4 reveal">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Puesto</th><th>Empresa</th><th>Ubicación</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($postulaciones as $item): ?>
                        <tr>
                            <td><?= e($item['puesto']) ?></td>
                            <td><?= e($item['empresa']) ?></td>
                            <td><?= e($item['ubicacion']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e($item['estado']) ?></span></td>
                            <td><?= e($item['fecha_postulacion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($esEmpresa && $seccion === 'candidatos'): ?>
            <h2 class="fw-bold">Candidatos</h2>
            <div class="table-responsive mt-4 reveal">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Candidato</th><th>Vacante</th><th>Profesión</th><th>Habilidades</th><th>Estado</th></tr></thead>
                    <tbody>
                    <?php foreach ($candidatos as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e($item['nombre']) ?></strong><br>
                                <small><?= e($item['correo']) ?></small>
                            </td>
                            <td><?= e($item['puesto']) ?></td>
                            <td><?= e($item['profesion']) ?></td>
                            <td><?= e($item['habilidades']) ?></td>
                            <td>
                                <form method="post" action="index.php?seccion=candidatos" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="estado_postulacion">
                                    <input type="hidden" name="postulacion_id" value="<?= (int) $item['postulacion_id'] ?>">
                                    <select name="estado" class="form-select form-select-sm">
                                        <?php foreach (['En revisión', 'Aceptado', 'Rechazado'] as $estado): ?>
                                            <option value="<?= e($estado) ?>" <?= $item['estado'] === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-dark btn-sm" type="submit">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>
<script src="public/js/app.js"></script>
</body>
</html>
