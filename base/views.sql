-- VISTA: VISUALIZAR INFORMACION DE TRABAJADORES

CREATE VIEW vw_Trabajadores AS

SELECT

    U.idUsuario,

    P.idPersona,

    CONCAT(
        P.Nombre,
        ' ',
        P.ApellidoP,
        ' ',
        IFNULL(P.ApellidoM, '')
    ) AS NombreCompleto,

    P.Telefono,
    P.Correo,
    P.Imagen,

    R.idRol,
    R.NombreRol,

    U.Usuario

FROM Usuarios U

INNER JOIN Personas P
    ON U.idPersona = P.idPersona

INNER JOIN Roles R
    ON U.idRol = R.idRol;


-- VISTA: VER CLIENTES / INQUILINOS

CREATE VIEW vw_Clientes AS

SELECT

    I.idInquilino,

    P.idPersona,

    CONCAT(
        P.Nombre,
        ' ',
        P.ApellidoP,
        ' ',
        IFNULL(P.ApellidoM,'')
    ) AS NombreCompleto,

    P.Telefono,
    P.Correo,
    P.Imagen,

    I.HistorialCrediticio,
    I.RegistroDeudasPrevias

FROM Inquilinos I

INNER JOIN Personas P
    ON I.idPersona = P.idPersona;


-- VISTA: VISUALIZAR VISITAS DE COBRANZA

CREATE VIEW vw_VisitasCobranza AS

SELECT

    VC.idVisita,

    VC.FechaVisita,

    VC.Observaciones,

    -- DATOS DEL COBRADOR
    U.idUsuario,

    CONCAT(
        PC.Nombre,
        ' ',
        PC.ApellidoP,
        ' ',
        IFNULL(PC.ApellidoM,'')
    ) AS NombreCobrador,

    -- DATOS DEL INQUILINO
    I.idInquilino,

    CONCAT(
        PI.Nombre,
        ' ',
        PI.ApellidoP,
        ' ',
        IFNULL(PI.ApellidoM,'')
    ) AS NombreInquilino,

    PI.Telefono,
    PI.Correo

FROM Visitas_Cobranza VC

INNER JOIN Usuarios U
    ON VC.idUsuario = U.idUsuario

INNER JOIN Personas PC
    ON U.idPersona = PC.idPersona

INNER JOIN Inquilinos I
    ON VC.idInquilino = I.idInquilino

INNER JOIN Personas PI
    ON I.idPersona = PI.idPersona;


-- VISTA: VER VISITAS

CREATE VIEW vw_VisitasCobranza AS

SELECT

    vc.idVisita,

    vc.FechaVisita,

    vc.Observaciones,

    vc.Estatus,

    -- DATOS DEL COBRADOR
    u.idUsuario,

    pu.Nombre AS NombreCobrador,
    pu.ApellidoP AS ApellidoPCobrador,
    pu.ApellidoM AS ApellidoMCobrador,
    pu.Telefono AS TelefonoCobrador,
    pu.Correo AS CorreoCobrador,
    pu.Imagen AS ImagenCobrador,

    r.NombreRol,

    -- DATOS DEL INQUILINO
    i.idInquilino,

    pi.Nombre AS NombreInquilino,
    pi.ApellidoP AS ApellidoPInquilino,
    pi.ApellidoM AS ApellidoMInquilino,
    pi.Telefono AS TelefonoInquilino,
    pi.Correo AS CorreoInquilino,
    pi.Imagen AS ImagenInquilino

FROM Visitas_Cobranza vc

INNER JOIN Usuarios u
    ON vc.idUsuario = u.idUsuario

INNER JOIN Personas pu
    ON u.idPersona = pu.idPersona

INNER JOIN Roles r
    ON u.idRol = r.idRol

INNER JOIN Inquilinos i
    ON vc.idInquilino = i.idInquilino

INNER JOIN Personas pi
    ON i.idPersona = pi.idPersona;

-- VISTA: VER ARRENDAMIENTOS

CREATE VIEW vw_Arrendamientos AS

SELECT

    ca.idContrato,

    ca.FechaInicio,
    ca.FechaFin,

    ca.MontoRenta,
    ca.MontoDeposito,

    ca.Observaciones,
    ca.PermitirAbonos,
    ca.Evidencia,

    -- DATOS DEL INQUILINO
    i.idInquilino,

    p.Nombre AS NombreInquilino,
    p.ApellidoP AS ApellidoPInquilino,
    p.ApellidoM AS ApellidoMInquilino,
    p.Telefono AS TelefonoInquilino,
    p.Correo AS CorreoInquilino,
    p.Imagen AS ImagenInquilino,

    i.HistorialCrediticio,
    i.RegistroDeudasPrevias,

    -- DATOS DE LA PROPIEDAD
    pr.idPropiedad,

    pr.TipoPropiedad,
    pr.NumeroIdentificador,
    pr.Descripcion AS DescripcionPropiedad,
    pr.EstadoFisico,
    pr.EstadoDisponibilidad

FROM ContratosArrendamiento ca

INNER JOIN Inquilinos i
    ON ca.idInquilino = i.idInquilino

INNER JOIN Personas p
    ON i.idPersona = p.idPersona

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad;

-- VISTA: VER PROPIEDADES

CREATE VIEW vw_Propiedades AS

SELECT

    idPropiedad,
    TipoPropiedad,
    NumeroIdentificador,
    Descripcion,
    EstadoFisico,
    EstadoDisponibilidad,
    Imagen

