-- ============================================
-- WORKMATCH - BASE DE DATOS COMPLETA
-- PROYECTO GRUPO 8
-- ============================================

DROP DATABASE IF EXISTS workmatch;

CREATE DATABASE workmatch
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE workmatch;


-- ============================================
-- USUARIOS
-- ============================================

CREATE TABLE usuarios (
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


-- ============================================
-- PERFIL CANDIDATO
-- ============================================

CREATE TABLE perfiles_candidato (
    usuario_id INT PRIMARY KEY,
    profesion VARCHAR(120) NULL,
    formacion TEXT NULL,
    experiencia TEXT NULL,
    habilidades TEXT NULL,
    ubicacion VARCHAR(120) NULL,

    CONSTRAINT fk_perfil_candidato_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);


-- ============================================
-- PERFIL EMPRESA
-- ============================================

CREATE TABLE perfiles_empresa (
    usuario_id INT PRIMARY KEY,
    sector VARCHAR(120) NULL,
    descripcion TEXT NULL,
    direccion VARCHAR(180) NULL,

    CONSTRAINT fk_perfil_empresa_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);


-- ============================================
-- VACANTES
-- ============================================

CREATE TABLE vacantes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    empresa_id INT NOT NULL,

    puesto VARCHAR(120) NOT NULL,

    area VARCHAR(100) NOT NULL,

    descripcion TEXT NOT NULL,

    requisitos TEXT NULL,

    ubicacion VARCHAR(120) NOT NULL,

    modalidad ENUM(
        'Presencial',
        'Híbrido',
        'Remoto'
    ) NOT NULL DEFAULT 'Presencial',

    tipo_contrato ENUM(
        'Tiempo completo',
        'Medio tiempo',
        'Temporal',
        'Práctica'
    ) NOT NULL DEFAULT 'Tiempo completo',

    salario DECIMAL(12,2) NULL,

    estado ENUM(
        'Activa',
        'Cerrada'
    ) DEFAULT 'Activa',

    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_vacante_empresa
        FOREIGN KEY (empresa_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);


-- ============================================
-- POSTULACIONES
-- ============================================

CREATE TABLE postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,

    vacante_id INT NOT NULL,

    candidato_id INT NOT NULL,

    estado ENUM(
        'En revisión',
        'Entrevista',
        'Aceptado',
        'Rechazado'
    ) DEFAULT 'En revisión',

    mensaje TEXT NULL,

    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_postulacion (
        vacante_id,
        candidato_id
    ),

    CONSTRAINT fk_postulacion_vacante
        FOREIGN KEY (vacante_id)
        REFERENCES vacantes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_postulacion_candidato
        FOREIGN KEY (candidato_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);


-- ============================================
-- FAVORITOS
-- ============================================

CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    vacante_id INT NOT NULL,

    candidato_id INT NOT NULL,

    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_favorito (
        vacante_id,
        candidato_id
    ),

    CONSTRAINT fk_favorito_vacante
        FOREIGN KEY (vacante_id)
        REFERENCES vacantes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_favorito_candidato
        FOREIGN KEY (candidato_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);


-- ============================================
-- CANDIDATO DE PRUEBA
-- Password: 123456
-- ============================================

INSERT INTO usuarios (
    nombre,
    identificacion,
    correo,
    telefono,
    sitio_web,
    password,
    tipo
)
VALUES (
    'Luis Candidato',
    '1-1111-1111',
    'candidato@workmatch.com',
    '8888-1111',
    NULL,
    '$2y$12$0eg5XOXY4J008U.r3DGsz.M8u4bsdnHq8HInwnYG8U9Lp89o7xRzC',
    'candidato'
);


-- ============================================
-- PERFIL CANDIDATO
-- ============================================

INSERT INTO perfiles_candidato (
    usuario_id,
    profesion,
    formacion,
    experiencia,
    habilidades,
    ubicacion
)
VALUES (
    1,
    'Desarrollador Web',
    'Ingeniería en Sistemas',
    'Experiencia en proyectos universitarios y desarrollo web.',
    'HTML, CSS, JavaScript, PHP, MySQL, Bootstrap',
    'San José, Costa Rica'
);


-- ============================================
-- EMPRESA DE PRUEBA
-- Password: 123456
-- ============================================

INSERT INTO usuarios (
    nombre,
    identificacion,
    correo,
    telefono,
    sitio_web,
    password,
    tipo
)
VALUES (
    'Tech Solutions',
    '3-101-999999',
    'empresa@workmatch.com',
    '2222-3333',
    'https://example.com',
    '$2y$12$0eg5XOXY4J008U.r3DGsz.M8u4bsdnHq8HInwnYG8U9Lp89o7xRzC',
    'empresa'
);


-- ============================================
-- PERFIL EMPRESA
-- ============================================

INSERT INTO perfiles_empresa (
    usuario_id,
    sector,
    descripcion,
    direccion
)
VALUES (
    2,
    'Tecnología',
    'Empresa dedicada al desarrollo de soluciones tecnológicas.',
    'San José, Costa Rica'
);


-- ============================================
-- VACANTES DE PRUEBA
-- ============================================

INSERT INTO vacantes (
    empresa_id,
    puesto,
    area,
    descripcion,
    requisitos,
    ubicacion,
    modalidad,
    tipo_contrato,
    salario,
    estado
)
VALUES
(
    2,
    'Desarrollador Front End',
    'Tecnología',
    'Apoyo en desarrollo y mantenimiento de interfaces web.',
    'Conocimientos en HTML, CSS, JavaScript y Bootstrap.',
    'San José',
    'Híbrido',
    'Tiempo completo',
    650000,
    'Activa'
),
(
    2,
    'Soporte Técnico',
    'Soporte',
    'Atención de incidencias y soporte técnico a usuarios.',
    'Conocimientos de Windows, hardware y redes.',
    'Heredia',
    'Presencial',
    'Tiempo completo',
    550000,
    'Activa'
),
(
    2,
    'Desarrollador PHP Junior',
    'Desarrollo',
    'Desarrollo y mantenimiento de aplicaciones web.',
    'Conocimientos básicos de PHP y MySQL.',
    'San José',
    'Remoto',
    'Tiempo completo',
    700000,
    'Activa'
),
(
    2,
    'Practicante de Sistemas',
    'Tecnología',
    'Apoyo en diferentes tareas del departamento de tecnología.',
    'Ser estudiante activo de Ingeniería en Sistemas.',
    'Alajuela',
    'Híbrido',
    'Práctica',
    250000,
    'Activa'
);


-- ============================================
-- POSTULACION DE EJEMPLO
-- ============================================

INSERT INTO postulaciones (
    vacante_id,
    candidato_id,
    estado,
    mensaje
)
VALUES (
    1,
    1,
    'En revisión',
    'Me interesa la vacante y considero que mis conocimientos se ajustan al puesto.'
);


-- ============================================
-- FAVORITO DE EJEMPLO
-- ============================================

INSERT INTO favoritos (
    vacante_id,
    candidato_id
)
VALUES (
    2,
    1
);


-- ============================================
-- VERIFICACION
-- ============================================

SELECT * FROM usuarios;

SELECT * FROM perfiles_candidato;

SELECT * FROM perfiles_empresa;

SELECT * FROM vacantes;

SELECT * FROM postulaciones;

SELECT * FROM favoritos;