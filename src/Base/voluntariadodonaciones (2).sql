

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE voluntariadodonaciones;
USE voluntariadodonaciones;

--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddProyecto` (IN `p_titulo` VARCHAR(255), IN `p_descripcion` TEXT, IN `p_objetivo` VARCHAR(255), IN `p_presupuesto` FLOAT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'), IN `p_idUsuario` INT)   BEGIN
    INSERT INTO Proyecto (titulo, descripcion, objetivo, presupuesto, estado, idUsuario) 
    VALUES (p_titulo, p_descripcion, p_objetivo, p_presupuesto, p_estado, p_idUsuario);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddUsuario` (IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_rol` ENUM('Donante','Voluntario','Organizador'))   BEGIN
    INSERT INTO Usuario (email, contraseña, Rol) 
    VALUES (p_email, p_contraseña, p_rol);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProyecto` (IN `p_idProyecto` INT)   BEGIN
    DELETE FROM Proyecto WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUsuario` (IN `p_idUsuario` INT)   BEGIN
    DELETE FROM Usuario WHERE idUsuario = p_idUsuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllProyectos` ()   BEGIN
    SELECT * FROM Proyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUsuarios` ()   BEGIN
    SELECT * FROM Usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_usuario` (IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_nombre` VARCHAR(255), IN `p_apellido` VARCHAR(255), IN `p_dni` VARCHAR(255), IN `p_edad` INT, IN `p_telefono` VARCHAR(20), OUT `p_id_usuario` INT)   BEGIN
    -- Insertar en la tabla usuario
    INSERT INTO usuario (email, contraseña) 
    VALUES (p_email, p_contraseña);

    -- Obtener el ID del usuario insertado
    SET p_id_usuario = LAST_INSERT_ID();

    -- Insertar en la tabla persona
    INSERT INTO persona (nombre, apellido, dni, edad, telefono, id_usuario)
    VALUES (p_nombre, p_apellido, p_dni, p_edad, p_telefono, p_id_usuario);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProyecto` (IN `p_idProyecto` INT, IN `p_titulo` VARCHAR(255), IN `p_descripcion` TEXT, IN `p_objetivo` VARCHAR(255), IN `p_presupuesto` FLOAT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'))   BEGIN
    UPDATE Proyecto 
    SET titulo = p_titulo, descripcion = p_descripcion, objetivo = p_objetivo, presupuesto = p_presupuesto, estado = p_estado
    WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUsuario` (IN `p_idUsuario` INT, IN `p_nombre` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_tipoUsuario` ENUM('Donante','Voluntario','Organizador'))   BEGIN
    UPDATE Usuario 
    SET nombre = p_nombre, email = p_email, contraseña = p_contraseña, tipoUsuario = p_tipoUsuario
    WHERE idUsuario = p_idUsuario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actualizacion`
--

CREATE TABLE `actualizacion` (
  `idActualizacion` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL,
  `idProyecto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donacion`
--

CREATE TABLE `donacion` (
  `idDonacion` int(11) NOT NULL,
  `monto` float NOT NULL,
  `fecha` date NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `idProyecto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `donacion`
--

INSERT INTO `donacion` (`idDonacion`, `monto`, `fecha`, `id_usuario`, `idProyecto`) VALUES
(3, 100, '2024-11-16', 1, 2),
(4, 100, '2024-11-16', 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `idPago` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto` float NOT NULL,
  `estado` enum('Procesado','Pendiente') NOT NULL,
  `idDonacion` int(11) DEFAULT NULL,
  `idUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `IdPersona` int(11) NOT NULL,
  `DNI` varchar(50) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Edad` varchar(50) NOT NULL,
  `Telefono` varchar(50) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`IdPersona`, `DNI`, `Nombre`, `Apellido`, `Edad`, `Telefono`, `id_usuario`) VALUES
(5, '23232312', 'Gerardo', 'Lopez', '32', '42232423432', 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `idProyecto` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `objetivo` varchar(255) NOT NULL,
  `presupuesto` float NOT NULL,
  `estado` enum('En Proceso','Completado','Cancelado') NOT NULL,
  `idUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa`
--

CREATE TABLE `recompensa` (
  `idRecompensa` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `montoMinimo` float NOT NULL,
  `fechaEntregaEstimada` date DEFAULT NULL,
  `idDonacion` int(11) DEFAULT NULL,
  `idProyecto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `riesgo`
--

CREATE TABLE `riesgo` (
  `idRiesgo` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `planMitigacion` text NOT NULL,
  `idProyecto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `Rol` enum('Donante','Voluntario','Organizador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `email`, `contraseña`, `Rol`) VALUES
(31, 'ger@gmail.com', 'ewrwerew', 'Donante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voluntario`
--

CREATE TABLE `voluntario` (
  `idVoluntario` int(11) NOT NULL,
  `habilidades` text NOT NULL,
  `disponibilidad` varchar(255) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actualizacion`
--
ALTER TABLE `actualizacion`
  ADD PRIMARY KEY (`idActualizacion`),
  ADD KEY `actualizacion_ibfk_1` (`idProyecto`);

--
-- Indices de la tabla `donacion`
--
ALTER TABLE `donacion`
  ADD PRIMARY KEY (`idDonacion`),
  ADD KEY `donacion_ibfk_1` (`id_usuario`),
  ADD KEY `donacion_ibfk_2` (`idProyecto`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`idPago`),
  ADD KEY `pago_ibfk_1` (`idDonacion`),
  ADD KEY `pago_ibfk_2` (`idUsuario`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`IdPersona`),
  ADD KEY `persona` (`id_usuario`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`idProyecto`),
  ADD KEY `proyecto_ibfk_1` (`idUsuario`);

--
-- Indices de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD PRIMARY KEY (`idRecompensa`),
  ADD KEY `recompensa_ibfk_1` (`idDonacion`),
  ADD KEY `recompensa_ibfk_2` (`idProyecto`);

--
-- Indices de la tabla `riesgo`
--
ALTER TABLE `riesgo`
  ADD PRIMARY KEY (`idRiesgo`),
  ADD KEY `riesgo_ibfk_1` (`idProyecto`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `voluntario`
--
ALTER TABLE `voluntario`
  ADD PRIMARY KEY (`idVoluntario`),
  ADD KEY `voluntario_ibfk_1` (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actualizacion`
--
ALTER TABLE `actualizacion`
  MODIFY `idActualizacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `donacion`
--
ALTER TABLE `donacion`
  MODIFY `idDonacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `IdPersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `idProyecto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  MODIFY `idRecompensa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `riesgo`
--
ALTER TABLE `riesgo`
  MODIFY `idRiesgo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `voluntario`
--
ALTER TABLE `voluntario`
  MODIFY `idVoluntario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actualizacion`
--
ALTER TABLE `actualizacion`
  ADD CONSTRAINT `actualizacion_ibfk_1` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`idDonacion`) REFERENCES `donacion` (`idDonacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD CONSTRAINT `recompensa_ibfk_1` FOREIGN KEY (`idDonacion`) REFERENCES `donacion` (`idDonacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recompensa_ibfk_2` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `riesgo`
--
ALTER TABLE `riesgo`
  ADD CONSTRAINT `riesgo_ibfk_1` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `voluntario`
--
ALTER TABLE `voluntario`
  ADD CONSTRAINT `voluntario_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
