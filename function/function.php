<?php
require_once('connection.php');

session_start();

global $ben_id;

if(isset($_POST['accept']))
{
    $id=$_POST['id'];
    activateUser($id);
}
if(isset($_POST['decline']))
{
    $id=$_POST['id'];
    deactivateUser($id);
}
if(isset($_POST['admin']))
{
    $id=$_POST['id'];
    premoteUserToAdmin($id);
}


function isAdmin(){
    $db = new DatabaseConnection();
    $query="select rolle_idrolle from Benutzer where ben_id=?";
    global $ben_id;
    $array=array($ben_id);

    $stmt = $db->makeStatement($query, $array);

    $result = $stmt->fetch(PDO::FETCH_ASSOC); // Hier wird das Ergebnis aus dem Statement geholt
    return ($result['rolle_idrolle'] == 1);

}

function logout(){
    if(isset($_POST['logout'])){
        unset($_SESSION['ben_id']);
        global $ben_id;
        $ben_id=0;
        // header("Location: mealmaster_web/Account/scripts/Account.php"); zu startseite navigieren

    }
}

function saveUserData(){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email=$_POST['hidden_mail'];
        $firstname=$_POST['hidden_firstname'];
        $lastname=$_POST['hidden_lastname'];
        $class=$_POST['hidden_class'];
        if (!empty($email) || !empty($firstname) || !empty($lastname) || !empty($class)) {
        global $ben_id;

        $db = new DatabaseConnection();

        $query="update benutzer set mail=?, vname=?, nname=?, class=? where ben_id=?";

        $array=array();
        array_push($array, $email);
        array_push($array, $firstname);
        array_push($array, $lastname);
        array_push($array, $class);
        array_push($array, $ben_id);

        $db->makeStatement($query, $array);
        }
        header("Location: ".$_SERVER['PHP_SELF']);
        exit(); 
    }
}

function setUserId(){
    global $ben_id;
    $ben_id=$_SESSION['ben_id'];
}

function getSite($site)
{
    if(isset($_GET['site'])){
        include_once('mealmaster_web/auth/scripts/'.$_GET['site'].'.php');
    } else{
        include_once('mealmaster_web/auth/scripts/'.$site.'.php');
    }
}

function deactivateUser($id){
    $db = new DatabaseConnection();
    $query="update benutzer set rolle_idrolle=0 where ben_id=?";
    $array=array($id);
    $db->makeStatement($query, $array);
}

function activateUser($id){
    $db = new DatabaseConnection();
    $query="update benutzer set rolle_idrolle=2 where ben_id=?";
    $array=array($id);
    $db->makeStatement($query, $array);
}

