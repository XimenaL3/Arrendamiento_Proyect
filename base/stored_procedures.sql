USE SunlightGardensDB;

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

-- PROCEDIMIENTO: REGISTRAR VISITA DE COBRANZA

DELIMITER $$

CREATE PROCEDURE sp_RegistrarVisita(

    IN pIdUsuario INT,
    IN pIdInquilino INT,
    IN pFechaVisita DATETIME,
    IN pObservaciones TEXT

)
BEGIN

    INSERT INTO Visitas_Cobranza(

        idUsuario,
        idInquilino,
        FechaVisita,
        Observaciones

    )
    VALUES(

        pIdUsuario,
        pIdInquilino,
        pFechaVisita,
        pObservaciones

    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR VISITA DE COBRANZA

DELIMITER $$

CREATE PROCEDURE sp_EditarVisita(

    IN pIdVisita INT,

    IN pIdUsuario INT,
    IN pIdInquilino INT,
    IN pFechaVisita DATETIME,
    IN pObservaciones TEXT

)
BEGIN

    UPDATE Visitas_Cobranza
    SET

        idUsuario = pIdUsuario,
        idInquilino = pIdInquilino,
        FechaVisita = pFechaVisita,
        Observaciones = pObservaciones

    WHERE idVisita = pIdVisita;

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

USE SunlightGardensDB;

-- =========================================
-- PROCEDIMIENTO: REGISTRAR PAGO / COBRO
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_RegistrarCobro(

    IN pIdContrato INT,
    IN pIdTienda INT,
    IN pIdUsuario INT,
    IN pMontoPagado DECIMAL(10,2),
    IN pTipoPago ENUM('Completo','Abono')

)
BEGIN

    DECLARE vMontoPendiente DECIMAL(10,2);
    DECLARE vIdAdeudo INT;

    -- OBTENER ADEUDO PENDIENTE
    SELECT idAdeudo, MontoPendiente
    INTO vIdAdeudo, vMontoPendiente
    FROM Adeudos
    WHERE idContrato = pIdContrato
    AND Estado = 'Pendiente'
    LIMIT 1;

    -- REGISTRAR PAGO
    INSERT INTO Pagos(
        idContrato,
        idTienda,
        idUsuario,
        MontoPagado,
        TipoPago
    )
    VALUES(
        pIdContrato,
        pIdTienda,
        pIdUsuario,
        pMontoPagado,
        pTipoPago
    );

    -- ACTUALIZAR ADEUDO
    UPDATE Adeudos
    SET
        MontoPendiente = MontoPendiente - pMontoPagado
    WHERE idAdeudo = vIdAdeudo;

    -- SI YA SE LIQUIDÓ
    UPDATE Adeudos
    SET Estado = 'Liquidado'
    WHERE idAdeudo = vIdAdeudo
    AND MontoPendiente <= 0;

END $$

DELIMITER ;

-- PROCEDIMIENTO: SOLICITAR ABONO

DELIMITER $$

CREATE PROCEDURE sp_SolicitarAbono(

    IN pIdUsuario INT,
    IN pIdInquilino INT,
    IN pMontoMinimoAceptado DECIMAL(10,2),
    IN pFechaExpiracion DATE

)
BEGIN

    INSERT INTO Autorizaciones_Abono(
        idUsuario,
        idInquilino,
        MontoMinimoAceptado,
        FechaExpiracionAutorizacion
    )
    VALUES(
        pIdUsuario,
        pIdInquilino,
        pMontoMinimoAceptado,
        pFechaExpiracion
    );

END $$

DELIMITER ;

-- PROCEDIMIENTO: REGISTRAR ABONO AUTORIZADO

DELIMITER $$

CREATE PROCEDURE sp_RegistrarAbono(

    IN pIdContrato INT,
    IN pIdTienda INT,
    IN pIdUsuario INT,
    IN pIdAutorizacion INT,
    IN pMontoPagado DECIMAL(10,2)

)
BEGIN

    DECLARE vMontoPendiente DECIMAL(10,2);
    DECLARE vMontoMinimo DECIMAL(10,2);
    DECLARE vIdAdeudo INT;

    -- OBTENER MONTO MÍNIMO AUTORIZADO
    SELECT MontoMinimoAceptado
    INTO vMontoMinimo
    FROM Autorizaciones_Abono
    WHERE idAutorizacion = pIdAutorizacion;

    -- VALIDAR MONTO
    IF pMontoPagado >= vMontoMinimo THEN

        -- OBTENER ADEUDO
        SELECT idAdeudo, MontoPendiente
        INTO vIdAdeudo, vMontoPendiente
        FROM Adeudos
        WHERE idContrato = pIdContrato
        AND Estado = 'Pendiente'
        LIMIT 1;

        -- REGISTRAR PAGO
        INSERT INTO Pagos(
            idContrato,
            idTienda,
            idUsuario,
            idAutorizacion,
            MontoPagado,
            TipoPago
        )
        VALUES(
            pIdContrato,
            pIdTienda,
            pIdUsuario,
            pIdAutorizacion,
            pMontoPagado,
            'Abono'
        );

        -- DESCONTAR ADEUDO
        UPDATE Adeudos
        SET
            MontoPendiente = MontoPendiente - pMontoPagado
        WHERE idAdeudo = vIdAdeudo;

        -- LIQUIDAR SI YA TERMINÓ
        UPDATE Adeudos
        SET Estado = 'Liquidado'
        WHERE idAdeudo = vIdAdeudo
        AND MontoPendiente <= 0;

    END IF;

END $$

DELIMITER ;

-- PROCEDIMIENTO: EDITAR AUTORIZACIÓN ABONO

DELIMITER $$

CREATE PROCEDURE sp_EditarAutorizacionAbono(

    IN pIdAutorizacion INT,
    IN pMontoMinimoAceptado DECIMAL(10,2),
    IN pFechaExpiracion DATE

)
BEGIN

    UPDATE Autorizaciones_Abono
    SET
        MontoMinimoAceptado = pMontoMinimoAceptado,
        FechaExpiracionAutorizacion = pFechaExpiracion
    WHERE idAutorizacion = pIdAutorizacion;

END $$

DELIMITER ;

USE SunlightGardensDB;

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

    IN pIdUsuario INT,
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

        idUsuario,
        idPropiedad,

        Titulo,
        Descripcion,

        TipoReporte,
        Prioridad,

        Evidencia

    )
    VALUES(

        pIdUsuario,
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