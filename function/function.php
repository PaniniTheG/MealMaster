<?php
require_once('connection.php');

function getSite($site)
{
    if(isset($_GET['site'])){
        include_once('mealmaster_web/Admin/scripts/'.$_GET['site'].'.php');
    } else{
        include_once('mealmaster_web/Admin/scripts/'.$site.'.php');
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
        $repeadPassword=$_POST['repeatPassword'];

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

        if($db->checkIfUserAlreadyExists($email)){
            global $pin;

            $pin = rand(100000, 999999);
            $db->sendResetRequest($email, $pin);
        }

        $subject = "My subject";
        $txt = "Sehr geehrter Nutzer,

Wir haben eine Anfrage zum Zurücksetzen Ihres Passworts erhalten. Sie wurden in Ihrem Web-Browser weitergeleitet. Bitte geben Sie den unten stehenden PIN ein!

PIN: $pin

Wenn Sie diese Anfrage nicht gestellt haben, können Sie diese E-Mail ignorieren. Ihr Konto bleibt weiterhin sicher.

Wenn Sie weitere Hilfe benötigen oder Fragen haben, zögern Sie bitte nicht, uns zu kontaktieren.

Mit freundlichen Grüßen,
Das Support-Team";

        $headers = "From: MealMasterSupport@noreply.com";
 
        mail($email,$subject,$txt,$headers);

        echo '<script>window.location.href = "index.php?site=Reset_Password";</script>';

    }
}

function resetPassword(){
    if(isset($_POST['reset'])){

        $password=$_POST['password'];
        $repeadPassword=$_POST['repeadPassword'];
        $pin=$_POST['pin'];

        if($password==$repeadPassword){
            confirmResetPassword($password, $pin);
            echo '<script>window.location.href = "index.php?site=LogIn";</script>';
        }
        else{
            echo '<p style="color:red;font-size:12px"><b>Passwörter stimmen nicht überein!</b></p>';
        }
    }
}

function confirmResetPassword($password, $pin){
    $db = new DatabaseConnection();

    if($db->checkPin($pin))
    {
        $db->updatePassword($password, $pin);
    }
}

function insertSpeise($gericht)
{
    $db = new DatabaseConnection();
    $db->insertNewGericht($gericht);

}

function insertMittag($date, $idgericht)
{
    $db = new DatabaseConnection();
    $db->insertNewMittag($date, $idgericht);

}

function insertAbend($date, $idgericht)
{
    $db = new DatabaseConnection();
    $db->insertNewAbend($date, $idgericht);

}

function ausgabeGericht()
{
    $db = new DatabaseConnection();
    $gt = $db->getGericht();

    while ($row = $gt->fetch(PDO::FETCH_ASSOC)){
        sort($row);
        $currentLetter = null;
        foreach ($row as $r) {
            $firstLetter = strtoupper(substr($r, 0, 1));
            if ($firstLetter !== $currentLetter) {
                echo "<h2>$currentLetter</h2>";
                $currentLetter = $firstLetter;
            }
            echo "<div class='gericht' draggable='true' ondragstart='drag(event)'>$r</div>";
            }
        }
    }

 function getID()
 {
    $db = new DatabaseConnection();
    $gt = $db->getGerichtID();
 }   