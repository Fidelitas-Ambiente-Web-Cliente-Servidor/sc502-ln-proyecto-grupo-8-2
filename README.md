# WorkMatch - Proyecto Grupo 8

Proyecto organizado con el mismo enfoque MVC utilizado en Caso 2, pero con menos archivos PHP.

## Estructura

- `index.php`: punto de entrada único.
- `app/controllers/WorkMatchController.php`: controla login, registro, perfiles, vacantes y postulaciones.
- `app/models/WorkMatch.php`: contiene consultas PDO.
- `app/views/acceso.php`: login y registro de candidato/empresa.
- `app/views/sistema.php`: panel completo, cambia según el tipo de usuario.
- `config/database.php`: conexión PDO.
- `mysql/db.sql`: base de datos y usuarios de prueba.

## Usuarios de prueba

- Candidato: `candidato@workmatch.com` / `123456`
- Empresa: `empresa@workmatch.com` / `123456`

## URL

`http://localhost:8080/sc502-ln-proyecto-grupo-8-mvc-simple/`
