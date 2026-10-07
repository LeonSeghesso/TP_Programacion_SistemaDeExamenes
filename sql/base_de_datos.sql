-- Sistema de Exámenes - Estructura de la base de datos
-- Programación III - TP N.° 2

CREATE DATABASE IF NOT EXISTS examenes_pagina
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE examenes_pagina;

-- Un usuario puede definir uno o muchos exámenes
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL   -- guarda el hash de password_hash()
);

-- Cada examen pertenece a un solo usuario
CREATE TABLE IF NOT EXISTS examen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombreExamen VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Cada pregunta pertenece a un solo examen (y por lo tanto a un solo usuario)
CREATE TABLE IF NOT EXISTS preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_examen INT NOT NULL,
    pregunta TEXT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_examen) REFERENCES examen(id) ON DELETE CASCADE
);
