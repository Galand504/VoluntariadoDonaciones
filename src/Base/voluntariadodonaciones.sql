

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
CREATE DATABASE IF NOT EXISTS `voluntariadodonaciones` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `voluntariadodonaciones`;

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUsuario` (IN `p_id_usuario` INT)   BEGIN
    -- Eliminar de la tabla persona si es tipo persona
    DELETE FROM persona WHERE id_usuario = p_id_usuario;

    -- Eliminar de la tabla empresa si es tipo empresa
    DELETE FROM empresa WHERE id_usuario = p_id_usuario;

    -- Eliminar de la tabla usuario
    DELETE FROM usuario WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUsuarios` ()   BEGIN
    SELECT u.id_usuario, u.email, u.FechaRegistro, u.Rol, u.Tipo, 
       p.nombre, p.apellido, p.dni, p.edad, p.Telefono, 
       e.nombreEmpresa, e.razonSocial, e.registroFiscal, 
       e.telefonoEmpresa, e.direccion
FROM usuario u
LEFT JOIN persona p ON u.id_usuario = p.id_usuario
LEFT JOIN empresa e ON u.id_usuario = e.id_usuario
WHERE p.id_usuario IS NOT NULL OR e.id_usuario IS NOT NULL;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estado_pago` (IN `p_idPago` INT, IN `p_estado` ENUM('Completado','Pendiente','Fallido'))   BEGIN
    UPDATE pago 
    SET estado = p_estado
    WHERE idPago = p_idPago;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_asignar_recompensa` (IN `p_idDonacion` INT)   BEGIN
    DECLARE v_monto DECIMAL(10,2);
    DECLARE v_moneda_pago VARCHAR(3);
    DECLARE v_monto_convertido DECIMAL(10,2);
    DECLARE v_idRecompensa INT;
    DECLARE v_idUsuario INT;
    DECLARE v_tasa_cambio DECIMAL(10,4);
    DECLARE v_moneda_recompensa VARCHAR(3);
    
    -- Obtener el monto, moneda y usuario de la donación
    SELECT p.monto, p.moneda, d.id_usuario
    INTO v_monto, v_moneda_pago, v_idUsuario
    FROM pago p
    JOIN donacion d ON p.idDonacion = d.idDonacion
    WHERE p.idDonacion = p_idDonacion
    AND p.estado = 'Completado'
    LIMIT 1;
    
    -- Verificar que obtuvimos los datos necesarios
    IF v_monto IS NOT NULL THEN
        -- Encontrar la recompensa correspondiente
        SELECT r.idRecompensa, r.moneda 
        INTO v_idRecompensa, v_moneda_recompensa
        FROM recompensa r
        WHERE r.aprobada = 'Aprobada'
        AND (
            CASE 
                WHEN r.moneda = v_moneda_pago THEN 
                    r.montoMinimo <= v_monto
                ELSE 
                    r.montoMinimo <= fn_convertir_moneda(v_monto, v_moneda_pago, r.moneda)
            END
        )
        ORDER BY r.montoMinimo DESC 
        LIMIT 1;
        
        -- Asignar la recompensa si existe
        IF v_idRecompensa IS NOT NULL AND v_idUsuario IS NOT NULL THEN
            INSERT INTO recompensa_usuario (
                idRecompensa,
                idUsuario,
                idDonacion,
                estadoEntrega
            ) VALUES (
                v_idRecompensa,
                v_idUsuario,
                p_idDonacion,
                'Pendiente'
            );
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_estado_proyecto` (IN `p_idProyecto` INT, IN `p_estado` ENUM('En Proceso','Completado','Cancelado'))   BEGIN
    UPDATE proyecto
    SET estado = p_estado
    WHERE idProyecto = p_idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cancelar_pago` (IN `p_idPago` INT, IN `p_motivo` VARCHAR(255))   BEGIN
    -- Verificar que el pago existe y no está completado
    IF NOT EXISTS (
        SELECT 1 FROM pago 
        WHERE idPago = p_idPago 
        AND estado != 'Completado'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El pago no existe o ya está completado';
    END IF;

    -- Actualizar estado del pago
    UPDATE pago 
    SET 
        estado = 'Cancelado',
        fecha = NOW(),
        motivo = p_motivo
    WHERE idPago = p_idPago;

    -- Registrar motivo de cancelación (opcional, requiere tabla adicional)
    -- INSERT INTO pago_cancelacion (idPago, motivo, fecha)
    -- VALUES (p_idPago, p_motivo, NOW());
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_actividades` ()   BEGIN
    SELECT 
        p.idProyecto,
        p.titulo,
        p.descripcion,
        p.objetivo,
        p.estado,
        p.tipo_actividad
    FROM proyecto p
    WHERE p.estado = 'En Proceso'
    ORDER BY p.idProyecto DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_dashboard_counts` ()   BEGIN
    -- Contar personas
    SELECT 
        (SELECT COUNT(*) FROM persona) as personas,
        (SELECT COUNT(*) FROM empresa) as empresas,
        (SELECT COUNT(*) FROM proyecto WHERE tipo_actividad = 'Voluntariado') as voluntariados,
        (SELECT COUNT(*) FROM proyecto WHERE tipo_actividad = 'Donacion') as donaciones;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_registros_por_fecha` ()   BEGIN
    SELECT 
        DATE(FechaRegistro) as fecha,
        COUNT(*) as total
    FROM usuario
    GROUP BY DATE(FechaRegistro)
    ORDER BY fecha ASC
    LIMIT 7;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login` (IN `u_email` VARCHAR(255), IN `u_contraseña` VARCHAR(255))   BEGIN
    DECLARE v_hashed_contraseña VARCHAR(255);
    DECLARE v_rol VARCHAR(20);
    DECLARE v_id_usuario INT;

    -- Obtener la contraseña hasheada almacenada
    SELECT id_usuario, contraseña, rol
    INTO v_id_usuario, v_hashed_contraseña, v_rol
    FROM usuario
    WHERE email = u_email;

    -- Validar si el usuario existe
    IF v_hashed_contraseña IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Usuario no encontrado.';
    END IF;

    -- La verificación de la contraseña se hará en PHP
    -- Solo devolvemos los datos necesarios
    SELECT v_id_usuario AS id_usuario, 
           v_hashed_contraseña AS contraseña,
           v_rol AS rol;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_donadores_estrella_general` ()   BEGIN
    SELECT 
        u.id_usuario,
        CASE 
            WHEN u.Tipo = 'Persona' THEN CONCAT(p.nombre, ' ', p.apellido)
            WHEN u.Tipo = 'Empresa' THEN e.nombreEmpresa
        END as nombre_donador,
        u.email,
        u.Tipo as tipo_donador,
        COUNT(DISTINCT d.idDonacion) as total_donaciones,
        SUM(pa.monto) as monto_total,
        COUNT(DISTINCT d.idProyecto) as proyectos_apoyados,
        MAX(pa.monto) as mayor_donacion,
        MIN(pa.fecha) as primera_donacion,
        MAX(pa.fecha) as ultima_donacion
    FROM usuario u
    LEFT JOIN persona p ON u.id_usuario = p.id_usuario
    LEFT JOIN empresa e ON u.id_usuario = e.id_usuario
    INNER JOIN donacion d ON u.id_usuario = d.id_usuario
    INNER JOIN pago pa ON d.idDonacion = pa.idDonacion
    WHERE pa.estado = 'Completado'
    GROUP BY u.id_usuario
    HAVING monto_total >= 1000
    ORDER BY monto_total DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_donadores_estrella_proyecto` (IN `p_idProyecto` INT)   BEGIN
    SELECT 
        u.id_usuario,
        CASE 
            WHEN u.Tipo = 'Persona' THEN CONCAT(p.nombre, ' ', p.apellido)
            WHEN u.Tipo = 'Empresa' THEN e.nombreEmpresa
        END as nombre_donador,
        u.email,
        u.Tipo as tipo_donador,
        pr.titulo as nombre_proyecto,
        COUNT(d.idDonacion) as donaciones_proyecto,
        SUM(pa.monto) as monto_total_proyecto,
        MAX(pa.monto) as mayor_donacion_proyecto,
        MIN(pa.fecha) as primera_donacion,
        MAX(pa.fecha) as ultima_donacion
    FROM usuario u
    LEFT JOIN persona p ON u.id_usuario = p.id_usuario
    LEFT JOIN empresa e ON u.id_usuario = e.id_usuario
    INNER JOIN donacion d ON u.id_usuario = d.id_usuario
    INNER JOIN proyecto pr ON d.idProyecto = pr.idProyecto
    INNER JOIN pago pa ON d.idDonacion = pa.idDonacion
    WHERE pa.estado = 'Completado'
    AND pr.idProyecto = p_idProyecto
    GROUP BY u.id_usuario
    HAVING monto_total_proyecto >= 1000
    ORDER BY monto_total_proyecto DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_estadisticas_pagos` ()   BEGIN
    SELECT 
        COUNT(*) as total_pagos,
        SUM(monto) as monto_total,
        COUNT(CASE WHEN estado = 'Completado' THEN 1 END) as pagos_completados,
        COUNT(CASE WHEN estado = 'Pendiente' THEN 1 END) as pagos_pendientes,
        COUNT(CASE WHEN estado = 'Cancelado' THEN 1 END) as pagos_cancelados,
        AVG(monto) as monto_promedio
    FROM pago;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_pagos_por_fecha` (IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
    SELECT p.*, d.id_usuario, d.idProyecto
    FROM pago p
    JOIN donacion d ON p.idDonacion = d.idDonacion
    WHERE p.fecha BETWEEN p_fecha_inicio AND p_fecha_fin
    ORDER BY p.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_pagos_usuario` (IN `p_id_usuario` INT)   BEGIN
    SELECT p.*, d.idProyecto, pr.titulo as proyecto_titulo
    FROM pago p
    JOIN donacion d ON p.idDonacion = d.idDonacion
    JOIN proyecto pr ON d.idProyecto = pr.idProyecto
    WHERE d.id_usuario = p_id_usuario
    ORDER BY p.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_recompensas_asignadas` (IN `p_estado_entrega` VARCHAR(50), IN `p_id_usuario` INT, IN `p_id_proyecto` INT)   BEGIN
    SELECT ru.*, r.descripcion, r.montoMinimo, r.moneda,
           CASE 
               WHEN u.Tipo = 'Persona' THEN CONCAT(p.Nombre, ' ', p.Apellido)
               WHEN u.Tipo = 'Empresa' THEN e.nombreEmpresa
           END as nombre_donante,
           u.email, u.Tipo as tipo_usuario,
           CASE 
               WHEN u.Tipo = 'Persona' THEN p.Telefono
               WHEN u.Tipo = 'Empresa' THEN e.telefonoEmpresa
           END as telefono,
           pa.monto as monto_donacion, pa.fecha as fecha_donacion,
           pr.titulo as nombre_proyecto
    FROM recompensa_usuario ru
    JOIN recompensa r ON ru.idRecompensa = r.idRecompensa
    JOIN usuario u ON ru.idUsuario = u.id_usuario
    LEFT JOIN persona p ON u.id_usuario = p.id_usuario
    LEFT JOIN empresa e ON u.id_usuario = e.id_usuario
    JOIN donacion d ON ru.idDonacion = d.idDonacion
    JOIN pago pa ON d.idDonacion = pa.idDonacion
    JOIN proyecto pr ON r.idProyecto = pr.idProyecto
    WHERE pa.estado = 'Completado'
    AND (p_estado_entrega IS NULL OR ru.estadoEntrega = p_estado_entrega)
    AND (p_id_usuario IS NULL OR ru.idUsuario = p_id_usuario)
    AND (p_id_proyecto IS NULL OR pr.idProyecto = p_id_proyecto)
    ORDER BY ru.idRecompensaUsuario DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_total_pagos_proyecto` (IN `p_idProyecto` INT)   BEGIN
    SELECT 
        p.idProyecto,
        COUNT(pa.idPago) as total_pagos,
        SUM(pa.monto) as monto_total,
        COUNT(CASE WHEN pa.estado = 'Completado' THEN 1 END) as pagos_completados,
        SUM(CASE WHEN pa.estado = 'Completado' THEN pa.monto ELSE 0 END) as monto_completado
    FROM proyecto p
    LEFT JOIN donacion d ON p.idProyecto = d.idProyecto
    LEFT JOIN pago pa ON d.idDonacion = pa.idDonacion
    WHERE p.idProyecto = p_idProyecto
    GROUP BY p.idProyecto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_actualizacion` (IN `p_idProyecto` INT, IN `p_descripcion` TEXT)   BEGIN
    -- Insertar un registro en la tabla de actualizaciones
    INSERT INTO actualizacion (fecha, descripcion, idProyecto)
    VALUES (CURDATE(), p_descripcion, p_idProyecto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_recompensa` (IN `p_idUsuario` INT, IN `p_descripcion` TEXT, IN `p_montoMinimo` FLOAT, IN `p_moneda` ENUM('HNL','USD','EUR','MXN'), IN `p_fechaEntrega` DATE, IN `p_idProyecto` INT)   BEGIN
    DECLARE v_rol VARCHAR(20);
    DECLARE v_idOrganizador INT;

    -- Obtener el rol del usuario que está registrando la recompensa
    SELECT rol INTO v_rol
    FROM usuario
    WHERE id_usuario = p_idUsuario;

    -- Verificar si el usuario tiene el rol 'organizador'
    IF v_rol != 'Organizador' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El usuario no tiene permisos para registrar recompensas.';
    END IF;

    -- Verificar si el proyecto corresponde al organizador que lo está intentando agregar
    SELECT id_usuario INTO v_idOrganizador
    FROM proyecto
    WHERE idProyecto = p_idProyecto;

    -- Asegurarse de que el organizador del proyecto es el mismo que está intentando registrar la recompensa
    IF v_idOrganizador != p_idUsuario THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El organizador del proyecto no es el usuario actual.';
    END IF;

    -- Validar moneda
    IF p_moneda NOT IN ('HNL', 'USD', 'EUR', 'MXN') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Moneda inválida. Monedas permitidas: HNL, USD, EUR, MXN';
    END IF;

    -- Validar monto mínimo
    IF p_montoMinimo <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El monto mínimo debe ser mayor a 0';
    END IF;

    -- Si todo está bien, registrar la recompensa
    INSERT INTO recompensa (
        descripcion, 
        montoMinimo, 
        moneda,
        fechaEntregaEstimada, 
        idProyecto, 
        aprobada
    )
    VALUES (
        p_descripcion, 
        p_montoMinimo, 
        p_moneda,
        p_fechaEntrega, 
        p_idProyecto, 
        'Pendiente'
    );

    -- Retornar la recompensa creada
    SELECT 
        r.idRecompensa,
        r.descripcion,
        r.montoMinimo,
        r.moneda,
        r.fechaEntregaEstimada,
        r.idProyecto,
        r.aprobada
    FROM recompensa r
    WHERE r.idRecompensa = LAST_INSERT_ID();
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_validar_pago` (IN `p_idPago` INT, OUT `p_es_valido` BOOLEAN, OUT `p_mensaje` VARCHAR(255))   BEGIN
    DECLARE v_estado VARCHAR(20);
    DECLARE v_monto FLOAT;
    DECLARE v_metodo_pago INT;
    
    -- Inicializar valores
    SET p_es_valido = FALSE;
    SET p_mensaje = '';
    
    -- Obtener información del pago
    SELECT estado, monto, id_metodopago 
    INTO v_estado, v_monto, v_metodo_pago
    FROM pago 
    WHERE idPago = p_idPago;
    
    -- Validar que el pago existe
    IF v_estado IS NULL THEN
        SET p_mensaje = 'El pago no existe';
        SET p_es_valido = FALSE;
    -- Validar monto
    ELSEIF v_monto <= 0 THEN
        SET p_mensaje = 'Monto inválido';
        SET p_es_valido = FALSE;
    -- Validar método de pago
    ELSEIF NOT EXISTS (SELECT 1 FROM metodo_pago WHERE id_metodopago = v_metodo_pago) THEN
        SET p_mensaje = 'Método de pago inválido';
        SET p_es_valido = FALSE;
    -- Si todas las validaciones pasan
    ELSE
        SET p_es_valido = TRUE;
        SET p_mensaje = 'Pago válido';
    END IF;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUsuario` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(255), IN `p_apellido` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_contraseña` VARCHAR(255), IN `p_telefono` VARCHAR(255), IN `p_dni` VARCHAR(255), IN `p_edad` VARCHAR(100), IN `p_rol` ENUM('Voluntario','Donante','Organizador'), IN `p_tipo` ENUM('Persona','Empresa'), IN `p_nombreEmpresa` VARCHAR(255), IN `p_razonSocial` VARCHAR(255), IN `p_telefonoEmpresa` VARCHAR(255), IN `p_direccion` VARCHAR(255), IN `p_registroFiscal` VARCHAR(255))   BEGIN
    -- Verificar si el usuario existe en la tabla usuario
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id_usuario = p_id_usuario) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no existe.';
    END IF;

    -- Actualizar la tabla usuario
    UPDATE usuario
    SET 
        email = p_email,
        contraseña = p_contraseña
    WHERE id_usuario = p_id_usuario;

    -- Si es tipo Persona, actualizar o insertar en la tabla persona
    IF p_tipo = 'Persona' THEN
        IF NOT EXISTS (SELECT 1 FROM persona WHERE id_usuario = p_id_usuario) THEN
            INSERT INTO persona (id_usuario, nombre, apellido, telefono, dni, edad)
            VALUES (p_id_usuario, p_nombre, p_apellido, p_telefono, p_dni, p_edad);
        ELSE
            UPDATE persona
            SET 
                nombre = p_nombre, 
                apellido = p_apellido, 
                telefono = p_telefono,
                dni = p_dni,
                edad = p_edad
            WHERE id_usuario = p_id_usuario;
        END IF;
    END IF;

    -- Si es tipo Empresa, actualizar o insertar en la tabla empresa
    IF p_tipo = 'Empresa' THEN
        IF NOT EXISTS (SELECT 1 FROM empresa WHERE id_usuario = p_id_usuario) THEN
            INSERT INTO empresa (id_usuario, nombreEmpresa, razonSocial, telefono, direccion, registroFiscal)
            VALUES (p_id_usuario, p_nombreEmpresa, p_razonSocial, p_telefonoEmpresa, p_direccion, p_registroFiscal);
        ELSE
            UPDATE empresa
            SET 
                nombreEmpresa = p_nombreEmpresa, 
                razonSocial = p_razonSocial, 
                telefono = p_telefonoEmpresa,
                direccion = p_direccion,
                registroFiscal = p_registroFiscal
            WHERE id_usuario = p_id_usuario;
        END IF;
    END IF;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_convertir_moneda` (`monto` DECIMAL(10,2), `moneda_origen` ENUM('HNL','USD','EUR','MXN'), `moneda_destino` ENUM('HNL','USD','EUR','MXN')) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE tasa_conversion DECIMAL(10,4);
    
    -- Si las monedas son iguales, retornar el mismo monto
    IF moneda_origen = moneda_destino THEN
        RETURN monto;
    END IF;
    
    -- Obtener la tasa de conversión
    SELECT tasa INTO tasa_conversion
    FROM tasa_cambio
    WHERE tasa_cambio.moneda_origen = moneda_origen
    AND tasa_cambio.moneda_destino = moneda_destino
    LIMIT 1;  -- Asegurar que solo tome una tasa
    
    -- Si no hay tasa directa, intentar conversión a través de HNL
    IF tasa_conversion IS NULL THEN
        SELECT (t1.tasa * t2.tasa) INTO tasa_conversion
        FROM tasa_cambio t1
        JOIN tasa_cambio t2 ON t1.moneda_destino = t2.moneda_origen
        WHERE t1.moneda_origen = moneda_origen
        AND t2.moneda_destino = moneda_destino
        AND t1.moneda_destino = 'HNL'
        LIMIT 1;
    END IF;
    
    -- Si aún no hay tasa, retornar NULL o lanzar error
    IF tasa_conversion IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'No se encontró tasa de conversión para las monedas especificadas';
    END IF;
    
    RETURN monto * tasa_conversion;
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

--
-- Volcado de datos para la tabla `donacion`
--

INSERT INTO `donacion` (`idDonacion`, `id_usuario`, `idProyecto`) VALUES
(7, 71, 5),
(8, 75, 5),
(9, 62, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `nombreEmpresa` varchar(255) NOT NULL,
  `razonSocial` varchar(255) NOT NULL,
  `registroFiscal` varchar(255) NOT NULL,
  `telefonoEmpresa` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `nombreEmpresa`, `razonSocial`, `registroFiscal`, `telefonoEmpresa`, `direccion`, `id_usuario`) VALUES
(15, 'gfgfhf', 'ghfghgf', 'dfgfdgdfgdf', '7894561231', 'gdfgfdg', 75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodopago`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Tarjeta de Crédito', NULL, 1),
(2, 'Tarjeta de Débito', NULL, 1),
(3, 'PayPal', NULL, 1),
(4, 'Transferencia Bancaria', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `idPago` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto` float NOT NULL,
  `estado` enum('Completado','Pendiente','Cancelado') NOT NULL,
  `idDonacion` int(11) NOT NULL,
  `id_metodopago` int(11) NOT NULL,
  `referencia_externa` varchar(255) DEFAULT NULL,
  `moneda` enum('USD','EUR','MXN','HNL') NOT NULL DEFAULT 'USD',
  `motivo` varchar(255) DEFAULT NULL COMMENT 'Motivo por el cual se canceló el pago'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`idPago`, `fecha`, `monto`, `estado`, `idDonacion`, `id_metodopago`, `referencia_externa`, `moneda`, `motivo`) VALUES
(3, '2024-12-07 17:26:20', 40000, 'Completado', 7, 2, 'ui4h39yrioer34$ioior4', 'USD', NULL),
(4, '2024-12-07 17:27:49', 75000, 'Completado', 8, 1, 'duh34h534$3', 'USD', NULL),
(5, '2024-12-08 17:03:38', 100, 'Cancelado', 8, 3, 'REF123', 'HNL', 'Sin plata'),
(6, '2024-12-08 18:34:06', 100, 'Completado', 9, 2, 'PAY-123456789', 'USD', NULL),
(7, '2024-12-08 18:37:37', 100, 'Completado', 9, 2, 'DEBT-20241208-123737-USD-0000010000', 'USD', NULL);

--
-- Disparadores `pago`
--
DELIMITER $$
CREATE TRIGGER `after_pago_update` AFTER UPDATE ON `pago` FOR EACH ROW BEGIN
    IF NEW.estado = 'Completado' AND OLD.estado != 'Completado' THEN
               
        -- Llamar al procedimiento con un solo argumento
        CALL sp_asignar_recompensa(NEW.idDonacion);
    END IF;
END
$$
DELIMITER ;

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
(19, '12345678', 'Juan', 'Pérez', '25', '1234567890', 62),
(25, '00000000', 'Gerardo', 'ewwew', '43', '1443234234', 69),
(26, '87978987', 'gfdgfdgdf', 'gfdgfdg', '43', '3232323232', 71);

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

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`idProyecto`, `titulo`, `descripcion`, `objetivo`, `Meta`, `estado`, `tipo_actividad`, `id_usuario`) VALUES
(5, 'ereterter', 'tertertre', 'retretre', 120000, 'En Proceso', 'Donacion', 69);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa`
--

CREATE TABLE `recompensa` (
  `idRecompensa` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `montoMinimo` decimal(10,2) NOT NULL,
  `fechaEntregaEstimada` date NOT NULL,
  `idProyecto` int(11) NOT NULL,
  `aprobada` enum('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
  `moneda` enum('HNL','USD','EUR','MXN') NOT NULL DEFAULT 'HNL' COMMENT 'Moneda en la que se debe alcanzar el monto mínimo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recompensa`
--

INSERT INTO `recompensa` (`idRecompensa`, `descripcion`, `montoMinimo`, `fechaEntregaEstimada`, `idProyecto`, `aprobada`, `moneda`) VALUES
(2, 'Medalla de Oro por Donación', 1000.00, '2024-12-31', 5, 'Aprobada', 'HNL'),
(3, 'Medalla de Oro por Donación', 1000.00, '2024-12-31', 5, 'Aprobada', 'HNL'),
(4, 'Medalla de Oro por Donación', 1000.00, '2024-12-31', 5, 'Aprobada', 'HNL'),
(7, 'Camiseta del proyecto', 25.00, '2024-12-31', 5, 'Pendiente', 'USD'),
(8, 'Camiseta del proyecto', 25.00, '2024-12-31', 5, 'Aprobada', 'USD'),
(16, 'Certificado de donación', 100.00, '2024-12-31', 5, 'Aprobada', 'USD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa_usuario`
--

CREATE TABLE `recompensa_usuario` (
  `idRecompensaUsuario` int(11) NOT NULL,
  `idRecompensa` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idDonacion` int(11) NOT NULL,
  `estadoEntrega` enum('Pendiente','En Proceso','Entregado') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recompensa_usuario`
--

INSERT INTO `recompensa_usuario` (`idRecompensaUsuario`, `idRecompensa`, `idUsuario`, `idDonacion`, `estadoEntrega`) VALUES
(2, 4, 75, 8, 'En Proceso'),
(3, 8, 62, 9, 'Entregado'),
(4, 3, 62, 9, 'Pendiente');

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
-- Estructura de tabla para la tabla `tasa_cambio`
--

CREATE TABLE `tasa_cambio` (
  `moneda_origen` enum('HNL','USD','EUR','MXN') NOT NULL,
  `moneda_destino` enum('HNL','USD','EUR','MXN') NOT NULL,
  `tasa` decimal(10,4) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tasa_cambio`
--

INSERT INTO `tasa_cambio` (`moneda_origen`, `moneda_destino`, `tasa`, `fecha_actualizacion`) VALUES
('HNL', 'USD', 0.0039, '2024-12-08 17:39:06'),
('HNL', 'EUR', 0.0370, '2024-12-08 17:39:06'),
('HNL', 'MXN', 0.8000, '2024-12-08 17:39:06'),
('USD', 'HNL', 25.3400, '2024-12-08 17:39:06'),
('EUR', 'HNL', 26.8100, '2024-12-08 17:39:06'),
('MXN', 'HNL', 1.2500, '2024-12-08 17:39:06');

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
(62, 'juan.perez@ejemplo.com', '$2y$10$Afpu368vvwp1KMB3FfZayeTe2xTVqRoIgHZgL7NraIs61d3eKuncW', '2024-12-05 02:22:48', 'Administrador', 'Persona'),
(69, 'juan@gmail.com', '$2y$10$/RrYEm/VT40rx2mgkoaQye5UZcc2EPMzbRidXXA9NYNzTr1Y9J7Zq', '2024-12-06 21:15:39', 'Organizador', 'Persona'),
(71, 'lola@ejemplo.com', '$2y$10$guJujO3JDKjeI5EHEKofY.MfVK3FpoHwYCdF/6avYP.7YR9y4Kk96', '2024-12-06 21:51:38', 'Voluntario', 'Persona'),
(75, 'zunigagerardo541@gmail.com', '$2y$10$XNbVDQ1L15g1kUozE2D09OokuQNNo.pUlIwaJOmY1TvVN05PGkXfS', '2024-12-06 22:12:46', 'Voluntario', 'Empresa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voluntariado`
--

CREATE TABLE `voluntariado` (
  `idVoluntario` int(11) NOT NULL,
  `disponibilidad` varchar(255) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `proyecto_idProyecto` int(11) NOT NULL
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
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idProyecto` (`idProyecto`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`),
  ADD KEY `fk_empresa_usuario1_idx` (`id_usuario`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodopago`);

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
-- Indices de la tabla `recompensa_usuario`
--
ALTER TABLE `recompensa_usuario`
  ADD PRIMARY KEY (`idRecompensaUsuario`),
  ADD KEY `fk_recompensa_usuario_recompensa` (`idRecompensa`),
  ADD KEY `fk_recompensa_usuario_usuario` (`idUsuario`),
  ADD KEY `fk_recompensa_usuario_donacion` (`idDonacion`);

--
-- Indices de la tabla `riesgo`
--
ALTER TABLE `riesgo`
  ADD PRIMARY KEY (`idRiesgo`),
  ADD KEY `riesgo_ibfk_1` (`idProyecto`);

--
-- Indices de la tabla `tasa_cambio`
--
ALTER TABLE `tasa_cambio`
  ADD PRIMARY KEY (`moneda_origen`,`moneda_destino`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `voluntariado`
--
ALTER TABLE `voluntariado`
  ADD PRIMARY KEY (`idVoluntario`),
  ADD KEY `voluntario_ibfk_1` (`idUsuario`),
  ADD KEY `fk_voluntariado_proyecto1_idx` (`proyecto_idProyecto`);

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
  MODIFY `idDonacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodopago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `IdPersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `idProyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  MODIFY `idRecompensa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `recompensa_usuario`
--
ALTER TABLE `recompensa_usuario`
  MODIFY `idRecompensaUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `riesgo`
--
ALTER TABLE `riesgo`
  MODIFY `idRiesgo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `voluntariado`
--
ALTER TABLE `voluntariado`
  MODIFY `idVoluntario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Filtros para la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `fk_empresa_usuario1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `recompensa_usuario`
--
ALTER TABLE `recompensa_usuario`
  ADD CONSTRAINT `fk_recompensa_usuario_donacion` FOREIGN KEY (`idDonacion`) REFERENCES `donacion` (`idDonacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recompensa_usuario_recompensa` FOREIGN KEY (`idRecompensa`) REFERENCES `recompensa` (`idRecompensa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recompensa_usuario_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `riesgo`
--
ALTER TABLE `riesgo`
  ADD CONSTRAINT `riesgo_ibfk_1` FOREIGN KEY (`idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `voluntariado`
--
ALTER TABLE `voluntariado`
  ADD CONSTRAINT `fk_voluntariado_proyecto1` FOREIGN KEY (`proyecto_idProyecto`) REFERENCES `proyecto` (`idProyecto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `voluntario_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
