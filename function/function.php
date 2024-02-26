<?php
require_once('connection.php');

function getSite($site)
{
    if(isset($_GET['site'])){
        include_once('mealmaster_web/auth/scripts/'.$_GET['site'].'.php');
    } else{
        include_once('mealmaster_web/auth/scripts/'.$site.'.php');
    }
}

function checkUserData(){
    if(isset($_POST['login'])){
        $email=$_POST['email'];
        $password=$_POST['password'];

        $db = new DatabaseConnection();

        if ($db->checkUser($email, $password)) {
            echo "accepted";
        } else {
            echo '<p style="color:red;font-size:12px"><b>Bitte geben Sie gültige Daten ein!</b></p>';
        }
    }
}