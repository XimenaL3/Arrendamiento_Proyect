CREATE DATABASE SunlightGardens;

USE SunlightGardens;

-- =========================================
-- TABLA: PERSONAS
-- =========================================

CREATE TABLE Personas(
    idPersona INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    ApellidoP VARCHAR(100) NOT NULL,
    ApellidoM VARCHAR(100),
    Telefono VARCHAR(20),
    Correo VARCHAR(150) UNIQUE,
    Imagen VARCHAR(255)
);

-- =========================================
-- TABLA: ROLES
-- =========================================

CREATE TABLE Roles(
    idRol INT AUTO_INCREMENT PRIMARY KEY,
    NombreRol VARCHAR(50) NOT NULL UNIQUE
);

-- =========================================
-- TABLA: USUARIOS
-- =========================================

CREATE TABLE Usuarios(
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    idPersona INT NOT NULL,
    idRol INT NOT NULL,
    Usuario VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,

    FOREIGN KEY (idPersona)
        REFERENCES Personas(idPersona)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (idRol)
        REFERENCES Roles(idRol)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: PROPIEDADES
-- =========================================

CREATE TABLE Propiedades(
    idPropiedad INT AUTO_INCREMENT PRIMARY KEY,

    TipoPropiedad ENUM(
        'Local comercial',
        'Casa',
        'Edificio'
    ) NOT NULL,

    NumeroIdentificador VARCHAR(50) NOT NULL UNIQUE,

    Descripcion TEXT,

    EstadoFisico ENUM(
        'Buenas condiciones',
        'Malas condiciones',
        'En mantenimiento'
    ) NOT NULL,

    EstadoDisponibilidad ENUM(
        'Disponible',
        'Rentado',
        'Aspecto Legal'
    ) NOT NULL,

    Imagen VARCHAR(255)
);

-- =========================================
-- TABLA: INQUILINOS
-- =========================================

CREATE TABLE Inquilinos(
    idInquilino INT AUTO_INCREMENT PRIMARY KEY,

    idPersona INT NOT NULL,

    HistorialCrediticio ENUM(
        'Bueno',
        'Malo',
        'Nuevo'
    ) NOT NULL,

    RegistroDeudasPrevias BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (idPersona)
        REFERENCES Personas(idPersona)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: CONTRATOS
-- =========================================

CREATE TABLE ContratosArrendamiento(
    idContrato INT AUTO_INCREMENT PRIMARY KEY,

    idInquilino INT NOT NULL,
    idPropiedad INT NOT NULL,

    FechaInicio DATE NOT NULL,
    FechaFin DATE NOT NULL,

    MontoRenta DECIMAL(10,2) NOT NULL,
    MontoDeposito DECIMAL(10,2) NOT NULL,

    Observaciones TEXT,

    PermitirAbonos BOOLEAN DEFAULT FALSE,

    Evidencia VARCHAR(255),

    FOREIGN KEY (idInquilino)
        REFERENCES Inquilinos(idInquilino)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idPropiedad)
        REFERENCES Propiedades(idPropiedad)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: SERVICIOS
-- =========================================

CREATE TABLE Servicios(
    idServicio INT AUTO_INCREMENT PRIMARY KEY,
    NombreServicio VARCHAR(100) NOT NULL UNIQUE
);

-- =========================================
-- TABLA: PROPIEDAD_SERVICIOS
-- =========================================

CREATE TABLE Propiedad_Servicios(
    idPropiedadServicio INT AUTO_INCREMENT PRIMARY KEY,

    idPropiedad INT NOT NULL,
    idServicio INT NOT NULL,

    ManejoPorPorcentaje BOOLEAN DEFAULT FALSE,

    PorcentajeAsignado DECIMAL(5,2),

    CostoFijo DECIMAL(10,2),

    FOREIGN KEY (idPropiedad)
        REFERENCES Propiedades(idPropiedad)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (idServicio)
        REFERENCES Servicios(idServicio)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: TIENDAS COBRO
-- =========================================

CREATE TABLE Tiendas_Cobro(
    idTienda INT AUTO_INCREMENT PRIMARY KEY,

    NombreTienda VARCHAR(150) NOT NULL,

    idPropiedad INT NOT NULL,

    FOREIGN KEY (idPropiedad)
        REFERENCES Propiedades(idPropiedad)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: ADEUDOS
-- =========================================

CREATE TABLE Adeudos(
    idAdeudo INT AUTO_INCREMENT PRIMARY KEY,

    idContrato INT NOT NULL,

    MontoTotal DECIMAL(10,2) NOT NULL,

    MontoPendiente DECIMAL(10,2) NOT NULL,

    FechaLimite DATE NOT NULL,

    PermitirAbonos BOOLEAN DEFAULT FALSE,

    Estado ENUM(
        'Pendiente',
        'Liquidado'
    ) NOT NULL,

    FOREIGN KEY (idContrato)
        REFERENCES ContratosArrendamiento(idContrato)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: SOLICITUDES ABONO
-- =========================================

CREATE TABLE Solicitudes_Abono(
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,

    -- COBRADOR
    idUsuarioSolicita INT NOT NULL,

    -- ADMINISTRADOR
    idAdministrador INT NULL,

    -- CONTRATO
    idContrato INT NOT NULL,

    -- INQUILINO
    idInquilino INT NOT NULL,

    -- MONTO QUE QUIERE PAGAR
    MontoSolicitado DECIMAL(10,2) NOT NULL,

    -- MONTO APROBADO
    MontoAutorizado DECIMAL(10,2) NULL,

    -- FECHA LIMITE
    FechaLimitePago DATE NULL,

    Observaciones TEXT NULL,

    EstadoSolicitud ENUM(
        'Pendiente',
        'Aprobada',
        'Rechazada',
        'Pagada',
        'Expirada'
    ) NOT NULL DEFAULT 'Pendiente',

    FechaSolicitud DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FechaRevision DATETIME NULL,

    FOREIGN KEY (idUsuarioSolicita)
        REFERENCES Usuarios(idUsuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idAdministrador)
        REFERENCES Usuarios(idUsuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    FOREIGN KEY (idContrato)
        REFERENCES ContratosArrendamiento(idContrato)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idInquilino)
        REFERENCES Inquilinos(idInquilino)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: PAGOS
-- =========================================

CREATE TABLE Pagos(
    idPago INT AUTO_INCREMENT PRIMARY KEY,

    idContrato INT NOT NULL,

    idTienda INT NOT NULL,

    idUsuario INT NOT NULL,

    idSolicitud INT NULL,

    FechaPago DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    MontoPagado DECIMAL(10,2) NOT NULL,

    TipoPago ENUM(
        'Completo',
        'Abono'
    ) NOT NULL,

    FOREIGN KEY (idContrato)
        REFERENCES ContratosArrendamiento(idContrato)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idTienda)
        REFERENCES Tiendas_Cobro(idTienda)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idUsuario)
        REFERENCES Usuarios(idUsuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idSolicitud)
        REFERENCES Solicitudes_Abono(idSolicitud)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: HISTORIAL APROBACIONES
-- =========================================

CREATE TABLE Historial_Aprobaciones_Abono(
    idHistorial INT AUTO_INCREMENT PRIMARY KEY,

    idSolicitud INT NOT NULL,

    idAdministrador INT NOT NULL,

    Accion ENUM(
        'Aprobado',
        'Rechazado',
        'Cancelado'
    ) NOT NULL,

    Comentario TEXT NULL,

    FechaMovimiento DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (idSolicitud)
        REFERENCES Solicitudes_Abono(idSolicitud)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (idAdministrador)
        REFERENCES Usuarios(idUsuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: INVENTARIO
-- =========================================

CREATE TABLE Bodega_Inventario(
    idProducto INT AUTO_INCREMENT PRIMARY KEY,

    NombreProducto VARCHAR(150) NOT NULL,

    CantidadDisponible INT NOT NULL DEFAULT 0,

    Descripcion TEXT,

    Imagen VARCHAR(255)
);

-- =========================================
-- TABLA: MANTENIMIENTO
-- =========================================

CREATE TABLE Mantenimiento_Detalle(
    idMantenimiento INT AUTO_INCREMENT PRIMARY KEY,

    idPropiedad INT NOT NULL,

    idUsuario INT NOT NULL,

    idProducto INT NOT NULL,

    TareaRealizada TEXT NOT NULL,

    FechaInicio DATETIME NOT NULL,

    FechaFin DATETIME,

    FOREIGN KEY (idPropiedad)
        REFERENCES Propiedades(idPropiedad)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idUsuario)
        REFERENCES Usuarios(idUsuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idProducto)
        REFERENCES Bodega_Inventario(idProducto)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: VISITAS COBRANZA
-- =========================================

CREATE TABLE Visitas_Cobranza(
    idVisita INT AUTO_INCREMENT PRIMARY KEY,

    idUsuario INT NOT NULL,

    idInquilino INT NOT NULL,

    FechaVisita DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    Observaciones TEXT,

    Estatus ENUM(
        'Pendiente',
        'En atencion',
        'Atendida',
        'Cancelada'
    ) NOT NULL DEFAULT 'Pendiente',

    FOREIGN KEY (idUsuario)
        REFERENCES Usuarios(idUsuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idInquilino)
        REFERENCES Inquilinos(idInquilino)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: REPORTES
-- =========================================

CREATE TABLE Reportes(
    idReporte INT AUTO_INCREMENT PRIMARY KEY,

    idInquilino INT NOT NULL,

    idPropiedad INT NOT NULL,

    Titulo VARCHAR(150) NOT NULL,

    Descripcion TEXT NOT NULL,

    TipoReporte ENUM(
        'Mantenimiento',
        'Cobranza',
        'Legal',
        'Inventario',
        'General'
    ) NOT NULL,

    Prioridad ENUM(
        'Baja',
        'Media',
        'Alta'
    ) NOT NULL DEFAULT 'Media',

    Estado ENUM(
        'Pendiente',
        'En proceso',
        'Finalizado',
        'Cancelado'
    ) NOT NULL DEFAULT 'Pendiente',

    Evidencia VARCHAR(255),

    FechaRegistro DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (idInquilino)
        REFERENCES Inquilinos(idInquilino)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (idPropiedad)
        REFERENCES Propiedades(idPropiedad)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =========================================
-- TABLA: NOTIFICACIONES
-- =========================================

CREATE TABLE Notificaciones(
    idNotificacion INT AUTO_INCREMENT PRIMARY KEY,

    idUsuario INT NOT NULL,

    Titulo VARCHAR(150) NOT NULL,

    Mensaje TEXT NOT NULL,

    TipoNotificacion ENUM(
        'Reporte',
        'Pago',
        'Cobranza',
        'Mantenimiento',
        'Sistema',
        'Legal'
    ) NOT NULL,

    Estado ENUM(
        'No leida',
        'Leida'
    ) NOT NULL DEFAULT 'No leida',

    FechaNotificacion DATETIME
    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (idUsuario)
        REFERENCES Usuarios(idUsuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

ALTER TABLE Mantenimiento_Detalle
ADD COLUMN idReporte INT NOT NULL,
ADD CONSTRAINT fk_mantenimiento_reporte
FOREIGN KEY (idReporte)
REFERENCES Reportes(idReporte)
ON DELETE RESTRICT
ON UPDATE CASCADE;

CREATE TABLE Reporte_Productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idReporte INT,
    idProducto INT,
    cantidad INT,
    fechaAsignacion DATETIME DEFAULT CURRENT_TIMESTAMP
);