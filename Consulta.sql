
CREATE DATABASE IF NOT EXISTS arcade_sistemas;
USE arcade_sistemas;


CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS categorias_juego (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_juego VARCHAR(50) NOT NULL
);


CREATE TABLE IF NOT EXISTS preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    texto_pregunta VARCHAR(255) NOT NULL,
    respuesta_correcta VARCHAR(100) NOT NULL,
    opcion_falsa_1 VARCHAR(100) NOT NULL,
    opcion_falsa_2 VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorias_juego(id_categoria) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS historial_partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL,
    puntuacion_final INT NOT NULL,
    errores INT NOT NULL,
    tiempo_segundos INT NOT NULL,
    fecha_jugada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categorias_juego(id_categoria) ON DELETE CASCADE
);

-- ==========================================
-- DATOS DE PRUEBA PARA EL JUEGO
-- ==========================================


INSERT INTO categorias_juego (nombre_juego) VALUES 
('Caza Conceptos'), 
('Tiro al Blanco');


INSERT INTO preguntas (id_categoria, texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2) VALUES 
(1, '¿Qué protocolo se utiliza para transferir páginas web?', 'HTTP', 'FTP', 'SMTP'),
(1, 'Lenguaje estándar para consultar bases de datos relacionales:', 'SQL', 'PHP', 'HTML'),
(1, '¿Qué significa la sigla RAM?', 'Random Access Memory', 'Read Access Memory', 'Run Active Memory'),
(2, 'Etiqueta HTML para insertar un hipervínculo:', '<a>', '<link>', '<href>'),
(2, '¿Qué estructura de datos usa el principio LIFO?', 'Pila (Stack)', 'Cola (Queue)', 'Árbol (Tree)');



INSERT INTO usuarios (nombre, correo, password_hash) VALUES 
('Jugador Prueba', 'prueba@itvillahermosa.edu', '$2y$10$wzV.D2/D/8.sA5yF8o4g.O3n3o.k7W.Tj9/z/Kj2Z/3.s/2.K/9/');




ALTER TABLE usuarios ADD COLUMN rol ENUM('alumno', 'maestro') DEFAULT 'alumno';


CREATE TABLE salas (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    id_maestro INT NOT NULL,
    codigo_sala VARCHAR(10) NOT NULL UNIQUE,
    estado ENUM('espera', 'jugando', 'finalizada') DEFAULT 'espera',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_maestro) REFERENCES usuarios(id_usuario)
);


ALTER TABLE historial_partidas ADD COLUMN id_sala INT NULL;
ALTER TABLE historial_partidas ADD FOREIGN KEY (id_sala) REFERENCES salas(id_sala);


ALTER TABLE usuarios ADD COLUMN rol ENUM('alumno', 'maestro') DEFAULT 'alumno';

CREATE TABLE salas (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    id_maestro INT NOT NULL,
    codigo_sala VARCHAR(10) NOT NULL UNIQUE,
    estado ENUM('espera', 'jugando', 'finalizada') DEFAULT 'espera',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_maestro) REFERENCES usuarios(id_usuario)
);

ALTER TABLE historial_partidas ADD FOREIGN KEY (id_sala) REFERENCES salas(id_sala);

ALTER TABLE usuarios ADD COLUMN rol ENUM('alumno', 'maestro') DEFAULT 'alumno';


INSERT IGNORE INTO categorias_juego (id_categoria, nombre_juego) VALUES 
(1, 'Pesca Técnica'),
(2, 'Caza Conceptos'),
(3, 'Tiro al Blanco');


SELECT * FROM historial_partidas

SELECT * FROM preguntas

ALTER TABLE salas ADD COLUMN id_categoria INT NULL;

SELECT * FROM usuarios

