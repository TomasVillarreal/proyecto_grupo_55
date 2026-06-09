-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2026 a las 18:10:29
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
-- Base de datos: `clinicks_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_medicamento` (IN `p_nombre` VARCHAR(255))   BEGIN

    IF EXISTS (
        SELECT 1
        FROM medicamento
        WHERE nombre_medicamento = p_nombre
    ) THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento ya existe';

    ELSE

        INSERT INTO medicamento(
            nombre_medicamento,
            activo_medicamento
        )
        VALUES(
            p_nombre,
            1
        );

        SELECT LAST_INSERT_ID() AS id_medicamento;

    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_desactivar_medicamento` (IN `p_id` INT)   BEGIN

    UPDATE medicamento
    SET activo_medicamento = 0
    WHERE id_medicamento = p_id;

    SELECT ROW_COUNT() AS filas_afectadas;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_existe_usuario` (IN `p_email` VARCHAR(255), IN `p_dni` INT)   BEGIN
    SELECT id_usuario 
    FROM Usuario
    WHERE email_usuario = p_email COLLATE utf8mb4_spanish_ci OR dni_usuario = p_dni
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_modificar_medicamento` (IN `p_id` INT, IN `p_nombre` VARCHAR(255))   BEGIN
    UPDATE medicamento
    SET nombre_medicamento = p_nombre
    WHERE id_medicamento = p_id;

    SELECT ROW_COUNT() AS filas_afectadas;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle_pedido` int(11) NOT NULL,
  `cantidad_medicamento` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_detalle_pedido`, `cantidad_medicamento`, `id_pedido`, `id_producto`, `id_proveedor`) VALUES
(7, 1, 6, 8, 1),
(8, 3, 7, 21, 3),
(9, 1, 8, 8, 1),
(10, 2, 8, 11, 1),
(11, 3, 8, 13, 1),
(12, 4, 8, 21, 1),
(13, 5, 8, 9, 1),
(14, 5, 8, 3, 1),
(15, 6, 8, 29, 1),
(16, 7, 8, 32, 1),
(17, 8, 8, 2, 1),
(18, 50, 9, 1, 4),
(19, 30, 9, 32, 5),
(20, 80, 9, 9, 2),
(21, 5, 10, 28, 5),
(22, 6, 11, 2, 3),
(23, 10, 12, 3, 4),
(24, 16, 13, 2, 3),
(25, 1, 14, 28, 2),
(26, 1, 15, 13, 1),
(27, 2, 15, 8, 4),
(28, 3, 15, 11, 2),
(29, 4, 15, 35, 3),
(30, 5, 15, 21, 5),
(31, 6, 15, 9, 1),
(32, 7, 15, 3, 4),
(33, 8, 15, 15, 3),
(34, 9, 15, 32, 5),
(35, 10, 15, 28, 4),
(36, 5, 20, 37, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedido`
--

