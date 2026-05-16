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