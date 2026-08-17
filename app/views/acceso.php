<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkMatch | Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-body">
<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-briefcase-fill"></i> WorkMatch</a>
</nav>

<main class="container py-5">
    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <?php if ($mensajeOk): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensajeOk) ?></div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center"><i class="bi bi-person-circle"></i> Iniciar Sesión</h2>
                    <form method="post" action="index.php">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-success w-100" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i> Ingresar</button>
                    </form>

                    <div class="demo-box mt-4">
                        <strong>Usuarios de prueba</strong><br>
                        Candidato: candidato@workmatch.com / 123456<br>
                        Empresa: empresa@workmatch.com / 123456
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-3">Crear una cuenta</h2>
                    <p class="text-muted">Elige el tipo de perfil y completa los datos.</p>

                    <form method="post" action="index.php" id="registroForm">
                        <input type="hidden" name="action" value="registro">

                        <div class="mb-3">
                            <label class="form-label">Tipo de cuenta</label>
                            <select name="tipo" id="tipoCuenta" class="form-select" required>
                                <option value="candidato">Candidato</option>
                                <option value="empresa">Empresa</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" id="labelNombre">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" id="labelIdentificacion">Cédula o Identificación</label>
                                <input type="text" name="identificacion" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="col-12 mb-3 d-none" id="sitioWebBox">
                                <label class="form-label">Sitio Web</label>
                                <input type="url" name="sitio_web" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" name="confirmar" class="form-control" minlength="6" required>
                            </div>
                        </div>

                        <button class="btn btn-dark w-100" type="submit"><i class="bi bi-person-plus me-1"></i> Crear Cuenta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="public/js/app.js"></script>
</body>
</html>
