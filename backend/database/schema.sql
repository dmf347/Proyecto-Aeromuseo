-- ============================================================
-- Script de creación de la base de datos del Aeromuseo
-- Ejecuta esto en tu MySQL/phpMyAdmin antes de usar el backend
-- ============================================================

CREATE DATABASE IF NOT EXISTS aeromuseo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE aeromuseo;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,          -- bcrypt hash
    rol         ENUM('admin', 'visitante')  NOT NULL DEFAULT 'visitante',
    activo      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Usuarios de prueba
-- Contraseña para ambos: "password123"
INSERT INTO usuarios (nombre, email, password, rol) VALUES
(
    'Administrador',
    'admin@aeromuseo.es',
    '$2y$12$K8wKbdplJQLrSepgIoN4a.9Bzo6Xy0f0M5e1K7j8.3Bm5NeVK6oXy',  -- password123
    'admin'
),
(
    'Visitante Demo',
    'visitante@aeromuseo.es',
    '$2y$12$K8wKbdplJQLrSepgIoN4a.9Bzo6Xy0f0M5e1K7j8.3Bm5NeVK6oXy',  -- password123
    'visitante'
);
