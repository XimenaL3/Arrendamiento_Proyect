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

    i.idInquilino

FROM Reportes r

INNER JOIN Inquilinos i
    ON r.idInquilino = i.idInquilino

INNER JOIN Personas per
    ON i.idPersona = per.idPersona

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


CREATE VIEW vw_TiendasCobro AS

SELECT

    tc.idTienda,

    tc.NombreTienda,

    p.idPropiedad,

    p.TipoPropiedad,

    p.NumeroIdentificador,

    p.EstadoDisponibilidad

FROM Tiendas_Cobro tc

INNER JOIN Propiedades p
    ON p.idPropiedad = tc.idPropiedad;

-- =========================================
-- VISTA:
-- HISTORIAL COMPLETO DE PAGOS
-- =========================================

CREATE VIEW vw_HistorialCobros AS

SELECT

    pa.idPago,

    pa.idSolicitud,

    pa.FechaPago,

    pa.MontoPagado,

    pa.TipoPago,

    -- INQUILINO
    perInq.Nombre AS NombreInquilino,
    perInq.ApellidoP,
    perInq.ApellidoM,
    perInq.Telefono,
    perInq.Imagen AS ImagenInquilino,

    -- PROPIEDAD
    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    -- CONTRATO
    ca.idContrato,

    -- ADEUDO
    ad.idAdeudo,
    ad.MontoTotal,
    ad.MontoPendiente,
    ad.Estado AS EstadoAdeudo,
    ad.FechaLimite,

    -- TIENDA
    tc.NombreTienda,

    -- COBRADOR
    perCob.Nombre AS NombreCobrador,
    perCob.ApellidoP AS ApellidoCobrador,

    -- SOLICITUD
    sa.EstadoSolicitud

FROM Pagos pa

INNER JOIN ContratosArrendamiento ca
    ON pa.idContrato = ca.idContrato

INNER JOIN Inquilinos i
    ON ca.idInquilino = i.idInquilino

INNER JOIN Personas perInq
    ON i.idPersona = perInq.idPersona

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

INNER JOIN Usuarios uCob
    ON pa.idUsuario = uCob.idUsuario

INNER JOIN Personas perCob
    ON uCob.idPersona = perCob.idPersona

INNER JOIN Tiendas_Cobro tc
    ON pa.idTienda = tc.idTienda

LEFT JOIN Adeudos ad
    ON ca.idContrato = ad.idContrato

LEFT JOIN Solicitudes_Abono sa
    ON pa.idSolicitud = sa.idSolicitud;

-- =========================================
-- VISTA:
-- SOLICITUDES DE ABONO
-- =========================================

CREATE VIEW vw_SolicitudesAbono AS

SELECT

    sa.idSolicitud,

    sa.idContrato,

    sa.idInquilino,

    sa.idUsuarioSolicita,

    sa.idAdministrador,

    sa.MontoSolicitado,

    sa.MontoAutorizado,

    sa.FechaLimitePago,

    sa.Observaciones,

    sa.EstadoSolicitud,

    sa.FechaSolicitud,

    sa.FechaRevision,

    -- INQUILINO
    perInq.Nombre AS NombreInquilino,
    perInq.ApellidoP,
    perInq.ApellidoM,
    perInq.Telefono,
    perInq.Imagen AS ImagenInquilino,

    -- PROPIEDAD
    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    -- ADEUDO
    ad.idAdeudo,
    ad.MontoTotal,
    ad.MontoPendiente,
    ad.FechaLimite,
    ad.Estado AS EstadoAdeudo,

    -- USUARIO SOLICITANTE
    perSol.Nombre AS NombreSolicitante,
    perSol.ApellidoP AS ApellidoSolicitante,

    -- ADMINISTRADOR
    perAdm.Nombre AS NombreAdministrador,
    perAdm.ApellidoP AS ApellidoAdministrador

FROM Solicitudes_Abono sa

INNER JOIN Inquilinos i
    ON sa.idInquilino = i.idInquilino

INNER JOIN Personas perInq
    ON i.idPersona = perInq.idPersona

INNER JOIN ContratosArrendamiento ca
    ON sa.idContrato = ca.idContrato

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

LEFT JOIN Adeudos ad
    ON sa.idContrato = ad.idContrato
    AND ad.Estado = 'Pendiente'