FROM Propiedades;

-- VISTA: HISTORIAL DE COBROS

CREATE VIEW vw_HistorialCobros AS
SELECT

    p.idPago,

    perInq.Nombre AS NombreInquilino,
    perInq.ApellidoP,
    perInq.ApellidoM,

    pr.NumeroIdentificador,
    pr.TipoPropiedad,

    pa.MontoPagado,
    pa.TipoPago,
    pa.FechaPago,

    ad.MontoPendiente,
    ad.Estado AS EstadoAdeudo,

    tc.NombreTienda,

    perUsr.Nombre AS NombreCobrador,
    perUsr.ApellidoP AS ApellidoCobrador,

    pa.idAutorizacion

FROM Pagos pa

INNER JOIN ContratosArrendamiento ca
    ON pa.idContrato = ca.idContrato

INNER JOIN Inquilinos i
    ON ca.idInquilino = i.idInquilino

INNER JOIN Personas perInq
    ON i.idPersona = perInq.idPersona

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

INNER JOIN Usuarios u
    ON pa.idUsuario = u.idUsuario

INNER JOIN Personas perUsr
    ON u.idPersona = perUsr.idPersona

INNER JOIN Tiendas_Cobro tc
    ON pa.idTienda = tc.idTienda

LEFT JOIN Adeudos ad
    ON ca.idContrato = ad.idContrato;

-- VISTA: SOLICITUDES DE ABONO

CREATE VIEW vw_SolicitudesAbono AS
SELECT

    aa.idAutorizacion,

    perInq.Nombre AS NombreInquilino,
    perInq.ApellidoP,
    perInq.ApellidoM,
    perInq.Telefono,

    aa.MontoMinimoAceptado,
    aa.FechaExpiracionAutorizacion,

    perUsr.Nombre AS NombreAutorizador,
    perUsr.ApellidoP AS ApellidoAutorizador,

    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    ad.MontoPendiente,
    ad.FechaLimite,
    ad.Estado

FROM Autorizaciones_Abono aa

INNER JOIN Inquilinos i
    ON aa.idInquilino = i.idInquilino

INNER JOIN Personas perInq
    ON i.idPersona = perInq.idPersona

INNER JOIN Usuarios u
    ON aa.idUsuario = u.idUsuario

INNER JOIN Personas perUsr
    ON u.idPersona = perUsr.idPersona

INNER JOIN ContratosArrendamiento ca
    ON i.idInquilino = ca.idInquilino

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

LEFT JOIN Adeudos ad
    ON ca.idContrato = ad.idContrato;

-- VISTA: ADEUDOS PENDIENTES

CREATE VIEW vw_AdeudosPendientes AS
SELECT

    ad.idAdeudo,

    per.Nombre AS NombreInquilino,
    per.ApellidoP,
    per.ApellidoM,

    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    ad.MontoTotal,
    ad.MontoPendiente,
    ad.FechaLimite,
    ad.Estado

FROM Adeudos ad

INNER JOIN ContratosArrendamiento ca
    ON ad.idContrato = ca.idContrato

INNER JOIN Inquilinos i
    ON ca.idInquilino = i.idInquilino

INNER JOIN Personas per
    ON i.idPersona = per.idPersona

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad;

-- VISTA: VER PRODUCTOS

CREATE VIEW vw_Productos AS
SELECT

    idProducto,
    NombreProducto,
    CantidadDisponible,
    Descripcion,
    Imagen,

    CASE
        WHEN CantidadDisponible = 0 THEN 'Sin stock'
        WHEN CantidadDisponible <= 5 THEN 'Stock bajo'
        ELSE 'Disponible'
    END AS EstadoStock

FROM Bodega_Inventario;

-- VISTA: VER REPORTES

CREATE VIEW vw_Reportes AS
SELECT

    r.idReporte,

    r.Titulo,
    r.Descripcion,

    r.TipoReporte,
    r.Prioridad,
    r.Estado,

    r.Evidencia,
    r.FechaRegistro,

    p.idPropiedad,
    p.TipoPropiedad,
    p.NumeroIdentificador,
    p.Imagen AS ImagenPropiedad,

    per.Nombre AS NombreUsuario,
    per.ApellidoP,
    per.ApellidoM,
    per.Imagen AS ImagenUsuario,

    u.Usuario

FROM Reportes r

INNER JOIN Usuarios u
    ON r.idUsuario = u.idUsuario

INNER JOIN Personas per
    ON u.idPersona = per.idPersona

INNER JOIN Propiedades p
    ON r.idPropiedad = p.idPropiedad;

-- VISTA: VER NOTIFICACIONES

CREATE VIEW Vista_Notificaciones AS
SELECT
    n.idNotificacion,
    n.idUsuario,

    CONCAT(
        p.Nombre, ' ',
        p.ApellidoP, ' ',
        IFNULL(p.ApellidoM,'')
    ) AS Usuario,

    n.Titulo,
    n.Mensaje,
    n.TipoNotificacion,
    n.Estado,
    n.FechaNotificacion,

    TIMESTAMPDIFF(MINUTE, n.FechaNotificacion, NOW()) AS MinutosTranscurridos

FROM Notificaciones n
INNER JOIN Usuarios u
    ON n.idUsuario = u.idUsuario
INNER JOIN Personas p
    ON u.idPersona = p.idPersona;