<?php
require_once __DIR__ . '/../models/WorkMatch.php';

class WorkMatchController
{
    private WorkMatch $model;
    public function __construct(){ $this->model=new WorkMatch(); }

    public function run(): void
    {
        if(session_status()===PHP_SESSION_NONE) session_start();
        if(($_GET['action']??'')==='logout'){ session_destroy(); header('Location: index.php'); exit; }
        if($_SERVER['REQUEST_METHOD']==='POST') $this->procesarFormulario();
        if(!isset($_SESSION['usuario'])){ $this->mostrarAcceso(); return; }
        $this->mostrarSistema();
    }

    private function procesarFormulario(): void
    {
        $a=$_POST['action']??'';
        if($a==='login'){
            $u=$this->model->login(trim($_POST['correo']??''),$_POST['password']??'');
            if($u){ $_SESSION['usuario']=$u; header('Location: index.php'); exit; }
            $_SESSION['mensaje']='Correo o contraseña incorrectos.'; return;
        }
        if($a==='registro'){
            $pass=$_POST['password']??''; $conf=$_POST['confirmar']??'';
            if($pass!==$conf || strlen($pass)<6){ $_SESSION['mensaje']='Las contraseñas deben coincidir y tener al menos 6 caracteres.'; return; }
            $d=['nombre'=>trim($_POST['nombre']??''),'identificacion'=>trim($_POST['identificacion']??''),'correo'=>trim($_POST['correo']??''),'telefono'=>trim($_POST['telefono']??''),'sitio_web'=>trim($_POST['sitio_web']??''),'password'=>$pass,'tipo'=>($_POST['tipo']??'')==='empresa'?'empresa':'candidato'];
            if($this->model->registrar($d)){ $_SESSION['mensaje_ok']='Cuenta creada correctamente.'; header('Location: index.php'); exit; }
            $_SESSION['mensaje']='No fue posible crear la cuenta.'; return;
        }
        if(!isset($_SESSION['usuario'])) return;
        $u=$_SESSION['usuario']; $id=(int)$u['id'];
        if($a==='actualizar_perfil' && $u['tipo']==='candidato'){
            $this->model->actualizarPerfilCandidato($id,['nombre'=>trim($_POST['nombre']??''),'telefono'=>trim($_POST['telefono']??''),'sitio_web'=>'','profesion'=>trim($_POST['profesion']??''),'formacion'=>trim($_POST['formacion']??''),'experiencia'=>trim($_POST['experiencia']??''),'habilidades'=>trim($_POST['habilidades']??''),'ubicacion'=>trim($_POST['ubicacion']??'')]);
            $this->refrescarSesion($id); $this->redirigir('perfil','Perfil actualizado correctamente.');
        }
        if($a==='actualizar_perfil' && $u['tipo']==='empresa'){
            $this->model->actualizarPerfilEmpresa($id,['nombre'=>trim($_POST['nombre']??''),'telefono'=>trim($_POST['telefono']??''),'sitio_web'=>trim($_POST['sitio_web']??''),'sector'=>trim($_POST['sector']??''),'descripcion'=>trim($_POST['descripcion']??''),'direccion'=>trim($_POST['direccion']??'')]);
            $this->refrescarSesion($id); $this->redirigir('perfil','Perfil actualizado correctamente.');
        }
        $vacanteDatos=['puesto'=>trim($_POST['puesto']??''),'area'=>trim($_POST['area']??''),'descripcion'=>trim($_POST['descripcion']??''),'requisitos'=>trim($_POST['requisitos']??''),'ubicacion'=>trim($_POST['ubicacion']??''),'modalidad'=>trim($_POST['modalidad']??'Presencial'),'tipo_contrato'=>trim($_POST['tipo_contrato']??'Tiempo completo'),'salario'=>trim($_POST['salario']??'')];
        if($a==='crear_vacante' && $u['tipo']==='empresa'){ $this->model->crearVacante($id,$vacanteDatos); $this->redirigir('vacantes','Vacante publicada correctamente.'); }
        if($a==='editar_vacante' && $u['tipo']==='empresa'){ $this->model->editarVacante((int)($_POST['vacante_id']??0),$id,$vacanteDatos); $this->redirigir('vacantes','Vacante actualizada.'); }
        if($a==='eliminar_vacante' && $u['tipo']==='empresa'){ $this->model->eliminarVacante((int)($_POST['vacante_id']??0),$id); $this->redirigir('vacantes','Vacante eliminada.'); }
        if($a==='estado_vacante' && $u['tipo']==='empresa'){ $this->model->cambiarEstadoVacante((int)($_POST['vacante_id']??0),$id); $this->redirigir('vacantes','Estado actualizado.'); }
        if($a==='postular' && $u['tipo']==='candidato'){ $ok=$this->model->postular((int)($_POST['vacante_id']??0),$id,trim($_POST['mensaje']??'')); $this->redirigir('vacantes',$ok?'Postulación enviada.':'No se pudo postular o ya existe una postulación.'); }
        if($a==='retirar_postulacion' && $u['tipo']==='candidato'){ $this->model->retirarPostulacion((int)($_POST['postulacion_id']??0),$id); $this->redirigir('postulaciones','Postulación retirada.'); }
        if($a==='favorito' && $u['tipo']==='candidato'){ $this->model->alternarFavorito((int)($_POST['vacante_id']??0),$id); $this->redirigir($_POST['volver']??'vacantes','Lista de guardadas actualizada.'); }
        if($a==='estado_postulacion' && $u['tipo']==='empresa'){ $this->model->actualizarPostulacion((int)($_POST['postulacion_id']??0),$id,$_POST['estado']??'En revisión'); $this->redirigir('candidatos','Estado de candidatura actualizado.'); }
    }

    private function mostrarAcceso(): void
    {
        $mensaje=$_SESSION['mensaje']??''; $mensajeOk=$_SESSION['mensaje_ok']??''; unset($_SESSION['mensaje'],$_SESSION['mensaje_ok']); require __DIR__.'/../views/acceso.php';
    }
    private function mostrarSistema(): void
    {
        $usuario=$_SESSION['usuario']; $seccion=$_GET['seccion']??'panel'; $mensajeOk=$_SESSION['mensaje_ok']??''; unset($_SESSION['mensaje_ok']);
        $perfil=$usuario['tipo']==='empresa'?$this->model->obtenerPerfilEmpresa((int)$usuario['id']):$this->model->obtenerPerfilCandidato((int)$usuario['id']);
        $vacantes=$usuario['tipo']==='empresa'?$this->model->listarVacantesEmpresa((int)$usuario['id']):$this->model->listarVacantes((int)$usuario['id']);
        $postulaciones=$usuario['tipo']==='candidato'?$this->model->postulacionesCandidato((int)$usuario['id']):[];
        $favoritas=$usuario['tipo']==='candidato'?$this->model->favoritasCandidato((int)$usuario['id']):[];
        $candidatos=$usuario['tipo']==='empresa'?$this->model->candidatosEmpresa((int)$usuario['id']):[];
        require __DIR__.'/../views/sistema.php';
    }
    private function refrescarSesion(int $id): void { $u=$this->model->obtenerUsuario($id); if($u) $_SESSION['usuario']=$u; }
    private function redirigir(string $seccion,string $mensaje): void { $_SESSION['mensaje_ok']=$mensaje; header('Location: index.php?seccion='.urlencode($seccion)); exit; }
}
