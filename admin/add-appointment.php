<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

if ($_POST) {
    // Importar la conexión a la base de datos
    include("../connection.php");

    // Obtener los valores del formulario
    $pid = $_POST["pid"];
    $scheduleid = $_POST["scheduleid"];

    // Obtener el número máximo de citas permitidas ("nop") para este horario
    $sql = "SELECT nop FROM schedule WHERE scheduleid = $scheduleid";
    $result = $database->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxAppointments = $row["nop"];

        // Comprobar si se puede programar una nueva cita
        $sql = "INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`)
                SELECT NULL, $pid, IFNULL(MAX(apponum), 0) + 1, $scheduleid, NOW()
                FROM appointment
                WHERE scheduleid = $scheduleid
                HAVING COUNT(*) < $maxAppointments";

        $result = $database->query($sql);

        if ($result) {
            // Verificar cuántas filas se vieron afectadas por la inserción
            if ($database->affected_rows > 0) {
                // Mostrar una alerta en lugar de redirigir
         
                header("location: appointment.php");
            } else {
                // Mostrar una alerta en lugar de redirigir
                
                header("location: appointment.php?action=appointment-error&message=No hay espacio disponible para programar una cita en este horario.");
            }
        }
    }
}
?>