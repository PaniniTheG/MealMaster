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
            if(!($db->isAccepted($email, $password))){
                echo '<p style="color:red;font-size:12px"><b>Benutzer wurde noch nicht durch einen Admin bestätigt!</b></p>';
            }
            else{
                echo "accepted";
            }
        } else {
            echo '<p style="color:red;font-size:12px"><b>Bitte geben Sie gültige Daten ein!</b></p>';
        }
    }
}

function registerNewUser(){
    if(isset($_POST['register'])){
        $firstName=$_POST['firstName'];
        $lastName=$_POST['lastName'];
        $class=$_POST['class'];
        $email=$_POST['email'];
        $password=$_POST['password'];
        $repeadPassword=$_POST['repeadPassword'];

        $db = new DatabaseConnection();

        if($password==$repeadPassword){
            if($db->createNewUser($firstName,$lastName,$class,$email,$password)){
                echo '<p style="color:green;font-size:12px"><b>Account freigabe wurde angefragt!</b></p>';
            }
            else{
                if($db->checkIfUserAlreadyExists($email)){
                    echo '<p style="color:orange;font-size:12px"><b>Es existiert bereits ein Account mit dieser Email!</b></p>';
                }
                else{
                    echo '<p style="color:red;font-size:12px"><b>Ein Fehler ist aufgetreten, bitte versuchen sie es später erneut!</b></p>';
                }
            }
        }
        else{
            echo '<p style="color:red;font-size:12px"><b>Passwörter stimmen nicht überein!</b></p>';
        }
    }
}

function sendResetRequest(){
    if(isset($_POST['send'])){
        $email=$_POST['ResetPwEmail'];

        $db = new DatabaseConnection();

        $db->sendResetRequest($email);
    }
}