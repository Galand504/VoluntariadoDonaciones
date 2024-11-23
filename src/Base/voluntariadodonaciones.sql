SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE voluntariadodonaciones;
USE voluntariadodonaciones;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddUsuario` (IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_rol` ENUM('Voluntario','Donante','Organizador','Administrador'), IN `p_Tipo` ENUM('Persona','Empresa'), IN `p_nombre` VARCHAR(255), IN `p_apellido` VARCHAR(255), IN `p_dni` VARCHAR(255), IN `p_edad` VARCHAR(10), IN `p_telefono` VARCHAR(20), IN `p_nombreEmpresa` VARCHAR(255), IN `p_direccion` VARCHAR(255), IN `p_telefonoEmpresa` VARCHAR(50), IN `p_razonSocial` VARCHAR(255), IN `p_registroFiscal` VARCHAR(255), OUT `p_id_usuario` INT)   BEGIN
    -- Insertar en la tabla usuario, sin necesidad de incluir la fecha de registro
    INSERT INTO usuario (email, contraseña, Rol, Tipo) 
    VALUES (p_email, p_contraseña, p_rol, p_Tipo);

    -- Obtener el ID del usuario insertado
    SET p_id_usuario = LAST_INSERT_ID();

    -- Insertar en la tabla persona si es tipo persona
    IF p_Tipo = 'Persona' THEN
        INSERT INTO persona (nombre, apellido, dni, edad, telefono, id_usuario)
        VALUES (p_nombre, p_apellido, p_dni, p_edad, p_telefono, p_id_usuario);
    END IF;

    -- Insertar en la tabla empresa si es tipo empresa
    IF p_Tipo = 'Empresa' THEN
        -- Validar que se han proporcionado todos los campos necesarios para una empresa
        IF p_direccion IS NULL OR p_direccion = '' OR p_razonSocial IS NULL OR p_razonSocial = '' OR p_registroFiscal IS NULL OR p_registroFiscal = '' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Faltan datos de la empresa (dirección, razón social o registro fiscal).';
        ELSE
            INSERT INTO empresa (nombreEmpresa, razonSocial, telefonoEmpresa, direccion, registroFiscal, id_usuario)
            VALUES (p_nombreEmpresa, p_razonSocial, p_telefonoEmpresa, p_direccion, p_registroFiscal, p_id_usuario);
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUsuario` (IN `p_idUsuario` INT)   BEGIN
    -- Eliminar de la tabla persona si es tipo persona
    DELETE FROM persona WHERE id_usuario = p_idUsuario;

    -- Eliminar de la tabla empresa si es tipo empresa
    DELETE FROM empresa WHERE id_usuario = p_idUsuario;

    -- Eliminar de la tabla usuario
    DELETE FROM usuario WHERE id_usuario = p_idUsuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUsuarios` ()   BEGIN
    SELECT u.id_usuario, u.email, u.FechaRegistro, u.Rol, u.Tipo, p.nombre, p.apellido, p.dni, p.edad, p.Telefono, e.nombreEmpresa, e.razonSocial,
    e.registroFiscal, e.telefonoEmpresa, e.direccion
    FROM usuario u
    LEFT JOIN persona p ON u.id_usuario = p.id_usuario
    LEFT JOIN empresa e ON u.id_usuario = e.id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUsuarioById` (IN `p_idUsuario` INT)   BEGIN
    -- Mostrar los datos completos del usuario
    SELECT 
        u.id_usuario,
        u.email,
        u.FechaRegistro,
        u.rol,
        u.tipo,
        -- Datos de persona
        IF(u.tipo = 'Persona', p.nombre, NULL) AS nombre,
        IF(u.tipo = 'Persona', p.apellido, NULL) AS apellido,
        IF(u.tipo = 'Persona', p.telefono, NULL) AS telefono,
        -- Datos de empresa
        IF(u.tipo = 'Empresa', e.nombreEmpresa, NULL) AS nombre_empresa,
        IF(u.tipo = 'Empresa', e.razonSocial, NULL) AS razon_social,
        IF(u.tipo = 'Empresa', e.telefonoEmpresa, NULL) AS telefono_empresa,
        IF(u.tipo = 'Empresa', e.direccion, NULL) AS direccion_empresa,
        IF(u.tipo = 'Empresa', e.registroFiscal,NULL) AS registro_fiscal
    FROM 
        usuario u
    LEFT JOIN 
        persona p ON u.id_usuario = p.id_usuario AND u.tipo = 'Persona'
    LEFT JOIN 
        empresa e ON u.id_usuario = e.id_usuario AND u.tipo = 'Empresa'
    WHERE 
        u.id_usuario = p_idUsuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarPago` (IN `p_monto` FLOAT, IN `p_estado` ENUM('Pendiente','Completado','Fallido'), IN `p_idDonacion` INT, IN `p_id_metodopago` INT, IN `p_moneda` ENUM('USD','EUR','MXN','HNL'), IN `p_referencia_externa` VARCHAR(255))   BEGIN
    -- Verificar que el método de pago exista
    IF NOT EXISTS (
        SELECT 1 
        FROM metodo_pago 
        WHERE id_metodopago = p_id_metodopago
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El método de pago no existe.';
    END IF;

    -- Insertar el pago en la tabla `pago`
    INSERT INTO pago (fecha, monto, estado, idDonacion, id_metodopago, moneda, referencia_externa)
    VALUES (NOW(), p_monto, p_estado, p_idDonacion, p_id_metodopago, p_moneda, p_referencia_externa);

    -- Confirmación de registro
    SELECT LAST_INSERT_ID() AS idPago;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estado_recompensa_usuario` (IN `p_idRecompensa` INT, IN `p_idUsuario` INT, IN `p_estadoEntrega` VARCHAR(50), IN `p_idAdmin` INT)   BEGIN
    DECLARE v_rol VARCHAR(20);

    -- Validar que el usuario que realiza la acción es un administrador
    SELECT rol INTO v_rol
    FROM usuario
    WHERE id_usuario = p_idAdmin;

    IF v_rol != 'Administrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no tiene permisos para actualizar el estado de recompensas.';
    END IF;

    -- Verificar si la recompensa existe en la tabla recompensa_usuario para el usuario
    IF NOT EXISTS (
        SELECT 1
        FROM recompensa_usuario
        WHERE idRecompensa = p_idRecompensa AND idUsuario = p_idUsuario
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La recompensa especificada no existe para este usuario.';
    END IF;

    -- Actualizar el estado de la recompensa para el usuario
    UPDATE recompensa_usuario
    SET estadoEntrega = p_estadoEntrega
    WHERE idRecompensa = p_idRecompensa AND idUsuario = p_idUsuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_proyecto` (IN `p_idProyecto` INT, IN `p_titulo` VARCHAR(255), IN `p_descripcion` TEXT, IN `p_objetivo` VARCHAR(255), IN `p_meta` FLOAT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'), IN `p_tipo_actividad` ENUM('Voluntariado','Donacion'), IN `p_id_usuario` INT)   BEGIN
    -- Actualizar los datos del proyecto
    UPDATE proyecto
    SET titulo = p_titulo,
        descripcion = p_descripcion,
        objetivo = p_objetivo,
        Meta = p_meta,
        estado = p_estado,
        tipo_actividad = p_tipo_actividad,
        id_usuario = p_id_usuario
    WHERE idProyecto = p_idProyecto;

    -- Insertar una nueva actualización en la tabla 'actualizacion'
    INSERT INTO actualizacion (fecha, descripcion, idProyecto)
    VALUES (CURDATE(), CONCAT('Actualización del proyecto: ', p_titulo), p_idProyecto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_riesgo` (IN `p_idRiesgo` INT, IN `p_descripcion` TEXT, IN `p_planMitigacion` TEXT)   BEGIN
    -- Validar que el riesgo existe
    IF NOT EXISTS (SELECT 1 FROM riesgo WHERE idRiesgo = p_idRiesgo) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El riesgo especificado no existe.';
    END IF;

    -- Actualizar el riesgo
    UPDATE riesgo
    SET descripcion = p_descripcion,
        planMitigacion = p_planMitigacion
    WHERE idRiesgo = p_idRiesgo;

    SELECT 'Riesgo actualizado correctamente.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregar_proyecto` (IN `p_titulo` VARCHAR(255), IN `p_descripcion` TEXT, IN `p_objetivo` VARCHAR(255), IN `p_meta` FLOAT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'), IN `p_tipo_actividad` ENUM('Voluntariado','Donacion'), IN `p_id_usuario` INT)   BEGIN
    -- Validar si el tipo de actividad es Donacion y la Meta es NULL o 0
    IF p_tipo_actividad = 'Donacion' AND (p_meta IS NULL OR p_meta <= 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La meta es obligatoria y debe ser un valor mayor que cero para actividades de tipo Donacion';
    END IF;

    -- Insertar el proyecto en la tabla
    INSERT INTO proyecto (titulo, descripcion, objetivo, Meta, estado, tipo_actividad, id_usuario)
    VALUES (p_titulo, p_descripcion, p_objetivo, p_meta, p_estado, p_tipo_actividad, p_id_usuario);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_aprobar_recompensa` (IN `p_idRecompensa` INT, IN `p_aprobada` VARCHAR(20), IN `p_idAdmin` INT)   BEGIN
    DECLARE v_rol VARCHAR(20);

    -- Verificar si el usuario que realiza la acción es un administrador
    SELECT rol INTO v_rol
    FROM usuario
    WHERE id_usuario = p_idAdmin;

    -- Si el rol no es 'administrador', se genera un error
    IF v_rol != 'Administrador' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo los administradores pueden aprobar o rechazar recompensas.';
    END IF;

    -- Actualizar el estado de la recompensa (Aprobada o Rechazada)
    UPDATE recompensa
    SET aprobada = p_aprobada
    WHERE idRecompensa = p_idRecompensa;

    -- Confirmación
    SELECT 'Recompensa actualizada correctamente' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_asignar_recompensa` (IN `p_idRecompensa` INT, IN `p_idUsuario` INT, IN `p_idDonacion` INT)   BEGIN
    DECLARE v_montoMinimo FLOAT;
    DECLARE v_montoDonacion FLOAT;

    -- Verificar que la recompensa exista
    IF NOT EXISTS (SELECT 1 FROM recompensa WHERE idRecompensa = p_idRecompensa) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La recompensa especificada no existe.';
    END IF;

    -- Verificar que la donación exista y corresponda al usuario
    IF NOT EXISTS (SELECT 1 FROM donacion WHERE idDonacion = p_idDonacion AND id_usuario = p_idUsuario) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La donación especificada no pertenece al usuario.';
    END IF;

    -- Obtener el monto mínimo de la recompensa
    SELECT montoMinimo INTO v_montoMinimo
    FROM recompensa
    WHERE idRecompensa = p_idRecompensa;

    -- Obtener el monto de la donación
    SELECT monto INTO v_montoDonacion
    FROM pago
    WHERE idDonacion = p_idDonacion AND estado = 'Completado';

    -- Verificar si el monto de la donación cumple el mínimo requerido
    IF v_montoDonacion < v_montoMinimo THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El monto de la donación no cumple con el mínimo requerido para la recompensa.';
    END IF;

    -- Registrar la recompensa en la tabla recompensa_usuario
    INSERT INTO recompensa_usuario (idRecompensa, idUsuario, idDonacion, estadoEntrega)
    VALUES (p_idRecompensa, p_idUsuario, p_idDonacion, 'Pendiente');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_estado_proyecto` (IN `p_idProyecto` INT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'))   BEGIN
    UPDATE proyecto
    SET estado = p_estado
    WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_progreso_donacion` (IN `p_idProyecto` INT)   BEGIN
    SELECT 
        p.idProyecto,
        p.titulo,
        p.Meta,
        COALESCE(SUM(pg.monto), 0) AS total_recaudado,
        CASE 
            WHEN COALESCE(SUM(pg.monto), 0) >= p.Meta THEN 'Meta Alcanzada'
            ELSE 'Meta No Alcanzada'
        END AS estado_meta
    FROM proyecto p
    LEFT JOIN donacion d ON p.idProyecto = d.idProyecto
    LEFT JOIN pago pg ON d.idDonacion = pg.idDonacion
    WHERE p.idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_proyectos_por_tipo` (IN `p_tipo_actividad` ENUM('Voluntariado','Donacion'))   BEGIN
    SELECT * 
    FROM proyecto
    WHERE tipo_actividad = p_tipo_actividad;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_proyectos_usuario` (IN `p_id_usuario` INT)   BEGIN
    SELECT *
    FROM proyecto
    WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_riesgos` (IN `p_idProyecto` INT)   BEGIN
    -- Validar que el proyecto existe
    IF NOT EXISTS (SELECT 1 FROM proyecto WHERE idProyecto = p_idProyecto) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El proyecto especificado no existe.';
    END IF;

    -- Consultar los riesgos
    SELECT idRiesgo, descripcion, planMitigacion
    FROM riesgo
    WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_proyecto` (IN `p_idProyecto` INT)   BEGIN
    DELETE FROM proyecto
    WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_riesgo` (IN `p_idRiesgo` INT)   BEGIN
    -- Validar que el riesgo existe
    IF NOT EXISTS (SELECT 1 FROM riesgo WHERE idRiesgo = p_idRiesgo) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El riesgo especificado no existe.';
    END IF;

    -- Eliminar el riesgo
    DELETE FROM riesgo
    WHERE idRiesgo = p_idRiesgo;

    SELECT 'Riesgo eliminado correctamente.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_voluntariado` (IN `p_idVoluntario` INT)   BEGIN
    -- Verificar que el registro exista
    IF NOT EXISTS (
        SELECT 1 
        FROM voluntariado 
        WHERE idVoluntario = p_idVoluntario
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El voluntario no existe';
    END IF;

    -- Eliminar al voluntario
    DELETE FROM voluntariado
    WHERE idVoluntario = p_idVoluntario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_voluntarios` (IN `p_idProyecto` INT, IN `p_idUsuario` INT)   BEGIN
    -- Validar que el proyecto pertenece al organizador que solicita
    IF NOT EXISTS (
        SELECT 1 
        FROM proyecto 
        WHERE idProyecto = p_idProyecto AND id_usuario = p_idUsuario
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El usuario no tiene acceso a este proyecto';
    END IF;

    -- Listar voluntarios del proyecto
    SELECT 
        v.idVoluntario,
        v.disponibilidad,
        pers.nombre AS nombreUsuario,
        pers.apellido AS apellidoUsuario,
        p.titulo AS tituloProyecto
    FROM voluntariado v
    JOIN usuario u ON v.idUsuario = u.id_usuario
    JOIN persona pers ON u.id_usuario = pers.id_usuario  -- Relación entre usuario y persona
    JOIN proyecto p ON v.proyecto_idProyecto = p.idProyecto
    WHERE v.proyecto_idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_actualizacion` (IN `p_idProyecto` INT, IN `p_descripcion` TEXT)   BEGIN
    -- Insertar un registro en la tabla de actualizaciones
    INSERT INTO actualizacion (fecha, descripcion, idProyecto)
    VALUES (CURDATE(), p_descripcion, p_idProyecto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_recompensa` (IN `p_idUsuario` INT, IN `p_descripcion` TEXT, IN `p_montoMinimo` FLOAT, IN `p_fechaEntrega` DATE, IN `p_idProyecto` INT)   BEGIN
    DECLARE v_rol VARCHAR(20);
    DECLARE v_idOrganizador INT;

    -- Obtener el rol del usuario que está registrando la recompensa
    SELECT rol INTO v_rol
    FROM usuario
    WHERE id_usuario = p_idUsuario;

    -- Verificar si el usuario tiene el rol 'organizador'
    IF v_rol != 'Organizador' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no tiene permisos para registrar recompensas.';
    END IF;

    -- Verificar si el proyecto corresponde al organizador que lo está intentando agregar
    SELECT id_usuario INTO v_idOrganizador
    FROM proyecto
    WHERE idProyecto = p_idProyecto;

    -- Asegurarse de que el organizador del proyecto es el mismo que está intentando registrar la recompensa
    IF v_idOrganizador != p_idUsuario THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El organizador del proyecto no es el usuario actual.';
    END IF;

    -- Si todo está bien, registrar la recompensa
    INSERT INTO recompensa (descripcion, montoMinimo, fechaEntregaEstimada, idProyecto, aprobada)
    VALUES (p_descripcion, p_montoMinimo, p_fechaEntrega, p_idProyecto, 'Pendiente');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_riesgo` (IN `p_descripcion` TEXT, IN `p_planMitigacion` TEXT, IN `p_idProyecto` INT)   BEGIN
    -- Validar que el proyecto existe
    IF NOT EXISTS (SELECT 1 FROM proyecto WHERE idProyecto = p_idProyecto) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El proyecto especificado no existe.';
    END IF;

    -- Insertar el riesgo
    INSERT INTO riesgo (descripcion, planMitigacion, idProyecto)
    VALUES (p_descripcion, p_planMitigacion, p_idProyecto);
    
    SELECT 'Riesgo registrado correctamente.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_voluntariado` (IN `p_disponibilidad` VARCHAR(255), IN `p_idUsuario` INT, IN `p_idProyecto` INT)   BEGIN
    -- Verificar que el proyecto exista
    IF NOT EXISTS (
        SELECT 1 
        FROM proyecto 
        WHERE idProyecto = p_idProyecto AND tipo_actividad = 'Voluntariado'
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El proyecto no existe o no es de tipo Voluntariado';
    END IF;

    -- Registrar al usuario en el proyecto de voluntariado
    INSERT INTO voluntariado (disponibilidad, idUsuario, proyecto_idProyecto)
    VALUES (p_disponibilidad, p_idUsuario, p_idProyecto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_vincular_donacion` (IN `p_id_usuario` INT, IN `p_idProyecto` INT)   BEGIN
    DECLARE v_tipo_actividad ENUM('Voluntariado', 'Donacion');

    -- Obtener el tipo de actividad del proyecto
    SELECT tipo_actividad INTO v_tipo_actividad FROM proyecto WHERE idProyecto = p_idProyecto;

    -- Verificar si el proyecto es de tipo 'Donacion'
    IF v_tipo_actividad = 'Donacion' THEN
        -- Insertar en la tabla donacion
        INSERT INTO donacion (id_usuario, idProyecto) VALUES (p_id_usuario, p_idProyecto);
    ELSE
        -- Si no es de tipo 'Donacion', retornar un mensaje de error
        SELECT 'Error: El proyecto no es de tipo Donacion' AS mensaje;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUsuario` (IN `p_idUsuario` INT, IN `p_nombre` VARCHAR(255), IN `p_apellido` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_telefono` VARCHAR(255), IN `p_dni` VARCHAR(255), IN `p_edad` VARCHAR(100), IN `p_rol` ENUM('Voluntario','Donante','Organizador'), IN `p_tipo` ENUM('Persona','Empresa'), IN `p_nombreEmpresa` VARCHAR(255), IN `p_razonSocial` VARCHAR(255), IN `p_telefonoEmpresa` VARCHAR(255), IN `p_direccion` VARCHAR(255), IN `p_registroFiscal` VARCHAR(255))   BEGIN
    -- Verificar si el usuario existe en la tabla usuario
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id_usuario = p_idUsuario) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no existe.';
    END IF;

    -- Actualizar la tabla usuario
    UPDATE usuario
    SET 
        email = p_email,
        contraseña = p_contraseña,
        rol = p_rol,
        tipo = p_tipo
    WHERE id_usuario = p_idUsuario;

    -- Si es tipo Persona, actualizar o insertar en la tabla persona
    IF p_tipo = 'Persona' THEN
        IF NOT EXISTS (SELECT 1 FROM persona WHERE id_usuario = p_idUsuario) THEN
            INSERT INTO persona (id_usuario, nombre, apellido, telefono, dni, edad)
            VALUES (p_idUsuario, p_nombre, p_apellido, p_telefono, p_dni, p_edad);
        ELSE
            UPDATE persona
            SET 
                nombre = p_nombre, 
                apellido = p_apellido, 
                telefono = p_telefono,
                dni = p_dni,
                edad = p_edad
            WHERE id_usuario = p_idUsuario;
        END IF;
    END IF;

    -- Si es tipo Empresa, actualizar o insertar en la tabla empresa
    IF p_tipo = 'Empresa' THEN
        IF NOT EXISTS (SELECT 1 FROM empresa WHERE id_usuario = p_idUsuario) THEN
            INSERT INTO empresa (id_usuario, nombreEmpresa, razonSocial, telefono, direccion, registroFiscal)
            VALUES (p_idUsuario, p_nombreEmpresa, p_razonSocial, p_telefonoEmpresa, p_direccion, p_registroFiscal);
        ELSE
            UPDATE empresa
            SET 
                nombreEmpresa = p_nombreEmpresa, 
                razonSocial = p_razonSocial, 
                telefono = p_telefonoEmpresa,
                direccion = p_direccion,
                registroFiscal = p_registroFiscal
            WHERE id_usuario = p_idUsuario;
        END IF;
    END IF;
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
  `id_usuario` int(11) DEFAULT NULL,
  `idProyecto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `idPago` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto` float NOT NULL,
  `estado` enum('Completado','Pendiente','Fallido') NOT NULL,
  `idDonacion` int(11) NOT NULL,
  `id_metodopago` int(11) NOT NULL,
  `referencia_externa` varchar(255) DEFAULT NULL,
  `moneda` enum('USD','EUR','MXN','HNL') NOT NULL DEFAULT 'USD'
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
(12, '12345678', 'Admin', 'User', '30', '1234567890', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `idProyecto` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `objetivo` varchar(255) NOT NULL,
  `Meta` float NOT NULL,
  `estado` enum('En Proceso','Completado','Cancelado') NOT NULL,
  `tipo_actividad` enum('Voluntariado','Donacion') NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa`
--

CREATE TABLE `recompensa` (
  `idRecompensa` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `montoMinimo` float NOT NULL,
  `fechaEntregaEstimada` date NOT NULL,
  `idProyecto` int(11) NOT NULL,
  `aprobada` enum('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente'
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
  `FechaRegistro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Rol` enum('Donante','Voluntario','Organizador','Administrador') NOT NULL,
  `Tipo` enum('Persona','Empresa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `email`, `contraseña`, `FechaRegistro`, `Rol`, `Tipo`) VALUES
(1, 'admin@example.com', '$2y$10$10xP/8w9B/TP.e1JM4/Rnu67GlAO.lBYoBWV38Ic.9hx.vmJnbnQC', '2024-11-22 19:09:05', 'Administrador', 'Persona');

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
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idProyecto` (`idProyecto`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`idPago`),
  ADD KEY `idDonacion` (`idDonacion`),
  ADD KEY `id_metodo_pago` (`id_metodopago`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`IdPersona`),
  ADD KEY `fk_persona_usuario1_idx` (`id_usuario`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`idProyecto`),
  ADD KEY `idx_id_usuario` (`id_usuario`);

--
-- Indices de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD PRIMARY KEY (`idRecompensa`),
  ADD KEY `fk_recompensa_proyecto` (`idProyecto`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actualizacion`
--
ALTER TABLE `actualizacion`
  MODIFY `idActualizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `donacion`
--
ALTER TABLE `donacion`
  MODIFY `idDonacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `IdPersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `idProyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  MODIFY `idRecompensa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `riesgo`
--
ALTER TABLE `riesgo`
  MODIFY `idRiesgo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actualizacion`
--
ALTER TABLE `actualizacion`
  ADD CONSTRAINT `actualizacion_ibfk_1` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `donacion`
--
ALTER TABLE `donacion`
  ADD CONSTRAINT `dona` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `donacion` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`idDonacion`) REFERENCES `donacion` (`idDonacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `pago_ibfk_2` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`) ON DELETE CASCADE;

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `fk_persona_usuario1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD CONSTRAINT `fk_recompensa_proyecto` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `riesgo`
--
ALTER TABLE `riesgo`
  ADD CONSTRAINT `riesgo_ibfk_1` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
