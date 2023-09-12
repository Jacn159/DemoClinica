<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" or $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

// Importar la conexión a la base de datos
include("../connection.php");

if ($_POST) {
    $error = '3';
    $pemail = $_POST['pemail'];
    $pname = $_POST['pname'];
    $ppassword = $_POST['ppassword'];
    $cpassword = $_POST['cpassword'];
    $paddress = $_POST['paddress'];
    $pnic = $_POST['pnic'];
    $pdob = $_POST['pdob'];
    $ptel = $_POST['ptel'];

    // Comprobar si el correo electrónico ya está registrado
    $result = $database->query("SELECT * FROM webuser WHERE email='$pemail'");

    if ($result->num_rows == 1) {
        $error = '1';
    } else {
        if ($ppassword == $cpassword) {
            // Insertar el nuevo paciente en la tabla de pacientes
            $sql1 = "INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, ptel) VALUES ('$pemail', '$pname', '$ppassword', '$paddress', '$pnic', '$pdob', '$ptel')";
            $sql2 = "INSERT INTO webuser (email, usertype) VALUES ('$pemail', 'p')";

            if ($database->query($sql1) && $database->query($sql2)) {
                $error = '4';
            } else {
                $error = '2';
            }
        }

    }
} else {
    $error = '3';
}

header("location: patient.php?action=add&error=" . $error);
?>