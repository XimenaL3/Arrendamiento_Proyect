<?php

session_start();

require_once "../../includes/Conexion.php";

if(isset($_POST['idNotificacion']))
{

    $id = intval($_POST['idNotificacion']);

    $sql = "
    UPDATE Notificaciones
    SET Estado = 'Leida'
    WHERE idNotificacion = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    echo "OK";

}