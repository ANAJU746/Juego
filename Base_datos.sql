-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.21.0.7344
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para arcade_sistemas
CREATE DATABASE IF NOT EXISTS `arcade_sistemas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `arcade_sistemas`;

-- Volcando estructura para tabla arcade_sistemas.categorias_juego
CREATE TABLE IF NOT EXISTS `categorias_juego` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_juego` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla arcade_sistemas.categorias_juego: ~5 rows (aproximadamente)
INSERT INTO `categorias_juego` (`id_categoria`, `nombre_juego`) VALUES
	(1, 'Caza Conceptos'),
	(2, 'Tiro al Blanco'),
	(3, 'Tiro al Blanco'),
	(4, 'Atrapar Globos'),
	(5, 'Torre de Conceptos');

-- Volcando estructura para tabla arcade_sistemas.historial_partidas
CREATE TABLE IF NOT EXISTS `historial_partidas` (
  `id_partida` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `puntuacion_final` int(11) NOT NULL,
  `errores` int(11) NOT NULL,
  `tiempo_segundos` int(11) NOT NULL,
  `fecha_jugada` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_sala` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_partida`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_sala` (`id_sala`),
  CONSTRAINT `historial_partidas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `historial_partidas_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_juego` (`id_categoria`) ON DELETE CASCADE,
  CONSTRAINT `historial_partidas_ibfk_3` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla arcade_sistemas.historial_partidas: ~7 rows (aproximadamente)
INSERT INTO `historial_partidas` (`id_partida`, `id_usuario`, `id_categoria`, `puntuacion_final`, `errores`, `tiempo_segundos`, `fecha_jugada`, `id_sala`) VALUES
	(1, 3, 1, 5, 2, 60, '2026-09-30 02:10:59', 1),
	(2, 3, 2, 5, 2, 75, '2026-09-30 04:44:49', 1),
	(3, 3, 2, 5, 2, 75, '2026-09-30 04:44:51', 1),
	(4, 3, 1, 5, 2, 60, '2026-09-30 05:00:37', 2),
	(5, 3, 1, 5, 2, 60, '2026-09-30 07:06:09', 2),
	(6, 8, 1, 5, 0, 60, '2026-09-30 13:32:35', 3),
	(7, 8, 1, 5, 4, 60, '2026-09-30 13:33:18', 3);

-- Volcando estructura para tabla arcade_sistemas.preguntas
CREATE TABLE IF NOT EXISTS `preguntas` (
  `id_pregunta` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria` int(11) NOT NULL,
  `texto_pregunta` varchar(255) NOT NULL,
  `respuesta_correcta` varchar(100) NOT NULL,
  `opcion_falsa_1` varchar(100) NOT NULL,
  `opcion_falsa_2` varchar(100) NOT NULL,
  PRIMARY KEY (`id_pregunta`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_juego` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla arcade_sistemas.preguntas: ~20 rows (aproximadamente)
INSERT INTO `preguntas` (`id_pregunta`, `id_categoria`, `texto_pregunta`, `respuesta_correcta`, `opcion_falsa_1`, `opcion_falsa_2`) VALUES
	(1, 1, '¿Qué protocolo se utiliza para transferir páginas web?', 'HTTP', 'FTP', 'SMTP'),
	(2, 1, 'Lenguaje estándar para consultar bases de datos relacionales:', 'SQL', 'PHP', 'HTML'),
	(3, 1, '¿Qué significa la sigla RAM?', 'Random Access Memory', 'Read Access Memory', 'Run Active Memory'),
	(4, 2, 'Etiqueta HTML para insertar un hipervínculo:', '<a>', '<link>', '<href>'),
	(5, 2, '¿Qué estructura de datos usa el principio LIFO?', 'Pila (Stack)', 'Cola (Queue)', 'Árbol (Tree)'),
	(6, 4, '¿Qué significa MVC?', 'Modelo Vista Controlador', 'Motor Virtual Central', 'Manejo Visual de Código'),
	(7, 4, '¿Para qué sirve PHP principalmente?', 'Lógica de servidor (Backend)', 'Diseño visual (Frontend)', 'Editar imágenes'),
	(8, 4, '¿Qué etiqueta de HTML usamos para la música?', '<audio>', '<sound>', '<music>'),
	(9, 4, '¿Qué es MySQL?', 'Un Gestor de Base de Datos', 'Un Lenguaje de Diseño', 'Un Sistema Operativo'),
	(10, 4, '¿Para qué sirve FETCH en JavaScript?', 'Hacer peticiones al servidor (API)', 'Crear animaciones 3D', 'Borrar variables de sesión'),
	(11, 5, '¿Qué hace la etiqueta <form>?', 'Crear un formulario', 'Dar formato al texto', 'Insertar un video'),
	(12, 5, '¿Qué significa CSS?', 'Hojas de Estilo en Cascada', 'Código Seguro de Servidor', 'Control de Sistema Simple'),
	(13, 5, '¿Qué usamos para guardar datos temporales en PHP?', '$_SESSION', '$_TEMPORAL', '$_DATA'),
	(14, 5, '¿Qué función genera un string aleatorio en PHP?', 'uniqid()', 'random_string()', 'rand_text()'),
	(15, 5, '¿Cuál es el lenguaje de las bases de datos?', 'SQL', 'HTML', 'Python'),
	(16, 3, '¿Qué significa HTML?', 'HyperText Markup Language', 'Hyper Tool Multi Language', 'High Text Machine Learning'),
	(17, 3, '¿Cuál de estos es un sistema operativo?', 'Linux', 'Python', 'React'),
	(18, 3, '¿Qué hace la etiqueta <a> en HTML?', 'Crear un enlace', 'Añadir audio', 'Alinear texto'),
	(19, 3, '¿Qué tipo de lenguaje es JavaScript?', 'Lenguaje de Programación', 'Lenguaje de Marcado', 'Gestor de Base de Datos'),
	(20, 3, '¿Qué puerto usa normalmente el protocolo web HTTP?', 'Puerto 80', 'Puerto 443', 'Puerto 21');

-- Volcando estructura para tabla arcade_sistemas.salas
CREATE TABLE IF NOT EXISTS `salas` (
  `id_sala` int(11) NOT NULL AUTO_INCREMENT,
  `id_maestro` int(11) NOT NULL,
  `codigo_sala` varchar(10) NOT NULL,
  `estado` enum('espera','jugando','finalizada') DEFAULT 'espera',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `id_categoria` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_sala`),
  UNIQUE KEY `codigo_sala` (`codigo_sala`),
  KEY `id_maestro` (`id_maestro`),
  CONSTRAINT `salas_ibfk_1` FOREIGN KEY (`id_maestro`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla arcade_sistemas.salas: ~4 rows (aproximadamente)
INSERT INTO `salas` (`id_sala`, `id_maestro`, `codigo_sala`, `estado`, `fecha_creacion`, `id_categoria`) VALUES
	(1, 3, 'KBZ4Q5', 'finalizada', '2026-09-29 18:43:09', NULL),
	(2, 3, 'WQBLOK', 'espera', '2026-09-29 22:59:50', NULL),
	(3, 8, '0D266', 'finalizada', '2026-09-30 07:31:41', 2),
	(4, 8, '82ABF', 'espera', '2026-09-30 07:32:53', NULL),
	(5, 9, 'B9FC0', 'espera', '2026-10-06 17:40:11', NULL);

-- Volcando estructura para tabla arcade_sistemas.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('alumno','maestro') DEFAULT 'alumno',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla arcade_sistemas.usuarios: ~7 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password_hash`, `fecha_registro`, `rol`) VALUES
	(1, 'Jugador Prueba', 'prueba@itvillahermosa.edu', '$2y$10$wzV.D2/D/8.sA5yF8o4g.O3n3o.k7W.Tj9/z/Kj2Z/3.s/2.K/9/', '2026-09-30 00:41:06', 'alumno'),
	(2, 'Ana', 'prueba3@itvillahermosa.edu', '$2y$10$p3gDy.TDMQnJMJnjc1ksce4kDjVkl6XCmWOeUxSgLeh8gHE1Itmk.', '2026-09-30 00:42:18', 'alumno'),
	(3, 'Ana1', 'prueba4@itvillahermosa.edu', '$2y$10$NHRopD1FzMbY8nVT3xAi5.eDs.CB1bku0inpWid.mHc6BYwB1oTWy', '2026-09-30 00:42:52', 'maestro'),
	(4, 'Ana3', 'prueba6@itvillahermosa.edu', '$2y$10$3cw7g9VlmHgjyX85eSWyvu1NIcmSv4iccwBIcE5/2Z9o4j6d1NkwK', '2026-09-30 13:25:38', 'alumno'),
	(5, 'Ana7', 'prueba7@itvillahermosa.edu', '$2y$10$ZdpHb6gsvscL16TUsjf6dOKiRTxcpB2o.VV7CFIBXygzh.RZF54A.', '2026-09-30 13:26:35', 'alumno'),
	(6, 'Ana8', 'prueba8@itvillahermosa.edu', '$2y$10$aXS/65enG04240fNkdXWIe/EUJRV0fEJqJKOll4DvUNKmvUAEKrui', '2026-09-30 13:29:38', 'alumno'),
	(7, 'Ana9', 'prueba9@itvillahermosa.edu', '$2y$10$5cPoFwm8xWX6PMdMobkI0.vUHcVlM2HZoyEsepXL/tRQahfgndgVa', '2026-09-30 13:30:30', 'alumno'),
	(8, 'Ana10', 'prueba10@villahermosa.edu', '$2y$10$uTxsbD0SCmkR7TuVhKZzqOn9fOsazQTfLOGc8iSYlrY3Bz74ygGmO', '2026-09-30 13:31:24', 'maestro'),
	(9, 'Mtra.Ana', 'prueba16@itvillahermosa.edu', '$2y$10$LJCoGCxrcUx3gAAsBV5LvOpiaPHL7ZALKbI.zq753Re5Nmh2YM/Xy', '2026-10-06 23:39:36', 'maestro');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