INNER JOIN Usuarios uSol
    ON sa.idUsuarioSolicita = uSol.idUsuario

INNER JOIN Personas perSol
    ON uSol.idPersona = perSol.idPersona

LEFT JOIN Usuarios uAdm
    ON sa.idAdministrador = uAdm.idUsuario

LEFT JOIN Personas perAdm
    ON uAdm.idPersona = perAdm.idPersona;

-- =========================================
-- VISTA:
-- ADEUDOS PENDIENTES
-- =========================================

CREATE VIEW vw_AdeudosPendientes AS

SELECT

    ad.idAdeudo,

    ad.idContrato,

    ad.MontoTotal,

    ad.MontoPendiente,

    ad.FechaLimite,

    ad.PermitirAbonos,

    ad.Estado,

    -- INQUILINO
    per.Nombre AS NombreInquilino,
    per.ApellidoP,
    per.ApellidoM,
    per.Telefono,
    per.Imagen,

    -- PROPIEDAD
    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    -- CONTRATO
    ca.FechaInicio,
    ca.FechaFin,
    ca.MontoRenta

FROM Adeudos ad

INNER JOIN ContratosArrendamiento ca
    ON ad.idContrato = ca.idContrato

INNER JOIN Inquilinos i
    ON ca.idInquilino = i.idInquilino

INNER JOIN Personas per
    ON i.idPersona = per.idPersona

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

WHERE ad.Estado = 'Pendiente';

-- =========================================
-- VISTA:
-- HISTORIAL DE APROBACIONES
-- =========================================

CREATE VIEW vw_HistorialSolicitudesAbono AS

SELECT

    ha.idHistorial,

    ha.idSolicitud,

    ha.Accion,

    ha.Comentario,

    ha.FechaMovimiento,

    -- SOLICITUD
    sa.MontoSolicitado,
    sa.MontoAutorizado,
    sa.EstadoSolicitud,

    -- INQUILINO
    perInq.Nombre AS NombreInquilino,
    perInq.ApellidoP,
    perInq.ApellidoM,

    -- PROPIEDAD
    pr.TipoPropiedad,
    pr.NumeroIdentificador,

    -- ADMINISTRADOR
    perAdm.Nombre AS NombreAdministrador,
    perAdm.ApellidoP AS ApellidoAdministrador

FROM Historial_Aprobaciones_Abono ha

INNER JOIN Solicitudes_Abono sa
    ON ha.idSolicitud = sa.idSolicitud

INNER JOIN Inquilinos i
    ON sa.idInquilino = i.idInquilino

INNER JOIN Personas perInq
    ON i.idPersona = perInq.idPersona

INNER JOIN ContratosArrendamiento ca
    ON sa.idContrato = ca.idContrato

INNER JOIN Propiedades pr
    ON ca.idPropiedad = pr.idPropiedad

INNER JOIN Usuarios uAdm
    ON ha.idAdministrador = uAdm.idUsuario

INNER JOIN Personas perAdm
    ON uAdm.idPersona = perAdm.idPersona;

CREATE OR REPLACE VIEW vw_abonos_adeudos AS
SELECT 
    a.idAdeudo,
    a.MontoTotal,
    a.MontoPendiente,
    a.FechaLimite,
    a.Estado,

    c.idContrato,
    c.idPropiedad,

    i.idInquilino,
    CONCAT(p.Nombre, ' ', p.ApellidoP) AS Inquilino,
    p.Imagen AS ImagenInquilino,

    pr.NumeroIdentificador AS Propiedad

FROM Adeudos a
INNER JOIN ContratosArrendamiento c ON a.idContrato = c.idContrato
INNER JOIN Inquilinos i ON c.idInquilino = i.idInquilino
INNER JOIN Personas p ON i.idPersona = p.idPersona
INNER JOIN Propiedades pr ON c.idPropiedad = pr.idPropiedad;

CREATE OR REPLACE VIEW vw_MantenimientoDetalle AS
SELECT

    md.idMantenimiento,
    md.idReporte,
    md.idPropiedad,
    md.idUsuario,
    md.idProducto,

    bi.NombreProducto,

    md.TareaRealizada,
    md.FechaInicio,
    md.FechaFin

FROM Mantenimiento_Detalle md

INNER JOIN Bodega_Inventario bi
ON bi.idProducto = md.idProducto;