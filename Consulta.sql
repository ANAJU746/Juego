create database juego;
use juego;

create table usuarios (
	id_usuarios int not null auto_increment primary key,
    nombre varchar (100) not null,
    rol	enum ('alumno', 'maestro'),
    nivel	varchar(50) null -- se puede guardar campos como Junior, Senior, etc...
)engine=InnoDB;

create table clases (
	id_clases int not null auto_increment primary key,
    id_maestro	int not null,
    codigo_acceso varchar (20) not null unique,
    estado 	tinyint default 1,
    foreign key (id_maestro) references usuarios(id_usuarios)
)engine=InnoDB;

create table juegos(
	id_juego int not null auto_increment primary key,
    nombre_juego	varchar (100) not null, 
    descrpcion text null
)engine=InnoDB;

create table preguntas (
	id_preguntas	int not null auto_increment primary key,
    id_juego int not null,
    texto_pregunta	text not null,
    repuestas_correctas varchar(255) not null,
    falsa_1 varchar(255) not null,
    falsa_2 varchar(255) not null,
    foreign key (id_juego) references juegos(id_juego)
)engine=InnoDB;

create table partidas(
	id_partida int not null auto_increment primary key,
    id_alumno int not null,
    id_juego int not null,
    errores_cometidos int (3) default 0,
    aciertos int (3) default 0,
    fecha_partida timestamp default current_timestamp,
    foreign key (id_juego) references juegos(id_juego),
    foreign key (id_alumno) references usuarios(id_usuarios)
)engine=INNODB;



INSERT INTO categorias_juego (id_categoria, nombre_juego) VALUES (4, 'Atrapar Globos') ON DUPLICATE KEY UPDATE nombre_juego=nombre_juego;




INSERT INTO preguntas (id_categoria, texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2) VALUES 
(4, '¿Qué significa MVC?', 'Modelo Vista Controlador', 'Motor Virtual Central', 'Manejo Visual de Código'),
(4, '¿Para qué sirve PHP principalmente?', 'Lógica de servidor (Backend)', 'Diseño visual (Frontend)', 'Editar imágenes'),
(4, '¿Qué etiqueta de HTML usamos para la música?', '<audio>', '<sound>', '<music>'),
(4, '¿Qué es MySQL?', 'Un Gestor de Base de Datos', 'Un Lenguaje de Diseño', 'Un Sistema Operativo'),
(4, '¿Para qué sirve FETCH en JavaScript?', 'Hacer peticiones al servidor (API)', 'Crear animaciones 3D', 'Borrar variables de sesión');




INSERT INTO categorias_juego (id_categoria, nombre_juego) VALUES (5, 'Torre de Conceptos') ON DUPLICATE KEY UPDATE nombre_juego=nombre_juego;

INSERT INTO preguntas (id_categoria, texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2) VALUES 
(5, '¿Qué hace la etiqueta <form>?', 'Crear un formulario', 'Dar formato al texto', 'Insertar un video'),
(5, '¿Qué significa CSS?', 'Hojas de Estilo en Cascada', 'Código Seguro de Servidor', 'Control de Sistema Simple'),
(5, '¿Qué usamos para guardar datos temporales en PHP?', '$_SESSION', '$_TEMPORAL', '$_DATA'),
(5, '¿Qué función genera un string aleatorio en PHP?', 'uniqid()', 'random_string()', 'rand_text()'),
(5, '¿Cuál es el lenguaje de las bases de datos?', 'SQL', 'HTML', 'Python');



INSERT INTO categorias_juego (id_categoria, nombre_juego) VALUES (3, 'Tiro al Blanco') ON DUPLICATE KEY UPDATE nombre_juego=nombre_juego;

INSERT INTO preguntas (id_categoria, texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2) VALUES 
(3, '¿Qué significa HTML?', 'HyperText Markup Language', 'Hyper Tool Multi Language', 'High Text Machine Learning'),
(3, '¿Cuál de estos es un sistema operativo?', 'Linux', 'Python', 'React'),
(3, '¿Qué hace la etiqueta <a> en HTML?', 'Crear un enlace', 'Añadir audio', 'Alinear texto'),
(3, '¿Qué tipo de lenguaje es JavaScript?', 'Lenguaje de Programación', 'Lenguaje de Marcado', 'Gestor de Base de Datos'),
(3, '¿Qué puerto usa normalmente el protocolo web HTTP?', 'Puerto 80', 'Puerto 443', 'Puerto 21');
