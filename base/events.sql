-- =========================================
-- ACTIVAR EVENTOS MYSQL
-- =========================================

SET GLOBAL event_scheduler = ON;

-- =========================================
-- EVENTO AUTOMATICO
-- =========================================

DELIMITER $$

CREATE EVENT IF NOT EXISTS ev_liberar_propiedades_vencidas
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN

    -- =====================================
    -- LIBERAR PROPIEDADES
    -- =====================================

    UPDATE Propiedades p
    INNER JOIN ContratosArrendamiento c
        ON p.idPropiedad = c.idPropiedad

    SET p.EstadoDisponibilidad = 'Disponible'

    WHERE c.FechaFin < CURDATE()
    AND c.EstadoContrato = 'Activo'
    AND p.EstadoDisponibilidad = 'Rentado';

    -- =====================================
    -- FINALIZAR CONTRATOS
    -- =====================================

    UPDATE ContratosArrendamiento

    SET EstadoContrato = 'Finalizado'

    WHERE FechaFin < CURDATE()
    AND EstadoContrato = 'Activo';

END $$

DELIMITER ;

DELIMITER $$

CREATE EVENT ev_visitas_en_atencion
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN

    UPDATE Visitas_Cobranza
    SET Estatus = 'En atencion'
    WHERE Estatus = 'Pendiente'
    AND FechaVisita <= NOW();

END$$

DELIMITER ;

DELIMITER $$

CREATE EVENT ev_visitas_atendidas
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN

    UPDATE Visitas_Cobranza
    SET Estatus = 'Atendida'
    WHERE Estatus = 'En atencion'
    AND DATE_ADD(FechaVisita, INTERVAL 3 HOUR) <= NOW();

END$$

DELIMITER ;

DELIMITER $$

CREATE EVENT ev_generar_adeudos_renta
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN

    INSERT INTO Adeudos
    (
        idContrato,
        MontoTotal,
        MontoPendiente,
        FechaLimite,
        PermitirAbonos,
        Estado
    )

    SELECT
        ca.idContrato,

        ca.MontoRenta,

        ca.MontoRenta,

        CURDATE(),

        ca.PermitirAbonos,

        'Pendiente'

    FROM ContratosArrendamiento ca

    WHERE

        /* CONTRATOS ACTIVOS */

        CURDATE() BETWEEN ca.FechaInicio
        AND ca.FechaFin

        /* QUE HOY SEA EL MISMO DIA DEL MES */

        AND DAY(CURDATE()) =
        DAY(ca.FechaInicio)

        /* EVITAR DUPLICADOS */

        AND NOT EXISTS(

            SELECT 1
            FROM Adeudos a

            WHERE a.idContrato =
            ca.idContrato

            AND MONTH(a.FechaLimite) =
            MONTH(CURDATE())

            AND YEAR(a.FechaLimite) =
            YEAR(CURDATE())

        );

END$$

DELIMITER ;