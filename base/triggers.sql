-- =========================================
-- PROCEDIMIENTO: REGISTRAR NOTIFICACION
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_RegistrarNotificacion(
    IN p_idUsuario INT,
    IN p_Titulo VARCHAR(150),
    IN p_Mensaje TEXT,
    IN p_TipoNotificacion ENUM(
        'Reporte',
        'Pago',
        'Cobranza',
        'Mantenimiento',
        'Sistema',
        'Legal'
    )
)
BEGIN

    INSERT INTO Notificaciones(
        idUsuario,
        Titulo,
        Mensaje,
        TipoNotificacion
    )
    VALUES(
        p_idUsuario,
        p_Titulo,
        p_Mensaje,
        p_TipoNotificacion
    );

END $$

DELIMITER ;

-- =========================================
-- PROCEDIMIENTO: MARCAR NOTIFICACION LEIDA
-- =========================================

DELIMITER $$

CREATE PROCEDURE sp_MarcarNotificacionLeida(
    IN p_idNotificacion INT
)
BEGIN

    UPDATE Notificaciones
    SET Estado = 'Leida'
    WHERE idNotificacion = p_idNotificacion;

END $$

DELIMITER ;