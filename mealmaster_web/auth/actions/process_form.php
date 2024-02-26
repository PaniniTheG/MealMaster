<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $password = $_POST["password"];

    if (!empty($name) && !empty($password)) {
        echo "Formular erfolgreich abgesendet!";
    } else {
        echo "<span style='color: red;'>Bitte fülle beide Felder aus!</span>";
    }
}
?>