-- PROCEDIMIENTO: REGISTRAR TRABAJADOR

DELIMITER $$

CREATE PROCEDURE sp_RegistrarTrabajador(

    IN pNombre VARCHAR(100),
    IN pApellidoP VARCHAR(100),
    IN pApellidoM VARCHAR(100),
    IN pTelefono VARCHAR(20),
    IN pCorreo VARCHAR(150),
    IN pImagen VARCHAR(255),

    IN pUsuario VARCHAR(100),
    IN pContrasena VARCHAR(255)

)
BEGIN

    DECLARE vIdPersona INT;
    DECLARE vIdRolCajero INT;

    -- OBTENER EL ID DEL ROL CAJERO
    SELECT idRol
    INTO vIdRolCajero
    FROM Roles
    WHERE NombreRol = 'Cajero'
    LIMIT 1;

    -- INSERTAR PERSONA
    INSERT INTO Personas(
        Nombre,
        ApellidoP,
        ApellidoM,
        Telefono,
        Correo,
        Imagen
    )
    VALUES(
        pNombre,
        pApellidoP,
        pApellidoM,
        pTelefono,
        pCorreo,
        pImagen
    );

    -- OBTENER ID GENERADO
    SET vIdPersona = LAST_INSERT_ID();

    -- INSERTAR USUARIO CON ROL CAJERO POR DEFECTO
    INSERT INTO Usuarios(
        idPersona,
        idRol,
        Usuario,
        Contrasena
    )
    VALUES(
        vIdPersona,
        vIdRolCajero,
        pUsuario,
        pContrasena
    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR TRABAJADOR

DELIMITER $$

CREATE PROCEDURE sp_EditarTrabajador(

    IN pIdUsuario INT,

    IN pNombre VARCHAR(100),
    IN pApellidoP VARCHAR(100),
    IN pApellidoM VARCHAR(100),
    IN pTelefono VARCHAR(20),
    IN pCorreo VARCHAR(150),
    IN pImagen VARCHAR(255),

    IN pIdRol INT,
    IN pUsuario VARCHAR(100),
    IN pContrasena VARCHAR(255)

)
BEGIN

    DECLARE vIdPersona INT;

    -- OBTENER ID PERSONA
    SELECT idPersona
    INTO vIdPersona
    FROM Usuarios
    WHERE idUsuario = pIdUsuario;

    -- ACTUALIZAR PERSONA
    UPDATE Personas
    SET
        Nombre = pNombre,
        ApellidoP = pApellidoP,
        ApellidoM = pApellidoM,
        Telefono = pTelefono,
        Correo = pCorreo,
        Imagen = pImagen
    WHERE idPersona = vIdPersona;

    -- ACTUALIZAR USUARIO
    UPDATE Usuarios
    SET
        idRol = pIdRol,
        Usuario = pUsuario,
        Contrasena = pContrasena
    WHERE idUsuario = pIdUsuario;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR CLIENTE / INQUILINO

DELIMITER $$

CREATE PROCEDURE sp_RegistrarCliente(

    IN pNombre VARCHAR(100),
    IN pApellidoP VARCHAR(100),
    IN pApellidoM VARCHAR(100),
    IN pTelefono VARCHAR(20),
    IN pCorreo VARCHAR(150),
    IN pImagen VARCHAR(255),

    IN pHistorialCrediticio ENUM('Bueno','Malo','Nuevo'),
    IN pRegistroDeudasPrevias BOOLEAN

)
BEGIN

    DECLARE vIdPersona INT;

    -- INSERTAR PERSONA
    INSERT INTO Personas(
        Nombre,
        ApellidoP,
        ApellidoM,
        Telefono,
        Correo,
        Imagen
    )
    VALUES(
        pNombre,
        pApellidoP,
        pApellidoM,
        pTelefono,
        pCorreo,
        pImagen
    );

    -- OBTENER ID DE PERSONA
    SET vIdPersona = LAST_INSERT_ID();

    -- INSERTAR INQUILINO
    INSERT INTO Inquilinos(
        idPersona,
        HistorialCrediticio,
        RegistroDeudasPrevias
    )
    VALUES(
        vIdPersona,
        pHistorialCrediticio,
        pRegistroDeudasPrevias
    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR CLIENTE / INQUILINO

DELIMITER $$

CREATE PROCEDURE sp_EditarCliente(

    IN pIdInquilino INT,

    IN pNombre VARCHAR(100),
    IN pApellidoP VARCHAR(100),
    IN pApellidoM VARCHAR(100),
    IN pTelefono VARCHAR(20),
    IN pCorreo VARCHAR(150),
    IN pImagen VARCHAR(255),

    IN pHistorialCrediticio ENUM('Bueno','Malo','Nuevo'),
    IN pRegistroDeudasPrevias BOOLEAN

)
BEGIN

    DECLARE vIdPersona INT;

    -- OBTENER ID PERSONA
    SELECT idPersona
    INTO vIdPersona
    FROM Inquilinos
    WHERE idInquilino = pIdInquilino;

    -- ACTUALIZAR PERSONA
    UPDATE Personas
    SET
        Nombre = pNombre,
        ApellidoP = pApellidoP,
        ApellidoM = pApellidoM,
        Telefono = pTelefono,
        Correo = pCorreo,
        Imagen = pImagen
    WHERE idPersona = vIdPersona;

    -- ACTUALIZAR INQUILINO
    UPDATE Inquilinos
    SET
        HistorialCrediticio = pHistorialCrediticio,
        RegistroDeudasPrevias = pRegistroDeudasPrevias
    WHERE idInquilino = pIdInquilino;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR VISITA

DELIMITER $$

CREATE PROCEDURE sp_RegistrarVisita(

    IN pIdUsuario INT,
    IN pIdInquilino INT,
    IN pFechaVisita DATETIME,
    IN pObservaciones TEXT,
    IN pEstatus ENUM('Pendiente','En atencion','Atendida','Cancelada')

)
BEGIN

    INSERT INTO Visitas_Cobranza(

        idUsuario,
        idInquilino,
        FechaVisita,
        Observaciones,
        Estatus

    )
    VALUES(

        pIdUsuario,
        pIdInquilino,
        pFechaVisita,
        pObservaciones,
        pEstatus

    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR VISITA

DELIMITER $$

CREATE PROCEDURE sp_EditarVisita(

    IN pIdVisita INT,

    IN pIdUsuario INT,
    IN pIdInquilino INT,
    IN pFechaVisita DATETIME,
    IN pObservaciones TEXT,
    IN pEstatus ENUM('Pendiente','En atencion','Atendida','Cancelada')

)
BEGIN

    UPDATE Visitas_Cobranza
    SET

        idUsuario = pIdUsuario,
        idInquilino = pIdInquilino,
        FechaVisita = pFechaVisita,
        Observaciones = pObservaciones,
        Estatus = pEstatus

    WHERE idVisita = pIdVisita;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR ARRENDAMIENTO

DELIMITER $$

CREATE PROCEDURE sp_RegistrarArrendamiento(

    IN pIdInquilino INT,
    IN pIdPropiedad INT,
    IN pFechaInicio DATE,
    IN pFechaFin DATE,
    IN pMontoRenta DECIMAL(10,2),
    IN pMontoDeposito DECIMAL(10,2),
    IN pObservaciones TEXT,
    IN pPermitirAbonos BOOLEAN,
    IN pEvidencia VARCHAR(255)

)
BEGIN

    INSERT INTO ContratosArrendamiento(

        idInquilino,
        idPropiedad,
        FechaInicio,
        FechaFin,
        MontoRenta,
        MontoDeposito,
        Observaciones,
        PermitirAbonos,
        Evidencia

    )
    VALUES(

        pIdInquilino,
        pIdPropiedad,
        pFechaInicio,
        pFechaFin,
        pMontoRenta,
        pMontoDeposito,
        pObservaciones,
        pPermitirAbonos,
        pEvidencia

    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR ARRENDAMIENTO

DELIMITER $$

CREATE PROCEDURE sp_EditarArrendamiento(

    IN pIdContrato INT,

    IN pIdInquilino INT,
    IN pIdPropiedad INT,
    IN pFechaInicio DATE,
    IN pFechaFin DATE,
    IN pMontoRenta DECIMAL(10,2),
    IN pMontoDeposito DECIMAL(10,2),
    IN pObservaciones TEXT,
    IN pPermitirAbonos BOOLEAN,
    IN pEvidencia VARCHAR(255)

)
BEGIN

    UPDATE ContratosArrendamiento
    SET

        idInquilino = pIdInquilino,
        idPropiedad = pIdPropiedad,
        FechaInicio = pFechaInicio,
        FechaFin = pFechaFin,
        MontoRenta = pMontoRenta,
        MontoDeposito = pMontoDeposito,
        Observaciones = pObservaciones,
        PermitirAbonos = pPermitirAbonos,
        Evidencia = pEvidencia

    WHERE idContrato = pIdContrato;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR PROPIEDAD

DELIMITER $$

CREATE PROCEDURE sp_RegistrarPropiedad(

    IN pTipoPropiedad ENUM('Local comercial','Casa','Edificio'),
    IN pNumeroIdentificador VARCHAR(50),
    IN pDescripcion TEXT,
    IN pEstadoFisico ENUM('Buenas condiciones','Malas condiciones','En mantenimiento'),
    IN pEstadoDisponibilidad ENUM('Disponible','Rentado','Aspecto Legal'),
    IN pImagen VARCHAR(255)

)
BEGIN

    INSERT INTO Propiedades(
        TipoPropiedad,
        NumeroIdentificador,
        Descripcion,
        EstadoFisico,
        EstadoDisponibilidad,
        Imagen
    )
    VALUES(
        pTipoPropiedad,
        pNumeroIdentificador,
        pDescripcion,
        pEstadoFisico,
        pEstadoDisponibilidad,
        pImagen
    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR PROPIEDAD

DELIMITER $$

CREATE PROCEDURE sp_EditarPropiedad(

    IN pIdPropiedad INT,

    IN pTipoPropiedad ENUM('Local comercial','Casa','Edificio'),
    IN pNumeroIdentificador VARCHAR(50),
    IN pDescripcion TEXT,
    IN pEstadoFisico ENUM('Buenas condiciones','Malas condiciones','En mantenimiento'),
    IN pEstadoDisponibilidad ENUM('Disponible','Rentado','Aspecto Legal'),
    IN pImagen VARCHAR(255)

)
BEGIN

    UPDATE Propiedades
    SET
        TipoPropiedad = pTipoPropiedad,
        NumeroIdentificador = pNumeroIdentificador,
        Descripcion = pDescripcion,
        EstadoFisico = pEstadoFisico,
        EstadoDisponibilidad = pEstadoDisponibilidad,
        Imagen = pImagen
    WHERE idPropiedad = pIdPropiedad;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR PRODUCTO

DELIMITER $$

CREATE PROCEDURE sp_RegistrarProducto(

    IN pNombreProducto VARCHAR(150),
    IN pCantidadDisponible INT,
    IN pDescripcion TEXT,
    IN pImagen VARCHAR(255)

)
BEGIN

    INSERT INTO Bodega_Inventario(
        NombreProducto,
        CantidadDisponible,
        Descripcion,
        Imagen
    )
    VALUES(
        pNombreProducto,
        pCantidadDisponible,
        pDescripcion,
        pImagen
    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR PRODUCTO

DELIMITER $$

CREATE PROCEDURE sp_EditarProducto(

    IN pIdProducto INT,
    IN pNombreProducto VARCHAR(150),
    IN pCantidadDisponible INT,
    IN pDescripcion TEXT,
    IN pImagen VARCHAR(255)

)
BEGIN

    UPDATE Bodega_Inventario
    SET
        NombreProducto = pNombreProducto,
        CantidadDisponible = pCantidadDisponible,
        Descripcion = pDescripcion,
        Imagen = pImagen
    WHERE idProducto = pIdProducto;

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR REPORTE

DELIMITER $$

CREATE PROCEDURE sp_RegistrarReporte(

    IN pIdInquilino INT,
    IN pIdPropiedad INT,

    IN pTitulo VARCHAR(150),
    IN pDescripcion TEXT,

    IN pTipoReporte ENUM(
        'Mantenimiento',
        'Cobranza',
        'Legal',
        'Inventario',
        'General'
    ),

    IN pPrioridad ENUM(
        'Baja',
        'Media',
        'Alta'
    ),

    IN pEvidencia VARCHAR(255)

)
BEGIN

    INSERT INTO Reportes(

        idInquilino,
        idPropiedad,

        Titulo,
        Descripcion,

        TipoReporte,
        Prioridad,

        Evidencia

    )
    VALUES(

        pIdInquilino,
        pIdPropiedad,

        pTitulo,
        pDescripcion,

        pTipoReporte,
        pPrioridad,

        pEvidencia

    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR REPORTE

DELIMITER $$

CREATE PROCEDURE sp_EditarReporte(

    IN pIdReporte INT,

    IN pTitulo VARCHAR(150),
    IN pDescripcion TEXT,

    IN pTipoReporte ENUM(
        'Mantenimiento',
        'Cobranza',
        'Legal',
        'Inventario',
        'General'
    ),

    IN pPrioridad ENUM(
        'Baja',
        'Media',
        'Alta'
    ),

    IN pEstado ENUM(
        'Pendiente',
        'En proceso',
        'Finalizado',
        'Cancelado'
    ),

    IN pEvidencia VARCHAR(255)

)
BEGIN

    UPDATE Reportes
    SET

        Titulo = pTitulo,
        Descripcion = pDescripcion,

        TipoReporte = pTipoReporte,
        Prioridad = pPrioridad,
        Estado = pEstado,

        Evidencia = pEvidencia

    WHERE idReporte = pIdReporte;

END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_RentarPropiedad(

    IN p_idInquilino INT,
    IN p_idPropiedad INT,
    IN p_FechaInicio DATE,
    IN p_FechaFin DATE,
    IN p_MontoRenta DECIMAL(10,2),
    IN p_MontoDeposito DECIMAL(10,2),
    IN p_Observaciones TEXT,
    IN p_PermitirAbonos BOOLEAN,
    IN p_Evidencia VARCHAR(255),

    IN p_Servicios JSON

)
BEGIN

    DECLARE v_estado VARCHAR(50);

    DECLARE v_total INT DEFAULT 0;
    DECLARE v_index INT DEFAULT 0;

    DECLARE v_idContrato INT;

    DECLARE v_idServicio INT;
    DECLARE v_manejoPorPorcentaje BOOLEAN;
    DECLARE v_porcentajeAsignado DECIMAL(10,2);
    DECLARE v_costoFijo DECIMAL(10,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error al procesar la renta' AS Resultado;
    END;

    START TRANSACTION;

    SELECT EstadoDisponibilidad
    INTO v_estado
    FROM Propiedades
    WHERE idPropiedad = p_idPropiedad;

    IF v_estado IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La propiedad no existe.';

    ELSEIF v_estado != 'Disponible' THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La propiedad no está disponible.';

    ELSE

        INSERT INTO ContratosArrendamiento(
            idInquilino,
            idPropiedad,
            FechaInicio,
            FechaFin,
            MontoRenta,
            MontoDeposito,
            Observaciones,
            PermitirAbonos,
            Evidencia,
            EstadoContrato
        )
        VALUES(
            p_idInquilino,
            p_idPropiedad,
            p_FechaInicio,
            p_FechaFin,
            p_MontoRenta,
            p_MontoDeposito,
            p_Observaciones,
            p_PermitirAbonos,
            p_Evidencia,
            'Activo'
        );

        SET v_idContrato = LAST_INSERT_ID();

        UPDATE Propiedades
        SET EstadoDisponibilidad = 'Rentado'
        WHERE idPropiedad = p_idPropiedad;

        INSERT INTO Adeudos(
            idContrato,
            MontoTotal,
            MontoPendiente,
            FechaLimite,
            Estado
        )
        VALUES(
            v_idContrato,
            p_MontoRenta,
            p_MontoRenta,
            p_FechaFin,
            'Pendiente'
        );

        SET v_total = JSON_LENGTH(p_Servicios);

        WHILE v_index < v_total DO

            SET v_idServicio = CAST(
                JSON_UNQUOTE(JSON_EXTRACT(
                    p_Servicios,
                    CONCAT('$[', v_index, '].idServicio')
                )) AS UNSIGNED
            );

            SET v_manejoPorPorcentaje = CAST(
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(
                    p_Servicios,
                    CONCAT('$[', v_index, '].ManejoPorPorcentaje')
                )), 0) AS UNSIGNED
            );

            SET v_porcentajeAsignado = CAST(
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(
                    p_Servicios,
                    CONCAT('$[', v_index, '].PorcentajeAsignado')
                )), 0) AS DECIMAL(10,2)
            );

            SET v_costoFijo = CAST(
                COALESCE(JSON_UNQUOTE(JSON_EXTRACT(
                    p_Servicios,
                    CONCAT('$[', v_index, '].CostoFijo')
                )), 0) AS DECIMAL(10,2)
            );

            INSERT INTO Propiedad_Servicios(
                idPropiedad,
                idServicio,
                ManejoPorPorcentaje,
                PorcentajeAsignado,
                CostoFijo
            )
            VALUES(
                p_idPropiedad,
                v_idServicio,
                v_manejoPorPorcentaje,
                v_porcentajeAsignado,
                v_costoFijo
            );

            SET v_index = v_index + 1;

        END WHILE;

        COMMIT;

        SELECT 'Renta procesada exitosamente' AS Resultado;

    END IF;

END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_AgregarTiendaCobro(

    IN pNombreTienda VARCHAR(150),
    IN pIdPropiedad INT

)
BEGIN

    INSERT INTO Tiendas_Cobro(

        NombreTienda,
        idPropiedad

    )
    VALUES(

        pNombreTienda,
        pIdPropiedad

    );

END $$

DELIMITER ;

-- =========================================
-- PROCEDIMIENTO:
-- REGISTRAR COBRO COMPLETO
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_RegistrarCobro(

    IN pIdContrato INT,
    IN pIdTienda INT,
    IN pIdUsuario INT,
    IN pMontoPagado DECIMAL(10,2)

)
BEGIN

    -- =====================================
    -- VARIABLES
    -- =====================================

    DECLARE vIdAdeudo INT;
    DECLARE vMontoPendiente DECIMAL(10,2);
    DECLARE vExisteContrato INT DEFAULT 0;
    DECLARE vExisteTienda INT DEFAULT 0;
    DECLARE vExisteUsuario INT DEFAULT 0;

    -- =====================================
    -- MANEJO DE ERRORES
    -- =====================================

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN

        ROLLBACK;

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Error al registrar el pago completo';

    END;

    START TRANSACTION;

    -- =====================================
    -- VALIDAR CONTRATO
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteContrato
    FROM ContratosArrendamiento
    WHERE idContrato = pIdContrato;

    IF vExisteContrato = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'El contrato no existe';

    END IF;

    -- =====================================
    -- VALIDAR TIENDA
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteTienda
    FROM Tiendas_Cobro
    WHERE idTienda = pIdTienda;

    IF vExisteTienda = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'La tienda no existe';

    END IF;

    -- =====================================
    -- VALIDAR USUARIO
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteUsuario
    FROM Usuarios
    WHERE idUsuario = pIdUsuario;

    IF vExisteUsuario = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'El usuario no existe';

    END IF;

    -- =====================================
    -- VALIDAR MONTO
    -- =====================================

    IF pMontoPagado IS NULL
    OR pMontoPagado <= 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Monto inválido';

    END IF;

    -- =====================================
    -- OBTENER ADEUDO
    -- =====================================

    SELECT
        idAdeudo,
        MontoPendiente
    INTO
        vIdAdeudo,
        vMontoPendiente
    FROM Adeudos
    WHERE idContrato = pIdContrato
    AND Estado = 'Pendiente'
    LIMIT 1
    FOR UPDATE;

    -- =====================================
    -- VALIDAR ADEUDO
    -- =====================================

    IF vIdAdeudo IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'No existe adeudo pendiente';

    END IF;

    -- =====================================
    -- EVITAR SOBREPAGO
    -- =====================================

    IF pMontoPagado > vMontoPendiente THEN

        SET pMontoPagado = vMontoPendiente;

    END IF;

    -- =====================================
    -- INSERTAR PAGO
    -- =====================================

    INSERT INTO Pagos(

        idContrato,
        idTienda,
        idUsuario,
        idSolicitud,
        FechaPago,
        MontoPagado,
        TipoPago

    )
    VALUES(

        pIdContrato,
        pIdTienda,
        pIdUsuario,
        NULL,
        NOW(),
        pMontoPagado,
        'Completo'

    );

    -- =====================================
    -- ACTUALIZAR ADEUDO
    -- =====================================

    UPDATE Adeudos
    SET

        MontoPendiente =
        MontoPendiente - pMontoPagado,

        Estado = CASE

            WHEN
            (MontoPendiente - pMontoPagado) <= 0
            THEN 'Liquidado'

            ELSE 'Pendiente'

        END

    WHERE idAdeudo = vIdAdeudo;

    -- =====================================
    -- CREAR NOTIFICACION
    -- =====================================

    INSERT INTO Notificaciones(

        idUsuario,
        Titulo,
        Mensaje,
        TipoNotificacion,
        Estado,
        FechaNotificacion

    )
    VALUES(

        pIdUsuario,

        'Pago registrado',

        CONCAT(
            'Se registró un pago completo de $',
            pMontoPagado,
            ' para el contrato #',
            pIdContrato
        ),

        'Pago',

        'No leida',

        NOW()

    );

    COMMIT;

END$$

DELIMITER ;

-- =========================================
-- PROCEDIMIENTO:
-- SOLICITAR ABONO
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_SolicitarAbono(

    IN pIdUsuarioSolicita INT,
    IN pIdContrato INT,
    IN pIdInquilino INT,
    IN pMontoSolicitado DECIMAL(10,2),
    IN pObservaciones TEXT

)
BEGIN

    -- =====================================
    -- VARIABLES
    -- =====================================

    DECLARE vPermitirAbonos BOOLEAN;
    DECLARE vMontoPendiente DECIMAL(10,2);
    DECLARE vExisteContrato INT DEFAULT 0;
    DECLARE vExisteUsuario INT DEFAULT 0;
    DECLARE vExisteInquilino INT DEFAULT 0;

    -- =====================================
    -- MANEJO DE ERRORES
    -- =====================================

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN

        ROLLBACK;

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Error al solicitar abono';

    END;

    START TRANSACTION;

    -- =====================================
    -- VALIDAR USUARIO
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteUsuario
    FROM Usuarios
    WHERE idUsuario = pIdUsuarioSolicita;

    IF vExisteUsuario = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Usuario inválido';

    END IF;

    -- =====================================
    -- VALIDAR CONTRATO
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteContrato
    FROM ContratosArrendamiento
    WHERE idContrato = pIdContrato;

    IF vExisteContrato = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Contrato inválido';

    END IF;

    -- =====================================
    -- VALIDAR INQUILINO
    -- =====================================

    SELECT COUNT(*)
    INTO vExisteInquilino
    FROM Inquilinos
    WHERE idInquilino = pIdInquilino;

    IF vExisteInquilino = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Inquilino inválido';

    END IF;

    -- =====================================
    -- VALIDAR MONTO
    -- =====================================

    IF pMontoSolicitado IS NULL
    OR pMontoSolicitado <= 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Monto inválido';

    END IF;

    -- =====================================
    -- OBTENER ADEUDO
    -- =====================================

    SELECT
        PermitirAbonos,
        MontoPendiente
    INTO
        vPermitirAbonos,
        vMontoPendiente
    FROM Adeudos
    WHERE idContrato = pIdContrato
    AND Estado = 'Pendiente'
    LIMIT 1
    FOR UPDATE;

    -- =====================================
    -- VALIDAR ADEUDO
    -- =====================================

    IF vMontoPendiente IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'No existe adeudo pendiente';

    END IF;

    -- =====================================
    -- VALIDAR ABONOS
    -- =====================================

    IF vPermitirAbonos = FALSE THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Este adeudo no permite abonos';

    END IF;

    -- =====================================
    -- EVITAR SOBREPAGO
    -- =====================================

    IF pMontoSolicitado > vMontoPendiente THEN

        SET pMontoSolicitado = vMontoPendiente;

    END IF;

    -- =====================================
    -- INSERTAR SOLICITUD
    -- =====================================

    INSERT INTO Solicitudes_Abono(

        idUsuarioSolicita,
        idAdministrador,
        idContrato,
        idInquilino,
        MontoSolicitado,
        MontoAutorizado,
        FechaLimitePago,
        Observaciones,
        EstadoSolicitud,
        FechaSolicitud,
        FechaRevision

    )
    VALUES(

        pIdUsuarioSolicita,
        NULL,
        pIdContrato,
        pIdInquilino,
        pMontoSolicitado,
        NULL,
        NULL,
        pObservaciones,
        'Pendiente',
        NOW(),
        NULL

    );

    -- =====================================
    -- NOTIFICACION
    -- =====================================

    INSERT INTO Notificaciones(

        idUsuario,
        Titulo,
        Mensaje,
        TipoNotificacion,
        Estado,
        FechaNotificacion

    )
    VALUES(

        pIdUsuarioSolicita,

        'Solicitud de abono',

        CONCAT(
            'Se solicitó un abono de $',
            pMontoSolicitado,
            ' para el contrato #',
            pIdContrato
        ),

        'Cobranza',

        'No leida',

        NOW()

    );

    COMMIT;

END$$

DELIMITER ;

-- =========================================
-- PROCEDIMIENTO:
-- REGISTRAR ABONO
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_RegistrarAbono(

    IN pIdContrato INT,
    IN pIdTienda INT,
    IN pIdUsuario INT,
    IN pIdSolicitud INT,
    IN pMontoPagado DECIMAL(10,2)

)
BEGIN

    -- =====================================
    -- VARIABLES
    -- =====================================

    DECLARE vIdAdeudo INT;
    DECLARE vMontoPendiente DECIMAL(10,2);
    DECLARE vNuevoMonto DECIMAL(10,2);
    DECLARE vEstadoSolicitud VARCHAR(20);

    -- =====================================
    -- MANEJO DE ERRORES
    -- =====================================

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN

        ROLLBACK;

        RESIGNAL;

    END;

    START TRANSACTION;

    -- =====================================
    -- VALIDAR MONTO
    -- =====================================

    IF pMontoPagado IS NULL
    OR pMontoPagado <= 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Monto inválido';

    END IF;

    -- =====================================
    -- VALIDAR SOLICITUD
    -- =====================================

    SELECT EstadoSolicitud
    INTO vEstadoSolicitud
    FROM Solicitudes_Abono
    WHERE idSolicitud = pIdSolicitud
    LIMIT 1
    FOR UPDATE;

    IF vEstadoSolicitud IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Solicitud inexistente';

    END IF;

    -- =====================================
    -- OBTENER ADEUDO
    -- =====================================

    SELECT
        idAdeudo,
        MontoPendiente
    INTO
        vIdAdeudo,
        vMontoPendiente
    FROM Adeudos
    WHERE idContrato = pIdContrato
    AND Estado = 'Pendiente'
    LIMIT 1
    FOR UPDATE;

    IF vIdAdeudo IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'No existe adeudo pendiente';

    END IF;

    -- =====================================
    -- VALIDAR EXCESO
    -- =====================================

    IF pMontoPagado > vMontoPendiente THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'El monto excede la deuda pendiente';

    END IF;

    -- =====================================
    -- INSERTAR PAGO
    -- =====================================

    INSERT INTO Pagos(

        idContrato,
        idTienda,
        idUsuario,
        idSolicitud,
        FechaPago,
        MontoPagado,
        TipoPago

    )
    VALUES(

        pIdContrato,
        pIdTienda,
        pIdUsuario,
        pIdSolicitud,
        NOW(),
        pMontoPagado,
        'Abono'

    );

    -- =====================================
    -- CALCULAR MONTO
    -- =====================================

    SET vNuevoMonto =
        vMontoPendiente - pMontoPagado;

    IF vNuevoMonto < 0 THEN

        SET vNuevoMonto = 0;

    END IF;

    -- =====================================
    -- ACTUALIZAR ADEUDO
    -- =====================================

    UPDATE Adeudos
    SET

        MontoPendiente = vNuevoMonto,

        Estado = CASE

            WHEN vNuevoMonto = 0
            THEN 'Liquidado'

            ELSE 'Pendiente'

        END

    WHERE idAdeudo = vIdAdeudo;

    -- =====================================
    -- ACTUALIZAR SOLICITUD
    -- =====================================

    UPDATE Solicitudes_Abono
    SET

        idAdministrador = pIdUsuario,

        MontoAutorizado = pMontoPagado,

        EstadoSolicitud = CASE

            WHEN vNuevoMonto = 0
            THEN 'Pagada'

            ELSE 'Aprobada'

        END,

        FechaRevision = NOW()

    WHERE idSolicitud = pIdSolicitud;

    -- =====================================
    -- HISTORIAL
    -- =====================================

    INSERT INTO Historial_Aprobaciones_Abono(

        idSolicitud,
        idAdministrador,
        Accion,
        Comentario,
        FechaMovimiento

    )
    VALUES(

        pIdSolicitud,
        pIdUsuario,
        'Aprobado',
        'Abono registrado correctamente',
        NOW()

    );

    -- =====================================
    -- NOTIFICACION
    -- =====================================

    INSERT INTO Notificaciones(

        idUsuario,
        Titulo,
        Mensaje,
        TipoNotificacion,
        Estado,
        FechaNotificacion

    )
    VALUES(

        pIdUsuario,

        'Abono registrado',

        CONCAT(
            'Se registró un abono de $',
            pMontoPagado,
            ' para el contrato #',
            pIdContrato
        ),

        'Pago',

        'No leida',

        NOW()

    );

    COMMIT;

END$$

DELIMITER ;

/* =========================================================
PROCEDIMIENTO: DESCONTAR PRODUCTOS DE BODEGA
========================================================= */

DELIMITER $$

CREATE PROCEDURE sp_DescontarProductoBodega(

    IN p_idProducto INT,
    IN p_cantidad INT

)
BEGIN

    DECLARE stockActual INT;

    /* =========================================
    OBTENER STOCK ACTUAL
    ========================================= */

    SELECT CantidadDisponible
    INTO stockActual
    FROM Bodega_Inventario
    WHERE idProducto = p_idProducto;

    /* =========================================
    VALIDAR EXISTENCIA
    ========================================= */

    IF stockActual IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto no existe';

    END IF;

    /* =========================================
    VALIDAR STOCK DISPONIBLE
    ========================================= */

    IF stockActual < p_cantidad THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente en bodega';

    END IF;

    /* =========================================
    DESCONTAR PRODUCTO
    ========================================= */

    UPDATE Bodega_Inventario
    SET CantidadDisponible = CantidadDisponible - p_cantidad
    WHERE idProducto = p_idProducto;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_RegistrarMantenimientoDetalle(
    
    IN p_idReporte INT,
    IN p_idUsuario INT,
    IN p_idProducto INT,
    IN p_TareaRealizada TEXT,
    IN p_FechaFin DATETIME

)

BEGIN

    DECLARE v_idPropiedad INT;
    DECLARE v_FechaInicio DATETIME;

    SELECT idPropiedad, FechaRegistro
    INTO v_idPropiedad, v_FechaInicio
    FROM Reportes
    WHERE idReporte = p_idReporte
    LIMIT 1;

    IF v_idPropiedad IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Reporte no encontrado';
    END IF;

    INSERT INTO Mantenimiento_Detalle(
        idReporte,
        idPropiedad,
        idUsuario,
        idProducto,
        TareaRealizada,
        FechaInicio,
        FechaFin
    )
    VALUES(
        p_idReporte,
        v_idPropiedad,
        p_idUsuario,
        p_idProducto,
        p_TareaRealizada,
        v_FechaInicio,
        p_FechaFin
    );

END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_FinalizarReporte(
    IN p_idReporte INT
)
BEGIN

    UPDATE Reportes
    SET Estado = 'Finalizado'
    WHERE idReporte = p_idReporte;

END $$

DELIMITER ;