<?php
require_once __DIR__ . '/../models/WorkMatch.php';

class WorkMatchController
{
    private WorkMatch $model;

    public function __construct()
    {
        $this->model = new WorkMatch();
    }

    public function run(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $action = $_GET['action'] ?? '';

        if ($action === 'logout') {
            session_destroy();
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarFormulario();
        }

        if (!isset($_SESSION['usuario'])) {
            $this->mostrarAcceso();
            return;
        }

        $this->mostrarSistema();
    }

    private function procesarFormulario(): void
    {
        $action = $_POST['action'] ?? '';

        if ($action === 'login') {
            $usuario = $this->model->login(trim($_POST['correo'] ?? ''), $_POST['password'] ?? '');

            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                header('Location: index.php');
                exit;
            }

            $_SESSION['mensaje'] = 'Correo o contraseña incorrectos.';
            return;
        }

        if ($action === 'registro') {
            $password = $_POST['password'] ?? '';
            $confirmar = $_POST['confirmar'] ?? '';

            if ($password !== $confirmar || strlen($password) < 6) {
                $_SESSION['mensaje'] = 'Las contraseñas deben coincidir y tener al menos 6 caracteres.';
                return;
            }

            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'identificacion' => trim($_POST['identificacion'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'sitio_web' => trim($_POST['sitio_web'] ?? ''),
                'password' => $password,
                'tipo' => ($_POST['tipo'] ?? '') === 'empresa' ? 'empresa' : 'candidato',
            ];

            if ($this->model->registrar($datos)) {
                $_SESSION['mensaje_ok'] = 'Cuenta creada correctamente. Ya puedes iniciar sesión.';
                header('Location: index.php');
                exit;
            }

            $_SESSION['mensaje'] = 'No fue posible crear la cuenta. Verifica que el correo no esté registrado.';
            return;
        }

        if (!isset($_SESSION['usuario'])) {
            return;
        }

        $usuario = $_SESSION['usuario'];
        $usuarioId = (int) $usuario['id'];

        if ($action === 'actualizar_perfil' && $usuario['tipo'] === 'candidato') {
            $this->model->actualizarPerfilCandidato($usuarioId, [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'sitio_web' => '',
                'profesion' => trim($_POST['profesion'] ?? ''),
                'formacion' => trim($_POST['formacion'] ?? ''),
                'experiencia' => trim($_POST['experiencia'] ?? ''),
                'habilidades' => trim($_POST['habilidades'] ?? ''),
                'ubicacion' => trim($_POST['ubicacion'] ?? ''),
            ]);
            $this->refrescarSesion($usuarioId);
            $this->redirigir('perfil', 'Perfil actualizado correctamente.');
        }

        if ($action === 'actualizar_perfil' && $usuario['tipo'] === 'empresa') {
            $this->model->actualizarPerfilEmpresa($usuarioId, [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'sitio_web' => trim($_POST['sitio_web'] ?? ''),
                'sector' => trim($_POST['sector'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
            ]);
            $this->refrescarSesion($usuarioId);
            $this->redirigir('perfil', 'Perfil actualizado correctamente.');
        }

        if ($action === 'crear_vacante' && $usuario['tipo'] === 'empresa') {
            $this->model->crearVacante($usuarioId, [
                'puesto' => trim($_POST['puesto'] ?? ''),
                'area' => trim($_POST['area'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                'salario' => trim($_POST['salario'] ?? ''),
            ]);
            $this->redirigir('vacantes', 'Vacante creada correctamente.');
        }

        if ($action === 'estado_vacante' && $usuario['tipo'] === 'empresa') {
            $this->model->cambiarEstadoVacante((int) ($_POST['vacante_id'] ?? 0), $usuarioId);
            $this->redirigir('vacantes', 'Estado de la vacante actualizado.');
        }

        if ($action === 'postular' && $usuario['tipo'] === 'candidato') {
            $ok = $this->model->postular((int) ($_POST['vacante_id'] ?? 0), $usuarioId);
            $this->redirigir('vacantes', $ok ? 'Postulación enviada correctamente.' : 'Ya te postulaste a esta vacante.');
        }

        if ($action === 'estado_postulacion' && $usuario['tipo'] === 'empresa') {
            $this->model->actualizarPostulacion(
                (int) ($_POST['postulacion_id'] ?? 0),
                $usuarioId,
                $_POST['estado'] ?? 'En revisión'
            );
            $this->redirigir('candidatos', 'Estado de la postulación actualizado.');
        }
    }

    private function mostrarAcceso(): void
    {
        $mensaje = $_SESSION['mensaje'] ?? '';
        $mensajeOk = $_SESSION['mensaje_ok'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['mensaje_ok']);
        require __DIR__ . '/../views/acceso.php';
    }

    private function mostrarSistema(): void
    {
        $usuario = $_SESSION['usuario'];
        $seccion = $_GET['seccion'] ?? 'panel';
        $mensajeOk = $_SESSION['mensaje_ok'] ?? '';
        unset($_SESSION['mensaje_ok']);

        $perfil = $usuario['tipo'] === 'empresa'
            ? $this->model->obtenerPerfilEmpresa((int) $usuario['id'])
            : $this->model->obtenerPerfilCandidato((int) $usuario['id']);

        $vacantes = $usuario['tipo'] === 'empresa'
            ? $this->model->listarVacantesEmpresa((int) $usuario['id'])
            : $this->model->listarVacantes();

        $postulaciones = $usuario['tipo'] === 'candidato'
            ? $this->model->postulacionesCandidato((int) $usuario['id'])
            : [];

        $candidatos = $usuario['tipo'] === 'empresa'
            ? $this->model->candidatosEmpresa((int) $usuario['id'])
            : [];

        require __DIR__ . '/../views/sistema.php';
    }

    private function refrescarSesion(int $usuarioId): void
    {
        $usuario = $this->model->obtenerUsuario($usuarioId);
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
        }
    }

    private function redirigir(string $seccion, string $mensaje): void
    {
        $_SESSION['mensaje_ok'] = $mensaje;
        header('Location: index.php?seccion=' . urlencode($seccion));
        exit;
    }
}
