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
        $sql = 'SELECT * FROM usuarios WHERE correo = :correo LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
            return $usuario;
        }

        return false;
    }

    public function registrar(array $datos): bool
    {
        $sql = 'INSERT INTO usuarios (nombre, identificacion, correo, telefono, sitio_web, password, tipo)
                VALUES (:nombre, :identificacion, :correo, :telefono, :sitio_web, :password, :tipo)';

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'identificacion' => $datos['identificacion'],
                'correo' => $datos['correo'],
                'telefono' => $datos['telefono'] ?: null,
                'sitio_web' => $datos['sitio_web'] ?: null,
                'password' => password_hash($datos['password'], PASSWORD_DEFAULT),
                'tipo' => $datos['tipo'],
            ]);

            $usuarioId = (int) $this->connection->lastInsertId();

            if ($datos['tipo'] === 'candidato') {
                $stmt = $this->connection->prepare('INSERT INTO perfiles_candidato (usuario_id) VALUES (:usuario_id)');
            } else {
                $stmt = $this->connection->prepare('INSERT INTO perfiles_empresa (usuario_id) VALUES (:usuario_id)');
            }

            $stmt->execute(['usuario_id' => $usuarioId]);
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function obtenerUsuario(int $id): array|false
    {
        $stmt = $this->connection->prepare('SELECT id, nombre, identificacion, correo, telefono, sitio_web, tipo FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function obtenerPerfilCandidato(int $usuarioId): array|false
    {
        $sql = 'SELECT u.id, u.nombre, u.identificacion, u.correo, u.telefono,
                       p.profesion, p.formacion, p.experiencia, p.habilidades, p.ubicacion
                FROM usuarios u
                INNER JOIN perfiles_candidato p ON p.usuario_id = u.id
                WHERE u.id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetch();
    }

    public function obtenerPerfilEmpresa(int $usuarioId): array|false
    {
        $sql = 'SELECT u.id, u.nombre, u.identificacion, u.correo, u.telefono, u.sitio_web,
                       p.sector, p.descripcion, p.direccion
                FROM usuarios u
                INNER JOIN perfiles_empresa p ON p.usuario_id = u.id
                WHERE u.id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetch();
    }

    public function actualizarPerfilCandidato(int $usuarioId, array $datos): void
    {
        $this->actualizarDatosUsuario($usuarioId, $datos);

        $sql = 'UPDATE perfiles_candidato
                SET profesion = :profesion, formacion = :formacion, experiencia = :experiencia,
                    habilidades = :habilidades, ubicacion = :ubicacion
                WHERE usuario_id = :usuario_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'profesion' => $datos['profesion'],
            'formacion' => $datos['formacion'],
            'experiencia' => $datos['experiencia'],
            'habilidades' => $datos['habilidades'],
            'ubicacion' => $datos['ubicacion'],
            'usuario_id' => $usuarioId,
        ]);
    }

    public function actualizarPerfilEmpresa(int $usuarioId, array $datos): void
    {
        $this->actualizarDatosUsuario($usuarioId, $datos);

        $sql = 'UPDATE perfiles_empresa
                SET sector = :sector, descripcion = :descripcion, direccion = :direccion
                WHERE usuario_id = :usuario_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'sector' => $datos['sector'],
            'descripcion' => $datos['descripcion'],
            'direccion' => $datos['direccion'],
            'usuario_id' => $usuarioId,
        ]);
    }

    private function actualizarDatosUsuario(int $usuarioId, array $datos): void
    {
        $sql = 'UPDATE usuarios
                SET nombre = :nombre, telefono = :telefono, sitio_web = :sitio_web
                WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'] ?: null,
            'sitio_web' => $datos['sitio_web'] ?: null,
            'id' => $usuarioId,
        ]);
    }

    public function listarVacantes(): array
    {
        $sql = "SELECT v.*, u.nombre AS empresa
                FROM vacantes v
                INNER JOIN usuarios u ON u.id = v.empresa_id
                WHERE v.estado = 'Activa'
                ORDER BY v.fecha_creacion DESC";
        return $this->connection->query($sql)->fetchAll();
    }

    public function listarVacantesEmpresa(int $empresaId): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM vacantes WHERE empresa_id = :empresa_id ORDER BY fecha_creacion DESC');
        $stmt->execute(['empresa_id' => $empresaId]);
        return $stmt->fetchAll();
    }

    public function crearVacante(int $empresaId, array $datos): void
    {
        $sql = 'INSERT INTO vacantes (empresa_id, puesto, area, descripcion, ubicacion, salario, estado)
                VALUES (:empresa_id, :puesto, :area, :descripcion, :ubicacion, :salario, :estado)';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'empresa_id' => $empresaId,
            'puesto' => $datos['puesto'],
            'area' => $datos['area'],
            'descripcion' => $datos['descripcion'],
            'ubicacion' => $datos['ubicacion'],
            'salario' => $datos['salario'] ?: null,
            'estado' => 'Activa',
        ]);
    }

    public function cambiarEstadoVacante(int $vacanteId, int $empresaId): void
    {
        $sql = "UPDATE vacantes
                SET estado = IF(estado = 'Activa', 'Cerrada', 'Activa')
                WHERE id = :id AND empresa_id = :empresa_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $vacanteId, 'empresa_id' => $empresaId]);
    }

    public function postular(int $vacanteId, int $candidatoId): bool
    {
        try {
            $sql = 'INSERT INTO postulaciones (vacante_id, candidato_id, estado)
                    VALUES (:vacante_id, :candidato_id, :estado)';
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                'vacante_id' => $vacanteId,
                'candidato_id' => $candidatoId,
                'estado' => 'En revisión',
            ]);
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function postulacionesCandidato(int $candidatoId): array
    {
        $sql = 'SELECT p.id, p.estado, p.fecha_postulacion, v.puesto, v.ubicacion, u.nombre AS empresa
                FROM postulaciones p
                INNER JOIN vacantes v ON v.id = p.vacante_id
                INNER JOIN usuarios u ON u.id = v.empresa_id
                WHERE p.candidato_id = :candidato_id
                ORDER BY p.fecha_postulacion DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['candidato_id' => $candidatoId]);
        return $stmt->fetchAll();
    }

    public function candidatosEmpresa(int $empresaId): array
    {
        $sql = 'SELECT p.id AS postulacion_id, p.estado, p.fecha_postulacion,
                       v.puesto, u.nombre, u.correo, u.telefono,
                       pc.profesion, pc.habilidades
                FROM postulaciones p
                INNER JOIN vacantes v ON v.id = p.vacante_id
                INNER JOIN usuarios u ON u.id = p.candidato_id
                INNER JOIN perfiles_candidato pc ON pc.usuario_id = u.id
                WHERE v.empresa_id = :empresa_id
                ORDER BY p.fecha_postulacion DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['empresa_id' => $empresaId]);
        return $stmt->fetchAll();
    }

    public function actualizarPostulacion(int $postulacionId, int $empresaId, string $estado): void
    {
        $permitidos = ['En revisión', 'Aceptado', 'Rechazado'];
        if (!in_array($estado, $permitidos, true)) {
            return;
        }

        $sql = 'UPDATE postulaciones p
                INNER JOIN vacantes v ON v.id = p.vacante_id
                SET p.estado = :estado
                WHERE p.id = :id AND v.empresa_id = :empresa_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'estado' => $estado,
            'id' => $postulacionId,
            'empresa_id' => $empresaId,
        ]);
    }
}
