<?php
require_once __DIR__ . '/../../config/database.php';

class WorkMatch
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    public function login(string $correo, string $password): array|false
    {
        $stmt = $this->connection->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();
        if ($usuario && password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
            return $usuario;
        }
        return false;
    }

    public function restablecerPassword(string $correo, string $identificacion, string $password): bool
    {
        $stmt = $this->connection->prepare('SELECT id FROM usuarios WHERE correo = :correo AND identificacion = :identificacion LIMIT 1');
        $stmt->execute(['correo' => $correo, 'identificacion' => $identificacion]);
        $usuario = $stmt->fetch();
        if (!$usuario) return false;

        $stmt = $this->connection->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
        return $stmt->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $usuario['id']
        ]);
    }

    public function registrar(array $datos): bool
    {
        try {
            $stmt = $this->connection->prepare('INSERT INTO usuarios (nombre, identificacion, correo, telefono, sitio_web, password, tipo) VALUES (:nombre,:identificacion,:correo,:telefono,:sitio_web,:password,:tipo)');
            $stmt->execute([
                'nombre'=>$datos['nombre'], 'identificacion'=>$datos['identificacion'], 'correo'=>$datos['correo'],
                'telefono'=>$datos['telefono'] ?: null, 'sitio_web'=>$datos['sitio_web'] ?: null,
                'password'=>password_hash($datos['password'], PASSWORD_DEFAULT), 'tipo'=>$datos['tipo']
            ]);
            $id = (int)$this->connection->lastInsertId();
            $tabla = $datos['tipo'] === 'empresa' ? 'perfiles_empresa' : 'perfiles_candidato';
            $stmt = $this->connection->prepare("INSERT INTO {$tabla} (usuario_id) VALUES (:id)");
            $stmt->execute(['id'=>$id]);
            return true;
        } catch (PDOException $e) { return false; }
    }

    public function obtenerUsuario(int $id): array|false
    {
        $stmt = $this->connection->prepare('SELECT id,nombre,identificacion,correo,telefono,sitio_web,tipo FROM usuarios WHERE id=:id');
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch();
    }

    public function obtenerPerfilCandidato(int $id): array|false
    {
        $stmt=$this->connection->prepare('SELECT u.id,u.nombre,u.identificacion,u.correo,u.telefono,p.profesion,p.formacion,p.experiencia,p.habilidades,p.ubicacion FROM usuarios u JOIN perfiles_candidato p ON p.usuario_id=u.id WHERE u.id=:id');
        $stmt->execute(['id'=>$id]); return $stmt->fetch();
    }

    public function obtenerPerfilEmpresa(int $id): array|false
    {
        $stmt=$this->connection->prepare('SELECT u.id,u.nombre,u.identificacion,u.correo,u.telefono,u.sitio_web,p.sector,p.descripcion,p.direccion FROM usuarios u JOIN perfiles_empresa p ON p.usuario_id=u.id WHERE u.id=:id');
        $stmt->execute(['id'=>$id]); return $stmt->fetch();
    }

    private function actualizarUsuario(int $id, array $d): void
    {
        $stmt=$this->connection->prepare('UPDATE usuarios SET nombre=:nombre,telefono=:telefono,sitio_web=:sitio_web WHERE id=:id');
        $stmt->execute(['nombre'=>$d['nombre'],'telefono'=>$d['telefono']?:null,'sitio_web'=>$d['sitio_web']?:null,'id'=>$id]);
    }

    public function actualizarPerfilCandidato(int $id,array $d): void
    {
        $this->actualizarUsuario($id,$d);
        $stmt=$this->connection->prepare('UPDATE perfiles_candidato SET profesion=:profesion,formacion=:formacion,experiencia=:experiencia,habilidades=:habilidades,ubicacion=:ubicacion WHERE usuario_id=:id');
        $stmt->execute(['profesion'=>$d['profesion'],'formacion'=>$d['formacion'],'experiencia'=>$d['experiencia'],'habilidades'=>$d['habilidades'],'ubicacion'=>$d['ubicacion'],'id'=>$id]);
    }

    public function actualizarPerfilEmpresa(int $id,array $d): void
    {
        $this->actualizarUsuario($id,$d);
        $stmt=$this->connection->prepare('UPDATE perfiles_empresa SET sector=:sector,descripcion=:descripcion,direccion=:direccion WHERE usuario_id=:id');
        $stmt->execute(['sector'=>$d['sector'],'descripcion'=>$d['descripcion'],'direccion'=>$d['direccion'],'id'=>$id]);
    }

    public function listarVacantes(int $candidatoId=0): array
    {
        $sql="SELECT v.*,u.nombre empresa,
              (SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id=v.id) total_postulantes,
              EXISTS(SELECT 1 FROM favoritos f WHERE f.vacante_id=v.id AND f.candidato_id=:cid) favorita,
              EXISTS(SELECT 1 FROM postulaciones p2 WHERE p2.vacante_id=v.id AND p2.candidato_id=:cid2) postulado
              FROM vacantes v JOIN usuarios u ON u.id=v.empresa_id WHERE v.estado='Activa' ORDER BY v.fecha_creacion DESC";
        $stmt=$this->connection->prepare($sql); $stmt->execute(['cid'=>$candidatoId,'cid2'=>$candidatoId]); return $stmt->fetchAll();
    }

    public function listarVacantesEmpresa(int $empresaId): array
    {
        $stmt=$this->connection->prepare('SELECT v.*,(SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id=v.id) total_postulantes FROM vacantes v WHERE empresa_id=:id ORDER BY fecha_creacion DESC');
        $stmt->execute(['id'=>$empresaId]); return $stmt->fetchAll();
    }

    public function crearVacante(int $empresaId,array $d): void
    {
        $stmt=$this->connection->prepare('INSERT INTO vacantes (empresa_id,puesto,area,descripcion,requisitos,ubicacion,modalidad,tipo_contrato,salario,estado) VALUES (:empresa,:puesto,:area,:descripcion,:requisitos,:ubicacion,:modalidad,:contrato,:salario,\'Activa\')');
        $stmt->execute(['empresa'=>$empresaId,'puesto'=>$d['puesto'],'area'=>$d['area'],'descripcion'=>$d['descripcion'],'requisitos'=>$d['requisitos'],'ubicacion'=>$d['ubicacion'],'modalidad'=>$d['modalidad'],'contrato'=>$d['tipo_contrato'],'salario'=>$d['salario']?:null]);
    }

    public function editarVacante(int $id,int $empresaId,array $d): void
    {
        $stmt=$this->connection->prepare('UPDATE vacantes SET puesto=:puesto,area=:area,descripcion=:descripcion,requisitos=:requisitos,ubicacion=:ubicacion,modalidad=:modalidad,tipo_contrato=:contrato,salario=:salario WHERE id=:id AND empresa_id=:empresa');
        $stmt->execute(['puesto'=>$d['puesto'],'area'=>$d['area'],'descripcion'=>$d['descripcion'],'requisitos'=>$d['requisitos'],'ubicacion'=>$d['ubicacion'],'modalidad'=>$d['modalidad'],'contrato'=>$d['tipo_contrato'],'salario'=>$d['salario']?:null,'id'=>$id,'empresa'=>$empresaId]);
    }

    public function eliminarVacante(int $id,int $empresaId): void
    {
        $stmt=$this->connection->prepare('DELETE FROM vacantes WHERE id=:id AND empresa_id=:empresa');
        $stmt->execute(['id'=>$id,'empresa'=>$empresaId]);
    }

    public function cambiarEstadoVacante(int $id,int $empresaId): void
    {
        $stmt=$this->connection->prepare("UPDATE vacantes SET estado=IF(estado='Activa','Cerrada','Activa') WHERE id=:id AND empresa_id=:empresa");
        $stmt->execute(['id'=>$id,'empresa'=>$empresaId]);
    }

    public function postular(int $vacanteId,int $candidatoId,string $mensaje=''): bool
    {
        try {
            $stmt=$this->connection->prepare("INSERT INTO postulaciones (vacante_id,candidato_id,estado,mensaje) SELECT :vacante,:candidato,'En revisión',:mensaje FROM vacantes WHERE id=:vacante2 AND estado='Activa'");
            $stmt->execute(['vacante'=>$vacanteId,'candidato'=>$candidatoId,'mensaje'=>$mensaje ?: null,'vacante2'=>$vacanteId]);
            return $stmt->rowCount()>0;
        } catch(PDOException $e){ return false; }
    }

    public function retirarPostulacion(int $postulacionId,int $candidatoId): void
    {
        $stmt=$this->connection->prepare("DELETE FROM postulaciones WHERE id=:id AND candidato_id=:candidato AND estado='En revisión'");
        $stmt->execute(['id'=>$postulacionId,'candidato'=>$candidatoId]);
    }

    public function alternarFavorito(int $vacanteId,int $candidatoId): void
    {
        $stmt=$this->connection->prepare('SELECT id FROM favoritos WHERE vacante_id=:v AND candidato_id=:c');
        $stmt->execute(['v'=>$vacanteId,'c'=>$candidatoId]);
        if($stmt->fetch()){
            $stmt=$this->connection->prepare('DELETE FROM favoritos WHERE vacante_id=:v AND candidato_id=:c');
        }else{
            $stmt=$this->connection->prepare('INSERT INTO favoritos (vacante_id,candidato_id) VALUES (:v,:c)');
        }
        $stmt->execute(['v'=>$vacanteId,'c'=>$candidatoId]);
    }

    public function favoritasCandidato(int $candidatoId): array
    {
        $stmt=$this->connection->prepare("SELECT v.*,u.nombre empresa FROM favoritos f JOIN vacantes v ON v.id=f.vacante_id JOIN usuarios u ON u.id=v.empresa_id WHERE f.candidato_id=:id ORDER BY f.fecha_guardado DESC");
        $stmt->execute(['id'=>$candidatoId]); return $stmt->fetchAll();
    }

    public function postulacionesCandidato(int $id): array
    {
        $stmt=$this->connection->prepare('SELECT p.id,p.estado,p.fecha_postulacion,p.mensaje,v.puesto,v.ubicacion,v.modalidad,u.nombre empresa FROM postulaciones p JOIN vacantes v ON v.id=p.vacante_id JOIN usuarios u ON u.id=v.empresa_id WHERE p.candidato_id=:id ORDER BY p.fecha_postulacion DESC');
        $stmt->execute(['id'=>$id]); return $stmt->fetchAll();
    }

    public function candidatosEmpresa(int $id): array
    {
        $stmt=$this->connection->prepare('SELECT p.id postulacion_id,p.estado,p.fecha_postulacion,p.mensaje,v.puesto,u.nombre,u.correo,u.telefono,pc.profesion,pc.habilidades,pc.ubicacion FROM postulaciones p JOIN vacantes v ON v.id=p.vacante_id JOIN usuarios u ON u.id=p.candidato_id JOIN perfiles_candidato pc ON pc.usuario_id=u.id WHERE v.empresa_id=:id ORDER BY p.fecha_postulacion DESC');
        $stmt->execute(['id'=>$id]); return $stmt->fetchAll();
    }

    public function actualizarPostulacion(int $id,int $empresaId,string $estado): void
    {
        if(!in_array($estado,['En revisión','Entrevista','Aceptado','Rechazado'],true)) return;
        $stmt=$this->connection->prepare('UPDATE postulaciones p JOIN vacantes v ON v.id=p.vacante_id SET p.estado=:estado WHERE p.id=:id AND v.empresa_id=:empresa');
        $stmt->execute(['estado'=>$estado,'id'=>$id,'empresa'=>$empresaId]);
    }
}
