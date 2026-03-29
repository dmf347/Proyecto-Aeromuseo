-- Creación de la base de datos para el Aeromuseo
CREATE DATABASE IF NOT EXISTS aeromuseo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aeromuseo_db;

-- Tabla de Usuarios (Administradores y Visitantes)
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'visitante') NOT NULL DEFAULT 'visitante'
);

-- Tabla de Eventos / Visitas Guiadas
CREATE TABLE IF NOT EXISTS Eventos (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha DATETIME NOT NULL,
    capacidad_maxima INT NOT NULL
);

-- Tabla de Reservas
CREATE TABLE IF NOT EXISTS Reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_evento INT NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('confirmada', 'cancelada') NOT NULL DEFAULT 'confirmada',
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_evento) REFERENCES Eventos(id_evento) ON DELETE CASCADE
);