function premoteUserToAdmin($id){
    $db = new DatabaseConnection();
    $query="update benutzer set rolle_idrolle=1 where ben_id=?";
    $array=array($id);
    $db->makeStatement($query, $array);
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
                global $ben_id;

                $query="select ben_id from benutzer where mail=?";
                $array=array($email);
                $stmt=$db->makeStatement($query, $array);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $result['ben_id'];

                $ben_id=$id;

                header("Location: mealmaster_web/Account/scripts/Account.php");
                $_SESSION['ben_id']=$ben_id;
                exit;
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

function makeTableForNewUser(){
    $query="select ben_id as id,mail as EMail, vname as Vorname, nname as Nachname, class as Klasse from Benutzer where rolle_idrolle=3";
    makeTableAcceptOrDeclineOrAdmin($query);
}

function makeTableForActiveUser(){
    $query="select ben_id as id,mail as EMail, vname as Vorname, nname as Nachname, class as Klasse from Benutzer where rolle_idrolle=2 or rolle_idrolle=1";
    makeTableDeleteOrAdmin($query);
}

function makeTableForDeactivatedUser(){
    $query="select ben_id as id,mail as EMail, vname as Vorname, nname as Nachname, class as Klasse from Benutzer where rolle_idrolle=0";
    makeTableAccept($query);
}


function makeTableAcceptOrDeclineOrAdmin($query, $array = null)
{
    $db = new DatabaseConnection();

    $stmt = $db->makeStatement($query, $array);
    if($stmt instanceof Exception)
    {
        echo $stmt->getCode().': '.$stmt->getMessage();
    } 
    else
    {
        if($stmt->rowCount() != 0){
            //Tabelle erstellen
            $meta = array();
            // Spaltenüberschrifen dynamisch
            echo '<div class="table-container">';
            echo '<table class="table table1">';
            echo '<thead>';
            echo '<tr>';
            for($i = 0; $i < $stmt->columnCount(); $i++)
            {
                $meta[] = $stmt->getColumnMeta($i);
                echo '<th>'.$meta[$i]['name'].'</th>';
            }
            echo '<th>Optionen</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            /*
            Zugriff auf Arrayelement
            FETCH_ASSOC:  $row['id']
            FETCH_NUM:    $row[0]
            */
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo '<tr>';
                foreach($row as $r)
                {
                    echo '<td>'.$r.'</td>';
                }
                echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <input type='submit' name='accept' value='akzeptieren'>
                            <input type='submit' name='decline' value='ablehenen'>
                            <input type='submit' name='admin' value='admin'>
                        </form>
                </td>";
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        else{
            echo "<h1>Keine Datensätze gefunden</h1>";
        }
    }
}

function makeTableDeleteOrAdmin($query, $array = null)
{
    $db = new DatabaseConnection();

    $stmt = $db->makeStatement($query, $array);
    if($stmt instanceof Exception)
    {
        echo $stmt->getCode().': '.$stmt->getMessage();
    } 
    else
    {
        if($stmt->rowCount() != 0){
            //Tabelle erstellen
            $meta = array();
            // Spaltenüberschrifen dynamisch
            echo '<div class="table-container">';
            echo '<table class="table table2">';
            echo '<thead>';
            echo '<tr>';
            for($i = 0; $i < $stmt->columnCount(); $i++)
            {
                $meta[] = $stmt->getColumnMeta($i);
                echo '<th>'.$meta[$i]['name'].'</th>';
            }
            echo '<th>Optionen</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            /*
            Zugriff auf Arrayelement
            FETCH_ASSOC:  $row['id']
            FETCH_NUM:    $row[0]
            */
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo '<tr>';
                foreach($row as $r)
                {
                    echo '<td>'.$r.'</td>';
                }
                echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <input type='submit' name='decline' value='decline'>
                            <input type='submit' name='admin' value='admin'>
                        </form>
                </td>";
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        else{
            echo "<h1>Keine Datensätze gefunden</h1>";
        }
    }
}

function makeTableAccept($query, $array = null)
{
    $db = new DatabaseConnection();

    $stmt = $db->makeStatement($query, $array);
    if($stmt instanceof Exception)
    {
        echo $stmt->getCode().': '.$stmt->getMessage();
    } 
    else
    {
        if($stmt->rowCount() != 0){
            //Tabelle erstellen
            $meta = array();
            // Spaltenüberschrifen dynamisch
            echo '<div class="table-container">';
            echo '<table class="table table3">';
            echo '<thead>';
            echo '<tr>';
            for($i = 0; $i < $stmt->columnCount(); $i++)
            {
                $meta[] = $stmt->getColumnMeta($i);
                echo '<th>'.$meta[$i]['name'].'</th>';
            }
            echo '<th>Optionen</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            /*
            Zugriff auf Arrayelement
            FETCH_ASSOC:  $row['id']
            FETCH_NUM:    $row[0]
            */
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo '<tr>';
                foreach($row as $r)
                {
                    echo '<td>'.$r.'</td>';
                }
                echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <input type='submit' name='accept' value='accept'>
                        </form>
                </td>";
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        else{
            echo "<h1>Keine Datensätze gefunden</h1>";
        }
    }
}

function getEmail(){
    global $ben_id;

    $db = new DatabaseConnection();

    $query="select mail from benutzer where ben_id=?";
    $array=array($ben_id);
    $stmt=$db->makeStatement($query, $array);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $mail = $result['mail'];

    return $mail;
}

function getFirstname(){
    global $ben_id;

    $db = new DatabaseConnection();

    $query="select vname from benutzer where ben_id=?";
    $array=array($ben_id);
    $stmt=$db->makeStatement($query, $array);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $firstName = $result['vname'];

    return $firstName;
}

function getLastname(){
    global $ben_id;

    $db = new DatabaseConnection();

    $query="select nname from benutzer where ben_id=?";
    $array=array($ben_id);
    $stmt=$db->makeStatement($query, $array);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastName = $result['nname'];

    return $lastName;
}

function getClass(){
    global $ben_id;

    $db = new DatabaseConnection();

    $query="select class from benutzer where ben_id=?";
    $array=array($ben_id);
    $stmt=$db->makeStatement($query, $array);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $class = $result['class'];

    return $class;
}