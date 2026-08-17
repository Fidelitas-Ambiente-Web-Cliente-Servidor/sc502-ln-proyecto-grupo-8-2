CREATE DATABASE IF NOT EXISTS workmatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workmatch;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    identificacion VARCHAR(40) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    sitio_web VARCHAR(180) NULL,
    password VARCHAR(255) NOT NULL,
    tipo ENUM('candidato', 'empresa') NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS perfiles_candidato (
    usuario_id INT PRIMARY KEY,
    profesion VARCHAR(120) NULL,
    formacion TEXT NULL,
    experiencia TEXT NULL,
    habilidades TEXT NULL,
    ubicacion VARCHAR(120) NULL,
    CONSTRAINT fk_perfil_candidato_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS perfiles_empresa (
    usuario_id INT PRIMARY KEY,
    sector VARCHAR(120) NULL,
    descripcion TEXT NULL,
    direccion VARCHAR(180) NULL,
    CONSTRAINT fk_perfil_empresa_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vacantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    puesto VARCHAR(120) NOT NULL,
    area VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    ubicacion VARCHAR(120) NOT NULL,
    salario DECIMAL(12,2) NULL,
    estado ENUM('Activa', 'Cerrada') DEFAULT 'Activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vacante_empresa FOREIGN KEY (empresa_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vacante_id INT NOT NULL,
    candidato_id INT NOT NULL,
    estado ENUM('En revisión', 'Aceptado', 'Rechazado') DEFAULT 'En revisión',
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_postulacion (vacante_id, candidato_id),
    CONSTRAINT fk_postulacion_vacante FOREIGN KEY (vacante_id) REFERENCES vacantes(id) ON DELETE CASCADE,
    CONSTRAINT fk_postulacion_candidato FOREIGN KEY (candidato_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nombre, identificacion, correo, telefono, password, tipo)
SELECT 'Luis Candidato', '1-1111-1111', 'candidato@workmatch.com', '8888-1111', '$2y$12$0eg5XOXY4J008U.r3DGsz.M8u4bsdnHq8HInwnYG8U9Lp89o7xRzC', 'candidato'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo = 'candidato@workmatch.com');

INSERT INTO usuarios (nombre, identificacion, correo, telefono, sitio_web, password, tipo)
SELECT 'Tech Solutions', '3-101-999999', 'empresa@workmatch.com', '2222-3333', 'https://example.com', '$2y$12$0eg5XOXY4J008U.r3DGsz.M8u4bsdnHq8HInwnYG8U9Lp89o7xRzC', 'empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo = 'empresa@workmatch.com');

INSERT INTO perfiles_candidato (usuario_id, profesion, formacion, experiencia, habilidades, ubicacion)
SELECT id, 'Desarrollador Web', 'Ingeniería en Sistemas', 'Experiencia en proyectos universitarios', 'HTML, CSS, JavaScript, PHP, SQL', 'San José'
FROM usuarios
WHERE correo = 'candidato@workmatch.com'
AND NOT EXISTS (SELECT 1 FROM perfiles_candidato pc WHERE pc.usuario_id = usuarios.id);

INSERT INTO perfiles_empresa (usuario_id, sector, descripcion, direccion)
SELECT id, 'Tecnología', 'Empresa dedicada al desarrollo de soluciones tecnológicas.', 'San José, Costa Rica'
FROM usuarios
WHERE correo = 'empresa@workmatch.com'
AND NOT EXISTS (SELECT 1 FROM perfiles_empresa pe WHERE pe.usuario_id = usuarios.id);

INSERT INTO vacantes (empresa_id, puesto, area, descripcion, ubicacion, salario)
SELECT id, 'Desarrollador Front End', 'Tecnología', 'Apoyo en desarrollo y mantenimiento de interfaces web.', 'San José', 650000
FROM usuarios
WHERE correo = 'empresa@workmatch.com'
AND NOT EXISTS (SELECT 1 FROM vacantes WHERE puesto = 'Desarrollador Front End');