CREATE TABLE `estado_pedido` (
  `id_estado_pedido` int(11) NOT NULL,
  `tipo_estado_pedido` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_pedido`
--

INSERT INTO `estado_pedido` (`id_estado_pedido`, `tipo_estado_pedido`) VALUES
(2, 'Aprobado'),
(1, 'Pendiente'),
(3, 'Rechazado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamento`
--

CREATE TABLE `medicamento` (
  `id_medicamento` int(11) NOT NULL,
  `nombre_medicamento` varchar(100) NOT NULL,
  `activo_medicamento` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamento`
--

INSERT INTO `medicamento` (`id_medicamento`, `nombre_medicamento`, `activo_medicamento`) VALUES
(1, 'Ibuprofeno', 1),
(2, 'Paracetamol', 1),
(3, 'Diclofenac', 1),
(4, 'Omeprazol', 1),
(5, 'Suero Fisiologico', 1),
(6, 'Amoxicilina', 1),
(7, 'Vitamina B12', 0),
(8, '123iburpfone', 0),
(9, 'Aminopiridina                  ', 1),
(10, 'Redoxon', 0),
(11, 'Loratadina', 1),
(12, 'Losartan', 1),
(16, 'Cortipirena', 1),
(17, 'Asdasdad', 0),
(18, 'Clonazempam', 1),
(19, 'Propofol', 1),
(20, 'Fentanilo', 1),
(21, 'Dexalergin', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medida_producto`
--

CREATE TABLE `medida_producto` (
  `id_medida_producto` int(11) NOT NULL,
  `nombre_medida` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medida_producto`
--

INSERT INTO `medida_producto` (`id_medida_producto`, `nombre_medida`) VALUES
(3, 'g'),
(5, 'L'),
(1, 'mcg'),
(2, 'mg'),
(4, 'ml'),
(7, 'Puff'),
(6, 'UI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `fecha_solicitud_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `comentario_pedido` varchar(100) DEFAULT NULL,
  `motivo_cancelacion_pedido` varchar(100) DEFAULT NULL,
  `id_estado_pedido` int(11) NOT NULL DEFAULT 1,
  `id_servicio_medico` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `fecha_solicitud_pedido`, `comentario_pedido`, `motivo_cancelacion_pedido`, `id_estado_pedido`, `id_servicio_medico`, `id_usuario`) VALUES
(6, '2026-06-05 00:00:00', '', NULL, 2, 1, 1),
(7, '2026-06-05 00:00:00', '', 'No corresponde a este servicio.', 3, 8, 1),
(8, '2026-06-05 00:00:00', 'Para el servicio de Cardiología!', NULL, 2, 5, 1),
(9, '2026-06-05 00:00:00', '', '-', 3, 4, 1),
(10, '2026-06-05 00:00:00', 'Es URGENTE', NULL, 1, 2, 8),
(11, '2026-06-05 00:00:00', '', NULL, 1, 6, 10),
(12, '2026-06-05 00:00:00', '', NULL, 1, 1, 1),
(13, '2026-06-05 00:00:00', '', NULL, 2, 6, 10),
(14, '2026-06-05 00:00:00', '', NULL, 2, 5, 1),
(15, '2026-06-07 00:00:00', '', NULL, 1, 1, 1),
(20, '2026-06-08 00:00:00', '', NULL, 1, 2, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_farmaceutico`
--

CREATE TABLE `producto_farmaceutico` (
  `id_producto` int(11) NOT NULL,
  `descripcion_producto` varchar(100) DEFAULT NULL,
  `dosis_producto` decimal(10,2) NOT NULL,
  `activo_producto` tinyint(1) NOT NULL DEFAULT 1,
  `id_medicamento` int(11) NOT NULL,
  `id_tipo_producto` int(11) NOT NULL,
  `id_medida_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_farmaceutico`
--

INSERT INTO `producto_farmaceutico` (`id_producto`, `descripcion_producto`, `dosis_producto`, `activo_producto`, `id_medicamento`, `id_tipo_producto`, `id_medida_producto`) VALUES
(1, 'Para leves dolores de cabeza', 200.00, 1, 1, 1, 2),
(2, NULL, 400.00, 1, 2, 2, 2),
(3, NULL, 50.00, 1, 1, 3, 4),
(4, NULL, 50.00, 0, 2, 3, 4),
(5, NULL, 50.00, 0, 1, 3, 2),
(8, NULL, 50.00, 1, 6, 2, 1),
(9, NULL, 50.00, 1, 3, 2, 2),
(10, NULL, 30.00, 0, 7, 1, 2),
(11, NULL, 50.00, 1, 6, 4, 3),
(12, NULL, 50.00, 0, 8, 4, 5),
(13, NULL, 50.00, 1, 9, 4, 4),
(14, NULL, 30.00, 0, 10, 1, 2),
(15, 'Para alergias', 200.00, 1, 11, 1, 2),
(16, 'Para la hipertension', 100.00, 0, 12, 1, 2),
(21, NULL, 50.00, 1, 16, 1, 2),
(22, NULL, 100.00, 0, 1, 4, 6),
(24, NULL, 100.00, 0, 5, 3, 6),
(25, NULL, 50.00, 0, 5, 4, 3),
(26, NULL, 50.00, 0, 5, 5, 1),
(28, NULL, 30.00, 1, 5, 3, 6),
(29, NULL, 51.00, 1, 1, 4, 3),
(32, NULL, 50.00, 1, 4, 1, 2),
(33, NULL, 50.00, 1, 12, 1, 3),
(34, NULL, 50.00, 0, 17, 1, 3),
(35, NULL, 50.00, 1, 18, 1, 2),
(36, NULL, 30.00, 0, 19, 4, 2),
(37, NULL, 50.00, 1, 20, 4, 4),
(38, 'Para los resfrios', 50.00, 0, 21, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_proveedor`) VALUES
(1, 'Droguería del Sud'),
(4, 'Droguería Itatí'),
(2, 'Droguería Monroe Americana'),
(3, 'Droguería Suizo Argentina'),
(5, 'PLAMECOR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(100) NOT NULL
) ;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
(2, 'No responsable'),
(1, 'Responsable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_medico`
--

CREATE TABLE `servicio_medico` (
  `id_servicio_medico` int(11) NOT NULL,
  `nombre_servicio_medico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio_medico`
--

INSERT INTO `servicio_medico` (`id_servicio_medico`, `nombre_servicio_medico`) VALUES
(1, 'Anestesiología'),
(5, 'Cardiología'),
(7, 'Clínica'),
(3, 'Emergencia'),
(8, 'Ginecoobstetricia'),
(6, 'Neurología'),
(4, 'UCI'),
(2, 'Urgencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_producto`
--

CREATE TABLE `tipo_producto` (
  `id_tipo_producto` int(11) NOT NULL,
  `nombre_tipo_producto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_producto`
--

INSERT INTO `tipo_producto` (`id_tipo_producto`, `nombre_tipo_producto`) VALUES
(4, 'Ampolla'),
(2, 'Capsula'),
(1, 'Comprimido'),
(5, 'Gota'),
(3, 'Jarabe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `dni_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `apellido_usuario` varchar(100) NOT NULL,
  `email_usuario` varchar(100) NOT NULL,
  `password_usuario` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo_usuario` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `dni_usuario`, `nombre_usuario`, `apellido_usuario`, `email_usuario`, `password_usuario`, `id_rol`, `activo_usuario`) VALUES
(1, 46316672, 'Juan Cruz', 'San Lorenzo', 'sanlorenzojuancruz@gmail.com', '$2y$10$JypKcC1PG8p9Xzjf3InzOeNt3Zgb12M050t2vOoLviR9Mntelfova', 1, 1),
(2, 17808611, 'Alejandro', 'Gomez', 'alegomez@hotmail.com', '$2y$10$Pg0LDtTuhQ1GjelNI2u.tOBTObUkwnPJbdBnLsa2ehm5V0CW4I33W', 2, 1),
(3, 12858308, 'Fernando', 'Perez', 'ferperez@hotmail.com', '$2y$10$yqjGgtojbFBSt2KS5K3goevGyMw9iAvfgL0mTyo3q5yc.rhg6iSmK', 1, 1),
(4, 12345678, 'Alfaro', 'Gutierrez', 'alfaro@gmail.com', '$2y$10$LSu/bc1Ki18RRNrXtBSw2ePj8F1PgNpSf968NuVZrgksh4GCRMth6', 2, 1),
(5, 45367890, 'Romina', 'Perez', 'romina@hotmail.com', '$2y$10$Xb0qU3K.XrM26l0IQgOkMurmvT/iVraXp0gxi43IHifD/zto0q6NS', 2, 1),
(6, 46316677, 'Matias', 'Gomez', 'matigomez@gmail.com', '$2y$10$OjZT/43hMQKnbXZ.xH/jfeIL5XWSw10jQP7PQVGob/sHQ8BQLemyC', 1, 1),
(7, 45312780, 'Fernanda', 'Fernandez', 'ferfer@gmail.com', '$2y$10$f3URxxK0vAX9FQDUYFFoTetqc.dTMTRGx7RIwjmXXReNfsPG5hGYy', 2, 1),
(8, 40329751, 'Gimena', 'Gimenez', 'gimegime@gmail.com', '$2y$10$ag4k2.aclPZpbKcDs6oxu.hn6P6JBGUKudbn8vGZaqiZgxXd2BbS2', 2, 1),
(9, 46147515, 'Tomas', 'Villarreal', 'tomiv@gmail.com', '$2y$10$Z5sgpeJ0o2J2pIuHVlii2.a4ju8Gzq0kaYKjqPMOQkbHugTwuIxau', 2, 1),
(10, 23456789, 'Jose', 'Perez', 'joseperez@gmail.com', '$2y$10$BRZJyvhB3QAslia2m7/5..vY/QgYiulSeSx1kVEsSAeNEMImn28kC', 1, 1),
(11, 45644949, 'Fernando', 'Alcaraz', 'feralcaraz@gmail.com', '$2y$10$G2Y.uf.AzrSVDaEv2zrT1ePcHDkpcdY.ctxolgQW/Fh87xOMm8U3K', 2, 1),
(12, 44713980, 'Fabian', 'Quintana', 'fabian@gmail.com', '$2y$10$AYXdW3w1Get3cX7BSIPnrOtCVMRLjr8hNdzlJ3YRRgcqB/phDS5/q', 2, 1),
(13, 43064294, 'Benjamin', 'Zimerman', 'benja@gmail.com', '$2y$10$b9gjjqvNiQqfwKJwnP3Y3OgcdDk0FRy/Zi70qcL/rDcMH.m//INfy', 2, 1),
(14, 46380532, 'Martina', 'Martinez', 'marti@gmail.com', '$2y$10$Er7Bh5c2eT21Vqh54Q4A/uaS5uJgPdJjADORIUgAFr/MplgFZKtMK', 2, 1),
(15, 1908456, 'Agustina', 'Solis', 'agus@gmail.com', '$2y$10$Xl9AriN8Y9LyAHSCtF.ig.UiDOjE//Hn.GavUfCIWgX7ZIXs3p4jK', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle_pedido`),
  ADD KEY `FK_id_pedido_detalle_pedido` (`id_pedido`),
  ADD KEY `FK_id_producto_detalle_pedido` (`id_producto`),
  ADD KEY `FK_id_proveedor_detalle_pedido` (`id_proveedor`);

--
-- Indices de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  ADD PRIMARY KEY (`id_estado_pedido`),
  ADD UNIQUE KEY `UQ_tipo_estado_pedido_estado_pedido` (`tipo_estado_pedido`);

--
-- Indices de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  ADD PRIMARY KEY (`id_medicamento`),
  ADD UNIQUE KEY `UQ_nombre_medicamento_medicamento` (`nombre_medicamento`);

--
-- Indices de la tabla `medida_producto`
--
ALTER TABLE `medida_producto`
  ADD PRIMARY KEY (`id_medida_producto`),
  ADD UNIQUE KEY `UQ_nombre_medida_medida_producto` (`nombre_medida`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `FK_id_estado_pedido_pedido` (`id_estado_pedido`),
  ADD KEY `FK_id_servicio_medico_pedido` (`id_servicio_medico`),
  ADD KEY `FK_id_usuario_pedido` (`id_usuario`);

--
-- Indices de la tabla `producto_farmaceutico`
--
ALTER TABLE `producto_farmaceutico`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `UQ_producto_producto` (`id_medicamento`,`dosis_producto`,`id_medida_producto`,`id_tipo_producto`),
  ADD KEY `FK_id_tipo_producto_producto` (`id_tipo_producto`),
  ADD KEY `FK_id_medida_producto_producto` (`id_medida_producto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `UQ_nombre_proveedor_proveedor` (`nombre_proveedor`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `UQ_nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `servicio_medico`
--
ALTER TABLE `servicio_medico`
  ADD PRIMARY KEY (`id_servicio_medico`),
  ADD UNIQUE KEY `UQ_nombre_servicio_medico` (`nombre_servicio_medico`);

--
-- Indices de la tabla `tipo_producto`
--
ALTER TABLE `tipo_producto`
  ADD PRIMARY KEY (`id_tipo_producto`),
  ADD UNIQUE KEY `UQ_nombre_tipo_producto_tipo_producto` (`nombre_tipo_producto`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `UQ_dni_usuario` (`dni_usuario`),
  ADD UNIQUE KEY `UQ_email_usuario` (`email_usuario`),
  ADD KEY `FK_id_rol_usuario` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  MODIFY `id_estado_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `medida_producto`
--
ALTER TABLE `medida_producto`
  MODIFY `id_medida_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `producto_farmaceutico`
--
ALTER TABLE `producto_farmaceutico`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio_medico`
--
ALTER TABLE `servicio_medico`
  MODIFY `id_servicio_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_producto`
--
ALTER TABLE `tipo_producto`
  MODIFY `id_tipo_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `FK_id_pedido_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`),
  ADD CONSTRAINT `FK_id_producto_detalle_pedido` FOREIGN KEY (`id_producto`) REFERENCES `producto_farmaceutico` (`id_producto`),
  ADD CONSTRAINT `FK_id_proveedor_detalle_pedido` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `FK_id_estado_pedido_pedido` FOREIGN KEY (`id_estado_pedido`) REFERENCES `estado_pedido` (`id_estado_pedido`),
  ADD CONSTRAINT `FK_id_servicio_medico_pedido` FOREIGN KEY (`id_servicio_medico`) REFERENCES `servicio_medico` (`id_servicio_medico`),
  ADD CONSTRAINT `FK_id_usuario_pedido` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `producto_farmaceutico`
--
ALTER TABLE `producto_farmaceutico`
  ADD CONSTRAINT `FK_id_medicamento_producto` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento` (`id_medicamento`),
  ADD CONSTRAINT `FK_id_medida_producto_producto` FOREIGN KEY (`id_medida_producto`) REFERENCES `medida_producto` (`id_medida_producto`),
  ADD CONSTRAINT `FK_id_tipo_producto_producto` FOREIGN KEY (`id_tipo_producto`) REFERENCES `tipo_producto` (`id_tipo_producto`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_id_rol_usuario` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
