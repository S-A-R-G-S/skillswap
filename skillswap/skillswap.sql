-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2026 a las 02:01:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `skillswap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id` int(11) NOT NULL,
  `intercambio_id` int(11) NOT NULL,
  `evaluador_id` int(11) NOT NULL,
  `evaluado_id` int(11) NOT NULL,
  `puntuacion` tinyint(4) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `intercambio_id`, `evaluador_id`, `evaluado_id`, `puntuacion`, `comentario`, `fecha`) VALUES
(1, 1, 4, 2, 5, 'Excelente explicación y mucha paciencia para enseñar.', '2026-11-16 00:12:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habilidades`
--

CREATE TABLE `habilidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habilidades`
--

INSERT INTO `habilidades` (`id`, `nombre`) VALUES
(6, 'Base de datos'),
(3, 'Diseño gráfico'),
(11, 'Diseño web'),
(4, 'Edición de video'),
(12, 'Fotografía'),
(2, 'Inglés'),
(7, 'Java'),
(9, 'Marketing digital'),
(5, 'Microsoft Office'),
(14, 'Photoshop'),
(1, 'Programación'),
(8, 'React'),
(13, 'Redacción'),
(10, 'UX/UI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intercambios`
--

CREATE TABLE `intercambios` (
  `id` int(11) NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `habilidad_ofrecida` varchar(80) NOT NULL,
  `habilidad_solicitada` varchar(80) NOT NULL,
  `estado` enum('pendiente','en_curso','completado','rechazado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `intercambios`
--

INSERT INTO `intercambios` (`id`, `emisor_id`, `receptor_id`, `habilidad_ofrecida`, `habilidad_solicitada`, `estado`, `fecha_creacion`) VALUES
(1, 4, 2, 'Inglés', 'Programación', 'en_curso', '2026-11-16 00:12:20'),
(2, 5, 2, 'Edición de video', 'Base de datos', 'pendiente', '2026-11-16 00:12:20'),
(3, 1, 4, 'Mi habilidad', 'Inglés', 'pendiente', '2026-11-16 00:35:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `intercambio_id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `intercambio_id`, `remitente_id`, `contenido`, `leido`, `fecha_envio`) VALUES
(1, 1, 4, '¡Hola María! Vi tu perfil y me gustaría hacer un intercambio de inglés por programación. ¿Te interesa?', 0, '2026-11-16 00:12:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('coincidencia','solicitud','calificacion','recordatorio') NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `descripcion`, `leida`, `fecha`) VALUES
(1, 4, 'solicitud', 'Nueva solicitud', 'José Diaz te envió una solicitud de intercambio.', 0, '2026-11-16 00:35:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `carrera` varchar(100) DEFAULT 'Ingeniería en Sistemas',
  `anio_academico` varchar(30) DEFAULT '1er año',
  `ubicacion` varchar(80) DEFAULT 'Panamá',
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `en_linea` tinyint(1) DEFAULT 0,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `correo`, `password_hash`, `carrera`, `anio_academico`, `ubicacion`, `bio`, `avatar`, `en_linea`, `fecha_registro`) VALUES
(1, 'José Diaz', 'josedro567@gmail.com', '$2y$10$e9gokT.AlX1KES5nKXhyUO/JaQutqdoxZgsHNQ5FHytuLbV0D3cJK', 'Ingeniería en Sistemas', '3 er año', 'Panamá', '', 'default.png', 1, '2026-11-14 21:44:45'),
(2, 'María Torres', 'maria.torres@up.ac.pa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ingeniería en Sistemas', '3er año', 'Panamá', 'Me encanta aprender cosas nuevas y compartir lo que sé. Busco personas con quienes intercambiar conocimientos de programación e idiomas.', 'default.png', 1, '2026-11-16 00:12:20'),
(3, 'Daniel López', 'daniel.lopez@up.ac.pa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ingeniería en Sistemas', '3er año', 'Panamá', 'Desarrollador backend y entusiasta de Java y React. Me interesa mejorar mi fluidez en inglés conversacional.', 'default.png', 1, '2026-11-16 00:12:20'),
(4, 'Ana Rodríguez', 'ana.rodriguez@up.ac.pa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Comunicación Social', '2do año', 'Panamá', 'Apasionada por la redacción creativa, el marketing y los idiomas. Quiero aprender bases de programación y desarrollo web.', 'default.png', 1, '2026-11-16 00:12:20'),
(5, 'Luis Martínez', 'luis.martinez@up.ac.pa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administración de Empresas', '3er año', 'Panamá', 'Creo contenido audiovisual y manejo suite de diseño. Busco aprender gestión de bases de datos y Excel avanzado.', 'default.png', 0, '2026-11-16 00:12:20'),
(6, 'Valentina Cruz', 'valentina.cruz@up.ac.pa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diseño Gráfico', '4to año', 'Panamá', 'Diseñadora enfocada en experiencias digitales (UX/UI) y fotografía. Me gustaría practicar inglés técnico.', 'default.png', 1, '2026-11-16 00:12:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_habilidades`
--

CREATE TABLE `usuario_habilidades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `habilidad_id` int(11) NOT NULL,
  `tipo` enum('ofrezco','aprender') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_habilidades`
--

INSERT INTO `usuario_habilidades` (`id`, `usuario_id`, `habilidad_id`, `tipo`) VALUES
(4, 2, 1, 'ofrezco'),
(10, 2, 2, 'aprender'),
(8, 2, 4, 'aprender'),
(1, 2, 6, 'ofrezco'),
(3, 2, 7, 'ofrezco'),
(5, 2, 8, 'ofrezco'),
(11, 2, 9, 'aprender'),
(12, 2, 10, 'aprender'),
(2, 2, 11, 'ofrezco'),
(9, 2, 12, 'aprender'),
(16, 3, 1, 'ofrezco'),
(18, 3, 2, 'aprender'),
(15, 3, 7, 'ofrezco'),
(17, 3, 8, 'ofrezco'),
(19, 3, 10, 'aprender'),
(25, 4, 1, 'aprender'),
(21, 4, 2, 'ofrezco'),
(22, 4, 9, 'ofrezco'),
(24, 4, 11, 'aprender'),
(23, 4, 13, 'ofrezco'),
(27, 5, 3, 'ofrezco'),
(28, 5, 4, 'ofrezco'),
(31, 5, 5, 'aprender'),
(30, 5, 6, 'aprender'),
(29, 5, 14, 'ofrezco'),
(36, 6, 2, 'aprender'),
(33, 6, 3, 'ofrezco'),
(37, 6, 8, 'aprender'),
(35, 6, 10, 'ofrezco'),
(34, 6, 12, 'ofrezco');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intercambio_id` (`intercambio_id`),
  ADD KEY `evaluador_id` (`evaluador_id`),
  ADD KEY `evaluado_id` (`evaluado_id`);

--
-- Indices de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `intercambios`
--
ALTER TABLE `intercambios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emisor_id` (`emisor_id`),
  ADD KEY `receptor_id` (`receptor_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intercambio_id` (`intercambio_id`),
  ADD KEY `remitente_id` (`remitente_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `usuario_habilidades`
--
ALTER TABLE `usuario_habilidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_skill` (`usuario_id`,`habilidad_id`,`tipo`),
  ADD KEY `habilidad_id` (`habilidad_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `intercambios`
--
ALTER TABLE `intercambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario_habilidades`
--
ALTER TABLE `usuario_habilidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`intercambio_id`) REFERENCES `intercambios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`evaluador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_ibfk_3` FOREIGN KEY (`evaluado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `intercambios`
--
ALTER TABLE `intercambios`
  ADD CONSTRAINT `intercambios_ibfk_1` FOREIGN KEY (`emisor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intercambios_ibfk_2` FOREIGN KEY (`receptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`intercambio_id`) REFERENCES `intercambios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_habilidades`
--
ALTER TABLE `usuario_habilidades`
  ADD CONSTRAINT `usuario_habilidades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_habilidades_ibfk_2` FOREIGN KEY (`habilidad_id`) REFERENCES `habilidades` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
