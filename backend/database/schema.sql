-- ============================================================
-- Script de creación de la base de datos del Aeromuseo
-- Ejecuta esto en tu MySQL/phpMyAdmin antes de usar el backend
-- ============================================================

CREATE DATABASE IF NOT EXISTS aeromuseo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE aeromuseo;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id                   INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre               VARCHAR(100)    NOT NULL,
    email                VARCHAR(150)    NOT NULL UNIQUE,
    password             VARCHAR(255)    NOT NULL,          -- bcrypt hash
    rol                  ENUM('admin', 'visitante')  NOT NULL DEFAULT 'visitante',
    activo               TINYINT(1)      NOT NULL DEFAULT 1,
    email_verificado     TINYINT(1)      NOT NULL DEFAULT 0, -- 0 = pendiente, 1 = verificado
    token_verificacion   VARCHAR(64)     NULL,               -- token de un solo uso
    created_at           TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Usuarios de prueba (ya marcados como verificados para no bloquear el desarrollo)
-- Contraseña para ambos: "password123"
INSERT INTO usuarios (nombre, email, password, rol, email_verificado) VALUES
(
    'Administrador',
    'admin@aeromuseo.es',
    '$2y$12$K8wKbdplJQLrSepgIoN4a.9Bzo6Xy0f0M5e1K7j8.3Bm5NeVK6oXy',  -- password123
    'admin',
    1  -- ya verificado
),
(
    'Visitante Demo',
    'visitante@aeromuseo.es',
    '$2y$12$K8wKbdplJQLrSepgIoN4a.9Bzo6Xy0f0M5e1K7j8.3Bm5NeVK6oXy',  -- password123
    'visitante',
    1  -- ya verificado
);

-- Tabla de eventos del museo
CREATE TABLE IF NOT EXISTS eventos (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    titulo      VARCHAR(200)    NOT NULL,
    descripcion TEXT            NOT NULL,
    fecha       DATE            NOT NULL,
    hora        TIME            NOT NULL,
    lugar       VARCHAR(200)    NOT NULL,
    imagen_url  VARCHAR(500)    NULL,
    activo      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de reservas de visitas guiadas
CREATE TABLE IF NOT EXISTS reservas (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED    NOT NULL,
    fecha_visita    DATE            NOT NULL,
    num_personas    INT             NOT NULL DEFAULT 1,
    comentarios     TEXT            NULL,
    estado          ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
